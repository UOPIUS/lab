<?php include_once 'functions/Functions.php'; 
$class = new Functions();
if(!$_SESSION['role_id']) header('location: logout.php');
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
    <title>Test Summary - <?= $_SESSION['name'] ?></title>
    <link href="css/materialdesignicons.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet" />
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
                        <div class="col-lg-12">
                            <div class="card mb-4">
                                <div class="card-body">
                                    <div class="card-header"><i class="fas fa-users mr-1"></i>Laboratory Test Statistics
                                    </div>
                                    <canvas id="lineChart" height="330"
                                        style="display: block; width: 708px; height: 330px;" width="708"></canvas>
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
    <script src="js/Chart.min.js"></script>
    <script src="js/scripts.js"></script>
    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/chartjs-plugin-datalabels/2.0.0/chartjs-plugin-datalabels.min.js">
    </script>
    <script>
    let jr = (e) =>
        new Promise((t, n) => {
            let o = new XMLHttpRequest();
            o.open("GET", e),
                o.send(),
                (o.onload = function() {
                    200 != o.status ? alert(`Error ${o.status}: ${o.statusText}`) : t(JSON.parse(o.response));
                }),
                (o.onerror = function() {
                    alert("Request failed");
                });
        });


    jr('functions/bdata.php?uf=scdlTestKats&rt4=' + Math.random() * 10).then((xhr) => {
        var ctx = document.getElementById("lineChart");
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: xhr.categoryOfTest,
                datasets: [
                    /*
                    {
                        label: "No of Test Carried Out",
                        borderColor: "rgba(0,0,0,.09)",
                        borderWidth: "1",
                        backgroundColor: "rgba(15,28,0,.07)",
                        data: xhr.response.testCarriedOut
                    },
                    */
                    {
                        label: "No of Test Carried Out",
                        borderColor: "rgba(85, 139, 47, 0.9)",
                        borderWidth: "1",
                        backgroundColor: xhr.response.daysColor,
                        pointHighlightStroke: "rgba(26,179,148,1)",
                        data: xhr.testCarriedOut
                    }
                ]
            },
            options: {
                responsive: true,
                tooltips: {
                    mode: 'index',
                    intersect: false
                },
                hover: {
                    mode: 'nearest',
                    intersect: true
                },
                title: {
                    display: true,
                    text: 'Laboratory Test Performed'
                },
                legend: {
                    display: false
                },
                plugins: {
                    // Change options for ALL labels of THIS CHART
                    datalabels: {
                        color: '#36A2EB'
                    }
                }
            }
        });
    }).catch((e) => {
        console.log(e)
    })
    </script>
</body>

</html>