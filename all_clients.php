<?php include_once 'functions/Functions.php'; 
if(!($_SESSION['role_id'] && $_SESSION['user_id'])) header('location: login.php');
$class = new Functions();
$ref = (filter_has_var(INPUT_GET,'ref')) ? htmlentities(filter_input(INPUT_GET,'ref')) : $_SESSION['user_id'];
$profile = $class->fetch('users_tbl'," WHERE user_id = '$ref'");
$config = $class->fetch('settings');
$clients = $class->rawQuery("
   SELECT c.fname,c.lname,c.oname,c.ref,c.phone,c.gender,c.created_at,u.full_name 
   FROM clients_tbl AS c JOIN users_tbl AS u ON
    c.created_by = u.user_id  ORDER BY created_at
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
    <title>Clients history | <?= $config->name ?></title>

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
                        <h4 class="mt-4">Registered Clients</h4>
                        <div class="card mb-4">
                            <div class="card-header">
                            <button class="btn btn-pill btn-outline-success" id="exportToExcel">
                                <i class="fa fa-file"></i>&nbsp;Export to Excel
                            </button>
                                <i class="fas fa-table mr-1"></i>Transactions
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
                                                <th>Gender</th>
                                                <th>Created By</th>
                                                <th>Date created</th>
                                            </tr>
                                        </thead>
                                        <tfoot>
                                            <tr>
                                                <th>#</th>
                                                <th>Client ID</th>
                                                <th>Name</th>
                                                <th>Mobile</th>
                                                <th>Gender</th>
                                                <th>Created By</th>
                                                <th>Date created</th>
                                            </tr>
                                        </tfoot>
                                        <tbody>
                                            <?php 
                                            $i=1;
                                            foreach($clients as $x): 
                                            $ref = $class->simple_encrypt($x->ref); ?>
                                            <tr>
                                                <td><?=$i++ ?></td>
                                                <td><a href="client_profile.php?refx=<?=$ref ?>"
                                                        class="btn btn-link"><?=$x->ref ?></a></td>
                                                <td><?=$x->fname.' '.$x->lname. ''.$x->oname ?></td>
                                                <td><?=$x->phone ?></td>
                                                <td><?=$x->gender ?></td>
                                                <td><?=$x->full_name ?></td>
                                                <td><?=$x->created_at ?></td>
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
    <script src="js/xlsx.full.min.js"></script>
    <script src="js/scripts.js"></script>
    <script>
        window.onload=()=>{
            document.getElementById("exportToExcel").addEventListener('click', (event) => {
            event.preventDefault();
            event.stopImmediatePropagation();
            const d = new Date();
            const table = document.getElementById("dataTable");
            let workbook = XLSX.utils.table_to_book(table, {
                sheet: "sheet1"
            });
            return XLSX.writeFile(workbook, "customers"+d.getTime() + ".xlsx")
        })
        }
    </script>

</body>

</html>