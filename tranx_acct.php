<?php include_once 'functions/Functions.php'; 
if(!($_SESSION['role_id'] && $_SESSION['user_id'])) header('location: login.php');
$class = new Functions();
$ref = (filter_has_var(INPUT_GET,'ref')) ? htmlentities(filter_input(INPUT_GET,'ref')) : $_SESSION['user_id'];
$profile = $class->fetch('users_tbl'," WHERE user_id = '$ref'");
$config = $class->fetch('settings');
$sales = $class->rawQuery("
    SELECT t.*,p.pay_method AS payment_method,c.fname,c.lname,c.phone AS cphone,c.ref AS client FROM transactions AS t LEFT JOIN
     clients_tbl AS c ON t.client_id = c.ref LEFT JOIN payment_types AS p ON
    t.payment_type = p.id WHERE t.created_by = '$ref' ORDER BY t.created_at DESC
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
    <title>Account Dashboard - <?= $config->name ?></title>

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
                                <?php if($_SESSION['role_id'] == 102): ?>

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
                                                <th>Mode of Pay</th>
                                                <th>Date created</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tfoot>
                                            <tr>
                                                <th>#</th>
                                                <th>Client ID</th>
                                                <th>Name</th>
                                                <th>Mobile</th>
                                                <th>Amount</th>
                                                <th>Mode of Pay</th>
                                                <th>Date created</th>
                                                <th>Status</th>
                                            </tr>
                                        </tfoot>
                                        <tbody>
                                            <?php 
                                            $i=1;
                                            foreach($sales as $x): ?>
                                            <tr>
                                                <td><?=$i++ ?></td>
                                                <td><a href="client_profile.php?rfn=<?=$x->client ?>"
                                                        class="btn btn-link"><?=$x->client ?></a></td>
                                                <td><?=$x->fname.' '.$x->lname. ''.$x->oname ?></td>
                                                <td><?=$x->cphone ?></td>
                                                <td><?=$x->amount ?></td>
                                                <td><?=$x->payment_method ?></td>
                                                <td><?=$x->created_at ?></td>
                                                <td>
                                                    <?php
                                            $status = $user->status;
                                            if($status == 1)
                                            echo "<a class='btn btn-success btn-sm' href='".$_SERVER['PHP_SELF'].'?id='.$x->id.'&s='.$status."'>Successful</a>";
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
                    <div class="container-fluid mt-4" id="newDiv">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h4>Add a new Transaction</h4>
                            </div>
                            <ol class="breadcrumb mb-4">
                                <li class="breadcrumb-item active">
                                    <p class="text-danger text-center">You must Collect Fee From Client Before Filling
                                        this Form</p>
                                </li>
                            </ol>
                            <div class="card-body">
                                <form action="<?=htmlentities($_SERVER['PHP_SELF']) ?>" id="accountForm" class="mb-4"
                                    method='POST'>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <label for="category">First Name: <strong
                                                    class="text-danger">*</strong></label>
                                            <input type="text" class="form-control" id='fname' required>

                                        </div>
                                        <div class="col-lg-6">
                                            <label for="category">Last Name: <strong
                                                    class="text-danger">*</strong></label>
                                            <input type="text" class="form-control" id='lname' required>
                                            <input type="hidden" id='token' value="<?=$_SESSION['token'] ?>">
                                        </div>
                                    </div>
                                    <div class="row mt-4">
                                        <div class="col-lg-6">
                                            <label>Phone Number: <strong class="text-danger">*</strong></label>
                                            <input type="text" class="form-control" id='phone' required maxlength="11">
                                        </div>
                                        <div class="col-lg-6">
                                            <label>Gender: <strong class="text-danger">*</strong></label>
                                            <select class="form-control" id="gender">
                                                <option value="">Select Gender</option>
                                                <option value="Male">Male</option>
                                                <option value="Female">Female</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mt-4">
                                        <div class="col-lg-6">
                                            <label>Method of Payment: <strong class="text-danger">*</strong></label>
                                            <select class="form-control" id="pay_method">
                                                <option value="">Select Method</option>
                                                <?php $method = $class->fetchAll('payment_types'," WHERE status = 1"); 
                                                foreach($method as $m): ?>
                                                <option value="<?=$m->id ?>"><?=$m->pay_method ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>


                                    <div class="text-center m-2" id="response"></div>
                                    <div class="m-4 text-center">
                                        <button type="submit" class="btn btn-primary" id="submit-user">Create Account <i
                                                class="fa fa-forward"></i></button>
                                    </div>
                                </form>
                            </div>
                            <div class="card-footer">
                                <button type="button" class="btn btn-danger float-right" onclick="hideDiv()">Cancel
                                </button>
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
    <script>
    window.onload = () => {
        document.getElementById('accountForm').addEventListener('submit', (e) => {
            e.preventDefault();
            let responseDiv = document.getElementById('response');
            if (!(document.getElementById('fname').value &&
                    document.getElementById('lname').value && document.getElementById('phone').value &&
                    document.getElementById('gender').value)) {
                responseDiv.innerHTML =
                    "<p class='bg-danger text-center p-2 text-white'>All fields Marked * are Required</p>";
                setTimeout(() => {
                    responseDiv.innerHTML = '';
                }, 3000);
                return false;
            }
            makeXHR(e, responseDiv, 'request/add_tranx.php', {
                'fname': document.getElementById('fname').value,
                'lname': document.getElementById('lname').value,
                'phone': document.getElementById('phone').value,
                'gender': document.getElementById('gender').value,
                token: document.getElementById('token').value,
                pay_method: document.getElementById('pay_method').value,
                tt: 't1'
            });
        });
    };
    </script>
</body>

</html>