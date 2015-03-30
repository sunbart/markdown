<?php 
// testing module for class functionality 
include 'classes.php';
echo 'Started testing.\n';
$new = 'Bla';
$author = 'Sergelia';

$document = new Document($author);
$document->setDocument($new);
echo $document->getVersion();
?>