<?php 

if($class->checkSession($_SESSION['user_id'])  === false) header('location: logout.php');

?>