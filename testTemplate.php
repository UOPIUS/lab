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
    $class->updateStatus('test_templates', 'id', $id, $new_status);
}
$testTemplates = $class->rawQuery("SELECT t.*,u.*,t_c.id AS category_id,t_c.name AS category_name FROM test_templates t 
LEFT JOIN users_tbl u ON t.created_by = u.user_id JOIN test_categories t_c ON t.category_id = t_c.id ORDER BY t.created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <meta name="csrf_token" content="<?=$_SESSION['token']?>">

    <link rel="icon" type="image/png" href="assets/img/favicon.png" />
    <title>Test Templates Setup | <?=$config->name ?></title>
    <link href="css/styles.css" rel="stylesheet" />
    <!--<link href="bootstrap-wysihtml5/bootstrap-wysihtml5.css" rel="stylesheet" type="text/css">-->
    <link rel="stylesheet" href="jodit/jodit.min.css"/>
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
                    <h4 class="mt-4">Test Templates</h4>
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-table mr-1"></i>Test Templates
                            <button class="btn btn-pill btn-danger btn-air-danger float-right"
                                data-name="Add New Test Template" onclick="displayBlock(this)">
                                <i class="fa fa-calendar"></i>&nbsp;Add New Test Template
                            </button>
                        </div>
                        <div class="card-body" id="divToShow">
                            <div class="table-responsive mt-4">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Category of Test</th>
                                            <th>View Template</th>
                                            <th>Created by</th>
                                            <th>Date created</th>
                                            <th></th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                            $i=1;
                                            foreach($testTemplates as $vl): ?>
                                        <tr>
                                            <td><?=$i++ ?></td>
                                            <td><?=$vl->template_name?></td>
                                            <td><?=$vl->category_name ?></td>
                                            <td>
                                                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal"
                                                    data-tptitle="<?=$vl->category_name?>" data-tpbody="<?=$vl->body?>" data-tname="<?=$vl->template_name?>"
                                                    data-tpref="<?=$vl->id?>" data-target="#showModal">Details<i class="fa fa-play-circle ml-1"></i>
                                                </button>
                                            </td>

                                            <td><?=$vl->full_name ?></td>

                                            <td><?=$vl->created_at ?></td>
                                            <td>
                                                <button type="button" class="btn btn-warning btn-sm" data-toggle="modal"
                                                    data-tptitle="<?=$vl->category_name?>" data-tpbody="<?=$vl->body?>"
                                                    data-categ="<?=$vl->category_id?>" data-tpref="<?=$vl->id?>" data-tname="<?=$vl->template_name?>"
                                                    data-target="#editModal"><i class="fa fa-edit ml-1"></i>
                                                </button>
                                            </td>
                                            <td>
                                                <?php
                                            $status = $vl->status;
                                            if($status == 1)
                                            echo "<a class='btn btn-success btn-sm' href='".$_SERVER['PHP_SELF'].'?id='.$vl->id.'&s='.$status."'>Active</a>";
                                            else echo "<a class='btn btn-danger btn-sm' href='".$_SERVER['PHP_SELF'].'?id='.$vl->id.'&s='.$status."'>Inactive</a>";
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

                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active">Fill &amp; Submit the form to create a new Template</li>
                        </ol>
                        <div class="card-body">
                            <form action="<?=htmlentities($_SERVER['PHP_SELF']) ?>" id="templateForm" class="mb-4">
                                <div class="row mb-4">
                                    <div class="col-lg-6">
                                        <label for="category">Name of Template: <strong
                                                class="text-danger">*</strong></label>
                                        <input class="form-control" id="templateName" required>
                                    </div>
                                    <div class="col-lg-6">
                                        <label for="category">Category of Test: <strong
                                                class="text-danger">*</strong></label>
                                        <select class="form-control" id="category">
                                            <option value="">Test Category</option>
                                            <?php $categories = $class->fetchAll("test_categories"," WHERE status = 1");
                                            foreach($categories as $cat): ?>
                                            <option value="<?=$cat->id ?>"><?=$cat->name ?></option>
                                            <?php endforeach; ?>

                                        </select>
                                    </div>

                                </div>
                                <div class="row mb-4">
                                    <div class="col-lg-12">
                                        <label for="templateBody">Body of Template: <strong
                                                class="text-danger">*</strong>
                                        </label>
                                        <textarea id="templateBody" class="form-control" rows="25"
                                            placeholder="Enter text ..."></textarea>
                                    </div>

                                </div>

                                <div class="text-center m-2" id="response"></div>
                                <div class="m-4">
                                    <button type="submit" class="btn btn-primary" id="submit-user">Create
                                        Template&nbsp;<i class="fa fa-arrow-right"></i></button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </main>
            <?php include 'footer.php' ?>
        </div>
    </div>

    <!-- Show Modal details starts -->
    <div class="modal fade" id="showModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Modal Title</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" id="updateForm">
                        <div class="form-group">
                            <div id="template-mbody"></div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
    <!-- Modal Ends show details -->

    <!-- Modal -->
    <div class="modal fade" id="editModal" role="dialog">
        <div class="modal-dialog modal-dialog-scrollable modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Edit</h4>
                    <p class="text-danger text-left">All fields Marked * are REQUIRED</p>
                </div>
                <div class="modal-body">
                    <form action="<?=htmlentities($_SERVER['PHP_SELF']) ?>" id="editTemplateForm" class="mb-4">
                        <div class="form-group">
                            <label for="templateName">Name of Template: <strong class="text-danger">*</strong>
                            </label>
                            <input class="form-control" name="mtemplateName">
                        </div>
                        <div class="form-group">
                            <label for="mTemplateName">Category: <strong class="text-danger">*</strong></label>
                            <select class="form-control" id="mcategory" name="mcategory">
                                <option value="">Test Category</option>
                                <?php $categories = $class->fetchAll("test_categories"," WHERE status = 1");
                                            foreach($categories as $cat): ?>
                                <option value="<?=$cat->id ?>"><?=$cat->name ?></option>
                                <?php endforeach; ?>
                            </select>
                            <input type="hidden" name="tt" value="t2">
                            <input type="hidden" name="id">
                        </div>
                        <div class="form-group">
                            <label for="templateBody">Template: <strong class="text-danger">*</strong>
                            </label>
                            <textarea id="templateBody2" class="form-control" rows="35" name="mtemplateBody"
                                placeholder="Enter text ..."></textarea>
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
    <!--<script src="bootstrap-wysihtml5/wysihtml5.js" type="text/javascript"></script>-->
    <!--<script src="bootstrap-wysihtml5/bootstrap-wysihtml5.js" type="text/javascript"></script>-->
    <script src="js/jquery.dataTables.min.js"></script>
    <script src="js/dataTables.bootstrap4.min.js"></script>
    <script src="assets/demo/datatables-demo.js"></script>
    <script src="js/tejiri.js"></script>
    <script src="jodit/jodit.min.js"></script>

    <script>
    // $(document).ready(function() {
    //     // $('#templateBody').wysihtml5();
    //     // $('#templateBody2').wysihtml5()
    //     tinymce.init({
    //         selector: '#templateBody',
    //         plugins: [
    //           'advlist', 'autolink', 'link', 'image', 'lists', 'charmap', 'preview', 'anchor', 'pagebreak',
    //           'searchreplace', 'wordcount', 'visualblocks', 'visualchars', 'code', 'fullscreen', 'insertdatetime',
    //           'media', 'table', 'emoticons', 'template', 'help'
    //         ],
    //         toolbar: 'undo redo | styles | bold italic | alignleft aligncenter alignright alignjustify | ' +
    //           'bullist numlist outdent indent | link image | print preview media fullscreen | ' +
    //           'forecolor backcolor emoticons | help',
    //         menubar: 'favs file edit view insert format table',
    //         content_css: 'css/content.css'
    //     });
        
    //     tinymce.init({
    //         selector: '#templateBody2',
    //         plugins: [
    //           'advlist', 'autolink', 'link', 'image', 'lists', 'charmap', 'preview', 'anchor', 'pagebreak',
    //           'searchreplace', 'wordcount', 'visualblocks', 'visualchars', 'code', 'fullscreen', 'insertdatetime',
    //           'media', 'table', 'emoticons', 'template', 'help'
    //         ],
    //         toolbar: 'undo redo | styles | bold italic | alignleft aligncenter alignright alignjustify | ' +
    //           'bullist numlist outdent indent | link image | print preview media fullscreen | ' +
    //           'forecolor backcolor emoticons | help',
    //         menubar: 'favs file edit view insert format table',
    //         content_css: 'css/content.css'
    //     });
    // });
    </script>
    <script>
    const editor = Jodit.make('#templateBody');
    const editor2 = Jodit.make('#templateBody2');
    window.onload = () => {
        
        document.getElementById('templateForm').addEventListener('submit', (e) => {
            e.preventDefault();
            let responseDiv = document.getElementById('response');
            if (!(document.getElementById('templateBody').value && document.getElementById('category')
                    .value)) {
                responseDiv.innerHTML =
                    "<p class='bg-danger text-center p-2 text-white'>All Fields Marked * Are Required</p>";
                return false;
            }
            makeXHR(e, responseDiv, 'request/test_template.php', {
                category: document.getElementById('category').value,
                template_body: document.getElementById('templateBody').value,
                token: document.getElementsByTagName("meta")["csrf_token"].getAttribute("content"),
                templateName: document.getElementById('templateName').value,
                tt: 't1'
            });
        });
    };
    //display full details of template
    $("#showModal").on("show.bs.modal", function(event) {
        var button = $(event.relatedTarget);
        var modal = $(this);
        let decodedHtml = (button.data('tpbody'));
        modal.find(".modal-title").text(button.data('tptitle'));
        modal.find(".modal-body div#template-mbody").html(decodedHtml);
    });

    //attempt to edit template
    $("#editModal").on("show.bs.modal", function(event) {
        var button = $(event.relatedTarget);
        var modal = $(this);
        let decodedHtml = (button.data('tpbody'));
        const category = button.data('categ');
        const previousWYSIHTML = button.data('tpbody')
        setSelectedIndex(document.getElementById("mcategory"), category);
        modal.find(".modal-title").text(button.data('tptitle'));
        modal.find(".modal-body input[name=mtemplateName]").val(button.data('tname'));
        modal.find(".modal-body input[name=mtemplateBody]").val(button.data('tptitle'));
        modal.find(".modal-body input[name=id]").val(button.data('tpref'));
        /********************************
        $('#templateBody2').wysihtml5()
        var editorObj = $("#templateBody2").data('wysihtml5');
        var editor = editorObj.editor;
        editor.setValue(previousWYSIHTML);
        **********************************/
        //$('#templateBody2').data("wysihtml5").editor.setValue(previousWYSIHTML);
        //tinymce.get('editorOne').setContent(contentOne);
        editor2.value = previousWYSIHTML;
        $("#editTemplateForm").submit(function(e) {
            e.preventDefault();
            e.stopPropagation();
            $("#edit-response").html(
                '<p class="bg-warning text-white p-2">Please Wait. . . '
            );
            let url = "request/test_template.php";
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

        });
    });
    //close edit modal
    $('#editModal').on('hidden.bs.modal', function(e) {
        
    })

    </script>
</body>

</html>