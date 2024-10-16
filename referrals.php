<?php include_once 'functions/Functions.php'; 
$class = new Functions();
if(!($_SESSION['user_id'] && $_SESSION['role_id'])) header('location: logout.php');
include_once 'functions/validate_session.php';
$config = $class->fetch('settings');
//UPDATE STATUS
if (NULL !== filter_input(INPUT_GET, 'id') && NULL !== filter_input(INPUT_GET, 's')) {
    $id = $_REQUEST['id'];
    $status = $_REQUEST['s'];
    $new_status = ($status == '1') ? '0' : '1';
    $class->updateStatus('users_tbl', 'user_id', $id, $new_status);
}
$clients = $class->rawQuery("
   SELECT full_name AS name,src,user_id,phone,status,created_at,username FROM users_tbl WHERE uflag = 'R' ORDER BY created_at DESC
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
    <title>Referral history | <?= $config->name ?></title>
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
                        <h4 class="mt-4">Registered Referrals</h4>
                        <div class="card mb-4">
                            <div class="card-header">
                                <?php if($_SESSION['role_id'] == 106): ?>
                                <button class="btn btn-danger float-right mb-2" onclick="displayBlock(this)"
                                    data-name='Add New Referral'>
                                    <i class="fa fa-plus-circle"></i>&nbsp;Add New Referral
                                </button>
                                <?php endif; ?>
                                <i class="fas fa-table mr-1"></i>Referrals
                            </div>
                            <div class="card-body">
                                <div class="table-responsive mt-4" id="divToShow">
                                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Name</th>
                                                <th>Mobile</th>
                                                <th>Destination</th>
                                                <th>Email</th>
                                                <th>Date created</th>
                                                <th></th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php 
                                            $i=1;
                                            foreach($clients as $x):
                                            $id = $class->simple_encrypt($x->user_id,'e');
                                            ?>
                                            <tr>
                                                <td><?=$i++ ?></td>
                                                <td><a href="url_referral.php?uref=<?=$id ?>"><?=$x->name ?></a></td>
                                                <td><?=$x->phone ?></td>
                                                <td><?=$x->src?></td>
                                                <td><?=$x->username ?></td>
                                                <td><?=$x->created_at ?></td>
                                                <td>
                                                <?php
                                            $status = $x->status;
                                            if($status == 1)
                                            echo "<a class='btn btn-success btn-sm' href='".$_SERVER['PHP_SELF'].'?id='.$x->user_id.'&s='.$status."'>Active</a>";
                                            else echo "<a class='btn btn-danger btn-sm' href='".$_SERVER['PHP_SELF'].'?id='.$x->user_id.'&s='.$status."'>Inactive</a>";
                                            ?>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>

                                <form id="divToHide" class="d-none">
                                    
                                    <p class="text-left text-danger">All fields Marked * are required</p>
                                    <div class="form-row">
                                        <div class="form-group col-md-12">
                                            <label for="fname">Full Name <sup class="text-danger">*</sup></label>
                                            <input type="text" class="form-control" id="fname">
                                        </div>
                                       
                                        <div class="form-group col-md-6">
                                            <label for="oname">Phone Number <sup class="text-danger">*</sup></label>
                                            <input type="text" class="form-control" id="phone" maxlength="11">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="username">Username <sup class="text-danger">*</sup></label>
                                            <input type="email" class="form-control" id="username">
                                        </div>
                                        
                                        <div class="form-group col-md-12">
                                            <label for="address">Where is this Referrral Coming from?&nbsp;(<small
                                                    class="text-danger">Required</small>)</label>
                                            <textarea class="form-control" aria-label="address" id="address"></textarea>
                                        </div>
                                       
                                        <input type="hidden" id="_token" value="<?=$_SESSION['token']?>">
                                        <div class="col-md-12 m-2" id='s1response'></div>
                                    </div>
                                    <div class="text-center">
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-save"></i> Save Referral
                                        </button>
                                    </div>

                                </form>
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
    <script type="text/javascript">
    window.onload = () => {
        document.getElementById('divToHide').addEventListener('submit', (e) => {
            let t = document.getElementById("s1response");
            makeXHR(e, t, "request/bMNbgqcqykedvkkXMskJ.php", {
                fname: document.getElementById("fname").value,
                phone: document.getElementById("phone").value,
                username: document.getElementById("username").value,
                token: document.getElementById('_token').value,
                address: document.getElementById('address').value,
                sst: 's2f'
            })
        });
    };
    </script>
</body>

</html>