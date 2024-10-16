<?php include '../functions/Functions.php'; 
$user = $_SESSION['user_id'];
if (!($user && $_SESSION['role_id'])) {
    $response = ['status' => 505, 'message' => 'You Have No Permission For this Operation'];
    echo json_encode($response);
    return false;
}
if($_SESSION['token'] !== $_POST['token']) {
    echo json_encode(['status' => 911, 'message' => 'Wrong request!']);
    return false;
}
switch ($_POST['sst']) {
    case 's1f':
        try {
            if (!($_SESSION['role_id'] == 102)) {
                $response = ['status' => 505, 'message' => 'You Have No Permission For this Operation'];
                echo json_encode($response);
                return false;
            }
            $class = new Functions();

            $dob = trim(htmlentities(trim(filter_input(INPUT_POST, 'dob')),ENT_QUOTES));
            $phone = trim(htmlentities(trim(filter_input(INPUT_POST, 'phone')),ENT_QUOTES));
            $gender = trim(htmlentities(trim(filter_input(INPUT_POST, 'gender'))));
            $fname = trim(htmlentities(trim(filter_input(INPUT_POST, 'fname')),ENT_QUOTES));
            $lname = trim(htmlentities(trim(filter_input(INPUT_POST, 'lname')),ENT_QUOTES));
            $oname = trim(htmlentities(trim(filter_input(INPUT_POST, 'oname')),ENT_QUOTES));
            $blood = trim(htmlentities(trim(filter_input(INPUT_POST, 'bloodgroup'))));
            $address = trim(htmlentities(trim(filter_input(INPUT_POST, 'address')),ENT_QUOTES));
            //$rhesus = trim(htmlentities(trim(filter_input(INPUT_POST, 'rhesus'))));
            if(!($fname && $lname && $phone && $gender && $dob && $blood)){
                echo json_encode(['status' => 210, 'message' => 'Fields marked * ARE REQUIRED']);    
            return false;
            }
            $uniqueValues = $class->unique_id();
            $class->store(
                'clients_tbl',
                ['ref','fname','lname','phone','blood_group','gender','created_by','oname','dob','address'],
                [$uniqueValues,$fname,$lname,$phone,$blood,$gender,$user,$oname,$dob,$address]
             );
              echo json_encode(['status' => 200, 'message' => 'Account Created Successfully!']);    
            return false;
        } catch (Exception $e) {
            echo json_encode(['status' => 210, 'message' => 'An error occurred! ','response'=>$e->getMessage()]);
        }
    break;
    default:
    if (!($_SESSION['role_id'] == 103)) {
        $response = ['status' => 505, 'message' => 'You Have No Permission For this Operation'];
        echo json_encode($response);
        return false;
    }
    try{
            $dob = trim(htmlentities(trim(filter_input(INPUT_POST, 'dob')),ENT_QUOTES));
            $phone = trim(htmlentities(trim(filter_input(INPUT_POST, 'phone')),ENT_QUOTES));
            $gender = trim(htmlentities(trim(filter_input(INPUT_POST, 'gender'))));
            $fname = trim(htmlentities(trim(filter_input(INPUT_POST, 'fname')),ENT_QUOTES));
            $lname = trim(htmlentities(trim(filter_input(INPUT_POST, 'lname')),ENT_QUOTES));
            $oname = trim(htmlentities(trim(filter_input(INPUT_POST, 'oname')),ENT_QUOTES));
            $blood = trim(htmlentities(trim(filter_input(INPUT_POST, 'bloodgroup'))));
            $address = trim(htmlentities(trim(filter_input(INPUT_POST, 'address')),ENT_QUOTES));

            if(!($fname && $lname && $phone && $dob && $gender)){
                echo json_encode(['status' => 210, 'message' => 'Fields marked * ARE REQUIRED']);    
                return false;
            }
            //log file
            $class = new Functions();
            $ref = $class->simple_encrypt(trim(htmlentities(filter_input(INPUT_POST,'refx'))),'d');
            $raw = $class->fetch('clients_tbl'," WHERE ref = '$ref'");
            $row = json_encode($raw);
                                    //Log in a file
                        $data = $_SESSION['user_id']." Edited ".$ref." on ".date('Y-m-d H:i:s').PHP_EOL;
                        $fp = fopen('patient_edits.txt', 'a');
                        fwrite($fp, $row);
                        fwrite($fp, json_encode($_POST));
                        fclose($fp);
                $class->updateRecord('ref',['fname','lname','phone','blood_group','gender','oname','dob','address'],
                [$fname,$lname,$phone,$blood,$gender,$oname,$dob,$address,$ref],"clients_tbl");
                echo json_encode(['status' => 200, 'message' => 'Account Updated Successfully!']);    

    }
    catch (Throwable $e) {
            echo json_encode(['status' => 210, 'message' => 'An error occurred! ','response'=>$e->getMessage()]);
        }
        break;
}