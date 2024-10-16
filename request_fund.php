<?php include_once 'functions/Functions.php'; 
$class = new Functions();
if(!($_SESSION['user_id'] && $_SESSION['role_id'])) header('location: logout.php');
include_once 'functions/validate_session.php';
$ref = (filter_has_var(INPUT_GET,'ref')) ? htmlentities(filter_input(INPUT_GET,'ref')) : $_SESSION['user_id'];
$profile = $class->fetch('users_tbl'," WHERE user_id = '$ref'");
$config = $class->fetch('settings');
//new request for fund
$msg = '';
if(filter_has_var(INPUT_POST,'submit')){
    if($_SESSION['role_id'] == 106 || $_SESSION['role_id'] == 103){
        $amount = htmlentities(filter_input(INPUT_POST,'amount'));
        if(is_nan($amount)){
            $msg .= "<p class='text-danger'><i class='fas fa-times'></i> Invalid Amount</p>";
        }else {
            $detail = htmlentities(filter_input(INPUT_POST,'description'));
            $category = trim(htmlentities(filter_input(INPUT_POST,'expenseKategory')));
            $recipient = trim(htmlentities(filter_input(INPUT_POST,'recipient')));
            if(!($detail && $category && $recipient)){
                $msg .= "<p class='text-danger'><i class='fas fa-times'></i> Detail is Required. . . </p>";
            }else {
                $save = $class->store('expenses',['id','amount_requested','created_by','description','recipient','category_id'],
                [$class->generateRandomString(10),$amount,$_SESSION['user_id'],$detail,$recipient,$category]);
                if($save) $msg .= "<p class='text-success'>Fund Request Submitted Successfuly</p>";
            }
        }
    }
}
//new request for fund ends.

$condition = '';
if($_SESSION["role_id"] == 103) $condition .= " AND (e.created_by = '{$_SESSION['user_id']}') ";

if($_GET['date_to'] && $_GET['date_from']){
  //date interval
  $from = (filter_has_var(INPUT_GET, 'date_from')) ? htmlentities(filter_input(INPUT_GET, 'date_from'),ENT_QUOTES) . ' 00:00:00' : '';
  $to = (filter_has_var(INPUT_GET, 'date_to')) ? htmlentities(filter_input(INPUT_GET, 'date_to'),ENT_QUOTES).' 23:59:59' : '';
  $condition .= " AND (t.created_at BETWEEN '$from' AND '$to') ";
}
if($_GET['category_id']){
  $category_id = htmlentities(filter_input(INPUT_GET, 'category_id'),ENT_QUOTES);
  $condition .= " AND (e.category_id = '$category_id') ";
}
$sales = $class->rawQuery("
   SELECT e.amount_requested,e.amount_approved,e.description,e.status,e.created_at,DATE_FORMAT(e.created_at, '%d/%m/%Y') AS created_at,e.recipient,ec.name AS category,
   e.created_by, e.approved_by FROM expenses e LEFT JOIN expense_categories ec ON e.category_id = ec.id
   WHERE 1 = 1 $condition ORDER BY e.created_at DESC
");
$paymentTypes = $class->fetchAll("expense_categories"," WHERE status = 1");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Cashier Expenses - <?= $config->name ?></title>
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
                            <button class="btn btn-pill btn-outline-success" id="exportToExcel">
                                <i class="fa fa-file"></i>&nbsp;Export to Excel
                            </button>
                            <button class="btn btn-pill btn-danger btn-air-danger float-right"
                                data-name="Request New Fund" onclick="displayBlock(this)">
                                <i class="fa fa-plus-circle"></i>&nbsp;Request New Fund
                            </button></a>
                            
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
                                        <label for="sel1">Category</label>
                                        <select name="category_id" class="custom-select">
                                            <option value="">Choose...</option>
                                            <?php
                                                foreach($paymentTypes as $exp): ?>
                                            <option value="<?=$exp->id?>"
                                                <?php if($exp->id == filter_input(INPUT_GET,'category_id')) echo 'selected' ?>>
                                                <?=$exp->name ?></option>
                                            <?php endforeach ?>
                                        </select>
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
                                            <th>Amount</th>
                                            <th>Category</th>
                                            <th>Receiver</th>
                                            <td>Description</td>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                            $i=1;
                                            foreach($sales as $x):
                                            $approvedBy = $class->fetchColumn('users_tbl','full_name','user_id',$x->approved_by) ?>
                                        <tr>
                                            <td><?=$i++?></td>
                                            <td>
                                                <!-- Button to Open the Modal -->
                                                <button type="button"
                                                    class="btn btn-sm <?php if($x->status == 0) {echo 'btn-warning'; }else {echo 'btn-success';} ?>"
                                                    data-toggle="modal" data-target="#payModal"
                                                    data-ramount="<?=$x->amount_requested?>"
                                                    data-aamount="<?=$x->amount_approved?>"
                                                    data-description="<?=$x->description?>"
                                                    data-recipient="<?=$x->recipient?>" data-status="<?=$x->status?>"
                                                    data-drapproval="<?=$approvedBy?>"
                                                    data-dtrequested="<?=$x->created_at?>"
                                                    data-approveddate="<?=$x->approved_at ?>">
                                                    <?=$x->amount_requested ?>
                                                </button>
                                            </td>
                                            <td><?=$x->category?></td>
                                            <td><?=$x->recipient?></td>
                                            <td><?=$class->substrwords($x->description,100) ?></td>
                                            <td><?=$x->created_at?></td>
                                            <!---
                                            <td><?=$x->approved_at?></td>
                                            <td><?=empty($approvedBy) ? $approvedBy : 'NA' ?></td>
                                            ------->
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4 d-none" id="divToHide">
                        <div class="card-body">
                            <form action="<?=htmlentities($_SERVER['PHP_SELF']) ?>" method="POST" id="requestFundForm">
                                <div class="form-group">
                                    <label for="amount">Amount Requested <sup class="text-danger">*</sup></label>
                                    <input type="text" class="form-control" name="amount">
                                </div>
                                <div class="form-group">
                                    <label for="amount">Recipient <sup class="text-danger">*</sup></label>
                                    <input type="text" class="form-control" name="recipient">
                                </div>
                                <div class="form-group">
                                    <label for="amount">Expense Category <sup class="text-danger">*</sup></label>
                                    <select class="form-control" name="expenseKategory">
                                        <option value="">Choose...</option>
                                        <?php $kategories = $class->fetchAll("expense_categories"," WHERE status = 1");
                                        foreach($kategories as $kat): ?>
                                        <option value="<?=$kat->id?>"><?=$kat->name ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="description">Desribe Expense Briefly <sup
                                            class="text-danger">*</sup></label>
                                    <textarea name="description" class="form-control" cols="30" rows="4"></textarea>
                                </div>


                                <div class="text-center"><?=$msg ?></div>
                                <div class="text-center">
                                    <button type="submit" class="btn btn-success" name="submit">Submit</button>
                                </div>
                            </form>
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
    <script src="js/jquery-3.4.1.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/jquery.dataTables.min.js"></script>
    <script src="js/dataTables.bootstrap4.min.js"></script>
    <script src="assets/demo/datatables-demo.js"></script>
    <script src="js/xlsx.full.min.js"></script>
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
        modal.find(".modal-body input#dr-approval").val(button.data('drapproval'));
        modal.find(".modal-body textarea#request-detail").val(button.data('description'));
    });

    window.onload = () => {
        document.getElementById('requestFundForm').addEventListener('submit', (t) => {
            if (confirm("You are about to Request for Fund, Are you sure You want to Continue?") === true) {
                t.currentTarget.submit();
                return false;
            }
            t.preventDefault();
        });
        document.getElementById("exportToExcel").addEventListener('click', (event) => {
            event.preventDefault();
            event.stopImmediatePropagation();
            const d = new Date();
            const table = document.getElementById("dataTable");
            let workbook = XLSX.utils.table_to_book(table, {
                sheet: "sheet1"
            });
            return XLSX.writeFile(workbook, "expense_records_"+d.getTime() + ".xlsx")
        })
    };
    </script>
</body>

</html>