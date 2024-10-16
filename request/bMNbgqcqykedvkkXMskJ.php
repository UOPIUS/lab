<?php include '../functions/Functions.php'; 
date_default_timezone_set("Africa/Lagos");
$user = $_SESSION['user_id'];
if ('106' != $_SESSION['role_id']) {
    $response = ['status' => 505, 'message' => 'You Have No Permission For this Operation...'];
    echo json_encode($response);
    return false;
}
if($_SESSION['token'] !== $_POST['token']) {
    echo json_encode(['status' => 911, 'message' => 'Wrong request!']);
    return false;
}
$date = date("Y-m-d H:m:s");
switch ($_POST['sst']) {
    case 's2f':
        try {
            
            $class = new Functions();

            $phone = trim(htmlentities(trim(filter_input(INPUT_POST, 'phone')),ENT_QUOTES));
            $fname = trim(htmlentities(trim(filter_input(INPUT_POST, 'fname')),ENT_QUOTES));
            $address = trim(htmlentities(trim(filter_input(INPUT_POST, 'address')),ENT_QUOTES));
            $email = filter_input(INPUT_POST, 'username');
            if(!($fname && $phone && $address && $email)){
                echo json_encode(['status' => 210, 'message' => 'Fields marked * ARE REQUIRED']);    
            return false;
            }
            //$uniqueValues = $class->referral();
            $password ='12345';
            $password = password_hash($password, PASSWORD_DEFAULT);
            $role_id = htmlentities($_POST['role_id']);
    
            /* Check if this phone number already exist */
            $record = $class->fetchColumn('users_tbl', 'full_name', 'phone', $phone);
            if($record){
                    echo json_encode(['status' => 51, 'message' => 'Phone Already Exist']);
                    return false;
            }
        
            // validate email 
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $record = $class->fetchColumn('users_tbl', 'password', 'username', $email);
                    if ($record === $email) {
                        $response = ['status' => 52, 'message' => 'Email already exist'];
                        echo json_encode($response);
                        return false;
                    }
                } else {
                    echo json_encode(['status' => 53, 'message' => 'Email is invalid']);
                    return false;
                }
            
        
            
            $id = $class->randomString(25);
        
            
            //Staff table
            $fields = ['user_id','full_name','phone','username','password','role_id','created_by','uflag','src'];
            $values = [$id,$fname,$phone,$email,$password,'107',$user,'R',$address];
        
            $class->store('users_tbl', $fields, $values);
            
              echo json_encode(['status' => 200, 'message' => 'Referral Created Successfully!']);    
            return false;
        } catch (Exception $e) {
            echo json_encode(['status' => 210, 'message' => 'An error occurred! ','response'=>$e->getMessage()]);
        }
    break;
    default:
    if ($_SESSION['role_id'] != 106) {
        $response = ['status' => 505, 'message' => 'Operation Failed'];
        echo json_encode($response);
        return false;
    }


        break;
}