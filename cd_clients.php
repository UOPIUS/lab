<?php include_once 'functions/Functions.php'; 
$class = new Functions();
if($class->checkSession($_SESSION['user_id'])  === false) header('location: logout.php');

$ref = (filter_has_var(INPUT_GET,'ref')) ? htmlentities(filter_input(INPUT_GET,'ref')) : $_SESSION['user_id'];
$profile = $class->fetch('users_tbl'," WHERE user_id = '$ref'");
$config = $class->fetch('settings');
$clients = $class->rawQuery("
   SELECT fname,lname,oname,ref,phone,gender,blood_group,dob,
   status,created_at FROM clients_tbl ORDER BY created_at DESC
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
                            <?php if($_SESSION['role_id'] == 102): ?>
                                <button class="btn btn-danger float-right mb-2" onclick="displayBlock(this)"
                                    data-name='Add New Client'>
                                    <i class="fa fa-plus-circle"></i>&nbsp;Add New Client
                                </button>
                            <?php endif; ?>
                                <i class="fas fa-table mr-1"></i>Clients
                            </div>
                            <div class="card-body">
                                <div class="table-responsive mt-4" id="divToShow">
                                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Client ID</th>
                                                <th>Name</th>
                                                <th>Mobile</th>
                                                <th>Gender</th>
                                                <th>Blood Group</th>
                                                <th>Age</th>
                                                <th>Date created</th>
                                            </tr>
                                        </thead>
                                      
                                        <tbody>
                                            <?php 
                                            $i=1;
                                            foreach($clients as $x):
                                            $id = $class->simple_encrypt($x->ref,'e');
                                            ?>
                                            <tr>
                                                <td><?=$i++ ?></td>
                                                <td><a><?=$x->ref ?></a></td>
                                                <td><?=$x->fname.' '.$x->lname. ' '.$x->oname ?></td>
                                                <td><?=$x->phone ?></td>
                                                <td><?=$x->gender ?></td>
                                                <td><?=$x->blood_group ?></td>
                                                <td>
                                                <?php ;
                                                 
                                                     $dob = $x->dob;
                                                     $age = floor((time() - strtotime($dob)) / 31556926);
                                                     echo $age.'YRS';
                                                 ?>
                                                 </td>
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
    <script src="js/scripts.js"></script>
</body>

</html>