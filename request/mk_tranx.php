<?php include '../functions/Functions.php'; 
$user = $_SESSION['user_id'];
if (!$user) {
    $response = ['status' => 505, 'message' => 'You Have No Permission For this Operation'];
    echo json_encode($response);
    return false;
}
if($_SESSION['token'] !== $_POST['token']) {
    echo json_encode(['status' => 911, 'message' => 'Wrong request!']);
    return false;
}
switch ($_POST['sst']) {
    case 's1f':
        try {
            $class = new Functions();
            $tempEncrypted = filter_input(INPUT_POST, 'userRef');
            $userRef = $class->simple_encrypt($tempEncrypted,'d');
            $referral = filter_input(INPUT_POST,'referral') ?? '';
            //test taken
            $string = trim(filter_input(INPUT_POST,'testsTaken'));
        
            if(!$string){
                echo json_encode(['status' => 210, 'message' => 'Please Select the Test(s) You want to Take']);    
            return false;
            }
            if(!$userRef){
                echo json_encode(['status' => 210, 'message' => 'Incomplete Request!']);    
            return false;
            }
            //process test taken
            $truncate = strlen($string);
            $string = substr($string,0,$truncate-1);
            $testsTaken = explode(';',$string); 
            $count = count($testsTaken);

            $totalAmount = 0;
            $tranx_id = $class->tranx_ref();
            $class->connect()->beginTransaction();
            
            //Test table
            for($i=0; $i<$count; $i++){
                $testAmount = $class->fetchColumn('sub_labtest_tbl','cost','id',$testsTaken[$i]);
                $totalAmount += $testAmount;
                $class->store('tests_taken',['id','test_id','client_id','tranx_id','created_by','created_at'],
                [time().uniqid(),$testsTaken[$i],$userRef,$tranx_id,$_SESSION['user_id'],date('Y-m-d H:m:s')]);
            }
            $outstanding = 0-$totalAmount;

              $class->store(
                'transactions',
                ['id','payable_amount','client_id', 'created_by','referral'],
                [$tranx_id,$outstanding,$userRef, $user,$referral]
             );
             $class->connect()->commit();
              echo json_encode(['status' => 200, 'message' => 'Transaction Saved Successfully, Please Proceed to Make Payment']);    
            return false;
        } catch (Exception $e) {
            echo json_encode(['status' => 210, 'message' => 'An error occurred! ','response'=>$e->getMessage()]);
        }
    
    default:
        
        break;
}