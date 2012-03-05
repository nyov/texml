<?php
/**
 * It is that using PHP4 OOP style
 *   
 * @package pdftexer
 * @author Roman Domrachev 
 * @version 0.1, 25.02.2012
 * @since engine v.0.1
 * @copyright (c) 2012+ by getfo.org project
 */

/**
 * Class contans methods for adding different objects to document object
 */
class EslaBase {
    var $packages = array();
    var $childs = array();
    var $isFirstRow = false;
    var $isTable = false;
    var $tfoot;
    /**
     * Add text object to the environment object
     * 
     * @param type $text text
     */
    function& text($text) {
        $texto = new EslaText($text);
        array_push($this->childs, $texto);
        return $texto;
    }
    /**
     * Add table object to the environment object
     * 
     * Parameters are widths for columns
     * 
     * @return EslaTable reference on table object 
     */
    function& table() {
        $table = new EslaEnvironment('calstable');
        $table->setTableFlag(true);
        array_push($this->childs, $table);
        if (func_num_args() > 0) {
            $widths = "";
            foreach(func_get_args() as $width) {
                $widths .= "{" . $width . "mm}";
            }
            $cmd = new EslaCommand('colwidths', $widths);
            array_push($table->getChilds(), $cmd);
        }        
        return $table;
    }
    /**
     * Set table flag
     * 
     * @param type $value flag value
     */
    function setTableFlag($value) {
        $this->isTable = $value;
    }
    /**
     * Add row object to the environment object
     * 
     * Method supports auto-making first and last rows as 
     * head and foot table rows
     * 
     * Parameters are text for cells
     */
    function row() {
         if (func_num_args() > 0) {
            $childs = array();
            array_push($childs, new EslaCommand('brow')); 
            foreach(func_get_args() as $text) {
                if ($text == null) {
                    $text = '\ ';
                }
                array_push($childs, new EslaCommand('cell', $text));
            }
            array_push($childs, new EslaCommand('erow'));
            
            if (!$this->isFirstRow) {
                $cmd = new EslaCommand('thead', $childs);
                array_push($this->childs, $cmd);
                $this->isFirstRow = true;
            } else {
                $cmd = new EslaCommand('tfoot', $childs);
                if ($this->tfoot != null) { 
                    $childs =& $this->tfoot->getChilds();
                    array_pop($this->childs);
                    foreach($childs as $c) {
                        array_push($this->childs, $c); 
                    }
                }                
                array_push($this->childs, $cmd);
                $this->tfoot =& $cmd;
            }           
        }
    }
    /**
     * Add package that using styles from it
     * 
     * @param type $name name of package
     */
    function addPackage($name) {
        array_push($this->packages, $name);
    }
    /**
     * Get package names
     */
    function& getPackages() {
        return $this->packages;
    }
    /**
     * Apply style to item of document object
     * 
     * @param type $name name of style 
     * @param type $data data of style
     */
    function& style($name = null, $data = null) {
        if ($this->isTable && $name != null) {
            $this->name = $name;
        } else {
            array_push($this->childs, new EslaCommand($name, $data));
        }
        return $this;
    }
    
    
}

?>
