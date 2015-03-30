<?php


function parsedown($input){
  $Parsedown = new Parsedown();  
  $json = array(
    'result' => $Parsedown->text($input)
  );
  
  echo(json_encode($json));  
}



?>