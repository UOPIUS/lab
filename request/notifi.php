<?php include '../functions/Functions.php'; 
$user = $_SESSION['user_id'];
if (!$user) {
    $response = ['status' => false, 'message' => 'You Have No Permission For this Operation'];
    echo json_encode($response);
    return false;
}
if (102 != $_SESSION['role_id']) {
    echo json_encode(['status' => false, 'message' => 'You Lack Right for this Operation']);
    die(9);
}
switch ($_POST['NOTIFY_ACTION']) {
	case 'HTTP_INFORM_ME':
		// code... alert front desk
		try {
			$conn = new Functions;
			$newTests = $conn->fetchAll("tests_taken"," WHERE status = 1 AND alert_flag = 'N'");

			if($newTests){
				$conn = null;
				echo json_encode(['status' => true, 'message' => 'You Have Some Tests Ready for Printing!']);
		    	exit(9);
			}
			echo json_encode(['status' => false, 'message' => 'No New Test']);
		    exit(9);
			
		} catch (\Throwable $e) {
			echo json_encode(['status' => false, 'message' => 'Something Happened So Request Failed']);
		    die(9);
		}

		break;
	
	default:
		// code... update status
		$test = htmlentities(filter_input(INPUT_POST,'http_test_id'),ENT_QUOTES);
		$conn = new Functions;
		$conn->connect()->query("UPDATE tests_taken SET alert_flag = 'YES' WHERE id ='".$test."'");
		break;
}
