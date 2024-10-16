<?php include 'functions/Functions.php'; $class = new Functions();
if($class->checkSession($_SESSION['user_id'])  === false) header('location: logout.php');
//UPDATE STATUS
if (NULL !== filter_input(INPUT_GET, 'id') && NULL !== filter_input(INPUT_GET, 's')) {
    $id = $_REQUEST['id'];
    $status = $_REQUEST['s'];
    $new_status = ($status == '1') ? '0' : '1';
    $class->updateStatus('test_categories', 'id', $id, $new_status);
}
$kindreds = $class->rawQuery("SELECT c.name,c.id,c.status,c.created_at,u.full_name 
FROM users_tbl AS u JOIN expense_categories c ON c.created_by = u.user_id ORDER BY c.created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Expense Category Setup | <?=$config->name ?></title>
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
                    <h4 class="mt-4"> Expense Types</h4>
                    <div class="card mb-4">
                        <div class="card-header">
                            
                            <button class="btn btn-pill btn-danger btn-air-danger float-right"
                                onclick="displayBlock(this)" data-name="Add New Expense Type">
                                <i class="fa fa-plus-circle"></i>&nbsp;Add New Expense Type
                            </button>
                        </div>
                        <div class="card-body" id="divToShow">
                            <div class="table-responsive mt-4">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Expense Category</th>
                                            <th>Created by</th>
                                            <th>Date created</th>
                                            <th>Status</th>
                                            <th></th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php 
                                            $i=1;
                                            foreach($kindreds as $vl): ?>
                                        <tr>
                                            <td><?=$i++ ?></td>
                                            <td><?=$vl->name ?></td>

                                            <td><?=$vl->full_name ?></td>

                                            <td><?=$vl->created_at ?></td>
                                            <td>
                                                <?php
                                            $status = $vl->status;
                                            if($status == 1)
                                            echo "<a class='btn btn-success btn-sm' href='".$_SERVER['PHP_SELF'].'?id='.$vl->id.'&s='.$status."'>Active</a>";
                                            else echo "<a class='btn btn-danger btn-sm' href='".$_SERVER['PHP_SELF'].'?id='.$vl->id.'&s='.$status."'>Inactive</a>";
                                            ?>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-warning btn-sm" data-toggle="modal"
                                                    data-target="#editModal" data-ref="<?=$vl->id?>"
                                                    data-name="<?=$vl->name?>">
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
                <div class="container-fluid mt-4 d-none" id="divToHide">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h4>Expense Category</h4>
                        </div>

                        <div class="card-body">
                            <form action="<?=htmlentities($_SERVER['PHP_SELF']) ?>" id="krd_form" class="mb-4">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label for="category">Category of Expense: <strong
                                                class="text-danger">*</strong></label>
                                        <input type="text" class="form-control" id='category'>
                                        <input type="hidden" id="token" value="<?=$_SESSION['token'] ?>">
                                    </div>

                                </div>

                                <div class="text-center m-2" id="response"></div>
                                <div class="m-4">
                                    <button type="submit" class="btn btn-primary" id="submit-user">Submit
                                        <i class="fa fa-forward"></i>
                                    </button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </main>
            <?php include 'footer.php' ?>
        </div>
    </div>

    <!-- Modal Edit Modal-->
    <div class="modal fade" id="editModal" role="dialog">
        <div class="modal-dialog modal-dialog-scrollable">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Edit</h4>
                    <p class="text-danger text-left">All fields Marked * are REQUIRED</p>
                </div>
                <div class="modal-body">
                    <form action="<?=htmlentities($_SERVER['PHP_SELF']) ?>" id="editCat" class="mb-4" method="POST">
                        <div class="form-group">
                            <label for="category">Name of Category: <strong class="text-danger">*</strong></label>
                            <input type="text" class="form-control" name='expense_cat'>
                            <input type="hidden" class="form-control" name='expense_id'>
                            <input type="hidden" name="tt" value="sf2A">
                        </div>

                        <div class="text-center" id="edit-response"></div>
                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-primary" id="submit-cm"><i class="fa fa-save"></i>
                                Submit</button>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>

        </div>
    </div>

    <!-- Template Modal Ends -->
    <script src="js/jquery-3.4.1.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/scripts.js"></script>
    <script src="js/jquery.dataTables.min.js"></script>
    <script src="js/dataTables.bootstrap4.min.js"></script>
    <script src="assets/demo/datatables-demo.js"></script>
    <script src="js/tejiri.js"></script>
    <script>
    window.onload = () => {
        document.getElementById('krd_form').addEventListener('submit', (e) => {
            e.preventDefault();
            let responseDiv = document.getElementById('response');
            if (!(document.getElementById('category').value)) {
                responseDiv.innerHTML =
                    "<p class='bg-danger text-center p-2 text-white'>Please Provide a Valid Category Name</p>";
                return false;
            }
            makeXHR(e, responseDiv, 'request/expense_division.php', {
                krd_name: document.getElementById('category').value,
                token: document.getElementById('token').value,
                tt: 't1'
            });
        });
    };
    //update job category
    $("#editModal").on("show.bs.modal", function(event) {

        var button = $(event.relatedTarget);
        var modal = $(this);
        modal.find(".modal-body input[name=expense_cat]").val(button.data('name'));
        modal.find(".modal-body input[name=expense_id]").val(button.data('ref'));

        $("#editCat").submit(function(e) {
            $("#edit-response").html(
                '<p class="bg-warning text-white p-2"><i class="fa fa-cog fa-spin"></i>Please Wait. . .</p>'
            );
            let url = "request/expense_division.php";
            // Send the data using post
            var posting = $.post(url, $(this).serialize());
            posting.done(function(data) {
                let result = JSON.parse(data);
                if (200 == result.status) {
                    $("#edit-response").html(
                        '<p class="bg-success text-white p-2">' + result.message + "</p>"
                    );
                    window.location.reload(true);
                    return;
                }
                $("#edit-response").html(
                    '<p class="bg-danger text-white p-2">' + result.message + "</p>"
                );
            });
            e.preventDefault();
        });
    });
    </script>
</body>

</html>