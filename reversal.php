<?php include_once 'functions/Functions.php'; 
$role = $_SESSION['role_id'];
if($role != 106) header('location: login.php');
$class = new Functions();
$ref = (filter_has_var(INPUT_GET,'ref')) ? htmlentities(filter_input(INPUT_GET,'ref'),ENT_QUOTES) : $_SESSION['user_id'];

//approve Reversal
if (NULL !== filter_input(INPUT_GET, 'id') && NULL !== filter_input(INPUT_GET, 's')) {
    $id = htmlentities($_REQUEST['id'],ENT_QUOTES);
    $status = htmlentities($_REQUEST['s'],ENT_QUOTES);
    $new_status = ($status == 3) ? 2 : 3;
    $conn = $class->connect();

    $query = $conn->prepare("UPDATE transactions SET who_approved_reversal = ?,
        reversed_at = ?,status = ? WHERE id = ?");
        $attempt = $query->execute([$_SESSION['user_id'],date('Y-m-d H:m:s'),$new_status,$id]);
        $query2 = $conn->query("UPDATE outstanding_tbl SET reversed_flag = 'YES' WHERE tranx_ref = '$id'");
    $conn=null;
}
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
    t.payment_type = p.id WHERE (t.status > 1) $condition ORDER BY t.created_at DESC
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
    <title>All time Reversal List</title>

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
                        <h4 class="mt-4">Transaction Reversal List</h4>
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-table mr-1"></i>Transactions
                                

                            </div>
                            <div class="card-body">
                                <form class="" action="<?= htmlentities($_SERVER['PHP_SELF']) ?>" method="get">
                                    <div class="form-row">
                                        <div class="form-group col-md-4">
                                            <label for="sel1">Date From</label>
                                            <input type="date" class="custom-select mr-sm-2" data-provide="datepicker"
                                                placeholder="Date From" data-date-format="yyyy-mm-dd" name="date_from"
                                                value="<?php echo ($_GET['date_from']) ?? '' ?>">
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="sel1">Date To</label>
                                            <input type="date" class="custom-select mr-sm-2" data-provide="datepicker"
                                                placeholder="Date To" data-date-format="yyyy-mm-dd" name="date_to"
                                                value="<?= filter_input(INPUT_GET, 'date_to') ?>">
                                        </div>


                                        <div class="form-group col-md-4">
                                            <label>Search Record</label>
                                            <input type="submit" class="form-control btn btn-success btn-sm"
                                                value="Search Records">
                                        </div>
                                    </div>
                                </form>
                                <div class="table-responsive mt-4">
                                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Tranx ID</th>
                                                <th>Name</th>
                                                <th>Mobile</th>
                                                <th>Amount</th>
                                                <th>Payment</th>
                                                <th>Date</th>
                                               <th>Status</th>
                                              
                                                <th>Date Reversed</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php 
                                            $i=1;
                                            foreach($sales as $x): 
                                            $idx = $class->simple_encrypt($x->client,'e');
                                            ?>
                                            <tr>
                                                <td><?=$i++ ?></td>
                                                <td><?=$x->id ?></td>
                                                <td>
                                                    <a href="client_profile.php?refx=<?=$idx ?>"
                                                        class="btn btn-link"><?=$x->fname.' '.$x->lname. ''.$x->oname ?>
                                                    </a>
                                                </td>
                                                <td><?=$x->cphone ?></td>
                                                <td><?=$x->amount ?></td>
                                                <td><?=$x->payment_method ?></td>
                                               <th><?=$x->date_reversal_requested ?></th>
                                                <td>
                                                    <?php
                                            $status = $x->status;
                                            if($status == 2)
                                            echo "<a class='btn btn-danger btn-sm' href='".$_SERVER['PHP_SELF'].'?id='.$x->id.'&s='.$status."'>Approve</a>";
                                            elseif($status = 3) echo "<a class='btn btn-success btn-sm' href='".$_SERVER['PHP_SELF'].'?id='.$x->id.'&s='.$status."'><i class='fa fa-check-circle'></i></a>";
                                            ?>
                                                </td>
                                                <td><?=$x->reversed_at ? $x->reversed_at : 'NA' ?></td>
                                                
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