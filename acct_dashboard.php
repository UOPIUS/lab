<?php include_once 'functions/Functions.php'; 
if(!($_SESSION['role_id'] && $_SESSION['user_id'])) header('location: login.php');
$class = new Functions();
$ref = (filter_has_var(INPUT_GET,'ref')) ? htmlentities(filter_input(INPUT_GET,'ref')) : $_SESSION['user_id'];
$config = $class->fetch('settings');
$date = date("Y-m-d");
$sql = $class->rawQuery("SELECT SUM(amount) total, payment_method FROM `outstanding_tbl` WHERE (created_at LIKE '%$date%') AND (reversed_flag = 'NO') GROUP BY payment_method ORDER BY payment_method DESC",5);
$total = 0;
$kount = 0;
foreach($sql as $row){
    switch($row->payment_method){
        case 1:
            $cash = $row->total;
            break;
        case 2:
            $transfer = $row->total;
            break;
        case 3: 
            $pos = $row->total;
            break;
        default:
            $cheque = $row->total;
    }
    $total += $row->total;
}
$expense = $class->rawQuery("SELECT SUM(amount_requested) total FROM `expenses` WHERE (created_at LIKE '%$date%')",1);
;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Cashier Dashboard - <?= $config->name ?></title>
    <link href="css/materialdesignicons.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="assets/img/favicon.png" />
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
                    <h4 class="m-4">Welcome <?=$_SESSION['name'] ?>,</h4>

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
                                            &#x20A6;<?= $pos ?? 0 ?></h3>
                                            
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
                                            &#x20A6;<?= number_format($cheque,2) ?></h3>
                                            
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
                                            
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="row flex-grow mb-4">
                        <div class="col-md-6 grid-margin stretch-card">
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
                                                &#x20A6;<?= number_format($total) ?></h3>
                                            
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
                                            <h3 class="mb-2 text-success"><?=$kount ?></h3>
                                            
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-baseline">
                                        <h6 class="card-title mb-0 text-warning">Expenses</h6>
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
                                                <?= number_format($expense->total,2) ?></h3>
                                            
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!--<div class="row flex-grow mb-4">-->
                    <!--    <div class="col-md-3 grid-margin stretch-card">-->
                    <!--        <div class="card">-->
                    <!--            <div class="card-body">-->
                    <!--                <div class="d-flex justify-content-between align-items-baseline">-->
                    <!--                    <h6 class="card-title mb-0 text-primary">Transactions this Month</h6>-->
                    <!--                    <div class="dropdown mb-2">-->
                    <!--                        <button class="btn p-0" type="button" id="dropdownMenuButton"-->
                    <!--                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">-->
                    <!--                            <i class="fa fa-certificate fa-2x text-primary"></i>-->
                    <!--                        </button>-->

                    <!--                    </div>-->
                    <!--                </div>-->
                    <!--                <div class="row">-->
                    <!--                    <div class="col-12 col-md-12 col-xl-12">-->
                    <!--                        <h3 class="mb-2 text-primary">-->
                    <!--                            &#x20A6;<?= number_format($salesM->amount,2) ?></h3>-->
                    <!--                        <div class="d-flex align-items-baseline">-->
                    <!--                            <p class="text-default">-->
                    <!--                                <span></span>-->
                    <!--                                <i class="fa fa-check-double text-primary"></i>-->
                    <!--                            </p>-->
                    <!--                        </div>-->
                    <!--                    </div>-->

                    <!--                </div>-->
                    <!--            </div>-->
                    <!--        </div>-->
                    <!--    </div>-->
                    <!--    <div class="col-md-3 grid-margin stretch-card">-->
                    <!--        <div class="card">-->
                    <!--            <div class="card-body">-->
                    <!--                <div class="d-flex justify-content-between align-items-baseline">-->
                    <!--                    <h6 class="card-title mb-0 text-success">No. of Transactions</h6>-->
                    <!--                    <div class="dropdown mb-2">-->
                    <!--                        <button class="btn p-0" type="button" id="dropdownMenuButton1"-->
                    <!--                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">-->
                    <!--                            <i class="fa fa-calendar-alt fa-2x text-success"></i>-->
                    <!--                        </button>-->

                    <!--                    </div>-->
                    <!--                </div>-->
                    <!--                <div class="row">-->
                    <!--                    <div class="col-12 col-md-12 col-xl-12">-->
                    <!--                        <h3 class="mb-2 text-success">-->
                    <!--                            <?= number_format($salesM->numerate,0) ?></h3>-->
                    <!--                        <div class="d-flex align-items-baseline">-->
                    <!--                            <p class="text-success">-->
                    <!--                                <span></span>-->
                    <!--                                <i class="fa fa-check-circle text-success"></i>-->
                    <!--                            </p>-->
                    <!--                        </div>-->
                    <!--                    </div>-->

                    <!--                </div>-->
                    <!--            </div>-->
                    <!--        </div>-->
                    <!--    </div>-->
                    <!--    <div class="col-md-3 grid-margin stretch-card">-->
                    <!--        <div class="card">-->
                    <!--            <div class="card-body">-->
                    <!--                <div class="d-flex justify-content-between align-items-baseline">-->
                    <!--                    <h6 class="card-title mb-0 text-warning">Pending Trans.</h6>-->
                    <!--                    <div class="dropdown mb-2">-->
                    <!--                        <button class="btn p-0" type="button" id="dropdownMenuButton2"-->
                    <!--                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">-->
                    <!--                            <i class="fa fa-cogs fa-2x text-warning"></i>-->
                    <!--                        </button>-->

                    <!--                    </div>-->
                    <!--                </div>-->
                    <!--                <div class="row">-->
                    <!--                    <div class="col-12 col-md-12 col-xl-12">-->
                    <!--                        <h3 class="mb-2 text-warning">-->
                    <!--                            <?= number_format($pendingOrders->amount,2) ?></h3>-->
                    <!--                        <div class="d-flex align-items-baseline">-->
                    <!--                            <p class="text-warning">-->
                    <!--                                <span><?= 0 ?></span>-->
                    <!--                                <i class="fa fa-clone text-warning"></i>-->
                    <!--                            </p>-->
                    <!--                        </div>-->
                    <!--                    </div>-->

                    <!--                </div>-->
                    <!--            </div>-->
                    <!--        </div>-->
                    <!--    </div>-->
                    <!--    <div class="col-md-3 grid-margin stretch-card">-->
                    <!--        <div class="card">-->
                    <!--            <div class="card-body">-->
                    <!--                <div class="d-flex justify-content-between align-items-baseline">-->
                    <!--                    <h6 class="card-title mb-0 text-warning"><?=date('M') ?> Expenses</h6>-->
                    <!--                    <div class="dropdown mb-2">-->
                    <!--                        <button class="btn p-0" type="button" id="dropdownMenuButton2"-->
                    <!--                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">-->
                    <!--                            <i class="fa fa-cogs fa-2x text-warning"></i>-->
                    <!--                        </button>-->

                    <!--                    </div>-->
                    <!--                </div>-->
                    <!--                <div class="row">-->
                    <!--                    <div class="col-12 col-md-12 col-xl-12">-->
                    <!--                        <h3 class="mb-2 text-warning">-->
                    <!--                            <?= number_format($expensesM->amount,2) ?></h3>-->
                    <!--                        <div class="d-flex align-items-baseline">-->
                    <!--                            <p class="text-warning">-->
                    <!--                                <span><?= 0 ?></span>-->
                    <!--                                <i class="fa fa-clone text-warning"></i>-->
                    <!--                            </p>-->
                    <!--                        </div>-->
                    <!--                    </div>-->

                    <!--                </div>-->
                    <!--            </div>-->
                    <!--        </div>-->
                    <!--    </div>-->
                    <!--</div>-->

                    <!--<div class="row flex-grow mb-4">-->
                    <!--    <div class="col-md-3 grid-margin stretch-card">-->
                    <!--        <div class="card">-->
                    <!--            <div class="card-body">-->
                    <!--                <div class="d-flex justify-content-between align-items-baseline">-->
                    <!--                    <h6 class="card-title mb-0 text-primary">Transactions this Year</h6>-->
                    <!--                    <div class="dropdown mb-2">-->
                    <!--                        <button class="btn p-0" type="button" id="dropdownMenuButton"-->
                    <!--                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">-->
                    <!--                            <i class="fa fa-certificate fa-2x text-primary"></i>-->
                    <!--                        </button>-->
                    <!--                    </div>-->
                    <!--                </div>-->
                    <!--                <div class="row">-->
                    <!--                    <div class="col-12 col-md-12 col-xl-12">-->
                    <!--                        <h3 class="mb-2 text-primary">-->
                    <!--                            &#x20A6;<?= number_format($sales->amount,2) ?></h3>-->
                    <!--                        <div class="d-flex align-items-baseline">-->
                    <!--                            <p class="text-default">-->
                    <!--                                <span></span>-->
                    <!--                                <i class="fa fa-check-double text-primary"></i>-->
                    <!--                            </p>-->
                    <!--                        </div>-->
                    <!--                    </div>-->

                    <!--                </div>-->
                    <!--            </div>-->
                    <!--        </div>-->
                    <!--    </div>-->
                    <!--    <div class="col-md-3 grid-margin stretch-card">-->
                    <!--        <div class="card">-->
                    <!--            <div class="card-body">-->
                    <!--                <div class="d-flex justify-content-between align-items-baseline">-->
                    <!--                    <h6 class="card-title mb-0 text-success">No. of Transactions</h6>-->
                    <!--                    <div class="dropdown mb-2">-->
                    <!--                        <button class="btn p-0" type="button" id="dropdownMenuButton1"-->
                    <!--                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">-->
                    <!--                            <i class="fa fa-calendar-alt fa-2x text-success"></i>-->
                    <!--                        </button>-->
                    <!--                    </div>-->
                    <!--                </div>-->
                    <!--                <div class="row">-->
                    <!--                    <div class="col-12 col-md-12 col-xl-12">-->
                    <!--                        <h3 class="mb-2 text-success">-->
                    <!--                            <?= number_format($sales->numerate,0) ?></h3>-->
                    <!--                        <div class="d-flex align-items-baseline">-->
                    <!--                            <p class="text-success">-->

                    <!--                                <i class="fa fa-check-circle text-success"></i>-->
                    <!--                            </p>-->
                    <!--                        </div>-->
                    <!--                    </div>-->

                    <!--                </div>-->
                    <!--            </div>-->
                    <!--        </div>-->
                    <!--    </div>-->
                    <!--    <div class="col-md-3 grid-margin stretch-card">-->
                    <!--        <div class="card">-->
                    <!--            <div class="card-body">-->
                    <!--                <div class="d-flex justify-content-between align-items-baseline">-->
                    <!--                    <h6 class="card-title mb-0 text-warning">Pending Trans.</h6>-->
                    <!--                    <div class="dropdown mb-2">-->
                    <!--                        <button class="btn p-0" type="button" id="dropdownMenuButton2"-->
                    <!--                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">-->
                    <!--                            <i class="fa fa-cogs fa-2x text-warning"></i>-->
                    <!--                        </button>-->

                    <!--                    </div>-->
                    <!--                </div>-->
                    <!--                <div class="row">-->
                    <!--                    <div class="col-12 col-md-12 col-xl-12">-->
                    <!--                        <h3 class="mb-2 text-warning">-->
                    <!--                            <?= number_format($pendingY->amount,2) ?></h3>-->
                    <!--                        <div class="d-flex align-items-baseline">-->
                    <!--                            <p class="text-warning">-->
                    <!--                                <span><?= 0 ?></span>-->
                    <!--                                <i class="fa fa-clone text-warning"></i>-->
                    <!--                            </p>-->
                    <!--                        </div>-->
                    <!--                    </div>-->

                    <!--                </div>-->
                    <!--            </div>-->
                    <!--        </div>-->
                    <!--    </div>-->
                    <!--    <div class="col-md-3 grid-margin stretch-card">-->
                    <!--        <div class="card">-->
                    <!--            <div class="card-body">-->
                    <!--                <div class="d-flex justify-content-between align-items-baseline">-->
                    <!--                    <h6 class="card-title mb-0 text-warning"><?= date('Y') ?> Expenses</h6>-->
                    <!--                    <div class="dropdown mb-2">-->
                    <!--                        <button class="btn p-0" type="button" id="dropdownMenuButton2"-->
                    <!--                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">-->
                    <!--                            <i class="fa fa-cogs fa-2x text-warning"></i>-->
                    <!--                        </button>-->

                    <!--                    </div>-->
                    <!--                </div>-->
                    <!--                <div class="row">-->
                    <!--                    <div class="col-12 col-md-12 col-xl-12">-->
                    <!--                        <h3 class="mb-2 text-warning">-->
                    <!--                            <?= number_format($expensesY->amount,2) ?></h3>-->
                    <!--                        <div class="d-flex align-items-baseline">-->
                    <!--                            <p class="text-warning">-->
                    <!--                                <span><?= 0 ?></span>-->
                    <!--                                <i class="fa fa-clone text-warning"></i>-->
                    <!--                            </p>-->
                    <!--                        </div>-->
                    <!--                    </div>-->

                    <!--                </div>-->
                    <!--            </div>-->
                    <!--        </div>-->
                    <!--    </div>-->
                    <!--</div>-->


                   

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