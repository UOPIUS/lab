<?php include_once 'functions/Functions.php'; 
$class = new Functions();
include_once 'functions/validate_session.php';
if(!($_SESSION['user_id'] && $_SESSION['role_id'])) header('location: logout.php');
$tempEncrypted = htmlentities(filter_input(INPUT_GET,'idx'),ENT_QUOTES);
$ref = $class->simple_encrypt($tempEncrypted,'d');
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
    <title>Test Summary | <?= $config->name ?></title>
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
                    <div class="container-fluid">
                        <h4 class="mt-4">Test ID: <?=$ref?></h4>
                        <div class="card mb-4">
                            <div class="card-header">

                            </div>
                            <div class="card-body">
                                <div class="table-responsive mt-4">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th colspan="4" class="bg-secondary text-white">
                                                    ANALYSIS AND RESULT
                                                </th>
                                            </tr>
                                            <tr>
                                                <th>Test</th>
                                                <th>Show Test</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $tests = $class->fetchAll("tests_taken"," WHERE tranx_id = '$ref'");
                   foreach($tests as $t):
                    $testInfo = $class->fetch('sub_labtest_tbl'," WHERE id = '$t->test_id'");
                    $testCategory = $testInfo->labtest_id;
                    ?>
                                            <tr>
                                                <td><?php $pformTestName = $testInfo->name;
                                                        echo $pformTestName; ?>
                                                </td>
                                               
                                                <td>
                                                    <?php if($t->test_result){ ?>
                                                    <a href="test_result.php?idx=<?=$class->simple_encrypt($t->id)?>"
                                                        class="btn btn-warning btn-sm" target="__blank">Click
                                                        for
                                                        Details<i class="fa fa-play-circle ml-1"></i>
                                                    </a>
                                                    <?php } else { echo "NA"; } ?>
                                                </td>
                                                <td>
                                                    <?php if($t->test_result) echo "<i class='fa fa-check-circle text-success'></i>";
                                                        else echo "<i class='fa fa-times text-danger'></i>"; ?>
                                                </td>

                                            </tr>

                                            <?php endforeach; ?>
                                        </tbody>
                                        <!--
                                                <?php if($tranx->lab_scientist_flag == 0): ?>
                                                <tfoot>
                                                    <tr>
                                                        <td colspan="4">
                                                            <div class="text-center">
                                                                <button id="finalSubmit" data-referral="<?=$txref?>"
                                                                    class="btn btn-lg btn-danger"><i
                                                                        class="fa fa-lock"></i>&nbsp;Submit All Test
                                                                    Result</button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tfoot>
                                                <?php endif; ?>
                                                -->
                                    </table>
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
</body>

</html>