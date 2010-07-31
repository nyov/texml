<?php
/**
 * TeXML Writer and string services
 * @package texml_proc
 * @author Roman Domrachev 
 * @version 0.1, 28.07.2010
 * @since engine v.0.1
 * @copyright (c) 2010+ by getfo.org project
 */

 /**
 * It is constant used when variable was'nt set
 */
 define("None", null);

 /**
 * Writer&Co class
 * 
 * @package texml_proc
 */
 class texmlwr {
     /**
      * Remember output stream, initialize data structures
      * 
      * @param $stream object stream for writing
      * @param $encoding string encoding for output
      * @param $autonl_width integer auto break of line is occured if length of line is more it
      * @param $always_ascii integer if encoding = 1 then encoding is set 'ascii' and encoding parameter is ignored
      */
     function texmlwr(&$stream, $encoding, $autonl_width, $always_ascii = 0) {
     }
     /**
      * Set flag if weak spaces can be converted to new lines
      * 
      * @param $flag integer constant of the flag 
      */
     function set_allow_weak_ws_to_nl($flag) {
     }
     /**
      * Write a new line unless already at the start of a line
      * 
      */
     function conditionalNewline() {
     }
     /**
      * Write a whitespace instead of whitespaces deleted from source XML
      * 
      * @param $hint integer a hack to make <opt/> and <parm/> in <env/> working good
      *                      hint=WEAK_WS_IS_NEWLINE if weak space should be converted to newline, not to a space 
      */
     function writeWeakWS($hint=1) {
     }
     /**
      * Returns whitespace state and clears WS flag
      * 
      */
     function ungetWeakWS() {
     }
     /**
      * Write a char, (maybe) escaping specials
      * 
      * @param $ch string character 
      * @param $esc_specials integer flag for escaping specials, 0 - don't it. 
      */
     function writech($ch, $esc_specials) {
     }
     /**
      * Write symbols char-by-char in current mode of escaping
      * 
      * @param $str string sequence of characters 
      * @param $escape integer mode of escaping 
      */
     function write($str, $escape = None) {
     }
     /**
      * Write char in Acrobat utf16be encoding
      * 
      * @param $ch string character 
      */
     function writepdfch($ch) {
     }
 }
 /**
 * Wrapper over output stream to write is desired encoding
 * 
 * @package texml_proc
 */
 class stream_encoder {
     /**
      * Construct a wrapper by stream and encoding
      * 
      * @param $stream object stream for processing 
      * @param $encoding string encoding for result output 
      */
     function stream_encoder(&$stream, $encoding) {
     }
     /**
      * Write string encoded
      * 
      * @param $str string string for encoding 
      */
     function write($str) {
     }
     /**
      * Close underlying stream
      * 
      */
     function close() {
     }
 }

?>