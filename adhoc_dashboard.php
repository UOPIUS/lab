<?php include_once 'functions/Functions.php'; 
$class = new Functions();
if(!($_SESSION['user_id'] && $_SESSION['role_id'])) header('location: logout.php');
include 'function/validate_session.php';
$config = $class->fetch('settings');

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="icon" type="image/png" href="assets/img/favicon.png" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Front Desk Dashboard - <?= $_SESSION['name'] ?></title>
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
                    <h4 class="m-4">Welcome <?=$_SESSION['name'] ?>,</h4>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="row">
                                <div class="col-xl-12">
                                    <div class="card mb-2 bg-warning text-white">
                                        <div class="card-body ">
                                            <h5 class="text-center">Client = [<?= number_format($statisticsToday,0) ?>],
                                                &nbsp;&nbsp; Tests = [<?= number_format($testTakenToday) ?>]</h5>
                                        </div>
                                        <div class="card-footer text-center">
                                            <i class="fa fa-users"></i>&nbsp;Today Statistics
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xl-12">
                                    <div class="card mb-2 bg-success text-white">
                                        <div class="card-body ">
                                            <h5 class="text-center">Clients =
                                                [<?= number_format($statisticsMonth,0) ?>], &nbsp;&nbsp; Tests =
                                                [<?= number_format($testTakenMonth) ?>]</h5>
                                        </div>
                                        <div class="card-footer text-center">
                                            <i class="fa fa-bath"></i>&nbsp;<?=date('M')?> Statistics
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xl-12">
                                    <div class="card mb-2 bg-danger text-white">
                                        <div class="card-body ">
                                            <h5 class="text-center">Clients = [<?= number_format($statisticsYear,0) ?>],
                                                &nbsp; &nbsp; Tests = [<?= number_format($testTakenAll,0) ?>]</h5>
                                        </div>
                                        <div class="card-footer text-center">
                                            <i class="fa fa-calendar"></i>&nbsp;<?=date('Y')?> Statistics
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-8">
                            <div class="card mb-4">
                                <div class="card-header"><i class="fas fa-chart-area mr-1"></i>Transactions Summary</div>
                                <div class="card-body"><canvas id="myAreaChart" width="100%" height="40"></canvas></div>
                                <div class="card-footer small text-muted">Transactions as at <?=date('Y-m-d h:m:s')?></div>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="card-header"><i class="fas fa-users mr-1"></i>Registered Client Statistics</div>
                            <div class="card-body"><canvas id="myBarChart" width="100%" height="40"></canvas></div>
                        </div>
                    </div>
                </div>
            </main>
            <?php include 'footer.php'; ?>
        </div>
    </div>
    <script src="js/jquery-3.4.1.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/Chart.min.js"></script>
    <script src="js/scripts.js"></script>
    <script src="js/tejiri.js"></script>
    <script>
    window.onload = () => {
        ajax("dsb/adhoc.php?sfx=sf1").then((result) => {
            // Set new default font family and font color to mimic Bootstrap's default styling
            Chart.defaults.global.defaultFontFamily =
                '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
            Chart.defaults.global.defaultFontColor = '#292b2c';

            // Bar Chart Example
            var ctx = document.getElementById("myBarChart");
            var myLineChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: result.response.label,
                    datasets: [{
                        label: "Clients",
                        backgroundColor: ["#00CD66", "#1D7CF2", "#3A3A38", "#8E8E38",
                            "#AEBB51", "#CD00CD", "#98FB98", "#9F5F9F", "#A6A6A6",
                            "#ADFF2F", "#D6C537", "#EED8AE"
                        ],
                        borderColor: "rgba(2,117,216,1)",
                        data: result.response.data,
                    }],
                },
                options: {
                    scales: {
                        xAxes: [{
                            time: {
                                unit: 'Clients'
                            },
                            gridLines: {
                                display: true
                            }
                        }],
                        yAxes: [{
                            ticks: {
                                min: 0,
                                maxTicksLimit: 5
                            },
                            gridLines: {
                                display: true
                            }
                        }],
                    },
                    legend: {
                        display: false
                    }
                }
            });

        })

        //line chart
        ajax("dsb/adhoc.php?sfx=sf2").then((result) => {
            // Set new default font family and font color to mimic Bootstrap's default styling
            Chart.defaults.global.defaultFontFamily =
                '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
            Chart.defaults.global.defaultFontColor = '#292b2c';

            // Area Chart Example
            var ctx = document.getElementById("myAreaChart");
            var myLineChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels:result.response.label,
                    datasets: [{
                        label: "Tests Done",
                        lineTension: 0.3,
                        backgroundColor: "rgba(2,117,216,0.2)",
                        borderColor: "rgba(2,117,216,1)",
                        pointRadius: 5,
                        pointBackgroundColor: "rgba(2,117,216,1)",
                        pointBorderColor: "rgba(255,255,255,0.8)",
                        pointHoverRadius: 5,
                        pointHoverBackgroundColor: "rgba(2,117,216,1)",
                        pointHitRadius: 50,
                        pointBorderWidth: 2,
                        data: result.response.data,
                    }],
                },
                options: {
                    scales: {
                        xAxes: [{
                            time: {
                                unit: 'date'
                            },
                            gridLines: {
                                display: true
                            },
                            ticks: {
                                maxTicksLimit: 7
                            }
                        }],
                        yAxes: [{
                            ticks: {
                                min: 0,
                                maxTicksLimit: 5
                            },
                            gridLines: {
                                color: "rgba(0, 0, 0, .125)",
                            }
                        }],
                    },
                    legend: {
                        display: false
                    }
                }
            });

        });

    };
    </script>
</body>

</html>