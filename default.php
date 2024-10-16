<?php include_once 'functions/Functions.php'; 
$class = new Functions();
$config = $class->fetch('settings');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Admin Dashboard - <?= $config->name ?></title>

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
                                <div class="col-md-4 grid-margin stretch-card">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-baseline">
                                                <h6 class="card-title mb-0">Total Certificates Processed</h6>
                                                <div class="dropdown mb-2">
                                                    <button class="btn p-0" type="button" id="dropdownMenuButton"
                                                        data-toggle="dropdown" aria-haspopup="true"
                                                        aria-expanded="false">
                                                        <i class="icon-lg text-muted pb-3px"
                                                            data-feather="more-horizontal"></i>
                                                    </button>

                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12 col-md-12 col-xl-12">
                                                    <h3 class="mb-2"><?= number_format($totalOrders->amount,2) ?></h3>
                                                    <div class="d-flex align-items-baseline">
                                                        <p class="text-primary">
                                                            <span><?= $totalOrders->numerate ?></span>
                                                            <i data-feather="arrow-up" class="icon-sm mb-1"></i>
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
                                                <h6 class="card-title mb-0 text-success">Total Certificates Issued</h6>
                                                <div class="dropdown mb-2">
                                                    <button class="btn p-0" type="button" id="dropdownMenuButton1"
                                                        data-toggle="dropdown" aria-haspopup="true"
                                                        aria-expanded="false">
                                                        <i class="icon-lg text-muted pb-3px"
                                                            data-feather="more-horizontal"></i>
                                                    </button>

                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12 col-md-12 col-xl-12">
                                                    <h3 class="mb-2 text-success">
                                                        <?= number_format($ordersSuccessful->amount,2) ?></h3>
                                                    <div class="d-flex align-items-baseline">
                                                        <p class="text-success">
                                                            <span><?= $ordersSuccessful->numerate ?></span>
                                                            <i data-feather="arrow-up" class="icon-sm mb-1"></i>
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
                                                <h6 class="card-title mb-0 text-warning">Total Income Revenue</h6>
                                                <div class="dropdown mb-2">
                                                    <button class="btn p-0" type="button" id="dropdownMenuButton2"
                                                        data-toggle="dropdown" aria-haspopup="true"
                                                        aria-expanded="false">
                                                        <i class="icon-lg text-muted pb-3px"
                                                            data-feather="more-horizontal"></i>
                                                    </button>

                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12 col-md-12 col-xl-12">
                                                    <h3 class="mb-2 text-warning">
                                                        <?= number_format($pendingOrders->amount,2) ?></h3>
                                                    <div class="d-flex align-items-baseline">
                                                        <p class="text-warning">
                                                            <span><?= $pendingOrders->numerate ?></span>
                                                            <i data-feather="arrow-down" class="icon-sm mb-1"></i>
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
                        <div class="col-xl-6">
                            <div class="card mb-4">
                                <div class="card-header"><i class="fas fa-chart-area mr-1"></i>Area Chart Example</div>
                                <div class="card-body"><canvas id="myAreaChart" width="100%" height="40"></canvas></div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="card mb-4">
                                <div class="card-header"><i class="fas fa-chart-bar mr-1"></i>Bar Chart Example</div>
                                <div class="card-body"><canvas id="myBarChart" width="100%" height="40"></canvas></div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                                <div class="card mb-4">
                                    <div class="card-header"><i class="fas fa-chart-pie mr-1"></i>Pie Chart Example</div>
                                    <div class="card-body"><canvas id="myPieChart" width="100%" height="50"></canvas></div>
                                    <div class="card-footer small text-muted">Updated yesterday at 11:59 PM</div>
                                </div>
                            </div>
                    </div>

                </div>
            </main>
            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">Copyright &copy; Your Website 2019</div>
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
    <script src="js/dataTables.bootstrap4.min.js"></script>
    <script src="assets/demo/datatables-demo.js"></script>
    <script src="assets/demo/chart-area-demo.js"></script>
        <script src="assets/demo/chart-bar-demo.js"></script>
        <script src="assets/demo/chart-pie-demo.js"></script>
    <script src="js/scripts.js"></script>
</body>

</html>