<?php include_once 'functions/Functions.php'; 
if(!($_SESSION['role_id'] && $_SESSION['user_id'])) header('location: login.php');
$class = new Functions();
$ref = (filter_has_var(INPUT_GET,'ref')) ? htmlentities(filter_input(INPUT_GET,'ref')) : $_SESSION['user_id'];
$profile = $class->fetch('users_tbl'," WHERE user_id = '$ref'");
$config = $class->fetch('settings');

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
    SELECT t.*,p.pay_method AS payment_method, c.fname,c.lname,c.phone AS cphone,c.ref AS client FROM transactions AS t LEFT JOIN
     clients_tbl AS c ON t.client_id = c.ref LEFT JOIN payment_types AS p ON
    t.payment_type = p.id WHERE t.lab_scientist_flag = 0 $condition ORDER BY t.created_at DESC
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
    <title>All time Transactions | <?= $config->name ?></title>

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
                    <div class="container-fluid" id="oldDiv">
                        <h4 class="mt-4"><i class="fas fa-table mr-1"></i>Pending Lab Tests</h4>
                        <div class="card mb-4">
                            <div class="card-header">

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
                                    <!------------------------
                                        <div class="form-group col-md-3">
                                            <label for="sel1">Transaction type</label>
                                            <select class="form-control" name='trans_type'>
                                                <option value=''>All</option>
                                                <?php $types = $class->fetchAll('payment_types'," WHERE status = 1");
                        foreach($types as $type){ ?>
                                                <option value="<?=$type->id ?>"
                                                    <?php if($_GET['trans_type'] == $type->id) echo 'selected' ?>>
                                                    <?= $type->pay_method ?>
                                                </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        ---------------------------->

                                        <div class="form-group col-md-3">
                                            <label>Search Record</label>
                                            <input type="submit" class="form-control btn btn-success btn-sm"
                                                value="Search Records">
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive mt-4">
                                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Trans. Ref</th>
                                                <th>Client ID</th>
                                                <th>Name</th>
                                                <th>Tests</th>
                                                <th>Date</th>
                                                <th></th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php 
                                            $Zi=1;
                                            foreach($sales as $x):
                                            $ref = $class->simple_encrypt($x->client_id,'e');
                                            $txref = $class->simple_encrypt($x->id,'e');
                                            ?>
                                            <tr>
                                                <td><?=$i++ ?></td>
                                                <td><?=$x->id ?></td>
                                                <td><a href="client_profile.php?refx=<?=$ref ?>"
                                                        class="btn btn-link"><?=$x->client ?></a></td>
                                                <td><?=$x->fname.' '.$x->lname. ''.$x->oname ?></td>
                                                <td>
                                                    <?php $tests = $class->fetchAll("tests_taken"," WHERE tranx_id = '{$x->id}'");
                                                    $i = 1;
                                                    <?php if($t->test_result) echo "<i class='fa fa-check-circle text-success'></i>";
                                                        else echo "<i class='fa fa-times text-danger'></i>"; ?>
                                                    foreach($tests as $t): ?>
                                                    <strong class="text-danger"><?=$i++.'.' .$class->fetchColumn('sub_labtest_tbl','name','id',$t->test_id) ?>
                                                    </strong><br>

                                                    <?php endforeach; ?>
                                                </td>
                                                <td><?=$x->created_at ?></td>
                                                <td>
                                                    <a href="report_test.php?refx=<?=$txref?>"
                                                        class="btn-info btn-sm btn">Report Test</a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
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
    <script src="js/tejiri.js"></script>
</body>

</html>