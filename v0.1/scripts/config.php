<?php
/**
 * Config for Runner
 * @package texml_proc
 * @author Roman Domrachev 
 * @version 0.1, 30.07.2010
 * @since engine v.0.1
 * @copyright (c) 2010+ by by getfo.org project
 */
    chdir(dirname( __FILE__));
    chdir("..");
    $workdir = getcwd();

/**
 * It is your directory for phpcode  
 */
 define("phpcode", $workdir . DIRECTORY_SEPARATOR . "php");

?>