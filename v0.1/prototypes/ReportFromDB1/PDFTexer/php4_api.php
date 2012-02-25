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
    var $childs = array();
    /**
     * Add text object to the environment object
     * 
     * @param type $text text
     */
    function text($text) {
        array_push($this->childs, new EslaText($text));
    }
    /**
     * Add table object to the environment object
     * 
     * @return EslaTable reference on table object 
     */
    function& table() {
        $table = new EslaEnvironment('calstable');
        array_push($this->childs, $table);
        return $table;
    }
    /**
     * Add row object to the environment object
     * 
     * @param type $text text text inserted into the row (while there is one cell) 
     */
    function row($text) {
        array_push($this->childs, new EslaCommand('brow')); 
        array_push($this->childs, new EslaCommand('cell', $text));
        array_push($this->childs, new EslaCommand('erow'));
    }
}

?>
