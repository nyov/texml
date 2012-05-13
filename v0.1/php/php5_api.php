<?php
require(dirname(__FILE__) . '/base_api.php');
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
     * 1st argument is text
     * 
     */
    function text() {
        $fargs = func_get_args();
        return $this->common_text($fargs);
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
     * @return style object   
     */
    function style() {
        $fargs = func_get_args();
        return $this->common_style($fargs);
    }
    /**
     * Apply style to item of document object (xgalley)
     * 
     * Parameters are style function arguments
     * @return style object   
     */
    function xstyle() {
        $fargs = func_get_args();
        return $this->common_xstyle($fargs);
    }    
    
}


?>
