<?php
/**
 * TeXML Writer and string services
 * @package texml_proc
 * @author Roman Domrachev 
 * @version 0.1, 28.07.2010
 * @since engine v.0.1
 * @copyright (c) 2010+ by getfo.org project
 */

 require_once('specmap.php');
 /**
  * It is constant used when variable was'nt set
  */
 define("None", null);
 /**
  * Number of weak ws for newline
  */
 define("WEAK_WS_IS_NEWLINE", 2);
 /**
  * Writer&Co class
  * 
  * @package texml_proc
  */
 class texmlwr {
     var $stream;
     var $after_char0d;    
     var $after_char0a;    
     var $last_ch;         
     var $line_is_blank;   
     var $escape;          
     var $escape_stack;    
     var $ligatures;       
     var $ligatures_stack; 
     var $emptylines;      
     var $emptylines_stack;
     var $bad_enc_warned;  
     var $textescmap;
     var $approx_current_line_len;
     var $allow_weak_ws_to_nl;
     var $is_after_weak_ws;
     var $autonewline_after_len;
     var $weak_is_newline;

     /**
      * Remember output stream, initialize data structures
      * 
      * @param object &$stream stream for writing
      */
     function texmlwr(&$stream) {
         // Tune output stream
         $this->stream =& new stream_encoder($stream);
         //if ($this->stream == None) {
         //   $e = "unknown reason";
         //   raise('ValueError', sprintf("Can't create encoder: '%s'", $e));
         //}
         // Continue initialization
         $this->after_char0d     = 1;
         $this->after_char0a     = 1;
         $this->last_ch          = None;
         $this->line_is_blank    = 1;
         $this->escape           = 1;
         $this->escape_stack     = array();
         $this->ligatures        = 0;
         $this->ligatures_stack  = array();
         $this->emptylines       = 0;
         $this->emptylines_stack = array();
         $this->bad_enc_warned   = 0;
         $this->approx_current_line_len = 0;
         $this->allow_weak_ws_to_nl = 1;
         $this->is_after_weak_ws = 0;
         $this->autonewline_after_len = 0;
         $tem =& new textescmap(); 
         $this->textescmap =& $tem->get();
         $this->weak_is_newline = WEAK_WS_IS_NEWLINE;
     }
     /**
      * Set input encoding for stream encoder
      * 
      * @param string $in_enc the input encoding
      */
     function set_in_enc($in_enc) {
         $s =& $this->stream;
         $s->set_in_enc($in_enc);
     }

     /**
      * Set if escaping is required. Remember old value.
      * 
      * @param string $ifdo flag that to set of the current escape value
      */
     function stack_escape($ifdo) {
         array_push($this->escape_stack, $this->escape);
         if ($ifdo != None) {
             $this->escape = $ifdo;
         }
     }
     /**
      * Restore old policy of escaping
      * 
      */
     function unstack_escape() {
         array_pop($this->escape_stack);
     }
     /**
      * Set if breaking of ligatures is required. Remember old value.
      * 
      * @param string $ifdo flag that to set of the current ligatures value
      */
     function stack_ligatures($ifdo) {
         array_push($this->ligatures_stack, $this->ligatures);
         if ($ifdo != None) {
             $this->ligatures = $ifdo;
         }
     }
     /**
      * Restore old policy of breaking ligatures
      * 
      */
     function unstack_ligatures() {
         array_pop($this->escape_stack);
     }
     /**
      * Set if empty lines are required. Remember old value.
      * 
      * @param string $ifdo flag that to set of the current empty lines value 
      */
     function stack_emptylines($ifdo) {
         array_push($this->emptylines_stack, $this->emptylines);
         if ($ifdo != None) {
            $this->emptylines = $ifdo;
         }
     }
     /**
      * Restore old policy of handling of empty lines
      * 
      */
     function unstack_emptylines() {
         $this->emptylines = array_pop($this->emptylines_stack);
     }
     /**
      * Write a new line unless already at the start of a line
      * 
      */
     function conditionalNewline() {
         if ($this->approx_current_line_len != 0) {
             $this->writech(get_linespr(), 0);
         }
     }
     /**
      * Write a whitespace instead of whitespaces deleted from source XML. 
      *
      * @param integer $hint the parameter is a hack to make <opt/> and <parm/> in <env/> working good.
      *                      hint=WEAK_WS_IS_NEWLINE if weak space should be converted to newline, not to a space
      */
     function writeWeakWS($hint = 1) {
         // weak WS that is newline can not be converted to ws that is space
         if ($hint <= $this->is_after_weak_ws) {
             // return or avoid next if(). I prefer return.
             return; // return
         }
         $this->is_after_weak_ws = $hint;
         //
         // Break line if it is too long
         // We should not break lines if we regard spaces
         // Check for WEAK_WS_IS_NEWLINE in order to avoid line break in
         //   \begin{foo}[aa.....aa]<no line break here!>[bbb]
         //

         if ($this->allow_weak_ws_to_nl && ($hint != WEAK_WS_IS_NEWLINE)) {

             $this->conditionalNewline();
             return; // return
         }
     }
     /**
      * Returns whitespace state and clears WS flag
      *
      * @return integer the hint
      */
     function ungetWeakWS() {
         $hint = $this->is_after_weak_ws;
         $this->is_after_weak_ws = 0;
         return $hint;
     }
     /**
      * Set flag if weak spaces can be converted to new lines
      *
      * @param integer $flag the flag
      */
     function set_allow_weak_ws_to_nl($flag) {
         $this->allow_weak_ws_to_nl = $flag;
     }
     /**
      * Write a char, (maybe) escaping specials
      * 
      * @param string $ch the character 
      * @param integer $esc_specials flag for escaping specials, 0 - don't it. 
      */
     function writech($ch, $esc_specials) {
         //
         // Write a suspended whitespace
         //
         if ($this->is_after_weak_ws) {
             $hint = $this->is_after_weak_ws;
             $this->is_after_weak_ws = 0;
             if ($hint == WEAK_WS_IS_NEWLINE) { 
                 if ((strcmp("\x0A", $ch) != 0) && (strcmp("\x0D", $ch) != 0)) {
                    $this->conditionalNewline();
                 }      
             } else {                         
                 if (($this->approx_current_line_len != 0) && !(is_ws_ch($ch))) {
                    $this->writech(' ', 0);
                 }                                  
             }                             
         }
         //
         // Update counter
         //
         $this->approx_current_line_len = $this->approx_current_line_len + 1;
         //
         // Handle well-known standard TeX ligatures
         //
         if (!($this->ligatures)) {
             if (strcmp("\x2D", $ch) == 0) { // -
                 if (strcmp("\x2D", $this->last_ch) == 0) {
                     $this->writech("\x7B", 0); // {
                     $this->writech("\x7D", 0); // }
                 }                
             } else if (strcmp("\x27", $ch) == 0) { // '
                 if (strcmp("\x27", $this->last_ch) == 0) {
                     $this->writech("\x7B", 0); // {
                     $this->writech("\x7D", 0); // }
                 }
             } else if (strcmp("\x60",$ch) == 0) { // `
                 if ((strcmp("\x60", $this->last_ch) == 0)   // `
                    || (strcmp("\x21", $this->last_ch) == 0) // !
                    || (strcmp("\x3F", $this->last_ch) == 0)) { // ?
                     $this->writech("\x7B", 0); // {
                     $this->writech("\x7D", 0); // }
                 }
             }
         } 
         //
         // Handle end-of-line symbols.
         // XML spec says: 2.11 End-of-Line Handling:
         // ... contains either the literal two-character sequence "#xD#xA" or
         // a standalone literal #xD, an XML processor must pass to the
         // application the single character #xA.
         //
         if ((strcmp("\x0A", $ch) == 0) || (strcmp("\x0D", $ch) == 0)) { // \n \r
             //
             // We should never get '\r', but only '\n'.
             // Anyway, someone will copy and paste this code, and code will
             // get '\r'. In this case rewrite '\r' as '\n'.
             //
             if (strcmp("\x0D", $ch) == 0) { 
                 $ch = "\x0A";
             }
             //
             // TeX interprets empty line as \par, fix this problem
             //
             if ($this->line_is_blank && !($this->emptylines)) {
                 $this->writech("\x25", 0); // %
             }
             //
             // Now create newline, update counters and return
             //

             $s =& $this->stream; 
             $s->write(get_linespr());
             $this->approx_current_line_len = 0;
             $this->last_ch       = $ch;
             $this->line_is_blank = 1;
             return; // return
         }
         //
         // Remember the last character
         //
         $this->last_ch = $ch;
         //
         // Reset the flag of a blank line
         //
         if (!(in_array($ch, array("\x20", "\x09")))) {
             $this->line_is_blank = 0;
         }      
         //     
         // Handle specials
         //  
         $tem =& $this->textescmap;  
         if ($esc_specials && array_key_exists($ch, $tem)) {
             $this->write($tem[$ch], 0);
             return; // return
         }                                                 
         //
         // First attempt to write symbol as-is
         //
         $s =& $this->stream;
         if ($s->write($ch)) {
              return; // return
         }
     }
     /**
      * Write symbols char-by-char in current mode of escaping
      * 
      * @param string $str sequence of characters 
      * @param integer $escape mode of escaping
      * @return bool true - if writing was successful, false - else. 
      */
     function write($str, $escape = None) {
        if ($escape === None) {       
            $escape = $this->escape;
        }
        $i=0;
        for (; $i < mb_strlen($str); $i++) {
            $this->writech(mb_substr($str,$i,1), $escape);
        }
        return true;
     }
 }
 /**
 * Wrapper over output stream to write is desired encoding
 * 
 * @package texml_proc
 */
 class stream_encoder {
     var $stream;
     var $in_enc;
     var $out_enc;
     /**
      * Construct a wrapper by stream and encoding
      * 
      * @param object $stream stream for processing 
      * @param string $in_enc the input encoding
      */
     function stream_encoder(&$stream, $in_enc = "UTF-8") {
         $this->in_enc = $in_enc;
         $this->out_enc = "UTF-8";
         $this->stream =& $stream;
     }
     /**
      * Write string encoded
      * 
      * @param string $str string for encoding
      * @return bool true - if writing was successful, false - else.  
      */
     function write($str) {
         $s =& $this->stream;
         $s->write($str);
         return true;
     }
     /**
      * Set input encoding for stream encoder
      * 
      * @param string $inc_enc the input encoding
      */
     function set_in_enc($in_enc) {
         $this->$in_enc = $in_enc;
     }

     /**
      * Close underlying stream
      * 
      */
     function close() {
     }
 }

?>