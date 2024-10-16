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
/*
$input = 201308131830; 
echo date("Y-M-d H:i:s",strtotime($input)) . "\n";
echo date("D", strtotime($input)) . "\n";
*/
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Test Result | <?=$config->name ?></title>
    <meta name="author" content="<?=$config->name ?>">
    <script src="js/all.min.js"></script>
    <style>
    h6,
    h1 {
        padding: 0;
        margin: 0;
    }

    #logo {
        max-width: 80%;
    }

    h1 {
        color: #cc381c;
        text-shadow: 3px 3px 0px #2c2e38, 5px 5px 0px #5c5f72;
        font-size: 3.5em;
        letter-spacing: 1px;
    }

    h6 {
        font-size: 14px;
        letter-spacing: 1;
    }

    body #container {
        margin: auto;
        width: 95%;
        height: 100%;
        font-family: verdana;
        background-image: url(assets/img/bg.jpg);
        background-repeat: no-repeat;
        background-position: center;
        font-size: 14px;
        color: #000;
    }

    #footNote .editButton {
        background-color: #f00;
        -moz-border-radius: 28px;
        -webkit-border-radius: 28px;
        border-radius: 5px;
        display: inline-block;
        cursor: pointer;
        color: #ffffff;
        font-family: Arial;
        font-size: 17px;
        padding: 2px 4px;
        text-decoration: none;
        text-shadow: 0px 1px 0px #2f6627;
        margin: 15px 20px;
    }

    #footNote .editButton:hover {
        background-color: #6c7c7c;
    }

    #footNote .editButton:active {
        position: relative;
        top: 1px;
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

    @media print {
        * {
            visibility: hidden;
            -webkit-print-color-adjust: exact;
        }

        #header,
        #header *,
        .gfg,
        .gfg * {
            visibility: visible;

        }

        .gfg {
            background-image: url(assets/img/bg.jpg);
            background-repeat: no-repeat;
            background-position: center;
        }

        h1 {
            color: #cc381c;
            text-shadow: 3px 3px 0px #2c2e38, 5px 5px 0px #5c5f72;
            font-size: 2em;
            letter-spacing: 1px;
        }

        h6 {
            font-size: 1em;
            letter-spacing: 1;
        }

        .vertical {
            left: 55%;
        }

    }

    .row {
        display: flex;
        width: 95%;
        padding: 1px;
    }

    .row>div {
        margin: 0;
    }

    .row .item-one {
        width: 20%;
    }

    .row .item-two {
        width: 60%;
    }

    i {
        margin-right: 4px;
        color: #f00;
        font-size: 16px;
    }

    #main-table {
        font-family: Arial, Helvetica, sans-serif;
        border-collapse: collapse;
        width: 100%;
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
        position: absolute;
        left: 18%;
    }
    </style>
    <script>
    window.addEventListener("contextmenu", e => e.preventDefault());
    </script>
</head>

<body>
    <div id="container">
        <div id="header">
            <div class="row">
                <div class="item-one">
                    <img src="assets/img/favicon.png" alt="logo" id="logo">
                </div>

                <div class="item-two">
                    <div class="vertical"></div>
                    <address>
                        <i class="fa fa-map-marker red"></i>&nbsp;<?=$config->address?>
                        <br>
                        <i class="fa fa-mobile red"></i>&nbsp;<?= $config->contact_phone ?>
                        <br>
                        <i class="fa fa-envelope red"></i>&nbsp;<?=$config->contact_email?>
                    </address>
                    <br>
                    <h1>CAPITAL MEDICARE ABAKALIKI</h1><br>

                </div>
            </div>

            <!--<hr style="border-top: 4px solid #cc381c">-->
        </div>

        <table class="gfg">
            <tbody>
                <tr>
                    <td colspan="4">
                        <table id="main-table">
                            <tr>
                                <th colspan="2"><span class="text-center">CLIENT INFORMATION</span></th>
                            </tr>
                            <tr>
                                <td>Patient Name: <?=$tranx->fname.' '.$tranx->lname.' '.$tranx->oname?></td>
                                <td>AGE: <?=$tranx->dob?>Years</td>
                            </tr>
                            <tr>
                                <td>Address: <?=$tranx->address ?></td>
                                <td>PATIENT ID: <?=$tranx->client_id?></td>
                            </tr>
                            <tr>
                                <td>Phone: <?=$tranx->phone ?></td>
                                <td>GENDER: <?=$tranx->gender ?></td>
                            </tr>
                            <tr>
                                <td colspan="2">Date: <?=$tranx->test_date ?></td>
                            </tr>
                        </table>
                        <!--<hr style="border-bottom: 4px solid #cc381c">-->
                    </td>
                </tr>
                <tr>
                    <td colspan="4" class="geeks" style="text-align:left">
                        <div style="text-align: left; margin-top: -30px ">

                            <article>
                                </br>
                                <?php $array = $class->breakLongText(html_entity_decode($tranx->test_result),2000,500);
                    foreach($array as $item){
                        echo "<p>".$item."</p>";
                    }
                 ?>
                            </article>
                        </div>
                    </td>

                </tr>
                <tr>
                    <td class="geeks"></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
    </div>

    </td>
    </tr>


    </tbody>

    </table>

    </div>
    <script>
    window.onload = function() {
        'use strict';
        if (typeof window.print == 'function') {
            var printButton = document.createElement("button");
            if (printButton.textContent != 'undefined') {
                printButton.textContent = "Print Affidavit";
            } else {
                printButton.innerText = "Print Affidavit";
            }
            printButton.onclick = function() {
                window.print();
            };
            var el = document.getElementsByTagName("body");
            el.appendChild(printButton);
        }
    };
    </script>
</body>

</html>