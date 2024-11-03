<?php include '../functions/Functions.php';
$class = new Functions();
if ($class->checkSession($_SESSION['user_id']) === false)
    header('location: ../logout.php');

$config = $class->fetchSettings();

$condition = "";

$product = "";
if(filter_has_var(INPUT_GET, 'product_id') && preg_match('/^[0-9]+$/', filter_input(INPUT_GET,'product_id'))){
  $product = filter_input(INPUT_GET,'product_id');
  $product = " AND (st.product_id = '$product') ";
}
$condition .= $product;

$raw = "SELECT p.name productName, st.unit, st.balance, st.created_at, st.updated_at, u.full_name as user,un.name AS measured
FROM user_stocks st JOIN products p ON st.product_id = p.id 
LEFT JOIN users_tbl u ON st.owner_id = u.user_id 
JOIN inventory_units un ON p.inventory_unit_id = un.id
WHERE st.owner_id = '{$_SESSION['user_id']}'
ORDER BY st.created_at DESC";

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

                            <form class="mt-4" action="<?= htmlentities($_SERVER['PHP_SELF']) ?>" method="get">
                                <div class="form-row">

                                    <div class="form-group col-md-3">
                                        <label for="sel1">Products</label>
                                        <select class="form-control" name='product_id'>
                                            <option value=''>All</option>
                                            <?php foreach($products as $p): ?>
                                            <option value="<?=$p->id?>"
                                                <?php if($p->id == filter_input(INPUT_GET,'product_id')) echo 'selected' ?>>
                                                <?=$p->name?></option>
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
                                            <th>Current Balance</th>
                                            <th>Pieces</th>
                                            <th>Owner</th>
                                            <th>Date</th>
                                            <th>Last update</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($data as $row): ?>
                                        <tr>
                                            <td><?= $row->productName ?></td>
                                            <td><?= $row->balance ?>[<?=$row->measured?>]</td>
                                            <td><?= $row->unit ?></td>
                                            <td><?= $row->user ?></td>
                                            <td><?= $row->created_at ?></td>
                                            <td><?= $row->updated_at ?></td>
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