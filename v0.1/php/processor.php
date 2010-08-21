<?php
 require_once('handler.php');
 require_once('texmlwr.php');

/**
 * Start process 
 * 
 * @param $in_stream object input stream 
 * @param $out_stream object output stream 
 * @param string $in_enc the input encoding
 */
 function process($xmlfile_name, &$out_stream, $in_enc = null) {
    // set encoding for processing 
    mb_internal_encoding("UTF-8");

    $transform_obj =& new ParseFile();
    $texml_writer =& new texmlwr($out_stream);
    if ($in_enc != null) {
        $transform_obj->parse_file($texml_writer, $xmlfile_name, $in_enc);
    } else {
        $transform_obj->parse_file($texml_writer, $xmlfile_name);
    }
 }
?>