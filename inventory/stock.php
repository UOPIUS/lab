<?php include '../functions/Functions.php';
$class = new Functions();
if ($class->checkSession($_SESSION['user_id']) === false)
    header('location: logout.php');
//UPDATE STATUS
if (NULL !== filter_input(INPUT_GET, 'id') && NULL !== filter_input(INPUT_GET, 's')) {
    $id = $_REQUEST['id'];
    $status = $_REQUEST['s'];
    $new_status = ($status == '1') ? '0' : '1';
    $class->updateStatus('products', 'id', $id, $new_status);
}
$kindreds = $class->rawQuery("SELECT p.name, c.name category_name, q.name unit, q.quantity, u.full_name author, p.created_at
FROM products p
JOIN inventory_units q ON p.inventory_unit_id = q.id 
JOIN inventory_categories c ON p.inventory_category_id = c.id
JOIN users_tbl u ON p.user_id = u.user_id ORDER BY p.created_at DESC");

$config = $class->fetchSettings();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Inventory | Stock <?= $config->name ?></title>
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
                    <h4 class="mt-4">Inventory Stock</h4>
                    <div class="card mb-4">
                        <div class="card-header">

                            <a class="btn btn-pill btn-danger btn-air-danger float-right" href="create.php">
                                <i class="fa fa-plus-circle"></i>&nbsp;Add New Stock
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive mt-4">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Product</th>
                                            <th>Category</th>
                                            <th>Unit Measured</th>
                                            <th>Quantity in Unit</th>
                                            <th>Created by</th>
                                            <th>Date created</th>
                                            <th>Status</th>
                                            <th></th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php
                                        $i = 1;
                                        foreach ($kindreds as $vl): ?>
                                        <tr>
                                            <td><?= $i++ ?></td>
                                            <td><?= $vl->name ?></td>
                                            <td><?= $vl->category_name ?></td>
                                            <td><?= $vl->unit ?></td>
                                            <td><?= $vl->quantity ?></td>
                                            <td><?= $vl->author ?></td>
                                            <td><?= $vl->created_at ?></td>
                                            <td>
                                                <?php
                                                    $status = $vl->status;
                                                    if ($status == 1)
                                                        echo "<a class='btn btn-success btn-sm' href='" . $_SERVER['PHP_SELF'] . '?id=' . $vl->id . '&s=' . $status . "'>Active</a>";
                                                    else
                                                        echo "<a class='btn btn-danger btn-sm' href='" . $_SERVER['PHP_SELF'] . '?id=' . $vl->id . '&s=' . $status . "'>Inactive</a>";
                                                    ?>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-warning btn-sm" data-toggle="modal"
                                                    data-target="#editModal" data-ref="<?= $vl->id ?>"
                                                    data-name="<?= $vl->name ?>" data-unit="<?= $vl->unit_id ?>">
                                                    Edit <i class="fa fa-edit"></i></button>

                                            </td>
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

    <!-- Template Modal Ends -->
    <script src="../js/jquery-3.4.1.min.js"></script>
    <script src="../js/bootstrap.bundle.min.js"></script>
    <script src="../js/scripts.js"></script>
    <script src="../js/jquery.dataTables.min.js"></script>
    <script src="../js/dataTables.bootstrap4.min.js"></script>
    <script src="../assets/demo/datatables-demo.js"></script>
</body>

</html>