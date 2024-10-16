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
            $description = htmlentities(trim(filter_input(INPUT_POST, 'krd_name')),ENT_QUOTES);
            if(!$description){
                echo json_encode(['status' => 210, 'message' => 'Specimen Name is REQUIRED']);    
            return false;
            }
              $class->store(
                'specimens',
                ['name', 'created_by'],
                [$description, $user]
             );
              echo json_encode(['status' => 200, 'message' => 'Specimen Saved Successfully']);    
            return false;
        } catch (Exception $e) {
            echo json_encode(['status' => 210, 'message' => 'An error occurred! ','response'=>$e->getMessage()]);
        }
    case 'sf2A':
        //update the category of test
            try {
                $class = new Functions();
                $description = htmlentities(trim(filter_input(INPUT_POST, 'mspecimen')),ENT_QUOTES);
                $id = htmlentities(trim(filter_input(INPUT_POST, 'specimen_id')),ENT_QUOTES);
                if(!$description){
                    echo json_encode(['status' => 210, 'message' => 'Test Specimen Name is REQUIRED']);    
                return false;
                }
                
                  $class->updateRecord('id',
                    ['name'],
                    [$description, $id],'specimens'
                 );
                  echo json_encode(['status' => 200, 'message' => 'Specimen Updated Successfully']);    
                return false;
            } catch (Exception $e) {
                echo json_encode(['status' => 210, 'message' => 'An error occurred! ','response'=>$e->getMessage()]);
            }
    break;
    
    default:
        
        break;
}