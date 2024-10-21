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
            $description = htmlentities(trim(filter_input(INPUT_POST, 'product')), ENT_QUOTES);
            $unitMeasured = htmlentities(trim(filter_input(INPUT_POST, 'unitMeasured')), ENT_QUOTES);
            $category = htmlentities(trim(filter_input(INPUT_POST, 'categoryId')), ENT_QUOTES);
            if (!($description && $unitMeasured && $category)) {
                echo json_encode(['status' => 210, 'message' => 'Unit, Name and Category are REQUIRED']);
                return false;
            }
            $class->store(
                'products',
                ['name', 'inventory_unit_id', 'inventory_category_id', 'user_id'],
                [$description, $unitMeasured, $category, $user]
            );
            echo json_encode(['status' => 200, 'message' => 'Product Saved Successfully']);
            return false;
        } catch (Exception $e) {
            echo json_encode(['status' => 210, 'message' => 'An error occurred! ', 'response' => $e->getMessage()]);
        }
    case 'sf2A':
        //update the category of test
        try {
            $class = new Functions();
            $description = htmlentities(trim(filter_input(INPUT_POST, 'product')), ENT_QUOTES);
            $unitMeasured = htmlentities(trim(filter_input(INPUT_POST, 'unitMeasured')), ENT_QUOTES);
            $category = htmlentities(trim(filter_input(INPUT_POST, 'category')), ENT_QUOTES);
            $id = htmlentities(trim(filter_input(INPUT_POST, 'product_id')), ENT_QUOTES);
            if (!($description && $unitMeasured && $category)) {
                echo json_encode(['status' => 210, 'message' => 'Unit, Name and Category are REQUIRED']);
                return false;
            }

            $class->updateRecord(
                'id',
                ['name', 'inventory_unit_id', 'inventory_category_id'],
                [$description, $unitMeasured, $category, $id],
                'products'
            );
            echo json_encode(['status' => 200, 'message' => 'Product Updated Successfully']);
            return false;
        } catch (Exception $e) {
            echo json_encode(['status' => 210, 'message' => 'An error occurred! ', 'response' => $e->getMessage()]);
        }
        break;

    default:

        break;
}