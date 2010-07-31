<?php
/**
 * Tranform TeXML SAX stream
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
 * Wrapper class to make the library easier to use.
 *
 * See the above notes for use.
 *
 * TeXML SAX handler works correct but misfeaturely when SAX parser
 * reports characters in several calls instead of one call.
 * This wrappers fixes the problem
 * 
 * @package texml_proc
 */
 class ParseFile {
    function parse_file(&$texml_writer, $filename) {

        $handler =& new glue_handler($texml_writer);
        $parser = xml_parser_create_ns();
        xml_set_object($parser, $handler);
        xml_set_element_handler($parser,"startElement","endElement");
        xml_set_character_data_handler($parser, "contentHandler");

        // TODO: set include external general entities

        // do parse
        if (!($fp = fopen($filename, "r"))) {
            die("could not open XML input");
        }
        while ($data = fread($fp, 4096)) {
            xml_parse($parser, $data, feof($fp));
        }
    }
 }
 /**
 * Not really a public class. use ParseFile instead.
 * 
 * @package texml_proc
 */
 class glue_handler {
     var $c; // content for process
     var $h; // real handler
     function glue_handler(&$texml_writer, $name_space = 'http://getfo.sourceforge.net/texml/ns1') {
        $this->c = None;
        $this->h =& new Handler($texml_writer);     
     }
    /**
     * Initialize data structures before parsing
     * 
     */
    function startDocument() {
        echo "start document";
    }
    /**
     * Finalize document
     * 
     */
    function endDocument() {
    }
    function startElement($parser, $elementname, $attributes) {
         echo "\nstart $elementname\n";
         echo "attributes: ";
         print_r($attributes);
         echo "\n";
    }
    function endElement($parser, $elementname) {
         echo "\nend $elementname\n";
    }
    function contentHandler($parser,$data) {
         print($data);
    }

 }
 /**
 * Not really a public class.
 *
 * Handles the infile, using the glue_handle class to get the data as 
 * elements or characters.
 * 
 * @package texml_proc
 */
 class Handler {
    var $writer;
    /**
     * Set writer, create maps
     * 
     * @param $texml_writer object the texml_writer object
     */
    function Handler(&$texml_writer) {
        // Set properties
        $this->writer =& $texml_writer;
        // Create handler maps    
    }
    /**
     * Initialize data structures before parsing
     * 
     */
    function startDocument() {
    }
    /**
     * Finalize document
     * 
     */
    function endDocument() {
    }
    function startElement($parser, $elementname, $attributes) {
    }
    function endElement($parser, $elementname) {
    }
    function contentHandler($parser,$data) {
    }
 }

?>