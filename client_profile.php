<?php include_once 'functions/Functions.php'; 
$class = new Functions();
if($class->checkSession($_SESSION['user_id'])  === false) header('location: logout.php');
$rfn = $class->simple_encrypt(trim(htmlentities(filter_input(INPUT_GET,'refx'))),'d');
if(!$rfn)header('location: logout.php');
$config = $class->fetch('settings');
$customer = $class->fetch('clients_tbl'," WHERE ref = '$rfn'");

//set passport
if($customer->passport){
    $passport = $customer->passport;
}else {
    $passport = $customer->gender == 'Female' ? 'female.png' : 'male.png';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title><?= $customer->fname. ' '.$customer->lname.' | '. $config->name ?></title>
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
                    <div class="row">
                        <?php if($_SESSION['role_id'] == 102): ?>
                        <div class="col-lg-12">
                            <a class="btn btn-danger float-right mb-2 mt-2 text-white"
                                href="new_tranx.php?ini_id=<?=filter_input(INPUT_GET,'refx')?>">
                                <i class="fa fa-plus-circle"></i>&nbsp;New Transaction
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="row mt-2">
                        <div class="col-lg-3">
                            
                                <div class="card border-0">

                                    <ul class="list-group">
                                        <li class="list-group-item">
                                            <div class="text-center">
                                                <img class="img-fluid img-circle" src="passport/<?=$passport ?>"
                                                    alt="Photo"
                                                    style="border: 3px solid #adb5bd; border-radius:50%;margin: 0 auto;padding: 3px;width: 100px;">
                                                <a href="photo.php?refx=<?=filter_input(INPUT_GET,'refx') ?>"
                                                    class="btn btn-sm btn-warning"><i class="fa fa-edit"></i></a>
                                            </div>
                                        </li>
                                        <li class="list-group-item">Ref. ID: <?= $customer->ref ?></li>
                                        <li class="list-group-item">Name:
                                            <?= $customer->lname." ".$customer->fname." ".$customer->oname ?></li>
                                        <li class="list-group-item">Gender: <?= $customer->gender ?></li>
                                        <li class="list-group-item">Phone: <?= $customer->phone ?></li>
                                        <li class="list-group-item">Age: &nbsp;
                                            <?php 
                                                        $dob = $customer->dob;
                                                        //$age = floor((time() - strtotime($dob)) / 31556926);
                                                        echo $dob."YRS";
                                                         ?>
                                        </li>
                                        <li class="list-group-item">Blood group: &nbsp; <?= $customer->blood_group ?>
                                        </li>
                                        <?php if($_SESSION['role_id'] == 103): ?>
                                        <li class="list-group-item">
                                            <a href="edit_client.php?refx=<?=filter_input(INPUT_GET,'refx') ?>"
                                                class="btn-danger btn btn-block btn-sm"><i class="fa fa-edit"></i>Edit
                                                Profile
                                            </a>
                                            <a href="client_card.php?refx=<?=filter_input(INPUT_GET,'refx') ?>"
                                                class="btn-success btn btn-block btn-sm" target="__blank"><i
                                                    class="fa fa-print"></i> Print Card
                                            </a>
                                        </li>
                                        <?php endif; ?>
                                    </ul>


                                </div>
                            
                        </div>
                        <div class="col-lg-9">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="text-center">Transactions</h4>
                                </div>

                                <div class="card-body">
                                    <div class="table-responsive mt-4">
                                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Ref.</th>
                                                    <th>Status</th>
                                                    <th>Amount</th>
                                                    <th>Outstanding</th>
                                                    <!--
                                                    <th>Payment Method</th>
                                                    --------------->
                                                    <th>Test Result</th>
                                                    <th>Date created</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                <?php 
                                                $sales = $class->rawQuery("
                                                SELECT t.*,p.pay_method AS payment_method, c.fname,c.lname,c.phone AS cphone,c.ref AS client FROM transactions AS t LEFT JOIN
                                                 clients_tbl AS c ON t.client_id = c.ref LEFT JOIN payment_types AS p ON
                                                t.payment_type = p.id WHERE t.client_id = '$rfn' AND (t.status <> 2 AND t.status <> 5) ORDER BY t.created_at DESC
                                            ",5);
                                            $i=1;
                                            
                                            foreach($sales as $x):
                                                $refx = $class->simple_encrypt($x->id,'e'); 
                                            ?>
                                                <tr>
                                                    <td><?=$i++ ?></td>
                                                    <td>
                                                    <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#paymentModal" 
													data-ref="<?= $x->id ?>">
													<?= $x->id ?>
													</button>
                                                    </td>
                                                    <td>
                                                        <?php if($x->payable_amount + $x->amount == 0){ ?>
                                                        <button class="btn btn-success btn-sm"><i
                                                                class="fa fa-check-circle"></i></button>
                                                        <?php } else { ?>
                                                        <?php if($_SESSION['role_id'] == 103) { ?>
                                                        <button type="button" class="btn btn-primary btn-sm"
                                                            data-toggle="modal" data-target="#payModal"
                                                            data-tref="<?=$refx?>"
                                                            data-amount="<?=$x->payable_amount + $x->amount ?>">
                                                            Pay Bal
                                                        </button>
                                                        <?php } else { ?>
                                                        <button class="btn btn-sm btn-danger">Owing</button>
                                                        <?php } } ?>
                                                    </td>
                                                    <td><?=$x->amount ?></td>
                                                    <td><?=$x->payable_amount + $x->amount;?></td>
                                                    <!--
                                                    <td><?=($x->payment_method) ?? "NA" ?></td>
                                                    -->
                                                    <td>
                                                        <?php if($x->doctor_flag): ?>
                                                        <a href="" class="btn text-success">View</a>
                                                        <?php else: 
                                                        echo "NA";
                                                        endif; ?>
                                                    </td>
                                                    <td><?=$x->created_at ?></td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
            <?php include 'footer.php'; ?>
        </div>
    </div>

    <!-- Show Payment History modal -->
<!-- Modal -->
<div id="paymentModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Payment History</h4>
      </div>
      <div class="modal-body">
        <table class="table table-bordered" id="loadPays"></table>
		<div id="show-response"></div>
      </div>
	 
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>
<!-- Show Payment History modal ends-->

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
                            <input type="text" class="form-control" id="amount" readonly name="amount">
                        </div>
                        <div class="form-group">
                            <label for="amount">Enter Amount Customer Wants to Pay</label>
                            <input type="text" class="form-control" name="amount_paid">
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
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i
                            class="fa fa-times"></i></button>
                </div>

            </div>
        </div>
    </div>
    <!-- Modal End Pay-->
    <script src="js/jquery-3.4.1.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/jquery.dataTables.min.js"></script>
    <script src="js/dataTables.bootstrap4.min.js"></script>
    <script src="assets/demo/datatables-demo.js"></script>
    <script src="js/scripts.js"></script>
    <script src="js/tejiri.js"></script>
    <script>
    /*
    window.onload = () => {
        //upload image
        document.getElementById('uploadPassportForm').addEventListener('submit', (e) => {
            //create an image element and show preview
            e.preventDefault();
            let form = e.target;
            responseDiv = document.getElementById('passport-response');
            var data = new FormData(form);
            data.append('id', document.getElementById('rfn').value);
            data.append('tt', 't3');
            responseDiv.innerHTML = '<p class="bg-warning p-2 text-white">Please Wait. . .</p>';
            let xhr = new XMLHttpRequest();
            xhr.open('POST', 'request/auth.php');
            xhr.send(data);
            xhr.onload = function() {
                if (xhr.status == 200) {
                    var jsonData = JSON.parse(xhr.response);
                    if (200 == jsonData.status) {
                        responseDiv.innerHTML = '<p class="bg-success p-2 text-white">' + jsonData
                            .message + '</p>';
                        setTimeout(function() {
                            window.location.reload(true);
                        }, 1000)
                    } else {
                        responseDiv.innerHTML = '<p class="bg-warning p-2 text-white">' + jsonData
                            .message + '</p>';
                        setTimeout(function() {
                            responseDiv.innerHTML = '';
                        }, 1000)
                    }
                }
                return false;
            };
            xhr.onerror = function() {
                console.log("Request failed");
            };
        });
    };
*/
    $("#payModal").on("show.bs.modal", function(event) {
        var button = $(event.relatedTarget);
        var modal = $(this);
        modal.find(".modal-body input#tranx-ref").val(button.data('tref'));
        modal.find(".modal-body input#amount").val(button.data('amount'));

        $("#payForm").submit(function(e) {
            $("#submitPay").prop("disabled",true);
            $("#pay-response").html(
                '<p class="bg-warning text-white p-2">Please Wait. . . </p>'
            );
            e.preventDefault();
            e.stopPropagation();
            let url = "request/bal.php";
            // Send the data using post
            var posting = $.post(url, $(this).serialize());
            posting.done(function(data) {
                $("#submitPay").prop("disabled",false);
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
        });
    });
    //show payment history modal
    $("#paymentModal").on("show.bs.modal", function(event) {
        var button = $(event.relatedTarget);
        var modal = $(this);
        var ref = button.data('ref');
        $("#show-response").html('<p class="bg-warning text-white p-2">Please Wait. . . </p>');
        let url = "request/payLoad.php";
        // Send the data using post
        var posting = $.post(url, {
            tid: ref
        });
        posting.done(function(data) {

            $("#show-response").html('');
            let result = JSON.parse(data);
            console.log(result.status)
            if (200 == result.status) {
                var header = '<tr><th>Date</th><th>Amount</th><th>Payement</th></tr>';
                for (var i = 0; i < result.data.length; i++) {
                    var tr = "<tr>";
                    tr += "<td>" + result.data[i].dt + "</td>";
                    tr += "<td>" + result.data[i].amount + "</td>";
                    tr += "<td>" + result.data[i].method + "</td>";
                    tr += "</tr>";
                    header += tr;
                }

                $('#loadPays').append(header);
                return false;
            }
            $("#show-response").html(
                '<p class="bg-danger text-white p-2">' + result.message + "</p>"
            );
        });
    });

    $("#paymentModal").on('hide.bs.modal', function() {
        $('#loadPays').html('');
    });
    </script>
</body>

</html>