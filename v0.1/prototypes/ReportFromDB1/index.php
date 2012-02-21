<?php
require('PDFTexer\main.php');
$hostname = "localhost";
$username = "root";
$password = "123";
$dbName = "ftxml_db";
$userstable = "tblItems";
$user_id = 3;
//mysql_connect($hostname,$username,$password) OR DIE("Can't create connection");
//mysql_select_db($dbName) or die(mysql_error()); 
// Subject not found
$query = 
    "SELECT ItemName title" .
          ", ItemAuthor author" .
          ", ItemStatus status" .
          ", RepValue repval" .
          ", BookISBN isbn" . 
     " FROM $userstable" . 
    " WHERE UserID = $user_id";
//$result =  mysql_query($query) or die(mysql_error());
// Now begin forming document 
$doc = esla_doc();
esla_add(textt, $doc, "All Items - Published: 04.01.2011");
esla_add(textt, $doc, "Books");
$table = &esla_add(textb, $doc);
//esla_add(textr, $table, "Title Author Status Replacement value ISBN-Barcode");
esla_add(textr, $table, "Title"); 
esla_add(textr, $table, "Book1");
esla_add(textr, $table, "Book2");
//while ($row=mysql_fetch_array($result))  {
    //esla_add(textt, $table, $row['author'] ." ". $row['status'] ." ". $row['repval'] ." ". $row['isbn']);
//}
esla_pdf($doc, "test1.pdf");
//mysql_close();
?>
