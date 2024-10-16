<?php include_once 'functions/Functions.php'; 
$class = new Functions();
if(!($_SESSION['user_id'] && $_SESSION['role_id'])) header('location: logout.php');
include_once 'functions/validate_session.php';
$config = $class->fetch('settings');
$uref = (filter_has_var(INPUT_GET,'uref')) ? $class->simple_encrypt(filter_input(INPUT_GET,'uref'),'d') : $_SESSION['user_id'];
$client = $class->rawQuery("
   SELECT full_name AS name,src,user_id,phone,status,created_at,username FROM users_tbl WHERE user_id = '$uref'
",1);


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
    SELECT t.amount,ABS(t.payable_amount) AS expectedAmount,t.created_at,t.status, CONCAT(c.fname,' ',c.lname) AS name,c.phone AS cphone,c.ref AS client FROM transactions AS t LEFT JOIN
     clients_tbl AS c ON t.client_id = c.ref WHERE referral = '$uref' $condition ORDER BY t.created_at DESC
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
    <title>Referral Profile | <?= $config->name ?></title>
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
                        <div class="card mb-4 mt-2">
                            <div class="card-header mt-0">
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
                                        <input type="hidden" name="uref" value="<?=filter_input(INPUT_GET,'uref')?>">
                                        <div class="form-group col-md-3">
                                            <label>Search Record</label>
                                            <input type="submit" class="form-control btn btn-success btn-sm"
                                                value="Search Records">
                                        </div>
                                    </div>
                                </form>
                                <div class="row mt-2">
                                   
                                    <div class="col-md-4 mb-4">
                                        <div class="card h-100 bg-info">
                                            <div class="card-body h-100 p-4">
                                                <div class="row align-items-center">
                                                    <div class="col-xl-12 col-xxl-12">
                                                        <div
                                                            class="text-center text-xl-start text-xxl-center mb-4 mb-xl-0 mb-xxl-4">
                                                            <h1 class="text-white"><?=count($sales);?></h1>
                                                            <p class="text-gray-700 mb-0">Total Referral Count</p>
                                                            <p></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-4">
                                        <div class="card h-100 bg-success">
                                            <div class="card-body h-100 p-4">
                                                <div class="row align-items-center">
                                                    <div class="col-xl-12 col-xxl-12">
                                                        <div
                                                            class="text-center text-xl-start text-xxl-center mb-4 mb-xl-0 mb-xxl-4">
                                                            <h1 class="text-white">0.00</h1>
                                                            <p class="text-gray-700 mb-0">Total Paid</p>
                                                            
                                                            <p></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-4">
                                        <div class="card h-100 bg-warning">
                                            <div class="card-body h-100 p-4">
                                                <div class="row align-items-center">
                                                    <div class="col-xl-12 col-xxl-12">
                                                        <div
                                                            class="text-center text-xl-start text-xxl-center mb-4 mb-xl-0 mb-xxl-4">
                                                            <h1 class="text-dark"><?=$client->name ?></h1>
                                                            <p class="text-white-700 mb-0">Phone: <?=$client->phone ?>
                                                            </p>
                                                            <p><?=$client->src ?></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="card-body">
                                <h4 class="mb-4">Clients Referred</h4>
                                <div class="table-responsive mt-4">
                                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Client ID</th>
                                                <th>Name</th>
                                                <th>Mobile</th>
                                                <th>Amount</th>
                                                <th>Date</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php $i = 1; $total = 0;
                                            foreach($sales as $sale):
                                            $total += $sale->amount; ?>
                                            <tr>
                                                <td><?=$i++?></td>
                                                <td><?=$sale->client ?></td>
                                                <td><?=$sale->name ?></td>
                                                <td><?=$sale->cphone ?></td>
                                                <td><?=$sale->amount ?></td>
                                                <td><?=$sale->created_at ?></td>
                                                <td>
                                                    <?php if($sale->expectedAmount > $sale->amount){ ?>
                                                    <button class="btn btn-danger btn-sm">Pending <i
                                                            class="fas fa-times-circle"></i></button>
                                                    <?php } else { ?>
                                                    <button class="btn btn-success btn-sm">Paid <i
                                                            class="fas fa-check-circle"></i></button>
                                                    <?php } ?>
                                                </td>

                                            </tr>
                                            <?php endforeach ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="3"></th>
                                                <th>Referral Count: <?=$i-1 ?></th>
                                                <th>Total:<?=number_format($total,2) ?></th>
                                                <th colspan="2"></th>
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
    <script src="assets/demo/datatables-demo.js"></script>
    <script src="js/scripts.js"></script>
    <script src="js/tejiri.js"></script>
</body>

</html>