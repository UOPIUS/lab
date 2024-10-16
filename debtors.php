<?php include_once 'functions/Functions.php'; 
if(!(106 == $_SESSION['role_id'] || 103 == $_SESSION['role_id'] || 101 == $_SESSION['role_id'])) header('location: login.php');
$class = new Functions();

$config = $class->fetch('settings');
$condition = '';

if($_GET['dt_to'] && $_GET['dt_from']){
  //date interval
  $from = (filter_has_var(INPUT_GET, 'dt_from')) ? htmlentities(filter_input(INPUT_GET, 'dt_from'),ENT_QUOTES) . " 00:00:59" : '';
  $to = (filter_has_var(INPUT_GET, 'dt_to')) ? htmlentities(filter_input(INPUT_GET, 'dt_to'),ENT_QUOTES). " 23:59:59" : '';
  $condition .= " AND (t.created_at BETWEEN '$from' AND '$to') ";
}

$clients = $class->rawQuery("
   SELECT c.fname,c.lname,c.ref,c.phone,DATE_FORMAT(t.created_at, '%d/%b/%Y') createdAt,ABS(t.payable_amount) AS expectedAmount, t.amount AS paidAmount
   FROM transactions t JOIN clients_tbl  c ON t.client_id = c.ref WHERE (t.status <> 2 AND t.status <> 5) AND (ABS(t.payable_amount) > t.amount) $condition ORDER BY t.created_at DESC
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
    <title>Debt history | <?= $config->name ?></title>
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
                    <div class="container-fluid" id="oldDiv">
                        <h4 class="mt-4">Clients Owning us Money</h4>
                        <div class="card mb-4">
                        <div class="card-header">
                                
                                <form class="" action="<?= htmlentities($_SERVER['PHP_SELF']) ?>" method="get">
                                    <div class="form-row">
                                        <div class="form-group col-md-3">
                                            <label for="sel1">Date From</label>
                                            <input type="date" class="custom-select mr-sm-2"
                                                value="<?php echo ($_GET['dt_from']) ?? '' ?>" name="dt_from">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label for="sel1">Date To</label>
                                            <input type="date" class="custom-select mr-sm-2"
                                                value="<?= filter_input(INPUT_GET, 'dt_to') ?>" name="dt_to">
                                        </div>
                                       <!--
                                        <div class="form-group col-md-3">
                                            <label for="sel1">Created by</label>
                                            <select class="form-control" name='referrals'>
                                                <option value=''>All</option>
                                                <?php $referrals = $class->rawQuery("SELECT full_name,phone,user_id FROM users_tbl ORDER BY full_name ASC");
                                                foreach($referrals as $rr): ?>
                                                <option value="<?=$rr->user_id?>" <?php if($_GET['referrals'] == $rr->user_id)echo "selected" ?>>
                                                <?=$rr->full_name.' ['.$rr->phone.']' ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
										-->
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
                                                <th>Client ID</th>
                                                <th>Name</th>
                                                <th>Mobile</th>
                                                <th>Expected Amount</th>
                                                <th>Paid Amount</th>
                                                <th>Debt</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                      
                                        <tbody>
                                            <?php 
                                            $i=1;
                                            $debtSum = 0;
                                            foreach($clients as $x): $id = $class->simple_encrypt($x->ref); 
                                            $debt = $x->paidAmount - $x->expectedAmount;
                                            $debtSum += $debt;
                                            ?>
                                            <tr>
                                                <td><?=$i++ ?></td>
                                                <td>
                                                    <a href="client_profile.php?refx=<?=$id ?>"
                                                            class="btn btn-link"><?=$x->ref ?>
                                                    </a>
                                                </td>
                                                <td><?=$x->fname ." ".$x->lname ?></td>
                                                <td><?=$x->phone ?></td>
                                                <td><?=$x->expectedAmount ?></td>
                                                <td><?=$x->paidAmount ?></td>
                                                <td><?= $debt ?></td>
                                                <td><?=$x->createdAt ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="4"></td>
                                                <td></td>
                                                <td colspan="3"><strong>Total: &nbsp;₦<?=$debtSum?></strong></td>
                                            </tr>
                                        </tfoot>
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
    <!--
    <script src="assets/demo/datatables-demo.js"></script>
    -->
    <script src="js/scripts.js"></script>

</body>

</html>