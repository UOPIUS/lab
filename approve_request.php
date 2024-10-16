<?php include_once 'functions/Functions.php'; 
$class = new Functions();
if( $_SESSION['role_id'] != 106) header('location: logout.php');
include_once 'functions/validate_session.php';
$config = $class->fetch('settings');
//reject request
if (NULL !== filter_input(INPUT_GET, 'id')) {
    $id = htmlentities($_REQUEST['id'],ENT_QUOTES);
    $class->connect()->query("UPDATE expenses SET status = 2,approved_by = '{$_SESSION['user_id']}',approved_at = '".date('Y-m-d H:m:s')."' WHERE id = '$id'");
}

$condition = '';

if($_GET['date_to'] && $_GET['date_from']){
  //date interval
  $from = (filter_has_var(INPUT_GET, 'date_from')) ? htmlentities(filter_input(INPUT_GET, 'date_from'),ENT_QUOTES) . ' 00:00:00' : '';
  $to = (filter_has_var(INPUT_GET, 'date_to')) ? htmlentities(filter_input(INPUT_GET, 'date_to'),ENT_QUOTES).' 23:59:59' : '';
  $condition .= " AND (t.created_at BETWEEN '$from' AND '$to') ";
}
if($_GET['trans_type']){
  $gateway = htmlentities(filter_input(INPUT_GET, 'trans_type'),ENT_QUOTES);
  $condition .= " AND (t.payment_type = '$gateway') ";
}
$sales = $class->rawQuery("
   SELECT e.id,e.amount_requested,e.amount_approved,e.created_by, e.approved_by,e.description,e.id,e.status,
   e.created_at,e.approved_at,e.recipient,e.created_by, e.approved_by  FROM expenses e
   WHERE 1 = 1 $condition ORDER BY e.created_at DESC
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
    <title>Doctor Dashboard | Expenses - <?= $config->name ?></title>
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
                    <h4 class="mt-4">Expense History</h4>
                    <div class="card mb-4">
                        <div class="card-header">

                            <i class="fas fa-table mr-1"></i>List of Expenses Incurred
                        </div>
                        <div class="card-body" id="divToShow">
                            <form class="" action="<?= htmlentities($_SERVER['PHP_SELF']) ?>" method="get">
                                <div class="form-row">
                                    <div class="form-group col-md-3">
                                        <label for="sel1">Date From</label>
                                        <input type="date" class="custom-select mr-sm-2" data-provide="datepicker"
                                            placeholder="Date From" data-date-format="yyyy-mm-dd" name="date_from"
                                            value="<?php echo ($_GET['date_from']) ?? '' ?>">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="sel1">Date To</label>
                                        <input type="date" class="custom-select mr-sm-2" data-provide="datepicker"
                                            placeholder="Date To" data-date-format="yyyy-mm-dd" name="date_to"
                                            value="<?= filter_input(INPUT_GET, 'date_to') ?>">
                                    </div>


                                    <div class="form-group col-md-3">
                                        <label>Search Record</label>
                                        <input type="submit" class="form-control btn btn-success btn-sm"
                                            value="Search Records">
                                    </div>
                                </div>
                            </form>
                            <div class="table-responsive mt-4">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Amount Requested</th>
                                            <th>Amount Approved</th>
                                            <th>Receiver</th>
                                            <th>Date Requested</th>
                                            <th>Cashier</th>
                                            <th>Date Approved</th>
                                            <th>Approved By</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                            $i=1;
                                            foreach($sales as $x):
                                                $approvedBy = $class->fetchColumn('users_tbl','full_name','user_id',$x->approved_by);
                                                $createdBy  = $class->fetchColumn('users_tbl','full_name','user_id',$x->created_by);
                                                $ref = $class->simple_encrypt($x->id);
                                             ?>
                                        <tr>
                                            <td><?=$i++?></td>
                                            <td>
                                                <!-- Button to Open the Modal -->
                                                <button type="button" class="btn btn-sm btn-link" data-toggle="modal"
                                                    data-target="#payModal" data-ramount="<?=$x->amount_requested?>"
                                                    data-aamount="<?=$x->amount_approved?>"
                                                    data-description="<?=$x->description?>"
                                                    data-recipient="<?=$x->recipient?>" data-status="<?=$x->status?>"
                                                    data-drapproval="<?=$approvedBy ?>" data-rcashier="<?=$createdBy ?>"
                                                    data-dtrequested="<?=$x->created_at?>"
                                                    data-approveddate="<?=$x->approved_at ?>">
                                                    <?=$x->amount_requested ?>
                                                </button>
                                            </td>
                                            <td><?=$x->amount_approved?></td>
                                            <td><?=$x->recipient?></td>
                                            <td><?=$x->created_at?></td>
                                            <td><?=$createdBy ?></td>
                                            <td><?=$x->approved_at?></td>
                                            <td><?=$approvedBy?></td>
                                            <td>
                                                <?php $status = $x->status;
                                                 if($status == 0): ?>
                                                <button type="button" class="btn btn-sm btn-warning" data-toggle="modal"
                                                    data-target="#approveReq" data-ramount="<?=$x->amount_requested?>"
                                                    data-aamount="<?=$x->amount_approved?>"
                                                    data-description="<?=$x->description?>"
                                                    data-recipient="<?=$x->recipient?>" data-status="<?=$x->status?>"
                                                    data-drapproval="<?=$approvedBy ?>" data-rcashier="<?=$createdBy ?>"
                                                    data-dtrequested="<?=$x->created_at?>"
                                                    data-apprid="<?=$x->id ?>"
                                                    data-approveddate="<?=$x->approved_at ?>">
                                                    <i class="fa fa-bell"></i> Respond
                                                </button>
                                                <?php elseif($status == 2): echo "<a class='btn-danger btn-sm btn'><i class='fas fa-times-circle'></i> Rejected</a>";
                                                elseif($status == 1): echo "<button class='btn btn-success btn-sm'><i class='fas fa-check-circle'></i> Approved</button>";
                                             endif; ?>
                                            </td>

                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
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
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">Fund Request Detail</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                    <form id="payForm" method="POST">
                        <div class="form-group">
                            <label for="amount">Requested Amount</label>
                            <input type="text" class="form-control" id="namount" readonly name="namount">
                        </div>
                        <div class="form-group">
                            <label for="amount">Approved Amount</label>
                            <input type="text" class="form-control" id="aamount" readonly>
                        </div>
                        <div class="form-group">
                            <label>Date Requested</label>
                            <input type="text" class="form-control" id="dt-requested" readonly>
                        </div>
                        <div class="form-group">
                            <label>Date Approved</label>
                            <input type="text" class="form-control" id="dt-approved">
                        </div>
                        <div class="form-group">
                            <label>Recipient</label>
                            <input type="text" class="form-control" id="receiver">
                        </div>
                        <div class="form-group">
                            <label>Doctor Who Approved</label>
                            <input type="text" class="form-control" id="dr-approved">
                        </div>
                        <div class="form-group">
                            <label>Detail of Expense</label>
                            <textarea id="request-detail" cols="30" rows="4" class="form-control"></textarea>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Attempt to Approve Req. Modal-->
    <div class="modal" id="approveReq">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">Expense Request Approval</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <!-- Modal body -->
                <div class="modal-body mb-4">
                    <form id="approveReqForm" method="POST">
                        <div class="form-group">
                            <label for="amount">Requested Amount</label>
                            <input type="text" class="form-control" readonly name="n_amount">
                            <input type="hidden" name="txref">
                        </div>
                        <div class="form-group">
                            <label for="amount">Approved Amount</label>
                            <input type="text" class="form-control" name="a_amount">
                        </div>
                        <div class="form-group">
                            <label>Date Requested</label>
                            <input type="text" class="form-control" name="dt_req" readonly>
                        </div>

                        <div class="form-group">
                            <label>Recipient</label>
                            <input type="text" class="form-control" name="n_recipient">
                        </div>

                        <div class="form-group">
                            <label>Detail of Expense</label>
                            <textarea id="request-detail" cols="30" rows="4" class="form-control" name="request_detail"></textarea>
                        </div>
                        <div id="edit-response" class="text-center"></div>
                        <div class="text-center">
                            <button class="btn btn-success btn-sm">Approve Expense
                                <i class="fas fa-check-circle"></i>
                            </button>
                            <a class="btn btn-danger btn-sm">Reject <i class="fas fa-times-circle"></i></a>

                        </div>

                    </form>
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
        modal.find(".modal-body input#namount").val(button.data('ramount'));
        modal.find(".modal-body input#aamount").val(button.data('aamount'));
        modal.find(".modal-body input#receiver").val(button.data('recipient'));
        modal.find(".modal-body input#dt-requested").val(button.data('dtrequested'));
        modal.find(".modal-body input#dt-approved").val(button.data('approveddate'));
        modal.find(".modal-body input#dr-approved").val(button.data('drapproval'));
        modal.find(".modal-body textarea#request-detail").val(button.data('description'));
    });
    //update job category
    $("#approveReq").on("show.bs.modal", function(event) {
        var button = $(event.relatedTarget);
        var modal = $(this);
        modal.find(".modal-body input[name=n_amount]").val(button.data('ramount'));
        modal.find(".modal-body input[name=a_amount]").val(button.data('ramount'));
        modal.find(".modal-body input[name=n_recipient]").val(button.data('recipient'));
        modal.find(".modal-body input[name=dt_req]").val(button.data('dtrequested'));
        modal.find(".modal-body input#dr-approval").val(button.data('drapproval'));
        modal.find(".modal-body input[name=txref]").val(button.data('apprid'));
        modal.find(".modal-body textarea#request-detail").val(button.data('description'));

        //prepare for reject too.
        location.toString().replace(location.search, "");
        const rejectTarget = modal.find(".modal-body a")[0];
        rejectTarget.addEventListener('click',function(tt){
            //build url
            const newURL = location.toString().replace(location.search, "")+'?id='+modal.find(".modal-body input[name=txref]").val();
            console.debug(newURL);
            window.location.href = newURL;
        });

        $("#approveReqForm").submit(function(e) {
            const responseDiv = $('#edit-response')
            responseDiv.fadeIn();
            responseDiv.html(
                '<p class="bg-warning text-white p-2"><i class="fa fa-cog fa-spin"></i>Please Wait. . .</p>'
            ).fadeOut(3000);
           
            let url = "request/appr_requ.php";
            // Send the data using post
            var posting = $.post(url, $(this).serialize());
            posting.done(function(data) {
                let result = JSON.parse(data);
                if (200 == result.status) {
                    responseDiv.html(
                        '<p class="bg-success text-white p-2">' + result.message + "</p>"
                    ).fadeOut(3000);
                    window.location.reload(true);
                    return;
                }
                responseDiv.html(
                    '<p class="bg-danger text-white p-2">' + result.message + "</p>"
                ).fadeOut(3000);
            });
            e.preventDefault();
        });
    });
    </script>
</body>

</html>