<?php include '../functions/Functions.php'; 
$user = $_SESSION['user_id'];
if (!$user) {
    $response = ['status' => 505, 'message' => 'You Have No Permission For this Operation'];
    echo json_encode($response);
    return false;
}
try{
    $data = trim(filter_input(INPUT_GET,'idx'));
    $q = trim(filter_input(INPUT_GET,'idReference'));
    
    //sanitize input
    $param  = preg_replace('#[^a-zA-Z0-9/]#s','',$data);
    $testId = preg_replace('#[^a-zA-Z0-9]#s','',$q);
                $class = new Functions();
                $m = "";
                $tranx = $class->rawQuery("SELECT payable_amount, id, amount FROM transactions WHERE id = '$param'",1);
            $sum = $tranx->amount + $tranx->payable_amount;
            $flag = false;
            $id = null;
            //get the test reference
            if($sum){
                $m .= "You need to clear your outstanding of ₦".number_format(abs($sum),2). " before you can print this Result.";
            } else {
                $m .= "Proceed to Print";
                $flag = true;
                $id = $class->simple_encrypt($testId);
            }
              echo json_encode(['status' => $flag, 'message' => $m, "id" => $id]);    
            return false;
        } catch (Exception $e) {
            echo json_encode(['status' => 210, 'message' => 'An error occurred! ','response'=>$e->getMessage()]);
        }
    
