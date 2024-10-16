<?php
try{
    $str = filter_input(INPUT_POST,'s');
    $id = filter_var($str, FILTER_SANITIZE_STRING);
    $response = [];
    include '../functions/Functions.php';
    $class = new Functions();
    $villages = $class->fetchAll('villages_tbl'," WHERE (status = 1) AND (community_id = '$id')");
   foreach($villages as $vl){
       $response[] = array('vid'=>$vl->id,'name'=>$vl->name);
   }
    echo (json_encode(array('data'=>$response)));
  }
  catch(Exception $e){
    echo $e->getMessage();
  }