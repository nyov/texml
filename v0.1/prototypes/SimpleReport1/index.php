<?php
require('PDFTexer\main.php');
$table_data = array (
    array("1","Legacy","Danielle Steel","","Wishlist","ВЈ13.38","9780593063033"),
    array("2","Other Peoples Secrets","Louise Candlish","Arts & Photography","Wishlist","ВЈ7.20","9781847443892"),
    array("3","Status Anxiety", "Alain De Botton","Arts & Photography","Owned","ВЈ10.28","9780141014869"),
    array("4","Sweet Little Lies: An L.a Candy Novel","Lauren Conrad","Business, Finance & Law","Owned","ВЈ17.07","9780061977282"),
    array("5","The Cricket in Times Square Chester Cricket and His Friends","George Selden","Sports","Owned","ВЈ4.43","Book")
);
// Now begin forming document 
$doc = esla_doc("mystyle");
$doc->style("ChBody");
$doc->text("All Items - Published: 04.03.2012")->style("HeadI");
$doc->text("Books")->style("HeadII");
$table =& $doc->table(10, 40, 40, 30, 15, 22, 28)->style("mytable");
$table->row("#", "Title", "Author", "Subject", "Status", "Replacement value", "ISBN-Barcode");
foreach ($table_data as $row) {
    $table->row($row[0], $row[1], $row[2], $row[3], $row[4], $row[5], $row[6]);
}
$table->row(" ", "-", "-", "-", "-", "-", "-");
$doc->style("showhyphens", "Friends");
esla_pdf($doc, "test1.pdf");
?>
