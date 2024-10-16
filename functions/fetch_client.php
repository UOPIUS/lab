<?php include_once "Database.php";
try {
    $db = new Database();
    $cnx = $db->connect();
    $id = trim(filter_input(INPUT_POST,'href'));
    $query = $cnx->prepare("SELECT CONCAT(fname,' ',lname,' ',oname) AS name, phone,dob,gender FROM clients_tbl WHERE ref = ?");
    $query->execute([$id]);
    $row = $query->fetch(PDO::FETCH_OBJ);
    echo json_encode(["status"=>true,"name"=>$row->name,"phone"=>$row->phone,"gender"=>$row->gender,"age"=>$row->dob]);
    return;
}
catch(\Throwable $th){
     echo json_encode(["status"=>false,"message"=>$th->getMessage()]);
    return;
}
