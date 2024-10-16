<?php include '../functions/Functions.php'; 
        $user = $_SESSION['user_id'];
        if (101 != $_SESSION['role_id']) {
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
            $tempEncrypted = filter_input(INPUT_POST, 'ulref');
            $ref = $class->simple_encrypt($tempEncrypted,'d');
            if(!$ref){
                echo json_encode(['status' => false, 'message' => 'Invalid Transaction Reference!']);
                die(8);
            }
             //if transaction exist, clear all records for this transaction
             $class->connect()->query("DELETE FROM tests_taken WHERE tranx_id = '$ref'");
             $clearPendingTranxIsChecked = $_POST['delOption'];
             if($clearPendingTranxIsChecked){
                 
                 $tranx = $class->connect()->query("UPDATE transactions SET status = 5 WHERE id = '$ref'");

                    if($tranx){
                        //remove the tests from client test
                        $class->connect()->query("INSERT INTO deletes(tranx_ref,reason,user_id) VALUES('$ref','I just decided to delete it','{$_SESSION['user_id']}')");
                        //Log in a file
                        $data = $_SESSION['user_id']." Deleted ".$xTransRef." on ".date('Y-m-d H:i:s').PHP_EOL;
                        $fp = fopen('deletes.txt', 'a');
                        fwrite($fp, $data);
                        fclose($fp);
                    }
                    echo json_encode(['status'=>true,'message'=>'You Have Successfully Deleted '.$ref]);
                    exit(0);
             }
            
            $userRef = htmlentities(trim(filter_input(INPUT_POST,'customerRef')),ENT_QUOTES);
            //test taken
            $string = trim(filter_input(INPUT_POST,'scdljload'));
        
            if(!$string){
                echo json_encode(['status' => false, 'message' => 'Please Select the Test(s) You want to Take']);    
            return false;
            }

            //process test taken
            $truncate = strlen($string);
            $string = substr($string,0,$truncate-1);
            $testsTaken = explode('_',$string); 
            $count = count($testsTaken);

            $totalAmount = 0;
            $tranx_id = $ref;
            $class->connect()->beginTransaction();
            
            //Test table
            for($i=0; $i<$count; $i++){
                $eachTest = json_decode($testsTaken[$i]);
                $testAmount = $class->fetchColumn('sub_labtest_tbl','cost','id',$eachTest->chosenTestRef);
                $totalAmount += $testAmount;
                $class->store('tests_taken',['id','test_id','client_id','tranx_id','created_by','created_at'],
                [time().uniqid(),$eachTest->chosenTestRef,$userRef,$tranx_id,$_SESSION['user_id'],date('Y-m-d H:m:s')]);
            }
            $outstanding = 0-$totalAmount;

              $class->updateRecord(
                'id',
                ['payable_amount',],
                [$outstanding,$tranx_id],"transactions"
             );
             $class->connect()->commit();
              echo json_encode(['status' => true, 'message' => 'Tests Saved Successfully']);    
            return false;
        } catch (Exception $e) {
            echo json_encode(['status' => false, 'message' => 'An error occurred! ','response'=>$e->getMessage()]);
        }