<?php include_once 'Functions.php'; 
$class = new Functions();
$clients = $class->rawQuery("
   SELECT CONCAT(c.fname,' ',c.lname,' ',c.oname) AS name,ref,phone,gender,blood_group,dob,rhesus,address,
   status,DATE_FORMAT(c.created_at, '%d-%m-%Y') AS dt FROM clients_tbl c
   ORDER BY c.created_at DESC LIMIT 57
");

$records = 0;

$fList = [];
$i = 1;
foreach($clients as $client){
    
    $fList[] = [
      "ASDEFE",
      $client->name,
      $client->phone,
      $client->gender,
      $client->blood_group,
      $client->dt
    ];
}


$data = [
  'draw' => 1,
  'recordsTotal' => $i-1,
  'recordsFiltered' => $i-1,
  'data' => $fList
];
echo json_encode($data);
?>