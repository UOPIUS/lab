<?php include_once "Database.php";
ini_set('memory_limit', '2048M');
try {
    $db = new Database();
    $cnx = $db->connect();
    $search = preg_replace('#[^a-zA-Z0-9/]#s','',($_GET["search"]["value"]));
    $limit = preg_replace('#[^0-9/]#s','',$_GET['length']);
    
    $dt = array(
        ':fname'   => "%" . $search . "%",
        ':phone'   => "%" . $search . "%",
        ':lname'     => "%" . $search . "%",
        ':ref'     => "%" . $search . "%"
    );
    $condition = " AND (c.fname LIKE :fname) OR (c.lname LIKE :lname) OR (c.phone LIKE :phone) OR (c.ref LIKE :ref) ";
   
   $query = "SELECT tt.test_id,tt.tranx_id, tt.client_id,c.fname,c.lname,slb.name test,c.phone, DATE_FORMAT(tt.created_at, '%d/%m/%Y') AS created_at, tt.tranx_id 
   FROM tests_taken tt JOIN clients_tbl c ON tt.client_id = c.ref JOIN sub_labtest_tbl slb ON tt.test_id = slb.id 
   WHERE 1 = 1 $condition
   ORDER BY tt.created_at DESC LIMIT 10";
   
   $rows = $cnx->prepare($query);
   $rows->execute($dt);

   //$rows = $cnx->query($query);
   $lines = $rows->fetchAll(PDO::FETCH_OBJ);
   
   $i = 1;
   $dataRows = [];
   foreach($lines as $line){
       $customer = $db->eccrpt($line->client_id,'e');
       $tnx = $db->eccrpt($line->tranx_id,'e');
       $dataRows[] = [
           "customer" => $customer,
           "tnx" => $tnx,
           "client_id" => $line->client_id,
           "fname" => $line->fname,
           "lname" => $line->lname,
           "phone" => $line->phone,
           "tests" => $line->test,
           "created_at" => $line->created_at
        ];
        $i++;
   }

     $results = array(
    //   "sEcho" => 1,
    //     "iTotalRecords" => count($dataRows),
    //     "iTotalDisplayRecords" => count($dataRows),
     "draw"=> intval( $_REQUEST['draw'] ),
  "recordsTotal" => count($dataRows),
  "recordsFiltered" => count($dataRows),
        "data"=>$dataRows
        );
    echo json_encode($results);
}
catch(\Throwable $th){
     echo json_encode(["status"=>false,"message"=>$th->getMessage()]);
    return;
}