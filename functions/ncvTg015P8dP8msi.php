<?php include_once "Database.php";

$db = new Database();
$connect = $db->connect();

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'GET') {
    $data = array(
        ':fname'   => "%" . $_GET['fname'] . "%",
        ':phone'   => "%" . $_GET['phone'] . "%",
        ':lname'     => "%" . $_GET['lname'] . "%",
        ':ref'     => "%" . $_GET['ref'] . "%"
        //':gender'    => "%" . $_GET['gender'] . "%"
    );
    $query = "SELECT c.fname,c.lname,c.oname,c.ref,c.phone,c.gender,DATE_FORMAT(c.created_at,'%d/%b/%Y') AS created_at FROM clients_tbl c
   WHERE c.fname LIKE :fname AND c.lname LIKE :lname AND c.phone LIKE :phone AND c.ref LIKE :ref ORDER BY c.created_at DESC LIMIT 10";

    $statement = $connect->prepare($query);
    $statement->execute($data);
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    foreach ($result as $row) {
        $x = $db->eccrpt($row['ref'],'e');
        $output[] = array(
            'id'    => $x,
            'ref'  => $row['ref'],
            'fname'   => $row['fname'],
            'lname'   => $row['lname'],
            'gender'    => $row['gender'],
            'phone'    => $row['phone'],
            'created_at'    => $row['created_at'],
        );
    }
    header("Content-Type: application/json");
    $result = json_encode($output);
    echo ($result);
    
}

if ($method == "POST") {
    $data = array(
        ':first_name'  => $_POST['first_name'],
        ':last_name'  => $_POST["last_name"],
        ':age'    => $_POST["age"],
        ':gender'   => $_POST["gender"]
    );

    $query = "INSERT INTO sample_data (first_name, last_name, age, gender) VALUES (:first_name, :last_name, :age, :gender)";
    $statement = $connect->prepare($query);
    $statement->execute($data);
}

if ($method == 'PUT') {
    parse_str(file_get_contents("php://input"), $_PUT);
    $data = array(
        ':id'   => $_PUT['id'],
        ':first_name' => $_PUT['first_name'],
        ':last_name' => $_PUT['last_name'],
        ':age'   => $_PUT['age'],
        ':gender'  => $_PUT['gender']
    );
    $query = "
 UPDATE sample_data 
 SET first_name = :first_name, 
 last_name = :last_name, 
 age = :age, 
 gender = :gender 
 WHERE id = :id
 ";
    $statement = $connect->prepare($query);
    $statement->execute($data);
}

if ($method == "DELETE") {
    parse_str(file_get_contents("php://input"), $_DELETE);
    $query = "DELETE FROM sample_data WHERE id = '" . $_DELETE["id"] . "'";
    $statement = $connect->prepare($query);
    $statement->execute();
}
