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
         //die("Cannot create abstract stream class!");
     }
     function nexto() {
         return false;
     }
     function printo($str) {
         $this->file .= trim($str) . "\n";
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
     function printo($str) {
         fwrite(parent::get_file(), trim($str));
     }
     function getvalue() {
         return file_get_contents(parent::get_file());        
     }
     function closet() {
         fclose(parent::get_file());
     }
 }

?>
