<?php include '../functions/Functions.php'; 
$user = $_SESSION['user_id'];
if (!($user && $_SESSION['role_id'] == 101)) {
    $response = ['status' => 505, 'message' => 'You Have No Permission For this Operation'];
    echo json_encode($response);
    return false;
}
if($_SESSION['token'] !== $_POST['token']) {
    echo json_encode(['status' => 911, 'message' => 'Wrong request!']);
    return false;
}
switch ($_POST['tt']) {
    case 't1':
        try {
            $class = new Functions();
            $address = htmlentities(trim(filter_input(INPUT_POST, 'address')),ENT_QUOTES);
            $name = htmlentities(trim(filter_input(INPUT_POST, 'name')),ENT_QUOTES);
            $percent = htmlentities(trim(filter_input(INPUT_POST, 'percent')),ENT_QUOTES);
            $phones = htmlentities(trim(filter_input(INPUT_POST, 'phones')),ENT_QUOTES);
            $acronymn = htmlentities(trim(filter_input(INPUT_POST, 'acronymn')),ENT_QUOTES);

            if(!($acronymn && $address && $name)){
                echo json_encode(['status' => 210, 'message' => 'Fields Marked * Are REQUIRED']);    
            return false;
            }
              $query=$class->connect()->prepare("UPDATE settings SET name = ?, acronymn = ?, address = ?,referral = ?,contact_phone = ? WHERE unique_id > 1");
              $query->execute([$name,$acronymn,$address,$percent,$phones]);
              if($query) echo json_encode(['status' => 200, 'message' => 'Settings Saved Successfully']);    
            return false;
        } catch (Exception $e) {
            echo json_encode(['status' => 210, 'message' => 'An error occurred! ','response'=>$e->getMessage()]);
        }
    
    default:
        
        break;
}