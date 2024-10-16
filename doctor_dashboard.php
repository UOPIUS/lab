<?php include_once 'functions/Functions.php'; 
$class = new Functions();

if(!(106 == $_SESSION['role_id'] && $_SESSION['user_id'])) header('location: logout.php');
include_once 'functions/validate_session.php';

$config = $class->fetch('settings');
$metrix = $class->countAndSumRecord('transactions','amount'," WHERE status = 1");
$pending = $class->countAndSumRecord('transactions','amount'," WHERE status = 0");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Doctor Dashboard - <?= $config->name ?></title>
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
                    <h1 class="mt-4">Dashboard</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item active">Dashboard Report</li>
                    </ol>
                    <div class="row">
                        <div class="col-12 col-xl-12 stretch-card mb-4">
                            <div class="row flex-grow">
                                <div class="col-md-3 grid-margin stretch-card">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-baseline">
                                                <h6 class="card-title mb-0 text-primary">No. of Test Processed</h6>
                                                <div class="dropdown mb-2">
                                                    <button class="btn p-0" type="button" id="dropdownMenuButton"
                                                        data-toggle="dropdown" aria-haspopup="true"
                                                        aria-expanded="false">
                                                        <i class="fa fa-certificate text-primary"></i>
                                                    </button>

                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12 col-md-12 col-xl-12">
                                                    <h3 class="mb-2 text-primary">
                                                        <?= number_format($metrix->numerate,0) ?></h3>
                                                    <div class="d-flex align-items-baseline">
                                                        <p class="text-default">
                                                            <span><?= $totalOrders->numerate ?></span>
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
                                                <h6 class="card-title mb-0 text-success">Total Income Generated</h6>
                                                <div class="dropdown mb-2">
                                                    <button class="btn p-0" type="button" id="dropdownMenuButton1"
                                                        data-toggle="dropdown" aria-haspopup="true"
                                                        aria-expanded="false">
                                                        <i class="fa fa-calendar-alt text-success"></i>
                                                    </button>

                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12 col-md-12 col-xl-12">
                                                    <h3 class="mb-2 text-success">
                                                        &#x20A6; <?= number_format($metrix->amount,2) ?></h3>
                                                    <div class="d-flex align-items-baseline">
                                                        <p class="text-success">
                                                            <span><?= $ordersSuccessful->numerate ?></span>
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
                                                <h6 class="card-title mb-0 text-warning">Outstanding</h6>
                                                <div class="dropdown mb-2">
                                                    <button class="btn p-0" type="button" id="dropdownMenuButton2"
                                                        data-toggle="dropdown" aria-haspopup="true"
                                                        aria-expanded="false">
                                                        <i class="fa fa-cogs text-warning"></i>
                                                    </button>

                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12 col-md-12 col-xl-12">
                                                    <h3 class="mb-2 text-warning" id="outstandingTab"></h3>
                                                    <div class="d-flex align-items-baseline">
                                                        <p class="text-warning">

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
                                                <h6 class="card-title mb-0 text-danger">Total Expenses</h6>
                                                <div class="dropdown mb-2">
                                                    <button class="btn p-0" type="button" id="dropdownMenuButton2"
                                                        data-toggle="dropdown" aria-haspopup="true"
                                                        aria-expanded="false">
                                                        <i class="fa fa-cogs text-danger"></i>
                                                    </button>

                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12 col-md-12 col-xl-12">
                                                    <h3 class="mb-2 text-danger" id="totalExpenses"><?= number_format($pending->numerate,0) ?></h3>
                                                    <div class="d-flex align-items-baseline">
                                                        <p class="text-warning">

                                                        </p>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div> <!-- row -->

                    <div class="row">
                        <div class="col-xl-9">
                            <div class="card mb-4">
                                <div class="card-header"><i class="fas fa-chart-area mr-1"></i>Daily Transaction
                                    Statistics</div>
                                <div class="card-body"><canvas id="myAreaChart" width="100%" height="400"></canvas></div>
                            </div>
                        </div>

                        <div class="col-lg-3">
                            <div class="row">
                                <div class="col-12 col-md-12 col-xl-12 mb-4">
                                    <div class="card bg-success">
                                        <div class="card-body">
                                            <h5 class="card-title text-white" id="mtotalSales">Test</h5>
                                            <h6 class="card-subtitle mb-2 text-white">Total Test for <?=date('F') ?></h6>
                                            <p class="card-text text-white"><i class="fas fa-check-circle"></i></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-12 col-xl-12 mb-4">
                                    <div class="card bg-warning">
                                        <div class="card-body">
                                            <h5 class="card-title text-white" id="monthlyExpense">Expenses</h5>
                                            <h6 class="card-subtitle mb-2 text-white">Expenses</h6>
                                            <p class="card-text"><i class="fas fa-pen"></i></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-12 col-xl-12 mb-4">
                                    <div class="card bg-danger">
                                        <div class="card-body">
                                            <h5 class="card-title text-white">Refund</h5>
                                            <h6 class="card-subtitle mb-2 text-white"></h6>
                                            <p class="card-text"><i class="fas fa-pen"></i></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="col-xl-12">
                            <div class="card mb-4">
                                <div class="card-header"><i class="fas fa-chart-bar mr-1"></i>
                                    Annual Transaction Report</div>
                                <div class="card-body"><canvas id="myBarChart" width="100%"></canvas></div>
                            </div>
                        </div>
                    </div>

                </div>
            </main>
            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">Copyright &copy; <?=$config->name?></div>
                        <div>
                            <a href="#">Privacy Policy</a>
                            &middot;
                            <a href="#">Terms &amp; Conditions</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <script src="js/jquery-3.4.1.min.js"></script>
    <script src="js/Chart.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/jquery.dataTables.min.js"></script>
    <script src="assets/demo/chart-area-demo.js"></script>
    <script src="assets/demo/chart-bar-demo.js"></script>
    <script src="js/scripts.js"></script>
</body>

</html>