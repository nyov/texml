<?php
/**
 * Forming document object and marshalling it to TeX 
 * @package pdftexer
 * @author Roman Domrachev 
 * @version 0.1, 21.02.2012
 * @since engine v.0.1
 * @copyright (c) 2012+ by getfo.org project
 */

// TeX marshalling
/**
 * Marshals header for TeX-file.
 * 
 * @param type $file TeX-file object 
 */
function esla_marsh_head($file) {
    fwrite($file, "\documentclass{article}\n");
    fwrite($file, "\usepackage{cals}\n");
} 

/**
 * Marshals document object to TeX-format. 
 * 
 * @param type $doc document object
 * @param type $file TeX-file object
 */
function esla_marshal(&$doc, $file) {
    esla_marsh_head($file);
    esla_marsh_item($file, $doc);
}

/**
 * Marashals item of document in recursieve mode.
 * 
 * @param type $file TeX-file object  
 * @param type $item item of document object
 */
function esla_marsh_item($file, &$item) {
    if (!array_key_exists('childs', $item)) { 
        if (array_key_exists('name', $item)) { // it is command
            esla_marsh_cmd($file, $item);
        } else { // it is text
            esla_marsh_text($file, $item);
        }
    } else { // it is environment
        esla_marsh_begin($file, $item);
        $childs = $item['childs'];
        if ($childs != null) {
            foreach($childs as $c) {
                esla_marsh_item($file, $c);
            }
        }
        esla_marsh_end($file, $item);
    }
}
/**
 * Marshals TeX-command.
 * 
 * @param type $file TeX-file object
 * @param type $item item of document object
 */
function esla_marsh_cmd($file, &$item) {
    if (array_key_exists('data', $item)) {
        $cmd = "\\" . $item['name'] . "{" . $item['data'] . "}\n";    
    } else {
        $cmd = "\\" . $item['name'] . "\n"; 
    }        
    fwrite($file,  $cmd);
}
/**
 * Marshals text.
 * 
 * @param type $file TeX-file object
 * @param type $item item of document object
 */
function esla_marsh_text($file, &$item) {
    fwrite($file, $item['data'] . "\\\\ \n");
}
/**
 * Marshals begin of environment.
 * 
 * @param type $file TeX-file object
 * @param type $item item of document object
 */
function esla_marsh_begin($file, &$item) {
    fwrite($file, "\n\begin{" . $item['name'] ."}\n");
}
/**
 * Marshals end of environment.
 * 
 * @param type $file TeX-file object
 * @param type $item item of document object
 */
function esla_marsh_end($file, &$item) {
    fwrite($file, "\end{" . $item['name'] ."}\n");
}

/**
 * Execute operations for making document in PDF-format.
 *  
 * @param type $doc document object
 * @param type $filename name of PDF-file
 */
function esla_pdf(&$doc, $filename) {
    $texfilename = $filename . ".tex";
    $file = fopen($texfilename, "w");
    esla_marshal($doc, $file);
    fclose($file); 
    // todo: execute TeX util for making PDF from TeX-file 
}
// Tex object forming
define("textt", "esla_text");
define("textb", "esla_table");
define("textr", "esla_row");
/**
 * Add TeX-item to other TeX-item.
 * Note: PHP can return by reference item of array only (but not array).
 * @param type $tex_cmd name of function for adding TeX-item
 * @param type $item item of document object
 */
function &esla_add($tex_cmd, &$item) {
    $cmd_args = func_get_args();
    switch ($tex_cmd) {
        case "esla_text":
            esla_text($item, $cmd_args[2]);
            return $text;
            break;
        case "esla_table":
            $i = &esla_table($item);
            $childs =& $item['childs'];
            $c = &$childs[$i];
            return $c;
            break;
        case "esla_row":
            esla_row($item, $cmd_args[2]);
            return $row;
            break;
    }
    
        
}


/**
 * Add text to item of document object.
 * @param array $item the item
 * @param type $text text
 */
function esla_text(&$item, $text) {
    $childs = &$item['childs'];
    if ($childs == null) {
        $childs = array();
    }
    $t = array('data' => $text);
    array_push($childs, $t);
}

/**
 * Add table to item of document object
 * @param array $item the item
 * @return type index of table item in childs array
 */
function &esla_table(&$item) {
    $childs = &$item['childs'];
    if ($childs == null) {
        $childs = array();
    }    
    array_push($childs, array('name' => 'calstable'));
    $index = count($childs) - 1;
    return $index;
}
 
/**
 * Add row to item of document object
 * @param array $item the item
 */ 
function esla_row(&$item, $text) {
    $childs = &$item['childs'];
    if ($childs == null) {
        $childs = array();
    }
    array_push($childs, array('name' => 'brow'));
    // cell
    $c = array('name' => 'cell', 'data' => $text);
    array_push($childs, $c);
    array_push($childs, array('name' => 'erow'));
}

/**
 * Returns new document object.
 * 
 * The Document object represents an array.
 *   
 * @return type 
 */
function esla_doc() {
    return array('name' => 'document');
}

?>
