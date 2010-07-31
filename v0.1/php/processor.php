<?php
 require('handler.php');
 require('texmlwr.php');
/**
 * Start process 
 * 
 * @param $in_stream object input stream 
 * @param $out_stream object output stream 
 * @param $encoding string encoding for output
 * @param $autonl_width integer auto break of line is occured if length of line is more it
 * @param $always_ascii integer if always_ascii = 1 then encoding is set 'ascii' and encoding parameter is ignored
 */
 function process($xmlfile_name, &$out_stream, $encoding = 'ascii', $autonl_width = 62, $always_ascii = 0) {
    $transform_obj =& new ParseFile();
    $texml_writer =& new texmlwr($out_stream, $encoding, $autonl_width, $always_ascii);
    $transform_obj->parse_file($texml_writer, $xmlfile_name);
 }
?>