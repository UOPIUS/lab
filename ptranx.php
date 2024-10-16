<?php include_once 'functions/Functions.php'; 
$class = new Functions();
if(!(103 == $_SESSION['role_id'] || 101 == $_SESSION['role_id'])) header('location: logout.php');
include_once 'functions/validate_session.php';
$sales = $class->rawQuery("
    SELECT t.client_id,t.id,t.payable_amount,principal_amount,t.amount,DATE_FORMAT(t.created_at, '%d/%m/%Y') AS created_at,p.pay_method AS payment_method,c.fname,c.lname,c.phone AS cphone,c.ref AS client FROM transactions AS t LEFT JOIN
     clients_tbl AS c ON t.client_id = c.ref LEFT JOIN payment_types AS p ON
    t.payment_type = p.id  WHERE t.status = '0' ORDER BY t.created_at DESC
");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Cashier Payment - Pending Transaction | Capital Medicares Lab</title>
<link rel="icon" type="image/png" href="assets/img/favicon.png" />
    <link href="css/materialdesignicons.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet" />
    <link href="css/dataTables.bootstrap4.min.css" rel="stylesheet" />
    <script src="js/all.min.js"></script>
</head>

<body class="sb-nav-fixed">
    <?php include_once 'header.php'; ?>
    <div id="layoutSidenav">
        <?php include_once 'menu.php' ?>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid">
                    <div class="container-fluid" id="oldDiv">
                        <h2 class="mt-4 text-dark">Pending Transaction History</h2>
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-table mr-1"></i>Transactions
                               
                            </div>
                            <div class="card-body">
                                <div class="table-responsive mt-4">
                                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>Receipt</th>
                                                <th>Client ID</th>
                                                <th>Name</th>
                                                <th>Mobile</th>
                                                <th>Amount</th>
                                                <th>Bal.</th>
                                                <th>Method</th>
                                                <th>Date</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            $i=1;
                                            foreach($sales as $x):
                                                $client_ref = $class->simple_encrypt($x->client_id,'e');
                                                $tranx_ref = $class->simple_encrypt($x->id,'e');
                                             ?>
                                            <tr>
                                                <td>
                                                    
                                                    <?php if(103 == $_SESSION['role_id']){
                                                    if($x->status == 0){ ?>
                                                    <!-- Button to Open the Modal -->
                                                    <button type="button" class="btn btn-primary btn-sm"
                                                        data-toggle="modal" data-target="#payModal" data-namount="<?=$x->payable_amount?>"
                                                        data-tref="<?=$tranx_ref?>" data-amount="<?=abs($x->payable_amount + $x->amount) ?>">
                                                        Make Payment
                                                    </button>
                                                    <?php } elseif($x->status == 1){ ?>
                                                    <a href="receipt.php?ref=<?=$tranx_ref ?>" target="__blank" class="btn btn-sm btn-link">
                                                        <i class="fa fa-print"></i>&nbsp;Print
                                                    </a>
                                                    <?php } } else { echo 'NA'; } ?>
                                                </td>

                                                <td>
                                                    <a href="client_profile.php?refx=<?=$client_ref ?>"
                                                        class="btn btn-link"><?=$x->client ?></a>
                                                </td>
                                                <td><?=$x->fname.' '.$x->lname. ''.$x->oname ?></td>
                                                <td><?=$x->cphone ?></td>
                                                <td><?=$x->amount ?></td>
                                                <td><?=$x->payable_amount + $x->amount;?></td>
                                                <td><?=$x->payment_method ?></td>
                                                <td><?=$x->created_at ?></td>
                                                
                                                <td>
                                                    <?php if(101 == $_SESSION['role_id']): ?>
                                                        <a href="editTranx.php?transRef=<?=$tranx_ref ?>" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                                                    <?php endif ?>
                                                </td>
                                                

                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
            <?php include 'footer.php'; ?>
        </div>
    </div>
    <!-- The Modal -->
    <div class="modal" id="payModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">Collect Payment</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                    <form id="payForm" method="POST">
                        <div class="form-group">
                            <label for="amount">Payable Amount</label>
                            <input type="hidden" id="tranx-ref" name="tranx_ref">
                            <input type="text" class="form-control" id="namount" readonly name="namount">
                        </div>
                        <div class="form-group">
                            <label for="amount">Amount Customer Wants to Pay</label>
                            <input type="text" class="form-control" id="amount" name="amount">
                        </div>
                        <div class="form-group">
                            <label for="discount">Discount given to Customer</label>
                            <input type="text" class="form-control" id="discount" name="discount" oninput="editXdis(this)">
                        </div>
                        <div class="form-group">
                            <label>Payment Method</label>
                            <select class="form-control" name="pay_method">
                                <option value="">Select Method</option>
                                <?php $method = $class->fetchAll('payment_types'," WHERE status = 1"); 
                                                foreach($method as $m): ?>
                                <option value="<?=$m->id ?>"><?=$m->pay_method ?></option>
                                <?php endforeach; ?>
                            </select>

                        </div>
                        <div class="text-center m-1" id="pay-response"></div>
                        <button type="submit" class="btn btn-primary" id="submitPay">Proceed</button>
                    </form>
                </div>

                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>

            </div>
        </div>
    </div>
    <script src="js/jquery-3.4.1.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/jquery.dataTables.min.js"></script>
    <script src="js/dataTables.bootstrap4.min.js"></script>
    <script src="assets/demo/datatables-demo.js"></script>
    <script src="js/scripts.js"></script>
    <script src="js/tejiri.js"></script>
    <script>

    $("#payModal").on("show.bs.modal", function(event) {
        var button = $(event.relatedTarget);
        var modal = $(this);
        modal.find(".modal-body input#tranx-ref").val(button.data('tref'));
        modal.find(".modal-body input#amount").val(button.data('amount'));
        modal.find(".modal-body input#namount").val(button.data('namount'));
        var wholeAmount = Math.abs(button.data('namount'));
        //alternative 
        $(modal.find(".modal-body input#discount")).on('input', function(ev){
             var discountValue = ev.currentTarget.value;
             modal.find(".modal-body input#amount").val(wholeAmount - discountValue);

        });

        $("#payForm").submit(function(e) {
            var btn = $("#submitPay");
            btn.prop("disabled",true);
            btn.text("Please Wait...")
            e.stopPropagation();
            let url = "request/make_payment.php";
            // Send the data using post
            var posting = $.post(url, $(this).serialize());
            posting.done(function(data) {
                btn.prop("disabled",false);
                btn.text("Proceed")
                let result = JSON.parse(data);
                if (200 == result.status) {
                    $("#pay-response").html(
                        '<p class="bg-success text-white p-2">' + result.message + "</p>"
                    );
                    window.location.reload(true);
                    return;
                }
                $("#pay-response").html(
                    '<p class="bg-danger text-white p-2">' + result.message + "</p>"
                );
            });
            e.preventDefault();
        });
    });
    function editXdis(param){
        
    }
    </script>
</body>

</html>