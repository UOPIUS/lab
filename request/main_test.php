<?php include '../functions/Functions.php'; 
$user = $_SESSION['user_id'];
if (!$user) {
    $response = ['status' => 505, 'message' => 'You Have No Permission For this Operation'];
    echo json_encode($response);
    return false;
}

switch ($_POST['tt']) {
    case 't1':
        try {
            if($_SESSION['token'] !== $_POST['token']) {
                echo json_encode(['status' => 911, 'message' => 'Wrong request!']);
                return false;
            }
            $class = new Functions();
            $category = htmlentities(trim(filter_input(INPUT_POST, 'cat_id')));
            $test_name = htmlentities(trim(filter_input(INPUT_POST, 'test_name')));
            $test_cost = htmlentities(trim(filter_input(INPUT_POST, 'test_cost')));
            if(!($category && $test_cost && $test_name)){
                echo json_encode(['status' => 210, 'message' => 'All fields Marked * are REQUIRED']);    
            return false;
            }
              $class->store(
                'sub_labtest_tbl',
                ['name','labtest_id','cost','created_by'],
                [$test_name,$category,$test_cost, $user]
             );
              echo json_encode(['status' => 200, 'message' => 'Test Saved Successfully']);    
            return false;
        } catch (Exception $e) {
            echo json_encode(['status' => 210, 'message' => 'An error occurred! ','response'=>$e->getMessage()]);
        }
    break;
    case 'stf2arutv':

    try {
        //code...
        $class = new Functions();
        $category = htmlentities(trim(filter_input(INPUT_POST, 'mcategory')));
        $test_name = htmlentities(trim(filter_input(INPUT_POST, 'mfull_name')));
        $test_cost = htmlentities(trim(filter_input(INPUT_POST, 'mtest_cost')));
        $id = htmlentities(trim(filter_input(INPUT_POST, 'mtest_id')));
        $class->updateRecord(
            'id',
            ['name','labtest_id','cost'],
            [$test_name,$category,$test_cost, $id],'sub_labtest_tbl'
         );
         echo json_encode(['status' => 200, 'message' => 'Test Updated Successfully']);    
            return false;
            
    } catch (\Throwable $th) {
        echo json_encode(['status' => 210, 'message' => 'An error occurred! ','response'=>$e->getMessage()]);
    }
        break;
}