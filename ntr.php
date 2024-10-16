<?php 
include 'functions/Functions.php';
$class = new Functions();
include_once 'functions/validate_session.php';
if(!($_SESSION['user_id'] && $_SESSION['role_id'])) header('location: login.php');
$config = $class->fetch('settings');
$tempEncrypted = htmlentities(filter_input(INPUT_GET,'idx'),ENT_QUOTES);
$ref = $class->simple_encrypt($tempEncrypted,'d');
if(!$ref) header("location: logout.php");
//change the status of the test to printed 
$class->connect()->query("UPDATE tests_taken SET alert_flag = 'Y' WHERE id = '$ref'");

$tranx = $class->rawQuery("SELECT tt.*,client.fname,client.lname,client.oname,
client.dob,client.phone,client.blood_group,client.address, client.gender,subt.name test_name,tt.created_at test_date
 FROM tests_taken tt JOIN sub_labtest_tbl subt ON
tt.test_id = subt.id JOIN clients_tbl client ON tt.client_id = client.ref WHERE tt.id = '$ref'",1);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Result|Capital Medicare</title>
    <style>
        table, th, td {
          border: 1px solid black;
          border-collapse: collapse;
        }
        table.mainTable{
            width:100%;
            margin-right:25px;
        }
        table.mainTable td{
            border: 1px solid #000;
            padding: 1px;
            text-align: left;
        }
        #container #header,#container {
            margin:0;
            padding:0;
        }
        #container #header {
            width: 100%;
            height: auto;
            display: flex;
        }
        #container #header #lHeader {
          width: 150px;
          border: 1px solid #fff;
        }
        #container #header div  {
            padding:15px;
            vertical-align: bottom;
        }
        #container #header #rHeader{
            border-bottom: 5px solid #cc381c;
            border-left: 5px solid #cc381c;
            
        }
        address {
            padding: 0;
            margin:0;
            text-align: right;
        }
        h1 {
            padding:0;
            margin:0;
        }
        @media print {
                    body {
            font-family: Arial, Helvetica, sans-serif;
            width:99%;
        }
        table, th, td {
          border: 1px solid black;
          border-collapse: collapse;
        }
        table.mainTable{
            width:99%;
            margin-right:25px;
        }
        table.mainTable td{
            border: 1px solid #000;
            padding: 10px;
            text-align: left;
        }
        #container #header,#container {
            margin:0;
            padding:0;
        }
        #container #header {
            width: 100%;
            height: 185px;
            display: flex;
        }
        #container #header #lHeader {
          width: 200px;
          border: 1px solid #fff;
        }
        #container #header div  {
            padding:15px;
            vertical-align: bottom;
        }
        #container #header #rHeader{
            border-bottom: 5px solid #cc381c;
            border-left: 5px solid #cc381c;
            
        }
        address {
            padding: 0;
            margin:0;
            text-align: right;
        }
        h1 {
            padding:0;
            margin:0;
        }
        }
    </style>
</head>
<body style="font-family: Arial, Helvetica, sans-serif;width:99%;">
    <div id="container">
        <div id="header">
            <div id="lHeader">
                <img src="assets/img/favicon.png" alt="logo" id="logo">
            </div>
            <div id="rHeader">
                <div class="addressBar">
                    <address>
                        1 Udi Str./36 Water Works Rd, <br> before Cas Campus, Abakaliki <br>
                       
                        TEL: 09055568648,<br>09055568658<br>
                        Email:capitalmedicareai@gmail.com
                    </address>
                    <h1 style="color:#cc381c;font-weight:800">CAPITAL MEDICARE ABAKALIKI</h1>
                </div>
            </div>
        </div>
        <div class="mainBody">
            
            <table class="mainTable" style="border: 1px solid black;border-collapse: collapse; margin-top:6px">
                            <tr>
                                <td>SURNAME: <?=strtoupper($tranx->lname)?></td>
                                <td>PATIENT ID: <?=$tranx->client_id?></td>
                                <td>DATE: <?=$tranx->test_date ?></td>
                            </tr>
                            <tr>
                                <td>OTHER NAMES: <?=$tranx->fname.' '.$tranx->oname?></td>
                                <td>SEX: <?=$tranx->gender?></td>
                                <td>PHONE:<?=$tranx->phone ?></td>
                            </tr>
                            <tr>
                                <td>ADDRESS: </td>
                                <td>AGE: <?=$tranx->dob ?>YEARS</td>
                                <td>DIAGNOSTICS:CMA</td>
                            </tr>
                  
            </table>
            <?php echo html_entity_decode($tranx->test_result); ?>
        </div>
    </div>
</body>
</html>