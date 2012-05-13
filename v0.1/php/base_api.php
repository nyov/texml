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
    var $thead_style;
    var $tbody_style;
    var $tfoot_style;
    var $instances = array();
    
    /**
     * Add text object to the environment object
     * If there aren't arguments then paragraph is made
     * @param type $fargs arguments
     * 
     */
    function& common_text(&$fargs) {
        $texto = "";
        if (count($fargs) > 0) {
            $texto = new EslaText(esla_escape($fargs[0]));
        } else {
            $texto = new EslaText("");
        }
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
    function& common_table(&$colwidths) { 
        $table = new EslaEnvironment('calstable');
        $table->setTableFlag(true);
        array_push($this->childs, $table);
        if (count($colwidths) > 0) {
            $widths = "";
            foreach($colwidths as $width) {
                $widths .= "{" . $width . "mm}";
            }
            // TODO: it should be as commmand with multiple parameters
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
     * TODO: empty row - add empty row
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
                $this->make_span_row_childs($fargs[0], $childs);
            } else {
                $this->make_row_childs($fargs, $childs);
            }
            array_push($childs, new EslaCommand('erow'));
            
            if (!$this->isFirstRow) {
                $this->make_head_row($num_args, $childs);
            } else {
                $this->update_foot_row($childs);
            }           
        }
    }
    
    /**
     * Make head row 
     * 
     * @param type $num_args count of columns of the row 
     * @param type $childs child array
     */
    function make_head_row($num_args, &$childs) {
        if ($this->thead_style != null) {
            $this->change_cellstyle($childs, $this->thead_style);
        }
        $cmd = new EslaCommand('thead', $childs);
        array_push($this->childs, $cmd);
        $this->isFirstRow = true;
        $this->countColumns = $num_args;
    }
    
    /**
     * Update foot row
     * 
     * @param type $childs child array
     */
    function update_foot_row(&$childs) {
        $cmd = new EslaCommand('tfoot', $childs);
        if ($this->tfoot != null) { 
            $childs =& $this->tfoot->getChilds();
            if ($this->tbody_style != null) {
                $this->change_cellstyle($childs, $this->tbody_style);
            }                    
            array_pop($this->childs);
            foreach($childs as $c) {
                array_push($this->childs, $c); 
            }
        }                
        array_push($this->childs, $cmd);
        $this->tfoot =& $cmd;
    }
    
    /**
     * Make span row childs
     * 
     * @param type $text text for cell
     * @param type $childs child array
     */
    function make_span_row_childs($text, &$childs) {
        array_push($childs, new EslaCommand('nullcell', 'ltb'));
        for($i=3; $i <= $this->countColumns; $i++) {
            array_push($childs, new EslaCommand('nullcell', 'tb'));
        }
        array_push($childs, new EslaCommand('nullcell', 'rtb'));
        $cmd_data = null;
        if ($this->tfoot_style != null) {
            $cmd_data = array($this->wrap_in_style(esla_escape($text)));
        } else {
            $cmd_data = esla_escape($text);
        }
        array_push($childs, new EslaCommand('spancontent', $cmd_data));
    }
    
    /**
     * Make row childs
     * 
     * @param type $texts array of texts for cells
     * @param type $childs child array 
     */
    function make_row_childs(&$texts, &$childs) {
        foreach($texts as $text) {
            if ($text == null) {
                $text = " ";
            }
            $cmd_data = null;
            if ($this->tfoot_style != null) {
                $cmd_data = array($this->wrap_in_style(esla_escape($text)));
            } else {
                $cmd_data = esla_escape($text);
            }
            array_push($childs, new EslaCommand('cell', $cmd_data));
        }
    } 
    /**
     * Make declare instance eobject
     * @param type $instance_name the instance name
     * @param type $style_args style arguments
     */
    function make_instance($instance_name, &$style_args) {
        $params = "";
        for($i=2; $i < count($style_args)-1; $i++) {
            $params .= $style_args[$i] . ",";            
        }
        $params .= $style_args[count($style_args)-1];
        array_push(
            $this->instances, 
            new EslaCommand(
                'DeclareInstance', 
                $style_args[0], 
                $instance_name, 
                $style_args[1], 
                $params
            )
        );
    }
    /**
     * Wrap text in style (environment)
     * 
     * @param type $text the text
     * @return EslaEnvironment style environment object with the text
     */
    function& wrap_in_style($text) {
        $tfoot_style_env = new EslaEnvironment($this->tfoot_style);
        $env_childs =& $tfoot_style_env->getChilds();
        array_push($env_childs, new EslaText($text));
        return $tfoot_style_env;
    }
    
    /**
     * Change style of cells for row 
     * 
     * Row should be follow structure:
     * \brow
     * \cell{..}
     * ..
     * \erow 
     * 
     * @param type $cells
     * @param type $stylename 
     */
    function change_cellstyle(&$cells, $stylename) {
        for($i=1; $i < count($cells)-1; $i++) {
            $c = $cells[$i];
            if ($c->isGroupped()) {
                $childs =& $c->getChilds();
                $childs[0]->setName($stylename);
            } else {
                $style_env = new EslaEnvironment($stylename);
                $env_childs =& $style_env->getChilds();
                $cell_data =& $c->getChilds(); 
                array_push($env_childs, new EslaText($cell_data[0]));
                $c->setChilds(array($style_env));
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
     * Get instances
     * @return type instances
     */
    function& getInstances() {
        return $this->instances;
    }
    /**
     * Apply style to item of document object (xgalley)
     *
     * @param type $style_args 
     */ 
    function& common_xstyle(&$style_args) {
        if (count($style_args) == 1) {
            $style_args =& $style_args[0];
        }
        $return_object = null;
        if (count($style_args) == 2) { // not need make instance
            $use_instance_args = 
                array(
                    "UseInstance",
                    $style_args[0], 
                    $style_args[1]
                );
            $return_object = new EslaCommand($use_instance_args);
            array_push($this->childs, $return_object);
        } else {
            $instance_name = "instance" . microtime();
            $instance_name = str_replace(" ", "", $instance_name);
            $instance_name = str_replace(".", "", $instance_name);
            $use_instance_args = 
                array(
                    "UseInstance",
                    $style_args[0], 
                    $instance_name                                
                );
            $this->make_instance($instance_name, $style_args);
            $return_object = new EslaCommand($use_instance_args);
            array_push($this->childs, $return_object);
        }
        return $return_object;
    }
    
    /**
     * Apply style to item of document object
     * 
     * @param type $style_args array: 1. name of style 2. data for style 
     * or table row styles (head row style, body row style, row foot style) 
     */
    function& common_style(&$style_args) {
        $return_object = null;
        $name = $style_args[0];
        $count = count($style_args);
        if ($this->isTable && $name != null) {
            $return_object = $this;
            $this->name = $name;            
            if ($count > 1) {
                $this->thead_style = $style_args[1];
            }
            if ($count > 2) {
                $this->tbody_style = $style_args[2];
            }
            if ($count > 3) {
                $this->tfoot_style = $style_args[3];
            }
        } else {
            if ($count == 1) {
                $return_object = new EslaCommand($name);
                array_push($this->childs, $return_object);
            } else {
                $return_object = new EslaCommand($style_args);
                array_push($this->childs, $return_object);
            }
        }
        return $return_object;
    }  

}



?>
