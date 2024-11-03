<?php include '../functions/Functions.php';
$class = new Functions();
if ($class->checkSession($_SESSION['user_id']) === false)
    header('location: ../logout.php');

$config = $class->fetchSettings();

$condition = "";
//date interval
$from = filter_input(INPUT_GET, 'date_from');
$to = filter_input(INPUT_GET, 'date_to');

//default: Display payment for the last 30 days
$default = " AND (i.created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)) ";
if ($from && $to) {
  $from .=  ' 00:00:00';
  $to .= ' 23:59:59';
  $default = " AND (i.created_at BETWEEN '$from' AND '$to') ";
}
$condition .= $default;

$product = "";
if(filter_has_var(INPUT_GET, 'product_id') && preg_match('/^[0-9]+$/', filter_input(INPUT_GET,'product_id'))){
  $product = filter_input(INPUT_GET,'product_id');
  $product = " AND (i.product_id = '$product') ";
}
$condition .= $product;

$raw = "SELECT p.name,u.full_name AS user, i.balance_before, i.balance_after, i.quantity, i.type, i.created_at  FROM inventory_transactions i JOIN products p ON i.product_id = p.id  LEFT JOIN users_tbl u ON i.owner_id = u.user_id 
WHERE i.owner_id IS NOT NULL $condition ORDER BY i.created_at DESC";

$data = $class->rawQuery($raw);

$products = $class->rawQuery("SELECT id, name FROM products WHERE status = 1 ORDER BY name ASC");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Assign Inventory Stock History</title>
    <link href="../css/styles.css" rel="stylesheet" />
    <link href="../css/dataTables.bootstrap4.min.css" rel="stylesheet" />
    <script src="../js/all.min.js"></script>
</head>

<body class="sb-nav-fixed">
    <?php include_once '../header0.php' ?>
    <div id="layoutSidenav">
        <?php include '../menu.php' ?>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid">
                    <div class="card mb-4 mt-4">
                        <div class="card-header">
                            <h4 class="mt-4 d-inline">ASSIGNED INVENTORY LIST</h4>
                            <a class="btn btn-pill btn-outline-dark btn-air-dark float-right"
                                href="/inventory/assign_inventory.php">
                                <i class="fa fa-plus-circle"></i>&nbsp;Assign item to User
                            </a>
                            <form class="mt-4" action="<?= htmlentities($_SERVER['PHP_SELF']) ?>" method="get">
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
                                    <div class="form-group col-md-3">
                                        <label for="sel1">Products</label>
                                        <select class="form-control" name='product_id'>
                                            <option value=''>All</option>
                                            <?php foreach($products as $p): ?>
                                                <option value="<?=$p->id?>" <?php if($p->id == filter_input(INPUT_GET,'product_id')) echo 'selected' ?>><?=$p->name?></option>
                                            <?php endforeach; ?>

                                        </select>
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label>Search Record</label>
                                        <input type="submit" class="form-control btn btn-success btn-sm"
                                            value="Search Records">
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Quantity</th>
                                            <th>Balance before</th>
                                            <th>Balance After</th>
                                            <th>Type</th>
                                            <th>Date</th>
                                            <th>Receiver</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($data as $row): ?>
                                            <tr>
                                                <td><?= $row->name ?></td>
                                                <td><?= $row->quantity ?></td>
                                                <td><?= $row->balance_before ?></td>
                                                <td><?= $row->balance_after ?></td>
                                                <td>
                                                    <?php
                                                    if ($row->type == 'credit')
                                                        echo '<span class="badge badge-success">Credit</span>';
                                                    else
                                                        echo '<span class="badge badge-danger">Debit</span>';
                                                    ?>
                                                </td>
                                                <td><?= $row->created_at ?></td>
                                                <td><?= $row->user ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </main>
            <?php include '../footer.php' ?>
        </div>
    </div>
    <script src="../js/jquery-3.4.1.min.js"></script>
    <script src="../js/bootstrap.bundle.min.js"></script>
    <script src="../js/scripts.js"></script>
    <script src="../js/jquery.dataTables.min.js"></script>
    <script src="../js/dataTables.bootstrap4.min.js"></script>
    <script src="../assets/demo/datatables-demo.js"></script>
    <script src="../assets/sweetalert/sweetalert.min.js"></script>
    <script src="../js/tejiri.js"></script>
</body>

</html>
