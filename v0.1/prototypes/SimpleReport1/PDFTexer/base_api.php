<?php
/**
 * It is base library for placing common functions
 *   
 * @package pdftexer
 * @author Roman Domrachev 
 * @version 0.1, 10.03.2012
 * @since engine v.0.1
 * @copyright (c) 2012+ by getfo.org project
 */
 $esla_replace_map = array(
      '\\'  =>  '\\textbackslash{}',
      '{'   =>  '\\{',
      '}'   =>  '\\}',
      '$'   =>  '\\textdollar{}',
      '&'   =>  '\\&',
      '#'   =>  '\\#',
      '^'   =>  '\\^{}',
      '_'   =>  '\\_',
      '~'   =>  '\\textasciitilde{}',
      '%'   =>  '\\%',
      '|'   =>  '\\textbar{}',
      '<'   =>  '\\textless{}',
      '>'   =>  '\\textgreater{}'
 );
/**
 * Special chars escaping
 * 
 * @param type $text input text
 * @return type the result text
 */
function esla_escape($text) {
    global $esla_replace_map; 
    return strtr($text, $esla_replace_map);
}

/**
 * Class contans methods for adding different objects to document object
 * 
 * It is common base class for PHP4 and PHP5 API version
 */

class EslaCommonBase {
    var $packages = array();
    var $isFirstRow = false;
    var $isTable = false;
    var $countColumns = 0;
    var $tfoot;
    var $childs = array();

    /**
     * Add text object to the environment object
     * 
     * @param type $text text
     */
    function& common_text($text) {
        $texto = new EslaText(esla_escape($text));
        array_push($this->childs, $texto);
        return $texto;
    }
    /**
     * Add table object to the environment object
     * 
     * @param type $colwidths widths for columns
     * 
     * @return EslaTable reference on table object 
     */
    function& common_table($colwidths) { 
        $table = new EslaEnvironment('calstable');
        $table->setTableFlag(true);
        array_push($this->childs, $table);
        if (count($colwidths) > 0) {
            $widths = "";
            foreach($colwidths as $width) {
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
         $num_args = func_num_args();
         if ($num_args > 0) {
            $childs = array();
            $fargs = func_get_args();
            array_push($childs, new EslaCommand('brow'));
            if ($this->isFirstRow && $num_args == 1 && $this->countColumns > 1) {
                array_push($childs, new EslaCommand('nullcell', 'ltb'));
                for($i=3; $i <= $this->countColumns; $i++) {
                    array_push($childs, new EslaCommand('nullcell', 'tb'));
                }
                array_push($childs, new EslaCommand('nullcell', 'rtb'));
                array_push($childs, new EslaCommand('spancontent', $fargs[0]));
            } else {
                foreach($fargs as $text) {
                    if ($text == null) {
                        //$text = '\ ';
                        $text = " ";
                    }
                    array_push($childs, new EslaCommand('cell', esla_escape($text)));
                }
            }
            array_push($childs, new EslaCommand('erow'));
            
            if (!$this->isFirstRow) {
                $cmd = new EslaCommand('thead', $childs);
                array_push($this->childs, $cmd);
                $this->isFirstRow = true;
                $this->countColumns = $num_args;
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
     * Add vertical whitespace object to environment object
     * 
     * @param type $length length of the whitespace in mm 
     */
    function ws($length) {
        array_push($this->childs, new EslaCommand('vspace', $length . "mm"));
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
    function& common_style($name = null, $data = null) {
        if ($this->isTable && $name != null) {
            $this->name = $name;
        } else {
            array_push($this->childs, new EslaCommand($name, $data));
        }
        return $this;
    }  

}



?>
