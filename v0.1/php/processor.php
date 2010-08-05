<?php
 require('handler.php');
 require('texmlwr.php');
/**
 * Start process 
 * 
 * @param $in_stream object input stream 
 * @param $out_stream object output stream 
 */
 function process($xmlfile_name, &$out_stream) {
    $transform_obj =& new ParseFile();
    $texml_writer =& new texmlwr($out_stream);
    $transform_obj->parse_file($texml_writer, $xmlfile_name);
 }
?>