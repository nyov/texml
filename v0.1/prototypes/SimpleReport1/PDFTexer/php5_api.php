<?php
require('base_api.php');
/**
 * It is that using PHP5 OOP style
 *   
 * @package pdftexer
 * @author Roman Domrachev 
 * @version 0.1, 12.03.2012
 * @since engine v.0.1
 * @copyright (c) 2012+ by getfo.org project
 */

/**
 * Class contans methods for adding different objects to document object
 */
class EslaBase extends EslaCommonBase {
    /**
     * Add text object to the environment object
     * 
     * @param type $text text
     */
    function text($text) {
        return $this->common_text($text);
    }
    /**
     * Add table object to the environment object
     * 
     * Parameters are widths for columns
     * 
     * @return EslaTable reference on table object 
     */
    function table() {
        $args = func_get_args();
        return $this->common_table($args);
    }
    /**
     * Apply style to item of document object
     * 
     * Parameters are style function arguments 
     */
    function style() {
        $fargs = func_get_args();
        $this->common_style($fargs);
        return $this;
    }    
}


?>
