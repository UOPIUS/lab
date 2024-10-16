<?php include_once 'functions/Functions.php';
$class = new Functions();
if (!$_SESSION['user_id']) header('location: login.php');
$tempEncrypted = htmlentities(filter_input(INPUT_GET,'ref'));
$ref = $class->simple_encrypt($tempEncrypted,'d');

$config = $class->fetch('settings');
$invoice = $class->rawQuery("
SELECT t.*,p.pay_method AS payment_method,c.fname,c.lname,c.phone AS cphone,
c.ref AS client FROM transactions AS t LEFT JOIN clients_tbl AS c 
ON t.client_id = c.ref LEFT JOIN payment_types AS p ON
t.payment_type = p.id WHERE  (t.id = '$ref') ORDER BY t.created_at DESC
",1);
?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Receipt | <?=$config->name ?></title>
    <link rel="icon" type="image/png" href="assets/img/favicon.png" />
    <style>
    
    body {
        max-width: 310px;
        margin: auto;
        height: auto;
        color: #000;
        font-family: Arial, Helvetica, sans-serif;
    }

    .invoice-box {
        max-width: 305px;
        margin: auto;
        max-height: auto;
        padding: 2px;
        font-size: 12px;
        line-height: 20px;
        color: #000;
        font-family: Arial, Helvetica, sans-serif;
    }

    .invoice-box table {
        width: 100%;
        line-height: inherit;
        text-align: left;
        color: #000;
        font-family: Arial, Helvetica, sans-serif;
    }

    .invoice-box table td {
        padding: 1px;
        padding-right: 2px;
        vertical-align: top;
        color: #000;
        font-family: Arial, Helvetica, sans-serif;
    }

    .invoice-box table tr td:nth-child(2) {
        text-align: right;
    }

    .invoice-box table tr.top table td {
        padding-bottom: 20px;
    }

    .invoice-box table tr.top table td.title {
        font-size: 18;
        line-height: 20px;
        color: #000;
    }

    .invoice-box table tr.information table td {
        padding-bottom: 20px;
        color: #000;
        font-family: Arial, Helvetica, sans-serif;
    }

    .invoice-box table tr.heading td {
        background: #eee;
        border-bottom: 1px solid #ddd;
        font-weight: bold;
        color: #000;
        font-family: Arial, Helvetica, sans-serif;
    }

    .invoice-box table tr.details td {
        padding-bottom: 10px;
    }

    .invoice-box table tr.item td {
        border-bottom: 1px solid #000;
    }

    .invoice-box table tr.item.last td {
        border-bottom: none;
    }

    .invoice-box table tr.total td:nth-child(2) {
        border-top: 2px solid #000;
        font-weight: bold;
    }


    .rtl {
        direction: rtl;
        color: #000;
        font-family: Arial, Helvetica, sans-serif;
    }

    .rtl table {
        text-align: right;
    }

    .rtl table tr td:nth-child(2) {
        text-align: left;
    }

    .header {
        display: flex;
        text-align: center;
    }
    .text-center{
        text-align: center;
    }
    
    </style>
</head>

<body>
    <div class="invoice-box">
        <table cellpadding="0" cellspacing="0">
            <tr class="top">
                <td colspan="2">
                    <div class="header">
                        <img src="assets/img/favicon.png" style="max-width:52px;">
                        <h4><?=$config->name?></h4>
                    </div>
                </td>
            </tr>
            <tr class="information">
                <td colspan='2'>
                    <?=$config->address ?><br>

                </td>

                <td>

                    <?=$config->email ?>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <strong class="text-center">Ref #: <?=$invoice->id ?></strong>
                </td>
             </tr>   
            <tr class="information">
                <td colspan='2'>
                   <label>Contact:</label> 09055568648, 080359227285

                </td>
            </tr>   
            <tr class="heading">
                <td colspan="2">
                
                    PAYMENT RECEIPT
                </td>

            </tr>


            <tr class="item">
                <td>
                    Payer ID
                </td>

                <td>
                    <?=$invoice->client_id ?>
                </td>
            </tr>

            <tr class="item last">
                <td>
                    Full name
                </td>

                <td>
                    <?= $invoice->fname.' '.$invoice->lname.' '.$invoice->oname ?>
                </td>
            </tr>
            <tr class="item last">
                <td>
                    Phone Number
                </td>

                <td>
                    <?= $invoice->cphone ?>
                </td>
            </tr>


            <tr class="heading">
                <td colspan="2">
                    TEST TO PERFORM
                </td>
            </tr>
            <?php $tests = $class->fetchAll("tests_taken"," WHERE tranx_id = '$ref'");
                   $i = 1;
                   foreach($tests as $t):
                       $pTest = $class->fetch('sub_labtest_tbl',' WHERE id='.$t->test_id)
                   
                   ?>
            <tr class='item'>
                <td>

                    <?= $i++ . '. '.$pTest->name ?>

                </td>
                <td>&#x20A6;<?=$pTest->cost ?></td>
        
            </tr>
            <?php endforeach; ?>


            <tr class="total">
                <td colspan="2">
                    <div style="float:right"><strong>Total Amount: &#8358;<?=$invoice->amount ?></strong></div>
                            <br></br>
                            <h2><b>NOTE: NO REFUND AFTER PAYMENT</></h2>
                    <h5>Thanks for your patronage........CMA cares.</5>
                </td>
            </tr>
        </table>
    </div>
    <div id="printButton" style="text-align:center; padding:5px; margin:auto; height:50px">
    </div>
    <script>
    window.onload = function() {
        document.addEventListener('contextmenu', event => event.preventDefault());
        
        'use strict';
        if (typeof window.print == 'function') {
            var printButton = document.createElement("button");
            printButton.style.backgroundColor = '#C0C0C0';
            printButton.style.padding = '2px';
            printButton.style.margin = '4px';
            printButton.style.color = '#fff';
            if (printButton.textContent != 'undefined') {
                printButton.textContent = "Print this Receipt";
            } else {
                printButton.innerText = "Print this Receipt";
            }
            printButton.onclick = function() {
                window.print();
            };
            document.querySelector("body").appendChild(printButton);
        }
        
    };
    </script>
</body>

</html>