<?php include '../functions/Functions.php';
$user = $_SESSION['user_id'];
if (!$user) {
    $response = ['status' => false, 'message' => 'You Have No Permission For this Operation'];
    echo json_encode($response);
    return false;
}
switch ($_POST['HTTP_REQUEST_ACTION']) {
    case 'HTTP_FETCH_PRODUCTS':
        try {
            $id = htmlentities(trim(filter_input(INPUT_POST, 's')));
            $flag = htmlentities(trim(filter_input(INPUT_POST, 'flag')));
            $response = [];
            $db = new Functions();
            $query = $db->connect()->prepare("SELECT p.id id, p.name name, u.name unit, u.quantity measure FROM products p JOIN inventory_units u
            ON p.inventory_unit_id = u.id WHERE p.inventory_category_id = :id");
            $query->bindParam(":id", $id);
            $query->execute();
            $list = $query->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(array("status" => true, "data" => $list));
            $db = null;
        } catch (\Throwable $th) {
            echo json_encode(["status" => false, "message" => "Failed to fetch Records " . $th->getMessage()]);
        }
        break;
    default:
        try {
            $db = new Functions();
            $input = file_get_contents('php://input');
            $request = json_decode($input, true);
            if ($request[0]["HTTP_REQUEST_ACTION"] == 'HTTP_REQUEST_ADD_STOCK') {
                $db->connect()->beginTransaction();
                //add stock to store
                foreach ($request as $key => $value) {
                    if ($key == 0)
                        continue;
                    $product = $value->product;
                    $quantity = $value->quantity;
                    $cost = $value->cost;

                    //get the current balance for this product
                    $query = $db->connect()->prepare("SELECT quantity FROM stocks WHERE product_id = :product_id");
                    $query->bindParam(":product_id", $product);
                    $query->execute();
                    $balance = $query->fetchColumn() ?? 0;
                    if ($balance > 0) {
                        //create product in the stocks table
                        $query = $db->connect()->prepare("INSERT INTO stocks product_id, quantity (product_id, quantity VALUES (:product_id, :quantity");
                        $query->bindParam(":quantity", $quantity);
                        $query->bindParam(":product_id", $product);
                        $query->execute();
                    } else {
                        //update the inventory table
                        $new_balance = $balance + $quantity;
                        $query = $db->connect()->prepare("UPDATE stocks SET quantity = :quantity WHERE product_id = :product_id");
                        $query->bindParam(":quantity", $new_balance);
                        $query->bindParam(":product_id", $product);
                        $query->execute();
                    }

                    //log the transaction
                    $query = $db->connect()->prepare("INSERT INTO inventory_transactions (product_id, quantity,balance_before,balance_after, cost, type) VALUES (:product_id, :quantity, :cost, 'add')");
                    $query->bindParam(":product_id", $product);
                    $query->bindParam(":quantity", $quantity);
                    $query->bindParam(":balance_before", $balance);
                    $query->bindParam(":balance_after", $new_balance);
                    $query->bindParam(":cost", $cost);

                    $query->execute();
                }
                $db->connect()->commit();

                echo json_encode(["status" => true, "message" => "Success"]);
            }

        } catch (Exception $e) {
            echo json_encode(['status' => false, 'message' => 'An error occurred! ' . $e->getMessage()]);
        }
}