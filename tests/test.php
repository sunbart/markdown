<?php 
// testing module for class functionality 
include '../classes.php';

$document = new Document(1);

if (isset($_POST['doc_body'])) {
	$new = $_POST['doc_body'];
	if(isset($_POST['button_ins'])) {
		$document->setDocument($new);
	} elseif (isset($_POST['button_upd'])) {
		$document->version->setData($new);
	} elseif (isset($_POST['button_ser'])) {
		$document->deleteDocument();
	} else {
		echo 'Please insert some data and test insertion, updating and deletion.';
	}
}

?>
