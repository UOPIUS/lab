<?php include_once 'functions/Functions.php'; 
$class = new Functions();
if(!($_SESSION['user_id'] && $_SESSION['role_id'])) header('location: logout.php');
include 'function/validate_session.php';
$config = $class->fetch('settings');

//KsAP2B8VrxXT
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
    <title>Lab Scientist Dashboard - <?= $_SESSION['name'] ?></title>
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

                        <div class="card mb-4">
                            <div class="card-body">
                                <div class="card-header"><i class="fas fa-users mr-1"></i>Test Statistics</div>
                                <div class="card-body"><canvas id="myChart" width="100%" height="40"></canvas></div>
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
    <script>
    window.onload = () => {
        var ctx = document.getElementById("myChart").getContext("2d");

        var data = {
            labels: ["X-Ray", "Sickle Cell", "Brain Tumor"],
            datasets: [{
                    label: "Blue",
                    backgroundColor: "blue",
                    data: [3, 7, 4]
                },
                {
                    label: "Red",
                    backgroundColor: "red",
                    data: [4, 3, 5]
                },
                {
                    label: "Green",
                    backgroundColor: "green",
                    data: [7, 2, 6]
                }
            ]
        };

        var myBarChart = new Chart(ctx, {
            type: 'bar',
            data: data,
            options: {
                barValueSpacing: 20,
                scales: {
                    yAxes: [{
                        ticks: {
                            min: 0,
                        }
                    }]
                }
            }
        });
    }
    </script>
     <script>
    $(document).ready(function() {

        $('.textarea_editor').wysihtml5();

    });
    </script>
</body>

</html>