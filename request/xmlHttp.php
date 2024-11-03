<?php include '../functions/Functions.php';
$user = $_SESSION['user_id'];
if (!$user) {
    $response = ['status' => false, 'message' => 'You Have No Permission For this Operation'];
    echo json_encode($response);
    return false;
}
$zero = 0;
$credit = "credit";
$debit = "debit";
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
                    $product = $value["product"];
                    $quantity = $value["quantity"];
                    $cost = $value["cost"];
                    $previous = 0;
                    $current = $quantity;

                    //get the current balance for this product
                    $query = $db->connect()->prepare("SELECT balance FROM stocks WHERE product_id = :product_id");
                    $query->bindParam(":product_id", $product);
                    $query->execute();
                    $balance = $query->fetchColumn();
                    if (!$balance) {
                        $id = $db->generateRandomString(12);
                        //create product in the stocks table
                        $query = $db->connect()->prepare("INSERT INTO stocks(id,product_id, balance, user_id) 
                        VALUES (:id, :product_id, :quantity, :user)");
                        $query->bindParam(":id", $id);
                        $query->bindParam(":quantity", $current);
                        $query->bindParam(":product_id", $product);
                        $query->bindParam(":user", $user);
                        $query->execute();
                        $balance = 0;
                    } else {
                        //update the inventory table
                        $previous = $balance;
                        $current = $previous + $quantity;
                        $query = $db->connect()->prepare("UPDATE stocks SET balance = :quantity WHERE product_id = :product_id");
                        $query->bindParam(":quantity", $current);
                        $query->bindParam(":product_id", $product);
                        $query->execute();
                    }
                    $type = "credit";
                    //log the transaction
                    $query = $db->connect()->prepare("INSERT INTO inventory_transactions(product_id, quantity,balance_before,balance_after, cost, type,user_id)
                     VALUES (:product_id, :quantity,:balance_before,:balance_after, :cost, :type, :user)");
                    $query->bindParam(":product_id", $product);
                    $query->bindParam(":quantity", $quantity);
                    $query->bindParam(":balance_before", $previous);
                    $query->bindParam(":balance_after", $current);
                    $query->bindParam(":cost", $cost);
                    $query->bindParam(":type", $type);
                    $query->bindParam(":user", $user);

                    $query->execute();
                } //end of foreach
                $db->connect()->commit();

                echo json_encode(["status" => true, "message" => "Success"]);
            } elseif ($request[0]["HTTP_REQUEST_ACTION"] == 'HTTP_REQUEST_ASSIGN_STOCK') {
                $staff = $request[0]["STAFF"];
                $errorBag = [];
                if (!$staff) {
                    echo json_encode(["status" => false, "message" => "You need to select a staff", "errors" => $errorBag]);
                    exit();
                }
                $db->connect()->beginTransaction();
                //add stock to store
                foreach ($request as $key => $value) {
                    if ($key == 0)
                        continue;
                    $product = $value["product"];
                    $quantity = $value["quantity"];
                    $previous = 0;
                    $current = $quantity;

                    //get the current balance for this product from the main store
                    $query = $db->connect()->prepare("SELECT s.balance,s.id,p.name as productName, i.quantity AS unit
                        FROM stocks s JOIN products p ON s.product_id=p.id JOIN inventory_units i ON p.inventory_unit_id = i.id WHERE product_id = :product");
                    $query->bindParam(":product", $product);
                    $query->execute();
                    $mainStock = $query->fetch(PDO::FETCH_OBJ);

                    $productName = $mainStock->productName;
                    $unit = $mainStock->unit * $quantity;
                    //insufficient stock notice
                    if($mainStock->balance > $quantity){
                        //get the current balance for this product and this staff
                        $query = $db->connect()->prepare("SELECT balance,unit FROM user_stocks WHERE owner_id = :owner AND product_id = :product");
                        $query->bindParam(":owner", $staff);
                        $query->bindParam(":product", $product);
                        $query->execute();
                        $ownerStock = $query->fetch(PDO::FETCH_OBJ);
                        
                        //staff currently does not have a store for this product
                        if (!$ownerStock) {

                            $id = $db->generateRandomString(12);
                            //create product in the stocks table
                            $query = $db->connect()->prepare("INSERT INTO user_stocks(id,product_id,owner_id, balance, user_id, unit) 
                            VALUES (:id, :product, :owner, :quantity, :user, :unit)");
                            $query->bindParam(":id", $id);
                            $query->bindParam(":quantity", $current);
                            $query->bindParam(":product", $product);
                            $query->bindParam(":owner", $staff);
                            $query->bindParam(":user", $user);
                            $query->bindParam(":unit", $unit);
                            $query->execute();
                            $balance = 0;
                        } else {
                            //staff already have a store for this product, update the inventory table
                            $previous = $ownerStock->balance;
                            $current = $previous + $quantity;
                            $unitMeasured = $ownerStock->unit + ($unit * $quantity);
                            $query = $db->connect()->prepare("UPDATE user_stocks SET balance = :quantity, unit = :unit 
                                WHERE owner_id = :owner AND product_id = :product");
                            $query->bindParam(":quantity", $current);
                            $query->bindParam(":product", $product);
                            $query->bindParam(":owner", $staff);
                            $query->bindParam(":unit", $unitMeasured);
                            $query->execute();
                        }
                        //deduct the equivalent from the main store
                        $mainStockPrevious = $mainStock->balance;
                        $mainStockCurrent = $mainStockPrevious - $quantity;
                        $query = $db->connect()->prepare("UPDATE stocks SET balance = :quantity WHERE id = :id");
                        $query->bindParam(":quantity", $mainStockCurrent);
                        $query->bindParam(":id", $mainStock->id);
                        $query->execute();

                        $type = "credit";
                        $unitMeasured = ($unit * $quantity);

                        //log the transaction for the main store
                        $query = $db->connect()->prepare("INSERT INTO 
                            inventory_transactions(product_id, quantity,balance_before,balance_after, cost, type,user_id, unit)
                         VALUES (:product_id, :quantity,:balance_before,:balance_after, :cost, :type, :user, :unit)");
                        $query->bindParam(":product_id", $product);
                        $query->bindParam(":quantity", $quantity);
                        $query->bindParam(":balance_before", $mainStockPrevious);
                        $query->bindParam(":balance_after", $mainStockCurrent);
                        $query->bindParam(":cost", $zero);
                        $query->bindParam(":type", $debit);
                        $query->bindParam(":user", $user);
                        $query->bindParam(":unit", $unitMeasured);
                        $query->execute();


                        //log transaction for staff store
                        $query = $db->connect()->prepare("INSERT INTO 
                            inventory_transactions(product_id, quantity,balance_before,balance_after, cost, type,user_id,owner_id, unit)
                         VALUES (:product_id, :quantity,:balance_before,:balance_after, :cost, :type, :user, :owner, :unit)");
                        $query->bindParam(":product_id", $product);
                        $query->bindParam(":quantity", $quantity);
                        $query->bindParam(":balance_before", $previous);
                        $query->bindParam(":balance_after", $current);
                        $query->bindParam(":cost", $zero);
                        $query->bindParam(":type", $credit);
                        $query->bindParam(":user", $user);
                        $query->bindParam(":owner", $staff);
                        $query->bindParam(":unit", $unitMeasured);
                        $query->execute();

                    }
                    else {
                        $errorBag[] = "Insufficient Stock Balance for $productName, Current Stock Balance: {$mainStock->balance}, Quantity to assign: $quantity";
                    }

                } //end of foreach
                $db->connect()->commit();
                echo json_encode(["status" => true, "message" => "Success", "errors" => $errorBag]);
            }

        } catch (Exception $e) {
            echo json_encode(['status' => false, 'message' => 'An error occurred! ' . $e->getMessage()]);
        }
}
