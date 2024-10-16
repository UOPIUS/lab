<?php

try{
    $str = filter_input(INPUT_POST,'tid');
    $id = filter_var($str, FILTER_SANITIZE_STRING);
    $response = [];
    include '../functions/Functions.php';
    $class = new Functions();
	
    $lgas = $class->rawQuery("SELECT o.*, t.pay_method AS method FROM outstanding_tbl o JOIN payment_types t ON o.payment_method = t.id WHERE o.tranx_ref = '$id'");
   foreach($lgas as $lga){
       $response[] = array('amount'=>$lga->amount,'dt'=>$lga->created_at,'method'=>$lga->method);
   }
    echo (json_encode(array('data'=>$response,'status'=>200)));
  }
  catch(Exception $e){
    echo $e->getMessage();
  }
