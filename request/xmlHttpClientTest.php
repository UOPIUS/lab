<?php include '../functions/Functions.php'; 
$user = $_SESSION['user_id'];
if (!$user) {
    $response = ['status' => false, 'message' => 'You Have No Permission For this Operation'];
    echo json_encode($response);
    return false;
}
if($_SESSION['token'] !== $_POST['customToken']) {
    echo json_encode(['status' => false, 'message' => 'Wrong request!']);
    return false;
}

        try {
            $class = new Functions();
            $tempEncrypted = filter_input(INPUT_POST, 'userRef');
            $userRef = $class->simple_encrypt($tempEncrypted,'d');
            $referral = filter_input(INPUT_POST,'referral') ?? '';
            //test taken
            $string = trim(filter_input(INPUT_POST,'scdljload'));
            if(!$string){
                echo json_encode(['status' => false, 'message' => 'Please Select the Test(s) You want to Take']);    
            return false;
            }
            if(!$userRef){
                echo json_encode(['status' => false, 'message' => 'Incomplete Request!']);    
            return false;
            }
            //process test taken
            $truncate = strlen($string);
            $string = substr($string,0,$truncate-1);
            $testsTaken = explode('_',$string); 
            $count = count($testsTaken);

            $totalAmount = 0;
            $tranx_id = $class->tranx_ref();
            $class->connect()->beginTransaction();
            
            //Test table
            for($i=0; $i<$count; $i++){
                /*
                $eachTest = json_decode($testsTaken[$i]);
                $testAmount = $class->fetchColumn('sub_labtest_tbl','cost','id',$eachTest->chosenTestRef);
                $totalAmount += $testAmount;
                $class->store('tests_taken',['id','test_id','client_id','tranx_id','created_by','created_at'],
                [time().uniqid(),$eachTest->chosenTestRef,$userRef,$tranx_id,$_SESSION['user_id'],date('Y-m-d H:m:s')]);
                */
                
                $eachTest = json_decode($testsTaken[$i]);
                $testDetail = $class->fetch("sub_labtest_tbl"," WHERE id ='".$eachTest->chosenTestRef."'");
                $totalAmount += $testDetail->cost;
                $class->store('tests_taken',['id','test_id','client_id','tranx_id','created_by','created_at','category_id'],
                [time().uniqid(),$eachTest->chosenTestRef,$userRef,$tranx_id,$_SESSION['user_id'],date('Y-m-d H:m:s'),$testDetail->labtest_id]);
            }
            $outstanding = -1*$totalAmount;

              $class->store(
                'transactions',
                ['id','payable_amount','client_id', 'created_by','referral'],
                [$tranx_id,$outstanding,$userRef, $user,$referral]
             );
             $class->connect()->commit();
              echo json_encode(['status' => true, 'message' => 'Transaction Saved Successfully, Please Proceed to Make Payment']);    
            return false;
        } catch (Exception $e) {
            echo json_encode(['status' => false, 'message' => 'An error occurred! ','response'=>$e->getMessage()]);
        }
