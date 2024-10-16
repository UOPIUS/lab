<?php include '../functions/Functions.php'; 
$user = $_SESSION['user_id'];
if (!$user) {
    $response = ['status' => 505, 'message' => 'You Have No Permission For this Operation'];
    echo json_encode($response);
    return false;
}
if ($_SESSION['role_id'] != 103) {
    $response = ['status' => 504, 'message' => 'You Lack Right for this Operation'];
    echo json_encode($response);
    return false;
}
        try {
            $class = new Functions();
            $tempEncrypted = filter_input(INPUT_POST, 'tranx_ref');
            $transRef = $class->simple_encrypt($tempEncrypted,'d');
			$paidAmount = (float)$_POST['amount_paid'];
            $method = $_POST['pay_method'];
			if (!$method) {
				$response = ['status' => 404, 'message' => 'Payment Method Is Required'];
				echo json_encode($response);
				return false;
			}
            
			if (is_nan($paidAmount)) {
				$response = ['status' => 505, 'message' => 'Enter A Valid Amount'];
				echo json_encode($response);
				return false;
			}
			
            //client and transaction record
            $tranx = $class->fetch('transactions'," WHERE id = '$transRef'");
            $client = $class->fetch('clients_tbl'," WHERE ref = '$tranx->client_id'");
			//calculate outstanding
			$outstanding = abs($tranx->payable_amount + $tranx->amount);
			if ($paidAmount > ($outstanding)) {
				echo json_encode(['status' => 503, 'message' => 'You cannot Pay More than the Amount You are Owing']);
				return false;
			}
            //balance account
            $clientBal = $client->bal + $paidAmount;
            $updateTranx = $paidAmount+$tranx->amount;
			
            $connection = $class->connect();
            $connection->beginTransaction();
			//transaction
            $class->updateRecord('id',['amount','updated_at'],
			[$updateTranx,date('Y-m-d H:m:s'),$transRef],'transactions');

			//save payment information
			$class->store('outstanding_tbl',['id','payment_method','client_id','tranx_ref','amount','previous_debt','cashier_id'],
			[$transRef.$paidAmount.time(),$method,$tranx->client_id,$transRef,$paidAmount,$outstanding,$_SESSION['user_id']]);
            $connection->commit();
              echo json_encode(['status' => 200, 'message' => 'Payment Accepted Successfully, Thank You']);   
            return false;
        } catch (Exception $e) {
            echo json_encode(['status' => 210, 'message' => 'An error occurred! ','response'=>$e->getMessage()]);
        }