<?php
/**
 * Runner for TeXML processor
 * @package texml_proc
 * @author Roman Domrachev 
 * @version 0.1, 30.07.2010
 * @since engine v.0.1
 * @copyright (c) 2010+ by by getfo.org project
 */

/**
 * It is set property of PHP for compatibility with PHP 4
*/
 ini_set('zend.ze1_compatibility_mode', 'On');

 require("config.php");
 require(phpcode . DIRECTORY_SEPARATOR . "common_getfo_lib.php");

 require(phpcode . DIRECTORY_SEPARATOR . "processor.php");

 $xmlfile_name = "examples" . DIRECTORY_SEPARATOR . "texmlapis.xml";
 $outfile =& new stream();

 processor.process($xmlfile_name, $outfile);

?>