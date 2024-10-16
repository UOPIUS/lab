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
            $category = htmlentities(trim(filter_input(INPUT_POST, 'category')));
            $template_body = htmlentities(trim(filter_input(INPUT_POST, 'template_body')));
            $templateName = htmlentities(trim(filter_input(INPUT_POST, 'templateName')));
            if(!($template_body && $category &&$templateName)){
                echo json_encode(['status' => 210, 'message' => 'All fields Marked * are REQUIRED']);    
            return false;
            }
              $class->store(
                'test_templates',
                ['category_id','body','created_by','template_name'],
                [$category,$template_body, $user,$templateName]
             );
              echo json_encode(['status' => 200, 'message' => 'Test Template Saved Successfully']);    
            return false;
        } catch (Exception $e) {
            echo json_encode(['status' => 210, 'message' => 'An error occurred! ','response'=>$e->getMessage()]);
        }
    break;
    case 't2':
        try {
            
            $class = new Functions();
            $category = htmlentities(trim(filter_input(INPUT_POST, 'mcategory')));
            $template_body = htmlentities(trim(filter_input(INPUT_POST, 'mtemplateBody')));
            $template_name = htmlentities(trim(filter_input(INPUT_POST, 'mtemplateName')));
            $id = htmlentities(trim(filter_input(INPUT_POST, 'id')));
            if(!($template_body && $category &&$template_name)){
                echo json_encode(['status' => 210, 'message' => 'All fields Marked * are REQUIRED']);    
            return false;
            }
              $class->updateRecord(
                'id',
                ['category_id','body','template_name'],
                [$category,$template_body,$template_name, $id],'test_templates'
             );
              echo json_encode(['status' => 200, 'message' => 'Test Template Updated Successfully']);    
            return false;
        } catch (Exception $e) {
            echo json_encode(['status' => 210, 'message' => 'An error occurred! ','response'=>$e->getMessage()]);
        }
    
        
    break;
}