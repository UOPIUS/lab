<?php include_once 'functions/Functions.php';
if (!$_SESSION['user_id']) header('location: login.php');
$class = new Functions();
$ref = $class->simple_encrypt(trim(htmlentities(filter_input(INPUT_GET,'refx'))),'d');

$config = $class->fetch('settings');
$client = $class->fetch("clients_tbl"," WHERE ref = '$ref'")
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Client Card | <?=$config->name ?></title>

    <style>
    .invoice-box {
        max-width: 400px;
        margin: auto;
        padding: 30px;
        border: 1px solid #eee;
        box-shadow: 0 0 10px rgba(0, 0, 0, .15);
        font-size: 16px;
        line-height: 24px;
        font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
        color: #555;
    }

    .invoice-box table {
        width: 100%;
        line-height: inherit;
        text-align: left;
    }

    .invoice-box table td {
        padding: 5px;
        vertical-align: top;
    }

    .invoice-box table tr td:nth-child(2) {
        text-align: right;
    }

    .invoice-box table tr.top table td {
        padding-bottom: 20px;
    }

    .invoice-box table tr.top table td.title {
        font-size: 45px;
        line-height: 45px;
        color: #333;
    }

    .invoice-box table tr.information table td {
        padding-bottom: 40px;
    }

    .invoice-box table tr.heading td {
        background: #eee;
        border-bottom: 1px solid #ddd;
        font-weight: bold;
    }

    .invoice-box table tr.details td {
        padding-bottom: 20px;
    }

    .invoice-box table tr.item td{
        border-bottom: 1px solid #eee;
    }

    .invoice-box table tr.item.last td {
        border-bottom: none;
    }

    .invoice-box table tr.total td:nth-child(2) {
        border-top: 2px solid #eee;
        font-weight: bold;
    }

    @media only screen and (max-width: 600px) {
        .invoice-box table tr.top table td {
            width: 100%;
            display: block;
            text-align: center;
        }

        .invoice-box table tr.information table td {
            width: 100%;
            display: block;
            text-align: center;
        }
    }

    /** RTL **/
    .rtl {
        direction: rtl;
        font-family: Tahoma, 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
    }

    .rtl table {
        text-align: right;
    }

    .rtl table tr td:nth-child(2) {
        text-align: left;
    }
    </style>
</head>

<body>
    <div class="invoice-box">
        <table cellpadding="0" cellspacing="0">
            <tr class="top">
                <td colspan="2">
                    <table>
                        <tr>
                            <td class="title" colspan="2">
                                <img src="assets/img/logo.png">
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr class="information">
                <td colspan="2">
                    <table>
                        <tr>
                            <td colspan="2">
                                Address: <small><?=$config->address ?></small>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr class="heading">
                <td colspan="2">
                  PATIENT CARD
                </td>

            </tr>


            <tr class="item">
                <td>
                    PATIENT NO:
                </td>

                <td>
                    <?=$client->ref ?>
                </td>
            </tr>

            <tr class="item last">
                <td>
                  FULL NAME:
                </td>

                <td>
                    <?= $client->fname.' '.$client->lname.' '.$client->oname ?>
                </td>
            </tr>
            <tr class="item last">
                <td>
                    GENDER:
                </td>

                <td>
                    <?= $client->gender ?>
                </td>
            </tr>
            <tr class="item last">
                <td>
                    PHONE NO:
                </td>

                <td>
                    <?= $client->phone ?>
                </td>
            </tr>
            <tr class="item last">
                <td>
                    BLOOD GROUP:
                </td>

                <td>
                    <?= $client->blood_group ?>
                </td>
            </tr>

        </table>
        <div id="printButton" style="text-align:center; padding:5px; margin:auto;">

        </div>
    </div>
    <script>
  window.onload = function() {
            'use strict';
            if (typeof window.print == 'function') {
                var printButton = document.createElement("button");
                    printButton.style.backgroundColor = '#D0312D';
                    printButton.style.padding = '15px';
                    printButton.style.margin = '4px';
                    printButton.style.color = '#fff';
                if (printButton.textContent != 'undefined') {
                    printButton.textContent = "Print this Card";
                } else {
                    printButton.innerText = "Print this Slip";
                }
                printButton.onclick = function() {
                    window.print();
                };
                document.getElementById("printButton").appendChild(printButton);
            }
        };
  </script>
</body>
</html>
