<?php 
include 'functions/Functions.php';
if(!($_SESSION['user_id'] && $_SESSION['role_id'])) header('location: login.php');
$class = new Functions();
$config = $class->fetch('settings');
//UPDATE STATUS
if (NULL !== filter_input(INPUT_GET, 'id') && NULL !== filter_input(INPUT_GET, 's')) {
    $id = $_REQUEST['id'];
    $status = $_REQUEST['s'];
    $new_status = ($status == '1') ? '0' : '1';
    $class->updateStatus('communities_tbl', 'id', $id, $new_status);
}
$communities = $class->rawQuery("SELECT c.name,c.id,c.status,c.created_at,u.full_name FROM users_tbl AS u JOIN communities_tbl AS c ON c.created_by = u.user_id ORDER BY c.created_at");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Communities | <?=$config->name ?></title>
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
                <div class="container-fluid" id="oldDiv">
                    <h4 class="mt-4">Community History</h4>
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-table mr-1"></i>Communities
                            <button class="btn btn-pill btn-danger btn-air-danger float-right" onclick="showDiv()">
                                <i class="fa fa-calendar"></i>&nbsp;Add New
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive mt-4">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name of Community</th>
                                            <th>Created by</th>
                                            <th>Date created</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>#</th>
                                            <th>Name of Community</th>
                                            <th>Created by</th>
                                            <th>Date created</th>
                                            <th>Status</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                        <?php 
                                            $i=1;
                                            foreach($communities as $cm): ?>
                                        <tr>
                                            <td><?=$i++ ?></td>
                                            <td><?=$cm->name ?></td>
                                            <td><?=$cm->full_name ?></td>

                                            <td><?=$cm->created_at ?></td>
                                            <td>
                                                <?php
                                            $status = $cm->status;
                                            if($status == 1)
                                            echo "<a class='btn btn-success btn-sm' href='".$_SERVER['PHP_SELF'].'?id='.$cm->id.'&s='.$status."'>Active</a>";
                                            else echo "<a class='btn btn-danger btn-sm' href='".$_SERVER['PHP_SELF'].'?id='.$cm->id.'&s='.$status."'>Inactive</a>";
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
                            <h4>Add a new Community</h4>
                        </div>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active">Fill &amp; Submit the form to create a new Community</li>
                        </ol>
                        <div class="card-body">
                            <form action="<?=htmlentities($_SERVER['PHP_SELF']) ?>" id="cm_form" class="mb-4">
                                <div class="row">
                                    <div class="col-lg-8">
                                        <label for="category">Name of Community: <strong
                                                class="text-danger">*</strong></label>
                                        <input type="text" class="form-control" id='cm_name'>
                                        <input type="hidden" id="token" value="<?=$_SESSION['token'] ?>">
                                    </div>

                                </div>

                                <div class="text-center m-2" id="response"></div>
                                <div class="m-4">
                                    <button type="submit" class="btn btn-primary" id="submit-user">Submit <i
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
            </main>
            <?php include 'footer.php' ?>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="userModal" role="dialog">
        <div class="modal-dialog modal-dialog-scrollable">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Edit</h4>
                    <p class="text-danger text-left">All fields Marked * are REQUIRED</p>
                </div>
                <div class="modal-body">
                    <form action="<?=htmlentities($_SERVER['PHP_SELF']) ?>" id="editUser" class="mb-4">
                        <div class="form-group">
                            <label for="category">Name of Community: <strong class="text-danger">*</strong></label>
                            <input type="text" class="form-control" name='full_name'>
                            <input type="hidden" name="tt" value="t2">
                        </div>

                        <div class="text-center" id="edit-response"></div>
                        <button type="submit" class="btn btn-primary" id="submit-cm">Submit</button>
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
        document.getElementById('cm_form').addEventListener('submit', (e) => {
            e.preventDefault();
            let responseDiv = document.getElementById('response');
            if (!(document.getElementById('cm_name').value)) {
                responseDiv.innerHTML =
                    "<p class='bg-danger text-center p-2 text-white'>Please Provide a Valid Community Name</p>";
                return false;
            }
            makeXHR(e, responseDiv, 'request/cm_save.php', {
                cm_name: document.getElementById('cm_name').value,
                token: document.getElementById('token').value,
                tt: 't1'
            });
        });
    };
    </script>
</body>

</html>