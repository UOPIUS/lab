<?php include '../functions/Functions.php'; 
$user = $_SESSION['user_id'];
if (!$user) {
    $response = ['status' => 505, 'message' => 'You Have No Permission For this Operation'];
    echo json_encode($response);
    return false;
}
if ($_SESSION['role_id'] != 106) {
    $response = ['status' => 504, 'message' => 'You Lack Right for this Operation'];
    echo json_encode($response);
    return false;
}
        try {
            $class = new Functions();
            $id = filter_input(INPUT_POST, 'txref');
			$approvedAmount = (float)$_POST['a_amount'];
            
			if (is_nan($approvedAmount)) {
				echo json_encode(['status' => 404, 'message' => 'Enter a Valid Amount']);
				return false;
			}
            
			if (empty($id)) {
				echo json_encode(['status' => 505, 'message' => 'Invalid Request']);
				return false;
			}
			
            $class = new Functions;
            $class->updateRecord('id',['amount_approved','approved_at','approved_by','status'],
			[$approvedAmount,date('Y-m-d H:m:s'),$_SESSION['user_id'],1,$id],'expenses');
              echo json_encode(['status' => 200, 'message' => 'Operation Completed Successfully, Thank You!']);   
            return false;
        } catch (Exception $e) {
            echo json_encode(['status' => 210, 'message' => 'An error occurred! ','response'=>$e->getMessage()]);
        }