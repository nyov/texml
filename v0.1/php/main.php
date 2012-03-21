<?php
require(dirname(__FILE__) . '/' . ESLA_PHP_VERSION . '_api.php');
require(dirname(__FILE__) . '/../' . ESLA_LIB_PATH . '/phptexrun/texrun_config.php');
require(dirname(__FILE__) . '/../' . ESLA_LIB_PATH . '/phptexrun/texrun.php');
/**
 * Forming document object and marshalling it to TeX 
 *
 * @package pdftexer
 * @author Roman Domrachev 
 * @version 0.1, 21.02.2012
 * @since engine v.0.1
 * @copyright (c) 2012+ by getfo.org project
 */

// TeX marshalling

/**
 * Class for marshalling document object to TeX representation
 */
class EslaMarshaller {
    var $file;

    /**
     * Marshal document to Tex-file
     * 
     * @param type $doc document object
     * @param type $filename name of the file 
     */
    function marshal(&$doc, $filename) {
        $texfilename = $filename . ".tex";
        $this->file = fopen($texfilename, "w");
        
        $this->marsh_head($doc);
        $this->marsh_item($doc);
        
        fclose($this->file); 
    }
    /**
     * Marshal header for TeX-file.
     * 
     * @param type $item item of the document object
     */
    function marsh_head(&$item) {
        fwrite($this->file, "\documentclass[a4paper]{article}\n");
        fwrite($this->file, "\usepackage[width=180mm,height=270mm]{geometry}\n");
        fwrite($this->file, "\usepackage{cals}\n");
        foreach($item->getPackages() as $name) {
            fwrite($this->file, "\usepackage{". $name ."}\n");
        }
    }
    
    /**
     * Marashals item of document
     * 
     * Method marsh_env() can call this method recursively. 
     * 
     * @param type $item item of the document object
     */
    function marsh_item(&$item) {
        switch ($item) {
            case ($item instanceof EslaCommand):
                $this->marsh_cmd($item);
                break;    
            case ($item instanceof EslaText):
                if ($item->getStyle() != null) {
                    $env = new EslaEnvironment($item->getStyle());
                    $env->text($item->getData());
                    $item =& $env;
                } else {
                    $this->marsh_text($item);
                    break;
                }
            case ($item instanceof EslaEnvironment):
                $this->marsh_env($item);
                break;
        }
    }
    
    /**
     * Marshal TeX-command
     * 
     * There is recursive call of method marsh_cmd  
     * 
     * @param type $item command object
     */
    function marsh_cmd(&$item) {
        $cmd = "";
        if ($item->getData() != null) {
            $cmd .= "\\" . $item->getName() . "{" 
                    . $item->getData() . "}\n";    
        } else {            
            if ($item->getCountChilds() > 0) { // it is grouped command
                $childs =& $item->getChilds();
                $cmd .= "\\" . $item->getName() . "{\n";
                fwrite($this->file,  $cmd);
                foreach($childs as $c) {
                    $this->marsh_item($c);
                }
                $cmd = "}\n";
            } else {
                $cmd .= "\\" . $item->getName() . "\n";
            }
        }
        fwrite($this->file,  $cmd);
    }
    
    /**
     * Marshal text
     * 
     * @param type $item text object
     */
    function marsh_text(&$item) {
        fwrite($this->file, $item->getData() . "\n");
    }
    
    /**
     * Marshal TeX-environment
     * 
     * There is recursive call of method marsh_item  
     * 
     * @param type $item environment object
     */
    function marsh_env(&$item) {
        fwrite($this->file, "\n\begin{" . $item->getName() ."}\n");
        if ($item->getCountChilds() > 0) {
            $childs =& $item->getChilds(); 
            foreach($childs as $c) {
                $this->marsh_item($c);
            }
        }
        fwrite($this->file, "\end{" . $item->getName() ."}\n");
    }
}

/**
 * Execute operations for making document in PDF-format.
 *  
 * @param type $doc document object
 * @param type $filename name of PDF-file
 * @param type $include_path path to include directory
 */
function esla_pdf(&$doc, $filename, $include_path = null) {
    $marshaller = new EslaMarshaller();
    $marshaller->marshal($doc, $filename);
    $tp = new tex_project();
    $tp->init_new_with_file($filename . '.tex');
    $tp->run_tex($include_path);
    $pdffile = $tp->get_pdf_file_name();
    $logfile = $tp->get_log_file_name();
    copy($pdffile, $filename);
    copy($logfile, $filename . '.log');
}

// Tex object forming

/**
 * Returns new document object.
 * 
 * Parameters for the function are packages for TeX-document.
 * (\usepackage{<stylename>})
 * 
 * @return type the object
 */
function esla_doc() {
    $doc = new EslaEnvironment("document");
    if (func_num_args() > 0) {
        foreach(func_get_args() as $style) {
            $doc->addPackage($style);
        }
    }
    return $doc;
}

/**
 * Class for text object
 */
class EslaText {
    var $data;
    var $styleName = null;
    /**
     * Constructor
     * 
     * @param type $data the text data
     */
    function EslaText($data) {
        $this->data = $data; 
    }
    /**
     * Getter
     * 
     * @return type data 
     */
    function getData() {
        return $this->data;
    }
    /**
     * Apply style for the text
     * 
     * @param type $name name of style 
     */
    function style($name = null) {
        $this->styleName = $name;
    }
    /**
     * Get name of the text style 
     */
    function getStyle() {
        return $this->styleName;
    }
}

/**
 * Class for TeX command object
 */
class EslaCommand {
    var $name = null;
    var $data = null;
    var $childs = array();
    /**
     * Constructor
     * 
     * @param type $name name of the command
     * @param type $data \\name{data}
     */
    function EslaCommand($name, $data = null) {
        $this->name = $name;
        
        if (is_array($data)) {
            foreach($data as $part) {
                array_push($this->childs, $part);
            }
        } else {
            $childs = array();
            $this->data = $data;
        }
    }
    /**
     * Setter
     * 
     * @param type $childs child array 
     */
    function setChilds(&$childs) {
        $this->data = null;
        foreach($childs as $part) {
            array_push($this->childs, $part);
        }
    }
    /**
     * Getter
     * 
     * @return type data of the command 
     */
    function getData() {
        return $this->data;
    } 
    /**
     * Getter 
     * 
     * @return type name of the command
     */
    function getName() {
        return $this->name;
    }
    /**
     * Get count of childs array
     */
    function getCountChilds() {
        return count($this->childs);
    }
    /**
     * Getter
     * 
     * @return type childs
     */
    function& getChilds() {
        return $this->childs;
    }
}

/**
 * Class for TeX environment object 
 */
class EslaEnvironment extends EslaBase {
    var $name;
    /**
     * Constructor
     * 
     * @param type $name name of environment 
     */
    function EslaEnvironment($name) {
        $this->name = $name;
    }
    /**
     * Get count of childs array
     */
    function getCountChilds() {
        return count($this->childs);
    }
    /**
     * Getter
     * 
     * @return type childs
     */
    function& getChilds() {
        return $this->childs;
    }
    /**
     * Getter
     * 
     * @return type name 
     */
    function getName() {
        return $this->name;
    }
    /**
     * Setter
     * 
     * @param type $name name 
     */
    function setName($name) {
        $this->name = $name;
    }
}

?>
