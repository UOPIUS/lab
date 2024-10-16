<?php 
include 'functions/Functions.php';
if(!(101 == $_SESSION['role_id'] && $_SESSION['role_id'])) header('location: login.php');
$class = new Functions();
//UPDATE STATUS
if (NULL !== filter_input(INPUT_GET, 'id') && NULL !== filter_input(INPUT_GET, 's')) {
    $id = htmlentities($_REQUEST['id'],ENT_QUOTES);
    $status = htmlentities($_REQUEST['s'],ENT_QUOTES);
    $new_status = ($status == '1') ? '0' : '1';
    $class->updateStatus('users_tbl', 'user_id', $id, $new_status);
}

$users = $class->rawQuery("SELECT u.*,r.description FROM users_tbl AS u JOIN roles AS r ON r.id = u.role_id WHERE uflag = 'S' ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Users | Saltcity Diagnostics</title>
    <link href="css/styles.css" rel="stylesheet" />
    <link href="css/dataTables.bootstrap4.min.css" rel="stylesheet" />
    <script src="js/all.min.js"></script>
</head>

<body class="sb-nav-fixed">
    <?php include_once 'header.php'?>
    <div id="layoutSidenav">
        <?php include 'menu.php' ?>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid">
                    <h4 class="mt-4">Users History</h4>
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-table mr-1"></i>System users
                            <button class="btn btn-pill btn-danger btn-air-danger float-right" onclick="displayBlock(this)" data-name="Add New User">
                                <i class="fa fa-user"></i>&nbsp;Add New User
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive mt-4" id="divToShow">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                   
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Mobile</th>
                                            <th>Username</th>
                                            <th>Role </th>
                                            <th>Last Access</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                            $i=1;
                                            foreach($users as $user): ?>
                                        <tr>
                                            <td><?=$i++ ?></td>
                                            <td>
                                                <?php if('R' == $user->uflag): ?>
                                                <a href="url_referral.php?uref=<?=$uref ?>" class="btn btn-link">
                                                        <?=$user->full_name ?>
                                                </a>
                                                <?php else: ?>
                                                    <?=$user->full_name ?>
                                                <?php endif; ?>
                                                
                                            </td>
                                            <td><?=$user->phone ?></td>
                                            <td><?=$user->username ?></td>

                                            <td><?=$user->description ?></td>
                                            <td><?=$user->last_login ?></td>
                                            <td>
                                                <?php
                                            $status = $user->status;
                                            if($status == 1)
                                            echo "<a class='btn btn-success btn-sm' href='".$_SERVER['PHP_SELF'].'?id='.$user->user_id.'&s='.$status."'>Active</a>";
                                            else echo "<a class='btn btn-danger btn-sm' href='".$_SERVER['PHP_SELF'].'?id='.$user->user_id.'&s='.$status."'>Inactive</a>";
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
                <div class="container-fluid mt-4 d-none" id="divToHide">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h4>Add a new System User</h4>
                        </div>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active">Fill &amp; Submit the form to create a user</li>
                        </ol>
                        <div class="card-body">
                            <form action="<?=htmlentities($_SERVER['PHP_SELF']) ?>" id="saveUserForm" class="mb-4" method='POST'>
                                <div class="row">
                                    <div class="col-lg-4">
                                        <label for="category">Full Name: <strong class="text-danger">*</strong></label>
                                        <input type="text" class="form-control" id='full_name'>

                                    </div>
                                    <div class="col-lg-4">
                                        <label>Mobile: <strong class="text-danger">*</strong></label>
                                        <input type="text" class="form-control" id='phone' required maxlength="11">
                                    </div>
                                    <div class="col-lg-4">
                                        <label>Role: <strong class="text-danger">*</strong></label>
                                        <select class="form-control" id="role_id">
                                    <option value="">Select role</option>
                                    <?php $roles = $class->fetchAll('roles', " WHERE id <> 104");
                                        foreach ($roles as $b) : ?>
                                    <option value="<?= $b->id ?>"><?= $b->description ?></option>
                                    <?php endforeach; ?>
                                </select>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-lg-6">
                                    <label for="email">Username: <strong class="text-danger">*</strong></label>
                                    <input type="text" class="form-control" id='username' required value="<?=$config->email ?>">
                                    </div>
                                    <div class="col-lg-6">
                                    <label for="password">Password: <strong class="text-danger">Default is
                                            12345</strong>
                                    </label>
                                    <input type="password" class="form-control" id='password'>
                                    </div>
                                </div>
                                
                                <div class="text-center m-2" id="response"></div>
                                <div class="m-4 text-center">
                                <button type="submit" class="btn btn-primary" id="submit-user">Create user <i class="fa fa-forward"></i></button>
                                </div>
                            </form>
                        </div>
                        
                    </div>
                </div>
            </main>
            <?php include 'footer.php' ?>
        </div>
    </div>
    //add user modal

    <!-- Modal -->
    <div class="modal fade" id="userModal" role="dialog">
        <div class="modal-dialog modal-dialog-scrollable">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add a Data Entry Staff</h4>
                    <p class="text-danger text-left">All fields Marked * are REQUIRED</p>
                </div>
                <div class="modal-body">
                    <form action="<?=htmlentities($_SERVER['PHP_SELF']) ?>" id="updateForm" class="mb-4">
                        <div class="form-group">
                            <label for="category">Full Name: <strong class="text-danger">*</strong></label>
                            <input type="text" class="form-control" name='full_name'>
                            <input type="hidden" name="tt" value="t2">
                        </div>
                        <div class="form-group">
                            <label>Mobile: <strong class="text-danger">*</strong></label>
                            <input type="text" class="form-control" name='mobile_phone' required maxlength="11">
                        </div>
                        <div class="form-group">
                            <label for="email">Email: <strong class="text-danger">*</strong></label>
                            <input type="email" class="form-control" name='email' required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password: <strong class="text-danger">Default is
                                    12345</strong></label>
                            <input type="password" class="form-control" name='password'>
                        </div>
                        <div class="text-center" id="response"></div>
                        <button type="submit" class="btn btn-primary" id="submit-user">Submit</button>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>

        </div>
    </div>
    <script src="js/jquery-3.4.1.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/scripts.js"></script>
    <script src="js/jquery.dataTables.min.js"></script>
    <script src="js/dataTables.bootstrap4.min.js"></script>
    <script src="assets/demo/datatables-demo.js"></script>
    <script src="js/tejiri.js"></script>
    <script>
    window.onload = () => {
        document.getElementById('saveUserForm').addEventListener('submit',(e)=>{
            e.preventDefault();
            let responseDiv = document.getElementById('response');
            if(!(document.getElementById('username').value 
            && document.getElementById('full_name').value && document.getElementById('phone').value && document.getElementById('role_id').value)) 
            {responseDiv.innerHTML = "<p class='bg-danger text-center p-2 text-white'>Please Provide a Valid username and Password</p>"; return false; }
            makeXHR(e,responseDiv,'request/save_user.php',{
                'full_name':document.getElementById('full_name').value,
                'password':document.getElementById('password').value,
                'phone':document.getElementById('phone').value,
                'username':document.getElementById('username').value,
                'role_id':document.getElementById('role_id').value,
                tt:'t1'
            });
        });
    };
    </script>
</body>

</html>