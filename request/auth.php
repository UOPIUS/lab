<?php include_once '../functions/Functions.php';
switch ($_POST['tt']) {
  case 't1':
    try {
      $class = new Functions();
      $email = htmlentities(filter_input(INPUT_POST,'username'),ENT_QUOTES);
      $password = htmlentities(filter_input(INPUT_POST,'password'),ENT_QUOTES);
      $status = 1;
      $statement = $class->connect()->prepare("SELECT user_id,full_name,session_id,password,status,role_id, username FROM users_tbl WHERE (username = :email)");
      $statement->bindParam(':email', $email);
      $statement->execute();
      $result = $statement->fetch(PDO::FETCH_OBJ);
      if ($result) {
          if(0 == $result->status){
              echo json_encode(['status' => 404, 'message' => 'User Account is not Active']);
              return false;
          }
          /*
          * IF THE USER IS ALREADY LOGGED IN SOMEWHERE,
          * DESTROY THE SESSION AND RECREATE IT TO LOG THEM OUT ELSEWHERE
          * THAT'S THE ESSENCE OF THE session_id. When it is 1, it means account is currently in use,
          * When it is 0, it means account is not in use.
          */
        if (password_verify($password, $result->password)) {
              $today = date('Y-m-d H:m:s');
              session_regenerate_id();
              $class->updateRecord('user_id',['last_login','session_id'],[$today,session_id(),$result->user_id],'users_tbl');
              $_SESSION['role_id'] =  $result->role_id;
              $_SESSION['username'] =  $email;
              $_SESSION['user_id'] =  $result->user_id;
              $_SESSION['name'] =  $result->full_name;
              $_SESSION['start'] = time();
              $_SESSION['token'] = md5(uniqid());
              $url = '';
              switch ($result->role_id) {
                case '101': 
                  # admin
                  $url .= 'admin_dashboard.php'; // Admin
                  break;
                case '103':
                  # account...
                  $url .= 'acct_dashboard.php'; //CASHIER
                  break;
                case '102':
                  $url .= 'adhoc_dashboard.php'; //FRONT DESK
                break;
                case '105':
                  $url .= 'lab_dashboard.php'; //LAB SCIENTIST
                break;
                case '106':
                  $url .= 'admin_dashboard.php'; //MEDICAL DOCTOR
                break;
                case '107':
                  $url .= 'dashboard.php'; //Referral
                break;
                
              }
              $class = null;
              echo json_encode(['status' => 200, 'message' => ' Authentication Successful!','url'=>$url]);
              return false;
        } else {
          echo json_encode(['status' => 405, 'message' => 'Username or Password is Wrong']);
          return false;
        }
      } else {
          echo json_encode(['status' => 405, 'message' => 'User Does not Exist']);
          return false;
      }
    } catch (Exception $e) {
      echo json_encode(['status' => 406, 'message' => 'An error occurred! Please try again','response'=>$e->getMessage()]);
      return false;
    }
    break;
  
  case 't2':
    $class = new Functions();
    $oname = htmlentities(trim(filter_input(INPUT_POST, 'cm_name')));
    $cm = filter_input(INPUT_POST,'cm_id');
    $email = filter_input(INPUT_POST,'email');
    $vlage = filter_input(INPUT_POST,'vl_id');
    $qualif = filter_input(INPUT_POST,'qualif');
    $mother_name = trim(filter_input(INPUT_POST,'mother_name'));
    $father_name = trim(filter_input(INPUT_POST,'father_name'));
    $kdred = trim(filter_input(INPUT_POST,'kdred'));
    $cm = filter_input(INPUT_POST,'cm_id');
    $career = filter_input(INPUT_POST,'career');
    $id = htmlentities(filter_input(INPUT_POST,'rfn'));
    $dob = htmlentities(filter_input(INPUT_POST,'dob'));
    if(!($cm && $vlage && $qualif && $career && $kdred
     && $mother_name && $father_name)){
      echo json_encode(['status' => 210, 'message' => 'Fields marked * are REQUIRED']);    
      return false;
     }

   try {
    $class->updateRecord('ref',['oname','email','kindred',
    'village_id','community_id','dob','father_name',
  'mother_name','qualification','career','status'],
  [$oname,$email,$kdred,$vlage,$cm,$dob,$father_name,
  $mother_name,$qualif,$career,1,$id],
  'clients_tbl');
  echo json_encode(['status' => 200, 'message' => 'Record Updated Successfully']);    
            return false;
   } catch (\Throwable $th) {
    echo json_encode(['status' => 210, 'message' => 'An error occurred! ','response'=>$th->getMessage()]);
   }
    break;
  case 't3':
      $class = new Functions;
         try {
            /* Getting file name */
          $productImage = $_FILES['passport']['name'];
          $location = "../passport/".$productImage;
          $uploadOk = 1;
          $productImageExtension = pathinfo($location,PATHINFO_EXTENSION); //get file extension
          //$fileSize = $_FILES['file']['size'];

          $productImageFIle = uniqid() . '.'.$productImageExtension;
          $dir = '../passport/'.$productImageFIle;
          /* Valid Extensions */
          $valid_extensions = array("jpg","jpeg","png");

          $ref = htmlentities(filter_input(INPUT_POST,'id'));
          /* Check file extension */
            if(!in_array(strtolower($productImageExtension),
            $valid_extensions)) {
              $uploadOk = 0;
            }
            if($uploadOk == 1 && move_uploaded_file($_FILES['passport']['tmp_name'],$dir))
            {
              $class->updateRecord('ref',[
                'passport'
              ],[
                $productImageFIle,$ref
              ],'clients_tbl');
              //update transaction 
              $class->updateRecord('client_id',[
                'status','adhoc_id','updated_at'
              ],[
                1,$_SESSION['user_id'],date('Y-m-d H:m:s'),$ref
              ],'transactions');
              $class = null;
              echo json_encode(['status'=>200,'message'=>'Passport Uploaded Successfully']);
              return false;
            }          
            else{
            echo json_encode(['status'=>405,'message'=>'File Not Saved']);
          }
         } catch (\Throwable $th) {
          echo json_encode(['status'=>407,'message'=>'File Not Saved','response'=>$th->getMessage()]);
         }
    break;
}