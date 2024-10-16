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
        #main-table th {
        padding-top: 12px;
        padding-bottom: 12px;
        text-align: left;
        background-color: #eee;
        color: #000;
    }
        table.mainTable{
            width:99%;
            margin-right:25px;
        }
        #container #header #rHeader #addressBar #title {
        color:#cc381c;
        font-weight:500;
        
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
        
        
        
        
    table {
        border-collapse: collapse;
        margin: auto;
        width: 100%;
        padding: 0;
    }

    th,
    td {

        text-align: left;
        padding: 5px;

    }

    .geeks {
        border-right: hidden;
    }

    .gfg {
        border-collapse: separate;
        border-spacing: 0 5px;

    }

    .text-center {
        text-align: center;
    }

    .uppercase {
        text-transform: uppercase;
    }
    #main-table {
        font-family: Arial, Helvetica, sans-serif;
        border-collapse: collapse;
        width: 100%;
        margin-top:10px;
    }

    #main-table td,
    #main-table th {
        border: 2px solid #ddd;
        padding: 8px;
        text-transform: uppercase;
    }

    #main-table th {
        padding-top: 12px;
        padding-bottom: 12px;
        text-align: left;
        background-color: #eee;
        color: #000;
    }
    .vertical {
      border-left: 6px solid #cc381c;
      height: 120px;
      position:absolute;
      left: 18%;
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
                    <h4 id="title" style="color:#cc381c;font-weight:700;">CAPITAL MEDICARE ABAKILIKI</h4>
                </div>
            </div>
        </div>
        <div class="mainBody">
            
        <table id="main-table">
                            <tr>
                                <th colspan="2"><span class="text-center">PATIENT INFORMATION</span></th>
                            </tr>
                            <tr>
                                <td>Patient Name: <?=$tranx->fname.' '.$tranx->lname.' '.$tranx->oname?></td>
                                <td>AGE: <?=$tranx->dob?> Years</td>
                            </tr>
                            <tr>
                                <td>DIAGNOSTICS:CMA</td>
                                <td>PATIENT ID: <?=$tranx->client_id?></td>
                            </tr>
                            <tr>
                                <td>Phone: <?=$tranx->phone ?></td>
                                <td>SEX: <?=$tranx->gender ?></td>
                            </tr>
                            <tr>
                                <td colspan="2">Date: <?=$tranx->test_date ?></td>
                            </tr>
                        </table>
            <?php echo html_entity_decode($tranx->test_result); ?>
        </div>
    </div>
</body>
</html>