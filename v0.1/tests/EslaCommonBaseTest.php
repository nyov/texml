<?php

class EslaCommonBaseTest extends PHPUnit_Framework_TestCase {

    public function testStyle() {
        $marshaller = new EslaMarshaller();
               
        $doc = new EslaEnvironment("document");
        
        $doc->style("Style1");
        
        $doc->text("Text1")->style("Style2");
        
        $table =& $doc->table()->style('testable', 'TheadPara', 'TbodyPara', 'TfootPara');
        $table->row('head1');
        $table->row('cell1');
        $table->row('foot1');
        
        $doc->style("Style3", "Parameter1");
        
        $marshaller->marshal($doc, 'results/Style1');
        
        $this->assertFileEquals('results/Style1.tex', 'samples/Style1.tex');
    }    
    
    public function testWs() {
        $marshaller = new EslaMarshaller();
        
        $doc = new EslaEnvironment("document");
        $doc->ws(3);
        
        $marshaller->marshal($doc, 'results/Ws1');
        
        $this->assertFileEquals('results/Ws1.tex', 'samples/Ws1.tex');
    }    

    public function testRow() {
        $marshaller = new EslaMarshaller();
        
        $doc = new EslaEnvironment("document");
        
        // all types of rows
        $table =& $doc->table(1,4);
        $table->row('head1','head2');
        $table->row('cell1', 'cell2');
        $table->row('foot1', 'foot2');
        
        // head & foot rows
        $table =& $doc->table(1,4);
        $table->row('head1','head2');
        $table->row('foot1', 'foot2');

        // head row
        $table =& $doc->table(1,4);
        $table->row('head1','head2');
       
        // span rows
        $table =& $doc->table(1,4);
        $table->row('head1','head2');
        $table->row('cell11');
        $table->row('cell21','cell22');
        $table->row('foot1');
        
        $marshaller->marshal($doc, 'results/Row1');
        
        $this->assertFileEquals('results/Row1.tex', 'samples/Row1.tex');
       
}    
    
    public function testTable() {
        $marshaller = new EslaMarshaller();
        
        $doc = new EslaEnvironment("document");

        // table 1
        $doc->table();

        // table 2       
        $table =& $doc->table(1,4);
        $table->row('head1','head2');
        $table->row('cell1', 'cell2');
        $table->row('foot1', 'foot2');
        
        // table 3
        $table =& $doc->table();
        $table->row('head1');
        $table->row('cell1');
        $table->row('foot1');
        
        $marshaller->marshal($doc, 'results/Table1');
        
        $this->assertFileEquals('results/Table1.tex', 'samples/Table1.tex');
    }    
}
?>
