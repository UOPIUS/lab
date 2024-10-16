<?php include_once 'functions/Functions.php'; 
if(!($_SESSION['role_id'] && $_SESSION['user_id'])) header('location: login.php');
$class = new Functions();
$config = $class->fetch('settings');

//date interval
$date_interval = "";
if($_GET['date_to'] && $_GET['date_from']){
    //date interval
    $from = (filter_has_var(INPUT_GET, 'date_from')) ? htmlentities(filter_input(INPUT_GET, 'date_from'),ENT_QUOTES) . ' 00:00:00' : '';
    $to = (filter_has_var(INPUT_GET, 'date_to')) ? htmlentities(filter_input(INPUT_GET, 'date_to'),ENT_QUOTES).' 23:59:59' : '';
    $date_interval = " AND (created_at BETWEEN '$from' AND '$to') ";
}else {
    $date_interval = " AND (created_at BETWEEN '".date('Y-m-d ')." 00:00:00' AND '".date('Y-m-d')." 23:59:59') ";
}

//monthly
$salesM = $class->countAndSumRecord("outstanding_tbl", "amount", " WHERE reversed_flag = 'NO' AND created_at LIKE '%".date('Y-m-')."%'");
//today
$salesT = $class->countAndSumRecord("outstanding_tbl", "amount", " WHERE reversed_flag = 'NO' $date_interval");
//reversals today
$reversalToday = $class->countAndSumRecord("transactions", "amount", " WHERE created_at LIKE '%".date('Y-m-d')."%' AND (status =3)");
//pending today
$pendingT = $class->countAndSumRecord("transactions", "amount", " WHERE created_at LIKE '%".date('Y-m-d')."%' AND (status = 0)");
//Pending Monthly
$pendingMonthly = $class->countAndSumRecord("transactions", "amount", " WHERE created_at LIKE '%".date('Y-m-')."%' AND (status =0)");
//Reversal Monthly
$reversalMonthly = $class->countAndSumRecord("transactions", "amount", " WHERE created_at LIKE '%".date('Y-m-')."%' AND (status =3)");
//pending yearly
$yearlyPending = $class->countAndSumRecord("transactions", "amount", " WHERE created_at LIKE '%".date('Y-')."%' AND (status =0)");
//REversal Yearly
$reversalYearly = $class->countAndSumRecord("transactions", "amount", " WHERE created_at LIKE '%".date('Y-')."%' AND (status =3)");
$cash = $pos = $cheque = $transfer = 0;
$paymentMethod = $class->fetchAll("payment_types"," WHERE status=1");
foreach($paymentMethod as $method):
    if($method->id == '1'){
            # cash code...
            $cash = $class->sumAmountInsideTable("outstanding_tbl", "amount", " WHERE reversed_flag = 'NO' AND (payment_method = '$method->id') AND (created_at LIKE '%".date('Y-m-d')."%')");
    }
    elseif($method->id == '2'){
            # Bank transfer
            $transfer = $class->sumAmountInsideTable("outstanding_tbl", "amount", " WHERE reversed_flag = 'NO' AND (payment_method = '$method->id') AND (created_at LIKE '%".date('Y-m-d')."%')");
    }
    elseif($method->id == '3'){
 # Bank check
 $cheque = $class->sumAmountInsideTable("outstanding_tbl", "amount", " WHERE reversed_flag = 'NO' AND payment_method = '$method->id' AND created_at LIKE '%".date('Y-m-d')."%'");
	}
elseif($method->id == 4){
 # Bank transfer
 $pos = $class->countAndSumRecord("outstanding_tbl", "amount", " WHERE reversed_flag = 'NO' AND payment_method = '$method->id' AND created_at LIKE '%".date('Y-m-d')."%'");
}

endforeach;
//total overall
$sales = $class->countAndSumRecord("outstanding_tbl", "amount"," WHERE reversed_flag = 'NO'");
$today = date('Y-m-d');


//$raw = $class->rawQuery("SELECT * FROM expenses WHERE DATE(expenses.created_at) = '$today'");
//expenses
$expensesT = $class->countAndSumRecord("expenses", "amount_requested", " WHERE created_at LIKE '%".date('Y-m-d')."%'");
$expensesM = $class->countAndSumRecord("expenses", "amount_requested", " WHERE created_at LIKE '%".date('Y-m-')."%'");
$expensesY = $class->countAndSumRecord("expenses", "amount_requested", " WHERE created_at LIKE '%".date('Y-')."%'");

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Account Summary | Dashboard - <?= $config->name ?></title>
    <link href="css/materialdesignicons.min.css" rel="stylesheet">
     <link rel="icon" href="assets/img/logo-circle.png" type="image/png">
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
                        <div class="col-">
                            <h4 class="m-4">Account Transaction Summary</h4>
                        </div>
                        <div class="col-lg-12 mt-4 mb-4">
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
                        </div>
                    </div>
                    <div class="row flex-grow mb-4">
                        <div class="col-md-3 grid-margin stretch-card">
                            <div class="card">

                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-baseline">
                                        <h6 class="card-title mb-0 text-danger">Cash Payment</h6>
                                        <div class="dropdown mb-2">
                                            <button class="btn p-0" type="button" id="dropdownMenuButton"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <img src="/passport/saltcity_cash.png" alt="saltcity">
                                            </button>

                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 col-md-12 col-xl-12">
                                            <h3 class="mb-2 text-danger">
                                                &#x20A6;<?= number_format($cash,2) ?></h3>
                                            <div class="d-flex align-items-baseline">
                                                <p class="text-danger">
                                                    <span></span>
                                                    <i class="fa fa-check-double text-danger"></i>
                                                </p>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-baseline">
                                        <h6 class="card-title mb-0 text-warning">POS payments</h6>
                                        <div class="dropdown mb-2">
                                            <button class="btn p-0" type="button" id="dropdownMenuButton1"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <img src="/passport/saltcity_pos.png" alt="saltcity">
                                            </button>

                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 col-md-12 col-xl-12">
                                            <h3 class="mb-2 text-warning">
                                                &#x20A6;<?= number_format($cheque,2) ?></h3>
                                            <div class="d-flex align-items-baseline">
                                                <p class="text-warning">
                                                    <span></span>
                                                    <i class="fa fa-check-circle text-warning"></i>
                                                </p>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-baseline">
                                        <h6 class="card-title mb-0 text-info">Cheque Payments</h6>
                                        <div class="dropdown mb-2">
                                            <button class="btn p-0" type="button" id="dropdownMenuButton2"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                 <img src="/passport/saltcity_cheque.png" alt="saltcity">
                                            </button>

                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 col-md-12 col-xl-12">
                                            <h3 class="mb-2 text-info">
                                                &#x20A6;<?= number_format($chequ,2) ?></h3>
                                            <div class="d-flex align-items-baseline">
                                                <p class="text-info">
                                                    <i class="fa fa-check-double text-danger"></i>
                                                </p>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="col-md-3 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-baseline">
                                        <h6 class="card-title mb-0 text-success">Bank Transfer</h6>
                                        <div class="dropdown mb-2">
                                            <button class="btn p-0" type="button" id="dropdownMenuButton2"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                 <img src="/passport/saltcity_transfer.png" alt="saltcity">
                                            </button>

                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 col-md-12 col-xl-12">
                                            <h3 class="mb-2 text-success">
                                                &#x20A6;<?= number_format($transfer,2) ?></h3>
                                            <div class="d-flex align-items-baseline">
                                                <p class="text-success">
                                                    <i class="fa fa-check-circle text-success"></i>
                                                </p>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Row -->

                    <div class="row flex-grow mb-4">
                        <div class="col-md-3 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-baseline">
                                        <h6 class="card-title mb-0 text-primary">Transactions Today</h6>
                                        <div class="dropdown mb-2">
                                            <button class="btn p-0" type="button" id="dropdownMenuButton"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fa fa-certificate fa-2x text-primary"></i>
                                            </button>

                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 col-md-12 col-xl-12">
                                            <h3 class="mb-2 text-primary">
                                                &#x20A6;<?= number_format($salesT->amount,2) ?></h3>
                                            <div class="d-flex align-items-baseline">
                                                <p class="text-default">

                                                    <i class="fa fa-check-double text-primary"></i>
                                                </p>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-baseline">
                                        <h6 class="card-title mb-0 text-success">No. of Transactions</h6>
                                        <div class="dropdown mb-2">
                                            <button class="btn p-0" type="button" id="dropdownMenuButton1"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fa fa-calendar-alt fa-2x text-success"></i>
                                            </button>

                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 col-md-12 col-xl-12">
                                            <h3 class="mb-2 text-success">
                                                <?= number_format($salesT->numerate) ?></h3>
                                            <div class="d-flex align-items-baseline">
                                                <p class="text-success">
                                                    <span><?=$transactionsToday->numerate ?></span>
                                                    <i class="fa fa-check-circle text-success"></i>
                                                </p>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-baseline">
                                        <h6 class="card-title mb-0 text-warning">Pending Transactions</h6>
                                        <div class="dropdown mb-2">
                                            <button class="btn p-0" type="button" id="dropdownMenuButton2"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fa fa-cogs fa-2x text-warning"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 col-md-12 col-xl-12">
                                            <h3 class="mb-2 text-warning">
                                                <?= number_format($pendingT->amount,2) ?></h3>
                                            <div class="d-flex align-items-baseline">
                                                <p class="text-warning">
                                                    <span><?= $pendingT->numerate ?></span>
                                                    <i class="fa fa-clone text-warning"></i>
                                                </p>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-baseline">
                                        <h6 class="card-title mb-0 text-warning"><?php date('Y-m-d')?> Expenses</h6>
                                        <div class="dropdown mb-2">
                                            <button class="btn p-0" type="button" id="dropdownMenuButton2"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fa fa-cogs fa-2x text-warning"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 col-md-12 col-xl-12">
                                            <h3 class="mb-2 text-warning">
                                                <?= number_format($expensesT->amount,2) ?></h3>
                                            <div class="d-flex align-items-baseline">
                                                <p class="text-warning">
                                                    <span><?= number_format($expensesT->numerate,2) ?></span>
                                                    <i class="fa fa-clone text-warning"></i>
                                                </p>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row flex-grow mb-4">
                        <div class="col-md-4 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-baseline">
                                        <h6 class="card-title mb-0 text-primary"><?=date('Y-m-d') ?> Reversal</h6>
                                        <div class="dropdown mb-2">
                                            <button class="btn p-0" type="button" id="dropdownMenuButton"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fa fa-certificate fa-2x text-primary"></i>
                                            </button>

                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 col-md-12 col-xl-12">
                                            <h3 class="mb-2 text-primary">
                                                &#x20A6;<?= number_format($reversalToday->amount,2) ?></h3>
                                            <div class="d-flex align-items-baseline">
                                                <p class="text-default">
                                                    <span class="text-danger"><?=$reversalToday->numerate ?></span>
                                                    <i class="fa fa-check-double text-primary"></i>
                                                </p>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-baseline">
                                        <h6 class="card-title mb-0 text-success"><?=date('F') ?> Reversal</h6>
                                        <div class="dropdown mb-2">
                                            <button class="btn p-0" type="button" id="dropdownMenuButton1"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fa fa-calendar-alt fa-2x text-success"></i>
                                            </button>

                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 col-md-12 col-xl-12">
                                            <h3 class="mb-2 text-success">
                                                <?= number_format($reversalMonthly->amount,2) ?></h3>
                                            <div class="d-flex align-items-baseline">
                                                <p class="text-success">
                                                    <span><?=$reversalMonthly->numerate ?></span>
                                                    <i class="fa fa-check-circle text-success"></i>
                                                </p>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-baseline">
                                        <h6 class="card-title mb-0 text-warning">Reversal for Year <?= date('Y')?></h6>
                                        <div class="dropdown mb-2">
                                            <button class="btn p-0" type="button" id="dropdownMenuButton2"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fa fa-cogs fa-2x text-warning"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 col-md-12 col-xl-12">
                                            <h3 class="mb-2 text-warning">
                                                <?= number_format($reversalYearly->amount,2) ?></h3>
                                            <div class="d-flex align-items-baseline">
                                                <p class="text-warning">
                                                    <span><?= $reversalYearly->numerate ?></span>
                                                    <i class="fa fa-clone text-warning"></i>
                                                </p>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                       
                    </div>

                    <div class="row flex-grow mb-4">
                        <div class="col-md-3 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-baseline">
                                        <h6 class="card-title mb-0 text-primary"><?=date('F')?> Transactions</h6>
                                        <div class="dropdown mb-2">
                                            <button class="btn p-0" type="button" id="dropdownMenuButton"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fa fa-certificate fa-2x text-primary"></i>
                                            </button>

                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 col-md-12 col-xl-12">
                                            <h3 class="mb-2 text-primary">
                                                &#x20A6;<?= number_format($salesM->amount,2) ?></h3>
                                            <div class="d-flex align-items-baseline">
                                                <p class="text-default">
                                                    <span></span>
                                                    <i class="fa fa-check-double text-primary"></i>
                                                </p>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-baseline">
                                        <h6 class="card-title mb-0 text-success">No. of Transactions</h6>
                                        <div class="dropdown mb-2">
                                            <button class="btn p-0" type="button" id="dropdownMenuButton1"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fa fa-calendar-alt fa-2x text-success"></i>
                                            </button>

                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 col-md-12 col-xl-12">
                                            <h3 class="mb-2 text-success">
                                                <?= number_format($salesM->numerate,0) ?></h3>
                                            <div class="d-flex align-items-baseline">
                                                <p class="text-success">
                                                    <span></span>
                                                    <i class="fa fa-check-circle text-success"></i>
                                                </p>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-baseline">
                                        <h6 class="card-title mb-0 text-warning"><?= date('F') ?> Pending</h6>
                                        <div class="dropdown mb-2">
                                            <button class="btn p-0" type="button" id="dropdownMenuButton2"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fa fa-cogs fa-2x text-warning"></i>
                                            </button>

                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 col-md-12 col-xl-12">
                                            <h3 class="mb-2 text-warning">
                                                <?= number_format($pendingMonthly->amount,2) ?></h3>
                                            <div class="d-flex align-items-baseline">
                                                <p class="text-warning">
                                                    <span><?= $pendingMonthly->numerate ?></span>
                                                    <i class="fa fa-clone text-warning"></i>
                                                </p>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Reversal -->
                        <div class="col-md-3 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-baseline">
                                        <h6 class="card-title mb-0 text-warning"><?=date('F') ?> Expenses</h6>
                                        <div class="dropdown mb-2">
                                            <button class="btn p-0" type="button" id="dropdownMenuButton2"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fa fa-cogs fa-2x text-warning"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 col-md-12 col-xl-12">
                                            <h3 class="mb-2 text-warning">
                                                <?= number_format($expensesM->amount,2) ?></h3>
                                            <div class="d-flex align-items-baseline">
                                                <p class="text-warning">
                                                    <span><?= number_format($expensesM->numerate,2) ?></span>
                                                    <i class="fa fa-clone text-warning"></i>
                                                </p>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row flex-grow mb-4">
                        <div class="col-md-3 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-baseline">
                                        <h6 class="card-title mb-0 text-primary"><?=date('Y') ?> Transactions</h6>
                                        <div class="dropdown mb-2">
                                            <button class="btn p-0" type="button" id="dropdownMenuButton"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fa fa-certificate fa-2x text-primary"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 col-md-12 col-xl-12">
                                            <h3 class="mb-2 text-primary">
                                                &#x20A6;<?= number_format($sales->amount,2) ?></h3>
                                            <div class="d-flex align-items-baseline">
                                                <p class="text-default">
                                                    <span></span>
                                                    <i class="fa fa-check-double text-primary"></i>
                                                </p>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-baseline">
                                        <h6 class="card-title mb-0 text-success">No. of Transactions</h6>
                                        <div class="dropdown mb-2">
                                            <button class="btn p-0" type="button" id="dropdownMenuButton1"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fa fa-calendar-alt fa-2x text-success"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 col-md-12 col-xl-12">
                                            <h3 class="mb-2 text-success">
                                                <?= number_format($sales->numerate,0) ?></h3>
                                            <div class="d-flex align-items-baseline">
                                                <p class="text-success">

                                                    <i class="fa fa-check-circle text-success"></i>
                                                </p>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-baseline">
                                        <h6 class="card-title mb-0 text-warning">Pending Transactions</h6>
                                        <div class="dropdown mb-2">
                                            <button class="btn p-0" type="button" id="dropdownMenuButton2"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fa fa-cogs fa-2x text-warning"></i>
                                            </button>

                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 col-md-12 col-xl-12">
                                            <h3 class="mb-2 text-warning">
                                                <?= number_format($yearlyPending->numerate,2) ?></h3>
                                            <div class="d-flex align-items-baseline">
                                                <p class="text-warning">
                                                    <span><?= 0 ?></span>
                                                    <i class="fa fa-clone text-warning"></i>
                                                </p>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Reversal -->
                        <div class="col-md-3 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-baseline">
                                        <h6 class="card-title mb-0 text-warning"><?=date('Y')?> Expenses</h6>
                                        <div class="dropdown mb-2">
                                            <button class="btn p-0" type="button" id="dropdownMenuButton2"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fa fa-cogs fa-2x text-warning"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 col-md-12 col-xl-12">
                                            <h3 class="mb-2 text-warning">
                                                <?= number_format($expensesY->amount,2) ?></h3>
                                            <div class="d-flex align-items-baseline">
                                                <p class="text-warning">
                                                    <span><?= number_format($expensesY->numerate,2) ?></span>
                                                    <i class="fa fa-clone text-warning"></i>
                                                </p>
                                            </div>
                                        </div>

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
    <script src="js/jquery-3.4.1.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/jquery.dataTables.min.js"></script>
    <script src="js/dataTables.bootstrap4.min.js"></script>
    <script src="assets/demo/datatables-demo.js"></script>
    <script src="js/scripts.js"></script>
    <script src="js/tejiri.js"></script>
</body>

</html>