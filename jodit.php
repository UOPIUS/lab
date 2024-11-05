<?php
include 'functions/Functions.php';
$class = new Functions();
include_once 'functions/validate_session.php';
if (!($_SESSION['user_id'] && $_SESSION['role_id']))
    header('location: login.php');

$config = $class->fetch('settings');
$txref = $class->simple_encrypt(trim(htmlentities(filter_input(INPUT_GET, 'refx'))), 'd');
$tranx = $class->fetch('transactions', " WHERE id = '$txref'");
$customer = $class->fetch('clients_tbl', " WHERE ref = '$tranx->client_id'");

$config = $class->fetch('settings');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <meta name="csrf_token" content="<?= $_SESSION['token'] ?>">
    <title>Report Test</title>
    <link href="css/styles.css" rel="stylesheet" />
    <link rel="icon" href="assets/img/favicon.png" />
    <!--<link href="bootstrap-wysihtml5/bootstrap-wysihtml5.css" rel="stylesheet" type="text/css">-->
    <link rel="stylesheet" href="jodit/jodit.min.css" />
    <script src="js/all.min.js"></script>
</head>

<body class="sb-nav-fixed">
    <?php include_once 'header.php' ?>
    <div id="layoutSidenav">
        <?php include 'menu.php' ?>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid">
                    <h4 class="mt-4">Patient Infomation</h4>
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-user mr-1"></i> Test ID: <?= $txref ?>
                        </div>
                        <div class="card">
                            <!-- Nav tabs -->
                            <ul class="nav nav-tabs customtab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-toggle="tab" href="#home2" role="tab"
                                        aria-selected="true">
                                        <span class="hidden-sm-up">
                                            <i class="fa fa-home"></i>
                                        </span>
                                        <span class="hidden-xs-down">Test </span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#profile2" role="tab"
                                        aria-selected="false">
                                        <span class="hidden-sm-up">
                                            <i class="fa fa-flask"></i>
                                        </span>
                                        <span class="hidden-xs-down">Check Reports</span></a>
                                </li>
                            </ul>
                            <!-- Tab panes -->
                            <div class="tab-content pl-2">
                                <div class="tab-pane active" id="home2" role="tabpanel">
                                    <div class="p-4">
                                        <div class="form-row">
                                            <div class="form-group col-md-12">
                                                <label for="full_name">Full Name</label>
                                                <input type="text" class="form-control" disabled
                                                    value="<?= $customer->fname . " " . $customer->lname . " " . $customer->oname ?>">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="oname">Gender</label>
                                                <input type="text" class="form-control" value="<?= $customer->gender ?>"
                                                    disabled>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="dob">Age</label>
                                                <input type="text" class="form-control" disabled value="<?php
                                                $dob = $customer->dob;
                                                //$age = floor((time() - strtotime($dob)) / 31556926);
                                                echo $dob . "YRS";
                                                ?>">
                                            </div>
                                            <div class="form-group col-md-12">
                                                <label>Referred by(Doctor who referred Patient) to CAPITAL
                                                    MEDICARE</label>
                                                <input type="text" class="form-control"
                                                    value="<?= $customer->blood_group ?>" disabled>
                                            </div>
                                            <input type="hidden" id="_token" value="<?= $_SESSION['token'] ?>">
                                            <input type="hidden" id="userRef"
                                                value="<?= filter_input(INPUT_GET, 'ini_id') ?>">
                                            <div class="col-md-12 m-2" id='s1response'></div>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-borderless">
                                                <thead>
                                                    <tr>
                                                        <th class="bg-warning text-white" colspan="3">
                                                            Test to Run
                                                        </th>
                                                        <th>Kits</th>
                                                        <th></th>

                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $tests = $class->fetchAll("tests_taken", " WHERE tranx_id = '$txref'");
                                                    $i = 1;
                                                    foreach ($tests as $t): ?>
                                                        <tr>
                                                            <td><?= $i++ . '. ' . $class->fetchColumn('sub_labtest_tbl', 'name', 'id', $t->test_id) ?>
                                                            </td>
                                                            <td></td>
                                                            <td></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>


                                    </div>
                                </div>
                                <div class="tab-pane" id="profile2" role="tabpanel">
                                    <div class="p-4">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th colspan="4" class="bg-secondary text-white">
                                                            ANALYSIS AND RESULT
                                                        </th>
                                                    </tr>
                                                    <tr>
                                                        <th>Test</th>
                                                        <th>Test Report</th>
                                                        <th>Patient Result</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $tests = $class->fetchAll("tests_taken", " WHERE tranx_id = '$txref'");
                                                    foreach ($tests as $t):
                                                        $testInfo = $class->fetch('sub_labtest_tbl', " WHERE id = '$t->test_id'");
                                                        $testCategory = $testInfo->labtest_id;
                                                        ?>
                                                        <tr>
                                                            <td><?php $pformTestName = $testInfo->name;
                                                            echo $pformTestName; ?>
                                                            </td>
                                                            <td>
                                                                <?php if ($_SESSION['role_id'] == 106 || $_SESSION['role_id'] == 105) { ?>
                                                                    <button type="button" class="btn btn-primary btn-sm"
                                                                        data-toggle="modal"
                                                                        data-pformtestname="<?= $pformTestName ?>"
                                                                        data-pformtestkey="<?= $t->id ?>"
                                                                        data-pformtestdetail="<?= $t->test_result ?>"
                                                                        data-categoryid="<?= $testCategory ?>"
                                                                        data-target="#makeReportModal">Edit Result<i
                                                                            class="fa fa-edit ml-1"></i>
                                                                    </button>
                                                                <?php } else
                                                                    "<button class='btn btn-default btn-sm'>NA</button>"; ?>
                                                            </td>
                                                            <td>
                                                                <?php if ($t->test_result && ($_SESSION['role_id'] == 106 || $_SESSION['role_id'] == 105)) { ?>
                                                                    <a href="#" data-xf="<?= $t->tranx_id ?>"
                                                                        data-zf="<?= $t->id ?>"
                                                                        class="btn btn-warning btn-sm aLink">Click
                                                                        for
                                                                        Test Print<i class="fa fa-play-circle ml-1"></i>
                                                                    </a>
                                                                <?php } else {
                                                                    echo "NA";
                                                                } ?>
                                                            </td>
                                                            <td>
                                                                <?php if ($t->test_result)
                                                                    echo "<i class='fa fa-check-circle text-success'></i>";
                                                                else
                                                                    echo "<i class='fa fa-times text-danger'></i>"; ?>
                                                            </td>

                                                        </tr>

                                                    <?php endforeach; ?>
                                                </tbody>
                                                <!--
                                                <?php if ($tranx->lab_scientist_flag == 0): ?>
                                                <tfoot>
                                                    <tr>
                                                        <td colspan="4">
                                                            <div class="text-center">
                                                                <button id="finalSubmit" data-referral="<?= $txref ?>"
                                                                    class="btn btn-lg btn-danger"><i
                                                                        class="fa fa-lock"></i>&nbsp;Submit All Test
                                                                    Result</button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tfoot>
                                                <?php endif; ?>
                                                -->
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </main>
            <?php include 'footer.php' ?>
        </div>
    </div>
    <!-- Modal starts -->

    <div class="modal fade" id="makeReportModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Report Medical Test</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" id="analyseTestForm">
                        <div class="form-row">
                            <!--
                            <div class="form-group col-lg-6">
                                <label>Rhesus factor<sup class="text-danger">*</sup></label>
                                <select class="form-control" name="rhesus">
                                    <option value="">Choose...</option>
                                    <option value="-">Negative(-)</option>
                                    <option value="+">Positive(+)</option>
                                </select>
                            </div>
                        -->
                            <div class="form-group col-lg-6">
                                <label>Specimen Sample<sup class="text-danger">*</sup></label>
                                <select class="form-control" name="specimen">
                                    <option value="">Choose...</option>
                                    <?php $specimens = $class->fetchAll('specimens');
                                    foreach ($specimens as $sp): ?>
                                        <option value="<?= $sp->id ?>"><?= $sp->name ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group col-lg-6">
                                <label>Choose Template</label>
                                <select class="form-control" id='template'>
                                    <option value=''>Choose...</option>
                                    <?php $templates = $class->fetchAll('test_templates');
                                    foreach ($templates as $t): ?>
                                        <option value="<?= $t->body ?>"><?= $t->template_name ?? 'Template ' . time() ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>

                            </div>
                            <div class="form-group col-lg-12">
                                <label>Result Analysis</label>
                                <textarea id="templateBody" class="form-control" rows="40"
                                    placeholder="Enter Test Result ..." name="test_result"></textarea>
                            </div>
                        </div>
                        <input name="token" type="hidden" value="<?= $_SESSION['token'] ?>">
                        <input name="each_test" id="each-test" type="hidden">
                        <input value="s1ftest" name="tst1form" type="hidden">
                        <div id="analyse-response" class="text-center"></div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>

            </div>
        </div>
    </div>
    <!-- Modal Ends -->
    <!-- show Test Modal ends -->
    <script src="js/jquery-3.4.1.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/scripts.js"></script>
    <script src="js/tejiri.js"></script>
    <!--<script src="bootstrap-wysihtml5/wysihtml5.js" type="text/javascript"></script>-->
    <!--<script src="bootstrap-wysihtml5/bootstrap-wysihtml5.js" type="text/javascript"></script>-->
    <script src="jodit/jodit.min.js"></script>
    <script src="assets/sweetalert/sweetalert.min.js"></script>

    <script>
        const editor = Jodit.make('#templateBody');
        window.onload = () => {
            document.getElementById('template').addEventListener('change', (t) => {
                //load template into editor
                editor.value = t.target.value
            });
        };

        function ajaxLoadCat(e) {
            var data = {
                category: e
            };
            var categories = document.getElementById("template");
            categories.innerHTML = "<option value=''>-Select - </option>"
            let xhr = new XMLHttpRequest();
            xhr.open('POST', 'request/ajax_template.php');
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.send(new URLSearchParams(data).toString());
            xhr.onload = function () {
                if (xhr.status != 200) {
                    console.log(xhr.statusText);
                } else {
                    var jsonData = JSON.parse(xhr.response);
                    var jsonLength = jsonData.data.length;
                    for (var i = 0; i < jsonLength; i++) {
                        var counter = jsonData.data[i];
                        var newSelect = document.createElement("option");
                        newSelect.value = counter.body;
                        newSelect.text = counter.name;
                        categories.appendChild(newSelect);
                    }
                }
            };
        }
        $("#makeReportModal").on("show.bs.modal", function (event) {
            var button = $(event.relatedTarget);
            var modal = $(this);
            modal.find(".modal-title").text(button.data('pformtestname'));
            modal.find(".modal-body input#each-test").val(button.data('pformtestkey'));

            editor.value = button.data('pformtestdetail');

            //populate the chosen category
            ajaxLoadCat(button.data('categoryid'));

            $("#analyseTestForm").submit(function (e) {
                let url = "request/testform_report.php",
                    $response = $("#analyse-response");
                // Send the data using post
                var posting = $.post(url, $(this).serialize());
                $response.html("<p class='bg-warning p-2 text-white'>Please Wait . . . </p>").fadeIn();
                posting.done(function (data) {
                    let result = JSON.parse(data);
                    if (200 == result.status) {
                        $response.html(
                            '<p class="bg-success text-white p-2">' + result.message + "</p>"
                        ).fadeOut(5000, function () {
                            window.location.reload()
                            $('#makeReportModal').modal('hide');
                        });
                        return false;
                    }
                    $response.html(
                        '<p class="bg-danger text-white p-2">' + result.message + "</p>"
                    );
                });
                e.preventDefault();
            });
        });
        $(".aLink").click(function (e) {
            e.preventDefault();
            var t = e.target
            var url = t.href,
                p = t.getAttribute('data-xf'),
                q = t.getAttribute('data-zf');
            $.get("request/vtyn2xASj5fsRteLoqjII7C.php?idx=" + p + "&idReference=" + q, function (data) {
                var jsonData = JSON.parse(data);
                if (jsonData.status)
                    window.location.href = "xtiny.php?idx=" + jsonData.id;
                else
                    swal("CMA Debtor Detected", jsonData.message, 'error');

            });
            //

        });
    </script>
</body>

</html>