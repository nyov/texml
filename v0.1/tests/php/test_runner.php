<?php
    require("test_config.php");
    require_once(phpunit . "/" ."Framework.php");
    require_once(phpcode . "/" ."texexec_lib.php");

    class texloginfoTest extends PHPUnit_Framework_TestCase {
        
        function test_check_miss_image() {
            $tli = new texloginfo();
            $tli->parse_log_file("tests/samples/logs/not_image.log");
            $this->assertEquals("overview", str_replace("\n","",$tli->get_missed()));
        }
        
        function test_check_warn_message() {
            $tli = new texloginfo();
            $tli->parse_log_file("tests/samples/logs/warnings.log");
            $warnings_get = explode("\n", $tli->get_warnings());
            $warnings_get = array_diff($warnings_get, array(''));
            $warnings_exp = array("Overfull \hbox (7.9967pt too wide) in paragraph at lines 4047--4146","Overfull \hbox (7.9967pt too wide) in paragraph at lines 8047--8146");
            $this->assertEquals($warnings_exp, $warnings_get);
        }
        
        function test_check_rerun() {
            $tli = new texloginfo();
            $tli->parse_log_file("tests/samples/logs/rerun.log");
            $this->assertEquals(1, $tli->get_rerun());
        }
        
        function test_check_err_message() {
            $tli = new texloginfo();
            $tli->parse_log_file("tests/samples/logs/errors.log");
            $errors_get = explode("\n", $tli->get_errors());
            $errors_get = array_diff($errors_get, array(''));
            $errors_exp = array("! there is error");
            $warnings_get = explode("\n", $tli->get_warnings());
            $warnings_exp = array("Overfull \hbox (7.9967pt too wide) in paragraph at lines 4047--4146\nthere is warning","! pdfTeX warning (ext4): destination with the same identifier...");
            $exp =  array_merge ($errors_exp, $warnings_exp);
            $get =  array_merge ($errors_get, $warnings_exp);
            $this->assertEquals($exp, $get);
        }
        
    }
?>
