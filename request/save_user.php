<?php include '../functions/Functions.php'; 
$user = $_SESSION['user_account'];
if ($user) {
    $response = ['status' => 505, 'message' => 'You Have No Permission For this Operation'];
    echo json_encode($response);
    return false;
}
switch ($_POST['tt']) {
    case 't1':
        
try {
    $class = new Functions();
        /**
         * 
         * Validate and sanitize inputs
         */
        $full_name = filter_input(INPUT_POST,'full_name');
        $full_name = filter_var($full_name, FILTER_SANITIZE_STRING);
    
        $phone = filter_input(INPUT_POST,'phone');
        $phone = filter_var($phone, FILTER_SANITIZE_STRING);
        
    
        $password = filter_input(INPUT_POST,'password') ?? '12345';
        $password = password_hash($password, PASSWORD_DEFAULT);
        $role_id = htmlentities($_POST['role_id']);

        if(!($full_name && $phone && $password && $role_id) ){
            echo json_encode(['status' => 49, 'message' => 'Fields marked * are required']);
            return false;
        }
        /* Check if this phone number already exist */
        $record = $class->fetchColumn('users_tbl', 'full_name', 'phone', $phone);
        if($record){
                echo json_encode(['status' => 51, 'message' => 'Phone Already Exist']);
                return false;
        }
    
        // validate email
        $email = filter_input(INPUT_POST, 'username') ?? "#";
        if (strlen($email) > 1) {
            //validate 
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
        }
    
        
        $id = $class->randomString(25);
    
        
        //Staff table
        $fields = ['user_id','full_name','phone','username','password','role_id','created_by'];
        $values = [$id,$full_name,$phone,$email,$password,$role_id,$user];
    
        $class->store('users_tbl', $fields, $values);
    
        echo json_encode(['status'=>200, 'message'=>'User Added Successfully! . . .']);
    } 
    catch(Exception $e){
        echo json_encode(['status'=>210, 'message'=>'An Error Occurred! ','response'=>$e->getMessage()]);
    }
    
        break;
    
    case 't2':
    try{
         /**
         * 
         * Validate and sanitize inputs
         */
        $id = htmlentities(filter_input(INPUT_POST,'id'));
        $fname = filter_input(INPUT_POST,'fname');
        $fname = filter_var($fname, FILTER_SANITIZE_STRING);
    
        $lname = filter_input(INPUT_POST,'lname');
        $lname = filter_var($lname, FILTER_SANITIZE_STRING);
    
        $phone = filter_input(INPUT_POST,'phone');
        $phone = filter_var($phone, FILTER_SANITIZE_STRING);
        $location = htmlentities(filter_input(INPUT_POST,'l'));
        $dt_employed = htmlentities(filter_input(INPUT_POST,'dt_employed'));
        $address = filter_input(INPUT_POST,'address');
        $address = filter_var($address, FILTER_SANITIZE_STRING);
    
        $dept = filter_input(INPUT_POST,'dept');
    
        $branch = htmlentities($_POST['branch']);
        $email = filter_input(INPUT_POST,'email');        
    
        if( !($address && $lname && $fname && $location && $dt_employed && $phone &&
         $email && $dept && $branch)){
            $response = ['status' => 49, 'message' => 'Fields marked * are required'];
            echo json_encode($response);
            return false;
        }
        $class = new Functions;
        /* Check if this phone number already exist */
        $record = $class->fetch('staff_info', " WHERE (phone = '$phone')  AND (id <> '$id')");
        if($phone){
            if ($record) {
                $response = ['status' => 51, 'message' => 'Phone Already Exist'];
                echo json_encode($response);
                return false;
            }
        }
        else {
            $response = ['status' => 56, 'message' => 'Phone Cannot Be Empty'];
        }
        
            //validate 
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $record = $class->fetch('userlogin', " WHERE (email = '$email') AND (user_id <> '$id')");
                if ($record) {
                    $response = ['status' => 52, 'message' => 'Email already exist, Choose another'];
                    echo json_encode($response);
                    return false;
                }
            } else {
                //Email is invalid
                $response = ['status' => 53, 'message' => 'Email is invalid, Choose another'];
                echo json_encode($response);
                return false;
            }
        
    
        if (empty($_POST['role_id'])) {
            $response = ['status' => 708, 'message' => 'Role cannot be empty'];
            echo json_encode($response);
            return false;
        }
        $role = $_POST['role_id'];
        
        //Staff table
        $fields = ['fname','lname','phone','address','branch_id','department_id','state_id','date_employed','updated_at'];
        $values = [$fname,$lname,$phone,$address,$branch,$dept,$location,date('Y-m-d H:m:s'),$dt_employed,$id];
    
        //Login table
        $fieldsA = ['email','role_id'];
        $valuesA = [$email,$role,$id];
    
        $class->connect()->beginTransaction();
    
        $class->updateRecord('id', $fields, $values,'staff_info');
        $class->updateRecord('user_id',$fieldsA,$valuesA,'userlogin');
        
        $class->connect()->commit();
    
        echo json_encode(['status'=>200, 'message'=>'Staff Updated Successfully! . . .']);
    } 
    catch(Exception $e){
        echo json_encode(['status'=>210, 'message'=>'An Error Occurred! ','response'=>$e->getMessage().' '.__LINE__]);
    }
        break;
        
        default:
    try {
        //code...
        $password = password_hash($_POST['new_lock'],PASSWORD_DEFAULT);
        if($_POST['new_lock'] !== $_POST['confirm_lock']){
            echo json_encode(['status'=>219, 'message'=>'Password Typed and Confirm does not Match']);
            return false;
        }
        
        $class = new Functions;

        $formerLock = $class->fetchColumn("users_tbl","password","user_id",$_SESSION['user_id']);
        $validateLock = password_verify($_POST['former_lock'],$formerLock);
        if(!$validateLock){
            //password is wrong
            echo json_encode(['status'=>211, 'message'=>'Your Old Password is Wrong!']);
            return false;
        }
        $class->updateRecord('user_id', ['password'], [$password,$_SESSION['user_id']],'users_tbl');
        $class = null;
        echo json_encode(['status'=>200, 'message'=>'Password Changed Successfully']);
        return false;
    } catch (\Throwable $e) {
        echo json_encode(['status'=>212, 'message'=>'An Error Occurred! ','response'=>$e->getMessage().' '.__LINE__]);

    }
}