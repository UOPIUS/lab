<?php include '../functions/Functions.php'; 
$user = $_SESSION['user_id'];
if (!$user) {
    $response = ['status' => 505, 'message' => 'You Have No Permission For this Operation'];
    echo json_encode($response);
    return false;
}
        try {
            $class = new Functions();
            $tempEncrypted = filter_input(INPUT_POST, 'tranx_ref');
            $transRef = $class->simple_encrypt($tempEncrypted,'d');
            $newAmount = htmlentities(filter_input(INPUT_POST,'amount'));
            $outstanding = htmlentities(filter_input(INPUT_POST,'namount'));
            $method = $_POST['pay_method'];
            if(!$method){
                echo json_encode(['status' => 505, 'message' => 'No Payment Method Selected']);
                return false;
            }
            //Validate amount
            if(is_nan($newAmount) && is_nan($outstanding)){
                echo json_encode(['status' => 504, 'message' => 'Invalid Amount Entered']);
                return false;
            }
            //if there's discount
            $expectedDiscount = htmlentities(filter_input(INPUT_POST,'discount'),ENT_QUOTES);
            $expectedDiscount = $expectedDiscount ? $expectedDiscount : 0;
            if(is_nan($expectedDiscount)){
                echo json_encode(['status' => 504, 'message' => 'Invalid Discount Entered']);
                die(8);
            }
            
            $tranx = $class->fetch("transactions"," WHERE id = '$transRef'");
            if($expectedDiscount > abs($tranx->payable_amount)){
                echo json_encode(['status' => 504, 'message' => 'Discount Must be Less than the Expected Amount']);
                die(8);
            }
            $amountWithoutDiscount = abs($tranx->payable_amount);
            $amountWithDiscount = $amountWithoutDiscount - $expectedDiscount;
            $paidAmount = $tranx->amount + $newAmount;
            $previousDebt = $amountWithDiscount;
            //validate amount
			if ($paidAmount > $previousDebt) {
				echo json_encode(['status' => 503, 'message' => 'You cannot Pay More than the Amount You are Owing']);
				return false;
			}
            $date = date('Y-m-d H:m:s');
            $conn = $class->connect();
            $conn->beginTransaction();
            //save payment information
			$class->store('outstanding_tbl',['id','payment_method','client_id','tranx_ref','amount','previous_debt','cashier_id'],
			[$transRef.$paidAmount.time(),$method,$tranx->client_id,$transRef,$paidAmount,$previousDebt,$_SESSION['user_id']]);
            //transaction
            $class->updateRecord('id',['cashier_id','amount','payable_amount','principal_amount','discount','payment_type','status','updated_at'],
            [$_SESSION['user_id'],$paidAmount,-$amountWithDiscount,$amountWithoutDiscount,$expectedDiscount,$method,1,$date,$transRef],'transactions');
            $conn->commit();
            unset($conn);
              echo json_encode(['status' => 200, 'message' => 'Payment Accepted Successfully, Please Proceed to Print Receipt']);    
            return false;
        } catch (Exception $e) {
            echo json_encode(['status' => 210, 'message' => 'An error occurred! ','response'=>$e->getMessage()]);
        }