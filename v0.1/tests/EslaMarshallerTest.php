<?php
require(dirname(__FILE__) . '/php4_config.php');
require(dirname(__FILE__) . '/../php/main.php');

class EslaMarshallerTest extends PHPUnit_Framework_TestCase {
    public function testMarshller() {
        $marshaller = new EslaMarshaller();
        
        $doc = new EslaEnvironment("document");
        $doc->text('Text1')->style('HeadI');
        $doc->text('Text2');
        $doc->style('Style1');
        $table =& $doc->table()->style('testable', 'TheadPara', 'TbodyPara', 'TfootPara');
        $table->row('head1');
        $table->row('cell1');
        $table->row('foot1');
                
        $marshaller->marshal($doc, 'results/Marshaller1');
        
        $this->assertFileEquals('results/Marshaller1.tex', 'samples/Marshaller1.tex');
    }
}
?>
