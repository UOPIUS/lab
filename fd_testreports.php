<?php include_once 'functions/Functions.php'; 
if(!($_SESSION['role_id'] && $_SESSION['user_id'])) header('location: login.php');
$class = new Functions();
$ref = (filter_has_var(INPUT_GET,'ref')) ? htmlentities(filter_input(INPUT_GET,'ref')) : $_SESSION['user_id'];
$profile = $class->fetch('users_tbl'," WHERE user_id = '$ref'");
$config = $class->fetch('settings');
$sales = $class->rawQuery("
    SELECT t.*,p.pay_method AS payment_method, c.fname,c.lname,c.phone AS cphone,c.ref AS client FROM transactions AS t LEFT JOIN
     clients_tbl AS c ON t.client_id = c.ref LEFT JOIN payment_types AS p ON
    t.payment_type = p.id ORDER BY t.created_at DESC LIMIT 50
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
                        <h4 class="mt-4">Transaction History</h4>
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-table mr-1"></i>Transactions
                                <?php if($_SESSION['role_id'] == 0): ?>

                                <button class="btn btn-pill btn-danger btn-air-danger float-right" onclick="showDiv()">
                                    <i class="fa fa-user"></i>&nbsp;New Transaction
                                </button>
                                <?php endif; ?>
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
                                                <th>Amount</th>
                                                <th>Payment</th>
                                                <!--
                                                <th>Created By</th>
                                                <!--
                                                <th>Completed By</th>
                                                -->
                                                <th>Date created</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        
                                        <tbody>
                                            <?php 
                                            $i=1;
                                            foreach($sales as $x):
                                            $ref = $class->simple_encrypt($x->client,'e');
                                            ?>
                                            <tr>
                                                <td><?=$i++ ?></td>
                                                <td><a href="client_profile.php?refx=<?=$ref ?>"
                                                        class="btn btn-link"><?=$x->client ?></a></td>
                                                <td><?=$x->fname.' '.$x->lname. ''.$x->oname ?></td>
                                                <td><?=$x->cphone ?></td>
                                                <td><?=$x->amount ?></td>
                                                <td><?=$x->payment_method ?></td>
                                                <!------------
                                                <td><?=$class->fetchColumn('users_tbl','full_name','user_id',($x->created_by) ? $x->created_by: '' ) ?></td>
                                                <td><?=$class->fetchColumn('users_tbl','full_name','user_id',($x->adhoc_id) ? $x->adhoc_id: '' ) ?></td>
                                                -->
                                                <td><?=$x->created_at ?></td>
                                                <td>
                                                    <?php
                                            $status = $user->status;
                                            if($status == 1)
                                            echo "<a class='btn btn-success btn-sm' href='#'>Successful</a>";
                                            else echo "<a class='btn btn-danger btn-sm' href='#'>Pending</a>";
                                            ?>
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