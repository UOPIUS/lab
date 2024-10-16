<?php include '../functions/Functions.php'; 
$user = $_SESSION['user_id'];
if (!$user) {
    echo json_encode(['status' => 505, 'message' => 'You Have No Permission For this Operation']);
    die(0);
}
if($_SESSION['token'] !== $_POST['token']) {
    echo json_encode(['status' => 911, 'message' => 'Wrong request!']);
    return false;
}
switch ($_POST['tst1form']) {
    case 's1ftest':
        try {
            $class = new Functions();
            $test_result = htmlentities(trim($_POST['test_result']),ENT_QUOTES);
            $test_name = htmlentities(trim(filter_input(INPUT_POST, 'each_test')));
            //$rhesus = htmlentities(trim(filter_input(INPUT_POST, 'rhesus')));
            $specimen = htmlentities(trim(filter_input(INPUT_POST, 'specimen')));
            if(!($test_result && $test_name && $specimen)){
                echo json_encode(['status' => 210, 'message' => 'All fields Marked * are REQUIRED']);    
            return false;
            }
              $class->updateRecord(
                'id',
                ['test_result','created_by','lab_id','specimen','status'],
                [$test_result, $user,$user,$specimen,'1',$test_name],'tests_taken'
             );
              echo json_encode(['status' => 200, 'message' => 'Test Report Saved Successfully']);    
            return false;
        } catch (Exception $e) {
            echo json_encode(['status' => 210, 'message' => 'An error occurred! ','response'=>$e->getMessage()]);
            return false;
        }
    
    default:
    try {
        $class = new Functions();
        $id = htmlentities(trim(filter_input(INPUT_POST, 'tests1ftest')));
        if(!$id){
            echo json_encode(['status' => 210, 'message' => 'A Fatal Error Occurred']);    
        return false;
        }
          $class->updateRecord(
            'id',
            ['transactions','created_by','lab_id'],
            [$test_result, $user,$user,$test_name],'tests_taken'
         );
          echo json_encode(['status' => 200, 'message' => 'Test Report Saved Successfully']);    
        return false;
    } catch (Exception $e) {
        echo json_encode(['status' => 210, 'message' => 'An error occurred! ','response'=>$e->getMessage()]);
    }
        break;
}