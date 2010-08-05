<?php
/**
 * TeXML Writer and string services
 * @package texml_proc
 * @author Roman Domrachev 
 * @version 0.1, 28.07.2010
 * @since engine v.0.1
 * @copyright (c) 2010+ by getfo.org project
 */

 require('specmap.php');
 require('unimap.php');
 /**
 * Modes of processing of special characters
 */
 define('DEFAULT', 0);
 define('TEXT'   , 1);
 define('MATH'   , 2);
 define('ASIS'   , 3);

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
     var $mode;
     var $mode_stack;

     /**
      * Remember output stream, initialize data structures
      * 
      * @param object $stream stream for writing
      */
     function texmlwr(&$stream) {
         // Tune output stream
         $encoding = "UTF-8";
         $this->stream =& new stream_encoder($stream, $encoding);
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
         $this->mode             = TEXT;
         $this->mode_stack       = array();

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
         array_pop($this->emptylines_stack);
     }
     /**
      * Write a new line unless already at the start of a line
      * 
      */
     function conditionalNewline() {
     }
     /**
      * Write a char, (maybe) escaping specials
      * 
      * @param string $ch the character 
      * @param integer $esc_specials flag for escaping specials, 0 - don't it. 
      */
     function writech($ch, $esc_specials) {
         //
         // Handle well-known standard TeX ligatures
         //
         if (not($this->ligatures)) {
             if (!strcmp('-', $ch)) {
                 if (!strcmp('-', $this->last_ch)) {
                     $this->writech('{', 0);
                     $this->writech('}', 0);
                 }
             } else if (!strcmp("'", $ch)) {
                 if (!strcmp("'", $this->last_ch)) {
                     $this->writech('{', 0);
                     $this->writech('}', 0);
                 }
             } else if (!strcmp('`',$ch)) {
                 if (!strcmp('`', $this->last_ch) || !strcmp('!', $this->last_ch) || !strcmp('?', $this->last_ch)) {
                     $this->writech('{', 0);
                     $this->writech('}', 0);
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
         if (!strcmp('\n', $ch) || !strcmp('\r', $ch)) {
             //
             // We should never get '\r', but only '\n'.
             // Anyway, someone will copy and paste this code, and code will
             // get '\r'. In this case rewrite '\r' as '\n'.
             //
             if (!strcmp('\r', $ch)) { 
                 $ch = '\n';
             }
             //
             // TeX interprets empty line as \par, fix this problem
             //
             if ($this->line_is_blank && not($this->emptylines)) {
                 $this->writech('%', 0);
             }
             //
             // Now create newline, update counters and return
             //
             $s = $this->stream; 
             $s->write(get_linespr());
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
         if (not(in_array($ch, array('\x20', '\x09')))) {
             $this->line_is_blank = 0;
         }
         //
         // Handle specials
         //
         if ($this->esc_specials) {
             if ($this->mode == TEXT) {
                 $this->write($textescmap[$ch], 0);
             } else {
                 $this->write($mathescmap[$ch], 0);
             }
             return; // return
         }                                                 
         //
         // First attempt to write symbol as-is
         //
         $s = $this->stream;
         if ($s->write($ch)) {
              return; // return
         }
         //
         // Symbol have to be rewritten. Let start with math mode.
         //
         $chord = ord($ch);
         if ($this->mode == TEXT) {
             //
             // Text mode, lookup text map
             //
             if ($this->write($textmap[$chord], 0)) {
                 return; // return
             } else {
                 //
                 // Text mode, lookup math map
                 //
                 $tostr = get_value($mathmap, $chord, None);
             }
         } else { // $this->mode == MATH:
             //
             // Math mode, lookup math map
             //
             if ($this->write($mathmap[$chord], 0)) {
                 return; // return
             } else {
                 //
                 // Math mode, lookup text map
                 //
                 $tostr = get_value($textmap, $chord, None);
             }
         }
         //
         // If mapping in another mode table is found, use a wrapper
         //
         if ($tostr != None) {
             if ($this->mode == TEXT) {
                 $this->write('\\ensuremath{', 0);
             } else {
                 $this->write('\\ensuretext{', 0);
             }
             $this->write($tostr, 0);
             $this->writech('}', 0);
             return; // return 
         }                                               
         //
         // Finally, warn about bad symbol and write it in the &#xNNN; form
         //
         if (not($this->bad_enc_warned)) {
             echo("texml: not all XML symbols are converted\n");
             $this->bad_enc_warned = 1;
         }
         $this->write(sprintf('\\unicodechar{%d}', $chord), 0);

     }
     /**
      * Write symbols char-by-char in current mode of escaping
      * 
      * @param string $str sequence of characters 
      * @param integer $escape mode of escaping
      * @return bool true - if writing was successful, false - else. 
      */
     function write($str, $escape = None) {
        if (None == $escape) {
            $escape = $this->escape;
        }
        foreach($str as $ch) {
            $this->writech($ch, $escape);
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
     /**
      * Construct a wrapper by stream and encoding
      * 
      * @param object $stream stream for processing 
      * @param string $encoding encoding for result output 
      */
     function stream_encoder(&$stream, $encoding) {
     }
     /**
      * Write string encoded
      * 
      * @param string $str string for encoding
      * @return bool true - if writing was successful, false - else.  
      */
     function write($str) {
         return true;
     }
     /**
      * Close underlying stream
      * 
      */
     function close() {
     }
 }

?>