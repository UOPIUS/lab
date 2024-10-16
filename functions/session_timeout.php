<?php
ob_start();

//set a time limit in seconds
$timeLimit = 15;
//get current time
$now = time();

$redirectPath = "http://localhost/plain/lab/login.php";
//if session variable not set, redirect to login page

if (!isset($_SESSION['user_id'])) {
    header("Location: $redirectPath");
    exit;
}
elseif($now > $_SESSION['start']+$timeLimit){
    //if timelimit has expired, destroy session and redirect
    $_SESSION = [];
    //invalidate the session cookie
    if(isset($_COOKIE[session_name()])){
        setcookie(session_name(),'',time()-8400,'/');
    }
    //end session and redirect with query string
    session_destroy();
}