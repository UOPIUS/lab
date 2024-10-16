<?php include '../functions/Functions.php'; 
$user = $_SESSION['user_id'];
if (!$user) {
    $response = ['status' => 505, 'message' => 'You Have No Permission For this Operation'];
    echo json_encode($response);
    return false;
}
if($_SESSION['token'] !== $_POST['token']) {
    echo json_encode(['status' => 911, 'message' => 'Wrong request!']);
    return false;
}
try{
    $str = filter_input(INPUT_POST,'cat_id');
    $cat = filter_var($str, FILTER_SANITIZE_STRING);
    $response = [];
    $class = new Functions();
    $tests = $class->fetchAll('sub_labtest_tbl',' WHERE labtest_id = '.$cat);
   foreach($tests as $t){
       $response[] = array('tid'=>$t->id,'name'=>$t->name,'cost'=>$t->cost);
   }
    echo (json_encode(array('data'=>$response)));
  }
  catch(Exception $e){
    echo $e->getMessage();
  }