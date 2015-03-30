<?php
function lorem($words){
  $lorem = '';
  $source = ['Lorem ', 'ipsum ', 'dolor ', 'sit ', 'amet ', 'consectetuer ', 'elit. '];
  
  for($i = 0; $i < $words; $i++){
    $lorem = $lorem . $source[$i % 7];
  }
  
  echo($lorem);
}

function hello($name){
  echo('Hello ' . $name);
}

function parsedown($input){
  $Parsedown = new Parsedown();
  
  //savedocument($input);
  
  $json = array(
    'result' => $Parsedown->text($input)
  );
  
  echo(json_encode($json));  
}

function saveDocument($text){
  $link = mysqli_connect("localhost", "root", "", "markdown");
  
  $sql = 'UPDATE documents SET documentContent = "' . mysqli_escape_string($link, $text) . '" WHERE documentId = 1';
  
  mysqli_query($link, $sql);
}


?>