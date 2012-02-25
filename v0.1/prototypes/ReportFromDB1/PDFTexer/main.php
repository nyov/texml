<?php
require('php4_api.php');
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
        
        $this->marsh_head();
        $this->marsh_item($doc);
        
        fclose($this->file); 
    }
    /**
     * Marshal header for TeX-file.
     */
    function marsh_head() {
        fwrite($this->file, "\documentclass{article}\n");
        fwrite($this->file, "\usepackage{cals}\n");
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
                $this->marsh_text($item);
                break;
            case ($item instanceof EslaEnvironment):
                $this->marsh_env($item);
                break;
        }
    }
    
    /**
     * Marshal TeX-command
     * 
     * @param type $item command object
     */
    function marsh_cmd(&$item) {
        if ($item->getData() != null) {
            $cmd = "\\" . $item->getName() . "{" . $item->getData() . "}\n";    
        } else {
            $cmd = "\\" . $item->getName() . "\n"; 
        }
        fwrite($this->file,  $cmd);
    }
    
    /**
     * Marshal text
     * 
     * @param type $item text object
     */
    function marsh_text(&$item) {
        fwrite($this->file, $item->getData() . "\\\\ \n");
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
 */
function esla_pdf(&$doc, $filename) {
    $marshaller = new EslaMarshaller();
    $marshaller->marshal($doc, $filename);
    // todo: execute TeX util for making PDF from TeX-file 
}

// Tex object forming

/**
 * Returns new document object.
 * 
 * @return type the object
 */
function esla_doc() {
    return new EslaEnvironment("document");
}

/**
 * Class for text object
 */
class EslaText {
    var $data;
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
}

/**
 * Class for TeX command object
 */
class EslaCommand {
    var $name;
    var $data;
    /**
     * Constructor
     * 
     * @param type $name name of the command
     * @param type $data \\name{data}
     */
    function EslaCommand($name, $data = null) {
        $this->name = $name;
        $this->data = $data;
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
}

?>
