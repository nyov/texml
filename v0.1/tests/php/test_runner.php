<?php
    require("test_config.php");
    require_once(phpunit . "/" ."Framework.php");
    require(phpcode . DIRECTORY_SEPARATOR . "common_getfo_lib.php");
    require(phpcode . DIRECTORY_SEPARATOR . "processor.php");

    class processorDataTest extends PHPUnit_Framework_TestCase {

        protected $fTest;

        protected function setUp() {
            $this->fTest =& new fileTest("tests/results/data/", "tests/samples/data/");
        }
        /*
        public function test_empty() {
            $this->fTest->run("empty");
            $this->assertFileEquals($this->fTest->exp_path, $this->fTest->res_path);
        }  
        public function test_quick() {
            $this->fTest->run("quick_lt");
            $this->assertFileEquals($this->fTest->exp_path, $this->fTest->res_path);
        }    
        public function test_thesis_basic_lt() {
            $this->fTest->run("thesis_basic_lt");
            $this->assertFileEquals($this->fTest->exp_path, $this->fTest->res_path);
        }
        */
        /*
        public function test_cmd() {
            $this->fTest->run("cmd");
            $this->assertFileEquals($this->fTest->exp_path, $this->fTest->res_path);
        } 
        */
        /*
        public function test_cmdnest() {
            $this->fTest->run("cmdnest_lt");
            $this->assertFileEquals($this->fTest->exp_path, $this->fTest->res_path);
        } 
        
        public function test_emptyline() {
            $this->fTest->run("emptyline");
            $this->assertFileEquals($this->fTest->exp_path, $this->fTest->res_path);
        } 
        public function test_entity() {
            $this->fTest->run("entity");
            $this->assertFileEquals($this->fTest->exp_path, $this->fTest->res_path);
        } 
        public function test_escape() {
            $this->fTest->run("escape");
            $this->assertFileEquals($this->fTest->exp_path, $this->fTest->res_path);
        } 
        */
        public function test_fordocs() {
            $this->fTest->run("fordocs");
            $this->assertFileEquals($this->fTest->exp_path, $this->fTest->res_path);
        } 
    }

    class fileTest {
        protected $res_dir;
        protected $out_dir;
        public $exp_path;
        public $res_path;

        function fileTest($res_dir, $out_dir) {
            $this->res_dir = $res_dir;
            $this->out_dir = $out_dir;
        }
        public function run($testname) {
            
            $xmlfile_name = $this->out_dir . $testname . ".xml";
            $outfile =& new fstream($this->res_dir .$testname . ".tex", "w");

            processor.process($xmlfile_name, $outfile, "UTF-8");

            $this->exp_path = $this->out_dir . $testname . ".out";
            $this->res_path = $this->res_dir . $testname . ".tex";
        }
    }
?>
