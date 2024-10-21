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
            if ($_SESSION['token'] !== $_POST['token']) {
                echo json_encode(['status' => 911, 'message' => 'Wrong request!']);
                return false;
            }
            $class = new Functions();
            $description = htmlentities(trim(filter_input(INPUT_POST, 'krd_name')), ENT_QUOTES);
            if (!$description) {
                echo json_encode(['status' => 210, 'message' => 'Test Category Name is REQUIRED']);
                return false;
            }
            $class->store(
                'inventory_categories',
                ['name', 'created_by'],
                [$description, $user]
            );
            echo json_encode(['status' => 200, 'message' => 'Category Saved Successfully']);
            return false;
        } catch (Exception $e) {
            echo json_encode(['status' => 210, 'message' => 'An error occurred! ', 'response' => $e->getMessage()]);
        }
    case 'sf2A':
        //update the category of test
        try {
            $class = new Functions();
            $description = htmlentities(trim(filter_input(INPUT_POST, 'expense_cat')), ENT_QUOTES);
            $id = htmlentities(trim(filter_input(INPUT_POST, 'expense_id')), ENT_QUOTES);
            if (!$description) {
                echo json_encode(['status' => 210, 'message' => 'Expense Category Name is REQUIRED']);
                return false;
            }

            $class->updateRecord(
                'id',
                ['name'],
                [$description, $id],
                'inventory_categories'
            );
            echo json_encode(['status' => 200, 'message' => 'Category Updated Successfully']);
            return false;
        } catch (Exception $e) {
            echo json_encode(['status' => 210, 'message' => 'An error occurred! ', 'response' => $e->getMessage()]);
        }
        break;

    default:

        break;
}