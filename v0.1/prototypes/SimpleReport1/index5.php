<?php
require('php5_config.php');
require('PDFTexer\main.php');
$table_data = array (
    array("Legacy","Danielle Steel","","Wishlist","13.38","9780593063033"),
    array("Other Peoples Secrets","Louise Candlish","Arts & Photography","Wishlist","7.20","9781847443892"),
    array("Status Anxiety", "Alain De Botton","Arts & Photography","Owned","10.28","9780141014869"),
    array("Sweet Little Lies: An L.a Candy Novel","Lauren Conrad","Business, Finance & Law","Owned","17.07","9780061977282"),
    array("The Cricket in Times Square Chester Cricket and His Friends","George Selden","Sports","Owned","4.43","Book")
);
date_default_timezone_set('Europe/Moscow'); 
// Now begin forming document 
$doc = esla_doc("mystyle");
$doc->style("ChBody");
$doc->text("All Items - Published: " . date("d.m.Y"))->style("HeadI");
$doc->ws(5);
$doc->text("Books")->style("HeadII");
$doc->ws(5);
$table = $doc->table(10, 40, 40, 30, 15, 22, 28)->style("mytable");
$table->row("#", "Title", "Author", "Subject", "Status", "Replacement value", "ISBN-Barcode");
$i = 0;
$sum = 0.0;
foreach ($table_data as $row) {
    $table->row(++$i, $row[0], $row[1], $row[2], $row[3], $row[4], $row[5]);
    $sum = $sum + $row[4];
}
$table->row("Total replacement cost for " . $i . " Books is " . $sum);
$doc->style("showhyphens", "Friends");
esla_pdf($doc, "test1.pdf");
?>
