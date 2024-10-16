<?php session_start();
try{
    $_SESSION = [];
    session_destroy();
    header('location: login.php');
}

catch(PDOEXception $e){

}