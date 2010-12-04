<?php
/**
 * Common Library for getfo.org project
 * @package common_getfo_lib
 * @author Roman Domrachev 
 * @version 0.1, 30.07.2010
 * @since engine v.0.1
 * @copyright (c) 2010+ by getfo.org project
 */

/**
 * Class of regular expressions 
 * 
 * @package common_getfo_lib
 */
 class re {
    var $regexp;
    var $matches;
    function re($regexp) {
        $this->regexp = $regexp; 
    }
    function match($str) {
        return preg_match($this->regexp, trim($str), $this->matches);
    }        
    function group($n) {
        return $this->matches[$n];
    }
    function search($str) {
        return $this->match($str);
    }

 }
/**
 * Class of stream
 *
 * You can to use it as memory output stream
 * 
 * @package common_getfo_lib
 */
 class stream {
     var $file;
     function stream() {
         echo "start\n";
         //die("Cannot create abstract stream class!");
     }
     function nexto() {
         return false;
     }
     function printo($str) {
         $this->write($str);
         $this->write("\n");
     }
     function write($str) {
         echo "instr=". $str . "\n";
         //$f =& $this->file;
         //$f = $this->file . trim($str);
         //$this->file = $f;
         $this->file .= trim($str);
         echo "instr1=". $this->file . "\n";
     }
     function getvalue() {
         return $this->file;
     }
     function closet() {
         unset($this->file);
         $this->file = null;
     }
     function set_file($file) {
         $this->file = $file;    
     }
     function get_file() {
         return $this->file;
     }
 }
/**
 * Class of file-stream
 * 
 * @package common_getfo_lib
 */
 class fstream extends stream {
     function fstream($filename, $mode) {
         parent::set_file(fopen($filename, $mode));
     }
     function nexto() {
         return fgets(parent::get_file());
     }
     //function printo($str) {
     //    $this->write($str);
     //}
     function write($str) {
         fwrite(parent::get_file(), $str);
     }
     function getvalue() {
         return file_get_contents(parent::get_file());        
     }
     function closet() {
         fclose(parent::get_file());
     }
 }
/**
 * Common class for exceptions
 * 
 * @package common_getfo_lib
 */
 class PException {
    function PException() {
        // some actions
    }
 }
/**
 * Common class for errors
 * 
 * @package common_getfo_lib
 */
 class PError {
 }
/**
 * Error of the value
 * 
 * @package common_getfo_lib
 */
 class ValueError extends PError {
    function ValueError() {
        // some actions
    }
 }
/**
 * Call by case of an exception
 * 
 * @param $ename string the exception class name 
 * @param $msg string the message about an occurence
 */
 function raise($ename, $msg) {
     new $ename();
     die($msg);
 }
/**
 * Get line separator
 * 
 */
 function get_linespr() {
     return "\x0A";
 }
/**
 * Check that the char is whitespace symbol
 * 
 * @param string $ch the char  
 */
 function is_ws_ch($ch) {
     //echo "*". $ch . "=" . in_array($ch, array("\x20", "\x09", "\x0B", "\x0A", "\x0D", "\x0C"))  . "*";
     return in_array($ch, array("\x20", "\x09", "\x0B", "\x0A", "\x0D", "\x0C"));
 }
/**
 * Get value from set by key
 * 
 * @param array $values the set of values 
 * @param string $key key that to get
 * @param string $default the default value for return if key was'nt found
 * @return string the value 
 */
 function get_value($values, $key, $default) {
     if (array_key_exists($key ,$values)) {
         return $values[$key];
     } else {
         return $default;
     }
 }

?>
