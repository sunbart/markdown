<?php
include '/parsedown/Parsedown.php';
include 'functions.php';

$fName = '';
$args = array();

if(isset($_GET['f'])){
  $fName = $_GET['f'];


foreach($_GET as $key => $value){
  
  if(!($key == 'f')){
    $args[] = $value;
  }
}

$functions = get_defined_functions()['user'];
$found = false;
  
foreach($functions as $functionName){    
  if($fName == $functionName){
    call_user_func_array($fName, $args);
    $found = true;
    break;
  }    
}
  
if(!$found){
  fallback($fName);
}

function fallback($fName){
  echo($fName . ' wasn\'t found!');
}
  } else {
$functions = get_defined_functions()['user'];
print_r($functions);

}
?>