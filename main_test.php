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
    $class->updateStatus('sub_labtest_tbl', 'id', $id, $new_status);
}
$vlages = $class->rawQuery("SELECT c.name,c.id,c.cost,cm.name AS 
cm_name,c.status,c.created_at,u.full_name,c.labtest_id FROM users_tbl
 AS u JOIN sub_labtest_tbl AS c ON c.created_by = u.user_id LEFT JOIN 
 test_categories AS cm ON c.labtest_id = cm.id ORDER BY c.created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Main Test Setup | <?=$config->name ?></title>
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
                    <h4 class="mt-4">Lab Tests</h4>
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-table mr-1"></i>Lab Tests
                            <button class="btn btn-pill btn-danger btn-air-danger float-right"
                                data-name="Add New Lab. Test" onclick="displayBlock(this)">
                                <i class="fa fa-calendar"></i>&nbsp;Add New Lab. Test
                            </button>
                        </div>
                        <div class="card-body" id="divToShow">
                            <div class="table-responsive mt-4">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name of Test</th>
                                            <th>Category</th>
                                            <th>Cost</th>
                                            <th>Created by</th>
                                            <th>Date created</th>
                                            <th>Status</th>
                                            <th></th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php 
                                            $i=1;
                                            foreach($vlages as $vl): ?>
                                        <tr>
                                            <td><?=$i++ ?></td>
                                            <td><?=$vl->name ?></td>
                                            <td><?=$vl->cm_name ?></td>
                                            <td> &#8358;<?=$vl->cost?></td>
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
                                                    data-tptitle="<?=$vl->name?>" data-cost="<?=$vl->cost?>"
                                                    data-categ="<?=$vl->labtest_id?>" data-tpref="<?=$vl->id?>"
                                                    data-target="#editModal">Click
                                                    to Edit<i class="fa fa-edit ml-1"></i>
                                                </button>
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

                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active">Fill &amp; Submit the form to create a new Lab Test</li>
                        </ol>
                        <div class="card-body">
                            <form action="<?=htmlentities($_SERVER['PHP_SELF']) ?>" id="vl_form" class="mb-4">
                                <div class="row mb-4">
                                    <div class="col-lg-6">
                                        <label for="category">Test Category: <strong
                                                class="text-danger">*</strong></label>
                                        <select class="form-control" id="cat_id">
                                            <option value="">-Select-</option>
                                            <?php $method = $class->fetchAll('test_categories'," WHERE status = 1"); 
                                                foreach($method as $m): ?>
                                            <option value="<?=$m->id ?>"><?=$m->name ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-lg-6">
                                        <label for="category">Name of Test: <strong
                                                class="text-danger">*</strong></label>
                                        <input type="text" class="form-control" id='test_name'>
                                        <input type="hidden" id="token" value="<?=$_SESSION['token'] ?>">
                                    </div>
                                </div>
                                <div class="row mb-4">
                                    <div class="col-lg-6">
                                        <label for="category">Cost of Test: <strong
                                                class="text-danger">*</strong></label>
                                        <input type="text" class="form-control" id='test_cost'>
                                    </div>

                                </div>

                                <div class="text-center m-2" id="response"></div>
                                <div class="m-4">
                                    <button type="submit" class="btn btn-primary" id="submit-user">Submit <i
                                            class="fa fa-forward"></i></button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </main>
            <?php include 'footer.php' ?>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="editModal" role="dialog">
        <div class="modal-dialog modal-dialog-scrollable">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Edit</h4>
                    <p class="text-danger text-left">All fields Marked * are REQUIRED</p>
                </div>
                <div class="modal-body">
                    <form action="<?=htmlentities($_SERVER['PHP_SELF']) ?>" id="editTestForm" class="mb-4">
                        <div class="form-group">
                            <label for="category">Name of Test: <strong class="text-danger">*</strong></label>
                            <input type="text" class="form-control" name='mfull_name'>
                            <input type="hidden" name="tt" value="stf2arutv">
                            <input type="hidden" name="mtest_id" >
                        </div>
                        <div class="form-group">
                            <label for="category">Category of Test: <strong class="text-danger">*</strong></label>
                            <select class="form-control" id="mcategory" name="mcategory">
                                <option value="">Test Category</option>
                                <?php $categories = $class->fetchAll("test_categories"," WHERE status = 1");
                                            foreach($categories as $cat): ?>
                                <option value="<?=$cat->id ?>"><?=$cat->name ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="category">Cost of Test: <strong class="text-danger">*</strong></label>
                            <input type="text" class="form-control" name='mtest_cost'>
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
        document.getElementById('vl_form').addEventListener('submit', (e) => {
            e.preventDefault();
            let responseDiv = document.getElementById('response');
            if (!(document.getElementById('cat_id').value && document.getElementById('test_name').value &&
                    document.getElementById('test_cost').value)) {
                responseDiv.innerHTML =
                    "<p class='bg-danger text-center p-2 text-white'>All Fields Marked * Are Required</p>";
                return false;
            }
            makeXHR(e, responseDiv, 'request/main_test.php', {
                cat_id: document.getElementById('cat_id').value,
                test_name: document.getElementById('test_name').value,
                test_cost: document.getElementById('test_cost').value,
                token: document.getElementById('token').value,
                tt: 't1'
            });
        });
    };
    //update job category
    $("#editModal").on("show.bs.modal", function(event) {
        event.stopPropagation();
        var button = $(event.relatedTarget);
        var modal = $(this);
        const category = button.data('categ');
        modal.find(".modal-body input[name=mfull_name]").val(button.data('tptitle'));
        modal.find(".modal-body input[name=mtest_id]").val(button.data('tpref'));
        modal.find(".modal-body input[name=mtest_cost]").val(button.data('cost'));
        setSelectedIndex(document.getElementById("mcategory"), category);
        $("#editTestForm").submit(function(e) {
            $("#edit-response").html(
                '<p class="bg-warning text-white p-2"><i class="fa fa-cog fa-spin"></i>Please Wait. . .</p>'
            );
            let url = "request/main_test.php";
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