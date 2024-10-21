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
            $quantity = htmlentities(trim(filter_input(INPUT_POST, 'quantity')), ENT_QUOTES);
            if (!($description && is_numeric($quantity))) {
                echo json_encode(['status' => 210, 'message' => 'Test Unit Name and Quantity is REQUIRED']);
                return false;
            }
            $class->store(
                'inventory_units',
                ['name', 'quantity', 'user_id'],
                [$description, $quantity, $user]
            );
            echo json_encode(['status' => 200, 'message' => 'Unit Saved Successfully']);
            return false;
        } catch (Exception $e) {
            echo json_encode(['status' => 210, 'message' => 'An error occurred! ', 'response' => $e->getMessage()]);
        }
    case 'sf2A':
        //update the category of test
        try {
            $class = new Functions();
            $description = htmlentities(trim(filter_input(INPUT_POST, 'expense_cat')), ENT_QUOTES);
            $quantity = htmlentities(trim(filter_input(INPUT_POST, 'quantity')), ENT_QUOTES);
            $id = htmlentities(trim(filter_input(INPUT_POST, 'expense_id')), ENT_QUOTES);
            if (!($description && is_numeric($quantity))) {
                echo json_encode(['status' => 210, 'message' => 'Unit Name and Quantity is REQUIRED']);
                return false;
            }

            $class->updateRecord(
                'id',
                ['name', 'quantity'],
                [$description, $quantity, $id],
                'inventory_units'
            );
            echo json_encode(['status' => 200, 'message' => 'Unit Updated Successfully']);
            return false;
        } catch (Exception $e) {
            echo json_encode(['status' => 210, 'message' => 'An error occurred! ', 'response' => $e->getMessage()]);
        }
        break;

    default:

        break;
}