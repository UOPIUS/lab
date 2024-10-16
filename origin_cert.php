<?php 

$data = "SELECT * FROM table WHERE id LIKE '%1%'";

$new_data = str_replace  ("'", "", $data);
$new_data = preg_replace ('/[^\p{L}\p{N}]/u', ' ', $new_data);

$str = ";'&%wf--!@#";
$res = preg_replace('/[^a-zA-Z0-9_ -]/s',' ',$str);
$res = preg_replace('/[^a-zA-Z0-9]/s',' ',$str);
echo $res;