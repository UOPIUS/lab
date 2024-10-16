<?php //declare(strict_types=1);
spl_autoload_register(function ($resource) {
  include $resource . '.php';
});

/**
 * SHIFT + ALT + F = format code & beautify VS Code
 */
class Functions extends Database
{
  /*
  Fetch all record
  @param string $table
  @param string $condition
  @returns an object of all row
  */
  public function fetchAll(string $table, string $condition = "")
  {
      $result = $this->connect()->query("SELECT * FROM " . $table . $condition);
      return $result->fetchAll(PDO::FETCH_OBJ);
      
  }
  /*
  Fetch single record
  @param string $table
  @returns an object of single row
  */
  public function fetch(string $table, $condition = '')
  {
    
    $result = $this->connect()->query("SELECT * FROM " . $table.$condition);
    return $result->fetch(PDO::FETCH_OBJ);
  }
  public function store(string $table, array $fields, array $values): bool
  {
    $placeholders = array();
    for ($i = 0; $i < (count($values)); $i++) {
      // One placeholder per field
      $placeholders[] = '?';
    }
    $st = $this->connect()->prepare("INSERT INTO $table (" .
      implode(',', $fields) . ') VALUES (' .
      implode(',', $placeholders) . ')');
    return $st->execute($values);
  }
  public function updateRecord(string $primaryKey, array $fields, array $values, string $table): bool
  {
    $update_fields = array();
    foreach ($fields as $field) {
      $update_fields[] = "$field = ?";
    }
    $st = $this->connect()->prepare("UPDATE $table SET " .
      implode(',', $update_fields) .
      " WHERE $primaryKey = ?");

    return $st->execute($values);
  }
  /**
   * abs((int) filter_var($str, FILTER_SANITIZE_NUMBER_INT));
   * Fetch a variable number of columns
   * @return collection of object of required columns
   *
   */
  public function fetchVariableColumnsAllCondition(string $table, array $fields, string $condition){
    $stmt = $this->connect()->query("SELECT " . implode(',', $fields) . " FROM $table $condition");
    return $stmt->fetchAll(PDO::FETCH_OBJ);
  }

  public function fetchColumn(string $table, string $field, string $primaryKey,string $value = null)
  {
    try {
      //code...
      $stmt = $this->connect()->prepare("SELECT $field FROM $table WHERE $primaryKey = :placeholder");
      $stmt->bindParam(':placeholder', $value);
      $stmt->execute();
      return $stmt->fetchColumn();
    } catch (TypeError $th) {
      return false;
    }
  }
  /**
   * @param string $table
   * @param string $field ~ Fie
   * 
   */
  public function fetchColumnWithManyConditions(string $table, string $field, $condition='')
  {
    $stmt = $this->connect()->query("SELECT $field FROM $table ".$condition);
    return $stmt->fetchColumn();
  }
  public function fetchVariableColumns(string $table, array $fields, string $primaryKey, string $value)
  {
    $stmt = $this->connect()->prepare("SELECT " . implode(',', $fields) . " FROM $table WHERE $primaryKey = :placeholder");
    $stmt->bindParam(':placeholder', $value);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_OBJ);
  }

  /**
   * FETCH VARIABLE COLUMNS AND RETURN ALL
   *
   * @param 
   ****/
  public function fetchVariableColumnsAll(string $table, array $fields,string $condition = '')
  {
    $stmt = $this->connect()->query("SELECT " . implode(',', $fields) . " FROM $table ".$condition);
    return $stmt->fetchAll(PDO::FETCH_OBJ);
  }
  public function updateStatus(string $table, string $primaryKey, string $value, string $status): bool
  {
    $statement = $this->connect()->prepare("UPDATE $table SET status = :status WHERE $primaryKey = :newValue");
    $statement->bindParam(':status', $status);
    $statement->bindParam(':newValue', $value);
    return $statement->execute();
  }
  /*
    Generates a random string
    @param int $length
    @return string
  */
  public function randomString($length)
  {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    if (!is_int($length) || $length < 0) {
      return false;
    }
    $characters_length = strlen($characters) - 1;
    $string = '';
    for ($i = $length; $i > 0; $i--) {
      $string .= $characters[mt_rand(0, $characters_length)];
    }
    return $string;
  }
  public function tranx_ref(){
    $query = $this->connect()->query("SELECT tranx_ref,acronymn FROM settings");
    $currentValue = $query->fetch(PDO::FETCH_OBJ);
    //increment by 1;
    $digits = strlen((string)$currentValue->tranx_ref);
    $digitsToPad = 6 - $digits;
    //pad string
    $expectedValue = str_pad($currentValue->tranx_ref, $digitsToPad, "0", STR_PAD_LEFT);
    //increase the db unique_id
    $newValue = $currentValue->tranx_ref + 1;
    $this->connect()->query("UPDATE settings SET tranx_ref = '$newValue' WHERE acronymn = '{$currentValue->acronymn}'");
    return "{$currentValue->acronymn}".date('Ymd').'/'.$expectedValue;
  }
  public function referral(){
    $query = $this->connect()->query("SELECT referral,acronymn FROM settings");
    $currentValue = $query->fetch(PDO::FETCH_OBJ);
    //increment by 1;
    $digits = strlen((string)$currentValue->referral);
    $digitsToPad = 4 - $digits;
    //pad string
    $expectedValue = str_pad($currentValue->referral, $digitsToPad, "0", STR_PAD_LEFT);
    //increase the db unique_id
    $newValue = $currentValue->referral + 1;
    $this->connect()->query("UPDATE settings SET referral = '$newValue' WHERE acronymn = '{$currentValue->acronymn}'");
    return "{$currentValue->acronymn}".date('Ymd').$expectedValue;
  }
  /**
   * Generate Ohaozara No.
   */
  public function unique_id(){
    $query = $this->connect()->query("SELECT unique_id,acronymn FROM settings");
    $currentValue = $query->fetch(PDO::FETCH_OBJ);
    $ref = $currentValue->unique_id;
    //increment by 1;
    $digits = strlen((string)$currentValue->unique_id);
    $digitsToPad = 8 - $digits;
    //pad string
    $expectedValue = str_pad($ref, $digitsToPad, "0", STR_PAD_LEFT);
    //increase the db unique_id
    $newValue = $ref + 1;
    $this->connect()->query("UPDATE settings SET unique_id = '$newValue' WHERE acronymn = '{$currentValue->acronymn}'");
    return $currentValue->acronymn.'/'.$expectedValue;
  }
    /*
    Generates a random Number
    @param int $length
    @return string
  */
  public function randomID($length)
  {
    $characters = '0123456789';
    if (!is_int($length) || $length < 0) {
      return false;
    }
    $characters_length = strlen($characters) - 1;
    $string = '';
    for ($i = $length; $i > 0; $i--) {
      $string .= $characters[mt_rand(0, $characters_length)];
    }
    return $string;
  }
  public function fetchRow(string $table, string $primaryKey, string $value)
  {
    $stmt = $this->connect()->prepare("SELECT * FROM $table WHERE $primaryKey = :placeholder");
    $stmt->bindParam(':placeholder', $value);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_OBJ);
  }
  /**
   * authenticate users
   * @param $email - user email address
   * @param $password - user password
   * @param boolean
   */
  public function authenticate(string $email, string $password): int
  {
    $status = '1';
    try {
      $statement = $this->connect()->prepare("SELECT name,user_id,password,status, role FROM other_users_tbl WHERE (username = :email)");
      $statement->bindParam(':email', $email);
      $statement->execute();
      $result = $statement->fetch(PDO::FETCH_OBJ);
      if ($result) {
      if($status != $result->status){
      	return 404;
      }
        if (password_verify($password, $result->password)) {
          $today = date('Y-m-d H:m:s');
          $this->updateRecord('username',['last_login'],[$today,$result->user_id],'other_users_tbl');
          $_SESSION['role'] =  $result->role;
          $_SESSION['email'] =  $email;
          $_SESSION['user_id'] =  $result->user_id;
          $_SESSION['full_name'] =  $result->name;
          $_SESSION['token'] = md5(uniqid());
            return 200;
        } else {
          return 300;
        }
      } else {
        return 400;
      }
    } catch (Exception $e) {
      echo 'An error Occurred!';
    }
  }

  /*
  Fetch single record
  @param string $table
  @returns an object of single row
  */
  public function fetchMultipleConditions(string $table,string $condition)
  {
    $result = $this->connect()->query("SELECT * FROM " . $table.$condition);
    return $result->fetch(PDO::FETCH_OBJ);
  }

  public function sendEmailFunctionFromOutside($name,$email,$title,$message)
  {
  $config = $this->fetch('settings');
  $shortcode = $config->short_code;
  $sender = $config->email;
  $headers = "MIME-Version: 1.0" . "\r\n";
  $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
  $headers .= 'Reply-To: ' .'The Sender'." <".$sender.">". "\r\n";
  $headers .= 'Return-Path: '.$shortcode ." <".$sender.">". "\r\n";
  $headers .= 'From: '.$shortcode ." <".$sender.">". "\r\n";

  $headers .= 'X-Priority: 1'. "\r\n";
  $headers .= 'X-Mailer: PHP'. phpversion() ."\r\n" ;
  $email_template_string = file_get_contents('../email_template/template2.html', true);
  $email_template = str_replace(array('%name%', '%email%', '%title%', '%msg%','%urlmain%'),array($name, $email, $title, $message),$email_template_string);
  mail($email,$title,$email_template,$headers);
  }
    /**
  * Function counts record in a table and return an integer
  * @param string $table
  * @param string $column
  * @return integer
  */
  public function countRecord(string $table, string $column, string $condition = ''): int
  {
    $result = $this->connect()->query("SELECT COUNT($column) FROM " . $table. $condition);
    return $result->fetchColumn();
  }
      /**
  * Function counts record in a table and return an integer
  * @param string $table
  * @param string $column
  * @return integer
  */
  public function countAndSumRecord(string $table, string $column, string $condition = ''): object
  {
    $result = $this->connect()->query("SELECT COUNT($column) AS numerate, SUM($column) AS amount FROM " . $table. $condition);
        return $result->fetch(PDO::FETCH_OBJ);
  }
  function simple_encrypt(String $string, String $action = 'e'){
    $output = false;
    $encrypt_method = "AES-256-CBC";
    $secret_key = '+ej1r1';
    $secret_iv = '0+se0gbu';
    // hash
    $key = hash('sha256', $secret_key);

    // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
    $iv = substr(hash('sha256', $secret_iv), 0, 16);
    if ($action == 'e') {
      $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
      $output = base64_encode($output);
    } else if ($action == 'd') {
      $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
    }
    return $output;
  }

  /**
  * Function sums record in a table and return an integer
  * @param string $table
  * @param string $column
  * @return integer
  */
  public function sumAmountInsideTable(string $table, string $column, string $condition = ''): int
  {
    $result = $this->connect()->query("SELECT SUM($column) FROM " . $table . $condition);
    $return = $result->fetchColumn() ?? 0;
    return ($return);
  }
    function generateRandomString($length = 10) {
    return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
  }
  public function rawQuery(string $raw,int $size = 5)
  {
    $x = $this->connect()->query($raw);
    if($size === 1) return $x->fetch(PDO::FETCH_OBJ);
    $y = $x->fetchAll(PDO::FETCH_OBJ);
    return $y;
  }
  function checkSession($param):bool{
    $x = $this->fetchColumn('users_tbl','session_id','user_id',$param);
    return ($x == session_id()) ? true : false;
  }
    function breakLongText($text, $length = 200, $maxLength = 250) {
    //Text length
    $textLength = strlen($text);
  
    //initialize empty array to store split text
    $splitText = array();
  
    //return without breaking if text is already short
    if (!($textLength > $maxLength)) {
        $splitText[] = $text;
        return $splitText;
    }
  
    //Guess sentence completion
    $needle = '.';
  
    /* iterate over $text length
      as substr_replace deleting it */
    while (strlen($text) > $length) {
  
        $end = strpos($text, $needle, $length);
  
        if (false === $end) {
  
            //Returns FALSE if the needle (in this case ".") was not found.
            $splitText[] = substr($text, 0);
            $text = '';
            break;
        }
  
        $end++;
        $splitText[] = substr($text, 0, $end);
        $text = substr_replace($text, '', 0, $end);
    }
  
    if ($text) {
        $splitText[] = substr($text, 0);
    }
  
    return $splitText;
  }
  
  function sanitize($param){
    $new_data = str_replace  ("'", "", $param);
    $new_data = preg_replace ('/[^\p{L}\p{N}]/u', '_', $new_data);
    return $new_data;
  }
  function substrwords($text, $maxchar, $end='...') {
    if (strlen($text) > $maxchar || $text == '') {
        $words = preg_split('/\s/', $text);      
        $output = '';
        $i      = 0;
        while (1) {
            $length = strlen($output)+strlen($words[$i]);
            if ($length > $maxchar) {
                break;
            } 
            else {
                $output .= " " . $words[$i];
                ++$i;
            }
        }
        $output .= $end;
    } 
    else {
        $output = $text;
    }
    return $output;
}
  
}
?>
