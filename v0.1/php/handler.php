<?php
/**
 * Handling of TeXML source
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
   /**
    * Parse the xml file
    *
    * @param object &$texml_writer the writer
    * @param string $filename the xml filename
    * @param string $in_enc the input encoding
    */
    function parse_file(&$texml_writer, $filename, $in_enc = null) {

        if ($in_enc != null) {
            $parser = xml_parser_create($in_enc);
        } else {
            $parser = xml_parser_create();
        }

        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
        $inc_enc = xml_parser_get_option($parser, XML_OPTION_TARGET_ENCODING);
        $texml_writer->set_in_enc($in_enc);
        $handler =& new glue_handler($texml_writer);

        xml_set_object($parser, $handler);
        xml_set_element_handler($parser,"startElement","endElement");
        xml_set_character_data_handler($parser, "characters");

        // TODO: set include external general entities

        // do parse
        $handler->startDocument();
        if (!($fp = fopen($filename, "r"))) {
            die("could not open XML input");
        }
        while ($data = fread($fp, 4096)) {
            xml_parse($parser, $data, feof($fp));
        }
        $handler->endDocument();
    }
 }
 /**
 * Handler invalid xml
 * 
 * @package texml_proc
 */
 class InvalidXmlException extends PException {
    function InvalidXmlException() {
        // some actions
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
     var $w; // writer
    /**
     * Initialize of the properties of the class
     * 
     * @param $texml_writer object the TeXML writer  
     */
     function glue_handler(&$texml_writer) {
        $this->w =& $texml_writer; 
        $this->c = None;
        //echo "construct-";
        $this->h =& new Handler($texml_writer);
     }
    /**
     * Set values of properties for parsing and begin document
     * 
     */
    function startDocument() {
        $this->c = None;
        $h =& $this->h;

        $h->startDocument();
    }
    /**
     * Free buffer from characters and write it.
     * 
     */
    function flushChars() {
        if ($this->c != None) {
            $h =& $this->h;
            $h->characters($this->c);
            $this->c = None;
        }
    }
    /**
     * Finalize document
     * 
     */
    function endDocument() {
        $this->flushChars();
        $h =& $this->h;
        $h->endDocument();
    }
    /**
     * The method is executed when start-xml-tag was occured. 
     * 
     * @param object $parser the SAX-parser
     * @param string $local_name the local name of the element
     * @param array $attrs the attributes of the element
     */
    function startElement($parser, $local_name, $attrs) {
        // get the column and line number and use the handler
        $col_num = xml_get_current_column_number($parser);
        $line_num =  xml_get_current_line_number($parser);
        $h =& $this->h; 
        $h->set_location($col_num, $line_num);

        $this->flushChars();
        $h->startElement($local_name, $attrs);
    }  
    /**
     * The method is executed when end-xml-tag was occured. 
     * 
     * @param object $parser the SAX-parser
     * @param string $local_name the local name of the element 
     */
    function endElement($parser, $local_name) {
        $h =& $this->h; 

        $col_num = xml_get_current_column_number($parser);
        $line_num = xml_get_current_line_number($parser);
        $h->set_location($col_num, $line_num);
        $this->flushChars();
        $h->endElement($local_name);

    }

    /**
     * No action. The only effect is that chunk
     * ... aa  <!-- xx -->  bb ...
     * is reported twice ('... aa  ' and ' bb ...')
     * instead of onece ('... aa    bb ...')
     *
     * @param object $parser the SAX-parser
     * @param string $target the parameter contains the processing-instruction-target
     * @param strig $data the parameter contains the processing-instruction-data
     */
    function processingInstruction($parser, $target, $data) {
        $this->flushChars();
    }
    /**
     * Handle text data
     *
     * @param object $parser the parser 
     * @param string $content the text content of element 
     */
    function characters($parser, $content) {        
        $h =& $this->h; 
        $col_num = xml_get_current_column_number($parser);
        $line_num =  xml_get_current_line_number($parser);
        $h->set_location($col_num, $line_num);
        if (None == $this->c) {
            $this->c = $content;
        } else {
            $this->c .= $content;
        }
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
    var $__col_num; 
    var $__line_num;
    var $cmdname_stack;
    var $endenv_stack;
    var $has_parm;
    var $no_text_content;
    var $text_is_only_spaces;
    var $model_nomath;
    var $model_content;
    var $model_cmd;
    var $model_env;
    var $model;
    var $model_stack;
    var $end_handlers;
    /**
     * Set writer and other properties, create maps
     * 
     * Object variables
     * writer
     * no_text_content
     * text_is_only_spaces
     *
     * For <env/> support:
     * cmdname
     * cmdname_stack
     * endenv
     * endenv_stack
     *
     * For <cmd/> support:
     * has_parm # Stacking is not required: if <cmd/> is in <cmd/>,
     *          # then is it wrapped by <parm/> or <opt/>
     *
     * @param $texml_writer object the texml_writer object
     */
    function Handler(&$texml_writer) {
        // Set properties
        $this->writer =& $texml_writer;
        $this->cmdname_stack = array();
        $this->endenv_stack  = array();
        $this->cmdname       = '';
        $this->endenv        = '';
        $this->has_parm      = 0;
        $this->no_text_content     = 0;
        $this->text_is_only_spaces = 0;
        // Create handler maps
        $this->model_nomath = array(
          'TeXML' => 'on_texml',
          'cmd' =>   'on_cmd',
          'env' =>   'on_env',
          'group' => 'on_group',
        );
        $this->model_content = $this->model_nomath;
        $this->model_cmd = array(
          'opt' =>    'on_opt',
          'parm' =>   'on_parm'
        );
        // copy, so == will true only for environment, not for any tag that shares model_content
        $this->model_env    = array_merge($this->model_content, $this->model_cmd);
        $this->model_opt    =& $this->model_content;
        $this->model_parm   =& $this->model_content;
        $this->end_handlers = array(
          'TeXML' =>  'on_texml_end',
          'cmd' =>    'on_cmd_end',
          'env' =>    'on_env_end',
          'group' =>  'on_group_end',
          'opt' =>    'on_opt_end',
          'parm' =>   'on_parm_end',
        );
        

    }
    /**
     * Set location of the position of the parsing 
     *
     * @param $col integer current column number for an XML parser 
     * @param $line integer current line number for an XML parser
     */
    function set_location($col, $line) {
        
        $this->__col_num = $col;
        $this->__line_num = $line;
    }
    /**
     * Handle 
     invalide xml element 
     *
     * @param $local_name string the local name of the element 
     */
    function invalid_xml($local_name) {
        $msg = sprintf(" Invalid XML %s, %s: ", $this->__col_num , $this->__line_num);
        $msg .= sprintf('%s not expected', $this->local_name);

        raise('InvalidXmlException', $msg);
    }
    /**
     * Handle invalide xml element for other types of invalid XML
     *
     * @param $msg string the message for showing of the info about the exception 
     */
    function invalid_xml_other($msg) {
        raise('InvalidXmlException', $msg);
    }
    // -------------------------------------------------------------------
    /**
     * Initialize data structures before parsing
     * 
     */
    function startDocument() {
        $this->model       = array('TeXML' => 'on_texml');
        $this->model_stack = array();
    }
    /**
     * Finalize document
     * 
     */
    function endDocument() {
    }
    /**
     * Handle start of an element
     *
     * @param parser object the SAX-parser 
     * @param name string the name of the element
     * @param attrs array the attribues of the element 
     */           
    function startElement($name, $attrs) {
        $m =& $this->model;
        if (array_key_exists($name, $m)) {
            call_user_func_array(array(get_class($this), $m[$name]), array($attrs));
        } else {
            $this->invalid_xml($name);
        }
    }               
    /**
     * Handle text data
     *
     * @param content string the text content of element 
     */          
    function characters($content) {
        //
        // First, check if content allowed at all
        //
        if ($this->no_text_content) {
           $msg = sprintf('Invalid XML %s, %s: ', $this->__col_num, $this->__line_num);
           //msg .= sprintf("Text content is not expected: '%s'", $content.encode('latin-1', 'replace')
           $this->invalid_xml_other($msg);
        }
        // Element <cmd/> should not have text content,
        // but it also may contain spaces due to indenting
        // Element <env/> may have <opt/> and <parm/>, so we do
        // magic to delete whitespace at beginning of environment
        if ($this->text_is_only_spaces) {
            
            $stripped = ltrim($content);
            if (0 != strlen($stripped)) {
                $msg = sprintf('Invalid XML %s, %s: ', $this->__col_num, $this->__line_num);
                //$msg .= sprintf("Only whitespaces are expected, not text content: '%s'", content.encode('latin-1', 'replace')
                $this->invalid_xml_other($msg);
            }
            return;       
        }
        //
        // Finally, write content
        //
        $w =& $this->writer;
        $w->write(trim($content));
    }
                   
    /**
     * Handle end of en element
     *
     * @param name string the name of the element
     */      
    function endElement($name) {
        call_user_func(array(get_class($this), $this->end_handlers[$name]));
        $this->unstack_model();
    }       
    /**
     * Remember content model of parent and set model for current node
     *
     * @param string $model the name of the model 
     */
    function stack_model($model) {
        if ($model == null) {
            return;
        }
        array_push($this->model_stack, $model);
        $this->model = $model;
    }
    /**
     * Restore content model of parent
     *
     */
    function unstack_model() {
        array_pop($this->model_stack);
        $this->model = end($this->model_stack);
    }
    // -----------------------------------------------------------------
    /**
     * Get value of attribute 
     *
     * Raises error in other cases.
     *
     * @param array $attrs the attributes  
     * @param string $aname string the name of the attribute
     * @param string $default the default value
     * @return integer returns 1 if aname is in the attrs, 0 if aname is'nt in the attrs and default if attribute not exists.   
     */
    function get_boolean($attrs, $aname, $default) {

        if ($attrs == null) {
            $attrs = array();
        }
        $aval = array_key_exists($aname, $attrs);
        if (None == $aval) {
            return $default;
        } else if (true == $aval) {
            return 1;
        } else if (false == $aval) {
            return 0;
        }
        raise('ValueError', sprintf("Value of boolean attribute '%s' is not '0' or '1', but '%s'", $aname, $aval));

        $msg = sprintf('Invalid XML %s, %s: ', $this->__col_num, $this->__line_num);
        $msg .= sprintf("Value of boolean attribute '%s' is not '0' or '1', but '%s'", $aname, $aval);
        $this->invalid_xml_other($msg);
    }
    /**
     * Handle TeXML element
     *
     * @param array $attrs the attributes  
     */
    function on_texml($attrs = null) {
        if ($attrs == null) {
            $attrs = array();
        }

        $mc =& $this->model_content;
        
        $this->stack_model($mc);
        $emptylines = $this->get_boolean($attrs, 'emptylines', None);
        $escape     = $this->get_boolean($attrs, 'escape',     None);
        $ligatures  = $this->get_boolean($attrs, 'ligatures',  None);
        $w =& $this->writer; 
        $w->stack_emptylines($emptylines);
        $w->stack_escape($escape);
        $w->stack_ligatures($ligatures);
    }
    /**
     * Finalize of handling TeXML element 
     *
     */
    function on_texml_end() {
        $w =& $this->writer; 
        $w->unstack_ligatures();
        $w->unstack_escape();
        $w->unstack_emptylines();
    }      
    // -----------------------------------------------------------------
    /**
     * Handle 'cmd' element
     *
     * @param attts array the attributes 
     */    
    function on_cmd($attrs) {
        $this->stack_model($this->model_cmd);
        //
        // Get name of the command
        //
        $name;
        $name_is = array_key_exists('name', $attrs);
        if ($name_is == None or $name_is == false) {
            $name = '';
        } else {
            $name = $attrs['name'];
        }
        if (0 == strlen($name)) {
            $msg = sprintf('Invalid XML %s, %s: ', $this->__col_num, $this->__line_num);
            $msg .= "Attribute cmd/@name is empty"; 
            $this->invalid_xml_other($msg);
        }
        $w =& $this->writer; 
        $w->writech('\\', 0);
        $w->write($name, 0);
        //
        // Setup in-cmd processing
        //
        $this->has_parm            = 0;
        $this->text_is_only_spaces = 1;
    }
    /**
     * Finalize of the handling of the 'cmd' element
     *
     */ 
    function on_cmd_end() {
        $this->text_is_only_spaces = 0;
        $w =& $this->writer; 
        $w->writech(get_linespr(), 0);
    }
    /**
     * Handle 'opt' element
     *
     * @param attts array the attributes 
     */ 
    function on_opt($attrs) {
        $this->on_opt_parm('[', $attrs);
    }
    /**
     * Handle 'parm' element
     *
     * @param attts array the attributes 
     */  
    function on_parm($attrs) {
        $this->on_opt_parm('{', $attrs);
    }
    /**
     * Finalize of the handling of the 'opt' element
     *
     */  
    function on_opt_end() {
        $this->on_opt_parm_end(']');
    }
    /**
     * Finalize of the handling of the 'parm' element
     *
     */  
    function on_parm_end() {
        $this->on_opt_parm_end('}');
    }
    /**
     * Handle 'parm' and 'opt' elements
     *
     * @param ch string the character
     * @param attrs array the attributes 
     */  
    function on_opt_parm($ch, $attrs) {
        $this->stack_model($this->model_opt);
        $w =& $this->writer; 
        $w->writech($ch, 0);
        $this->text_is_only_spaces = 0;
    }
    /**
     * Finalize of the handling of the 'opt' and 'parm' element
     *
     * @param ch string character
     */   
    function on_opt_parm_end($ch) {
        $w =& $this->writer; 
        $w->writech($ch, 0);
        $this->has_parm            = 1; // At the end to avoid collision of nesting
        // <opt/> can be only inside <cmd/> or (very rarely) in <env/>
        $last_ms = end($this->model_stack);

        if (count(array_diff_assoc($last_ms, $this->model_env)) > 0) { // maps not equal
          $this->text_is_only_spaces = 1;
        } else {
          $this->text_is_only_spaces = 0;
        }
    }
    // -----------------------------------------------------------------
    /**
     * Handle 'env' element
     *
     * @param attrs array the attributes 
     */    
    function on_env($attrs) {
        $this->stack_model($this->model_env);
        //
        // Get name of the environment, and begin and end commands
        //
        $name;
        $name_is = array_key_exists('name', $attrs);
        if ($name_is == None or $name_is == false) {
            $name = '';
        } else {
            $name = $attrs['name'];
        }
        if (0 == strlen($name)) {
            $msg = sprintf('Invalid XML %s, %s: ', $this->__col_num, $this->__line_num);
            $msg .= "Attribute env/@name is empty"; 
            $this->invalid_xml_other($msg);
        }
        /*$begenv;
        $begin_is = array_key_exists('begin', $attrs);
        if ($begin_is == None or $begin_is == false) {
            $begenv = '';
        } else {
            $begenv = $attrs['begin'];
        } */
        $begenv = get_value($attrs, 'begin', 'begin');

        $cmdname_s = $this->cmdname_stack;
        array_push($cmdname_s, $this->cmdname);
        $endenv_s = $this->endenv_stack;
        array_push($endenv_s, $this->endenv);
        $this->cmdname = $name;

        /*
        $end_is = array_key_exists('end', $attrs);
        if ($end_is == None or $end_is == false) {
            $this->endenv = '';
        } else {
            $this->endenv = $attrs['end'];
        } */
        $this->endenv = get_value($attrs, 'end', 'end');


        $w =& $this->writer; 
        $w->write(sprintf('\%s{%s}',$begenv, $name), 0);
        $w->writech(get_linespr(), 0);
    }
    /**
     * Finalize of the handling of the 'env' element
     *
     */     
    function on_env_end() {
        $w =& $this->writer; 
        $w->writech(get_linespr(), 0);
        $w->write(sprintf('\%s{%s}', $this->endenv, $this->cmdname), 0);
        $w->writech(get_linespr(), 0);
        $this->cmdname = array_pop($this->cmdname_stack);
        $this->endenv  = array_pop($this->endenv_stack);
    }
    /**
     * Handle 'group' element
     *
     * @param attrs array the attributes 
     */     
    function on_group($attrs) {
        $w =& $this->writer; 
        $this->stack_model($this->model_content);
        $w->writech('{', 0);
    }
    /**
     * Finalize of the handling 'group' element
     *
     */    
    function on_group_end() {
        $w =& $this->writer; 
        $w->writech('}', 0);
    }    
 }

?>