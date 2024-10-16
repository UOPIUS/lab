<?php include_once 'functions/Functions.php';
$class = new Functions();
if (!($_SESSION['user_id'] && $_SESSION['role_id']))
    header('location: logout.php');
include 'function/validate_session.php';
$rfn = $class->simple_encrypt(trim(htmlentities(filter_input(INPUT_GET, 'ini_id'))), 'd');
$config = $class->fetch('settings');
$customer = $class->fetch('clients_tbl', " WHERE ref = '$rfn'");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="<?= $config->name ?>" />
    <meta name="csrf_token" content="<?= $_SESSION['token'] ?>">
    <title>New Transaction - <?= $config->name ?></title>
    <link href="css/materialdesignicons.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet" />
    <script src="js/all.min.js"></script>
</head>

<body class="sb-nav-fixed">
    <?php include_once 'header.php'; ?>
    <div id="layoutSidenav">
        <?php include_once 'menu.php' ?>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid">
                    <div class="row flex-grow mb-4 mt-4">
                        <div class="col-md-10 mx-auto">
                            <div class="card">
                                <div class="card-header">
                                    <i class="fas fa-table mr-1"></i>New Transaction
                                </div>
                                <div class="card-body">
                                    <form id="newLabTestForm" method="POST"
                                        action="<?= htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES) ?>">
                                        <!-- Post -->
                                        <h4>Client Information</h4>
                                        <hr style="height: 2px; width:99%; background-color:brown;">
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
                                                $age = floor((time() - strtotime($dob)) / 31556926);
                                                echo $age . "YRS";
                                                ?>">
                                            </div>
                                            <div class="form-group col-md-12">
                                                <label>Blood group</label>
                                                <input type="text" class="form-control"
                                                    value="<?= $customer->blood_group ?>" disabled>
                                            </div>
                                            <input type="hidden" id="_token" value="<?= $_SESSION['token'] ?>">
                                            <input type="hidden" id="userRef"
                                                value="<?= filter_input(INPUT_GET, 'ini_id') ?>">
                                            <div class="col-md-12 m-2" id='s1response'></div>
                                        </div>
                                        <h4>Test Information</h4>
                                        <hr style="height: 2px; width:99%; background-color:brown;">
                                        <div class="form-row">
                                            <div class="form-group col-md-5">
                                                <label>Select The Test Category</label>
                                                <select class="form-control custom-select" id="testClassRef">
                                                    <option value="">-Select-</option>
                                                    <?php $method = $class->fetchAll('test_categories', " WHERE status = 1");
                                                    foreach ($method as $m): ?>
                                                    <option value="<?= $m->id ?>"><?= $m->name ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-5">
                                                <label for="mainTestRef">Choose Lab. Test</label>
                                                <select class="form-control custom-select" id="mainTestRef">
                                                    <option selected value="">-Select-</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label></label>
                                                <button class="btn btn-danger" type="button" id="addToList">Add To
                                                    List</button>

                                            </div>
                                            <div class="form-group col-md-12 mt-4">

                                                <div class="table-responsive">
                                                    <table class="table table-bordered" id="testListTable">
                                                        <thead>
                                                            <tr>
                                                                <th>Test</th>
                                                                <th>Cost</th>
                                                                <th></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>

                                                        </tbody>
                                                        <tfoot>
                                                            <tr>
                                                                <th></th>
                                                                <th><strong>Total: ₦<span
                                                                            id="finalTestCost">0.00</span></strong></th>
                                                                <th></th>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            </div>

                                            <div class="form-group col-md-12">
                                                <label for="sel1">Referral <small class="text-danger">Who referred you
                                                        for this Test?</small></label>
                                                <select class="form-control" id='referrals'>
                                                    <option value=''>None</option>
                                                    <?php $referrals = $class->rawQuery("SELECT full_name,phone,user_id FROM users_tbl WHERE uflag = 'R' ORDER BY full_name ASC");
                                                    foreach ($referrals as $rr): ?>
                                                    <option value="<?= $rr->user_id ?>">
                                                        <?= $rr->full_name . ' [' . $rr->phone . ']' ?>
                                                    </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="m-1 text-center" id="newTestResponse"></div>
                                        <div class="text-center">
                                            <button type="submit" class="btn btn-success btn-block btn-lg"
                                                id="finalSubmitTest">
                                                Submit Transaction <i class="fas fa-arrow-right"></i>
                                            </button>
                                        </div>

                                    </form>

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
    <script src="js/scripts.js"></script>
    <script src="assets/sweetalert/sweetalert.min.js"></script>
    <script src="js/tejiri.js"></script>
    <script>
    window.onload = () => {
        document.getElementById('testClassRef').addEventListener('change', (testClassEvt) => {
            const selectedTestCategory = testClassEvt.currentTarget.value;
            //fetch test under this category
            showOverlay();
            var data = new URLSearchParams({
                cat_id: selectedTestCategory,
                token: document.getElementsByTagName("meta")["csrf_token"].getAttribute("content")
            }).toString()
            var responseHolder = document.getElementById("mainTestRef");
            let xhr = new XMLHttpRequest();
            xhr.open('POST', 'request/ajax_category.php');
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.send(data);
            xhr.onload = function() {
                removeOverlay();
                if (xhr.status != 200) {
                    console.log(xhr.statusText);
                } else {
                    responseHolder.innerHTML = ''; //clear options
                    var jsonData = JSON.parse(xhr.response);
                    var jsonLength = jsonData.data.length;
                    for (var i = 0; i < jsonLength; i++) {
                        var counter = jsonData.data[i];
                        var option = document.createElement("option");
                        option.innerText = counter.name;
                        option.setAttribute('data-ref', counter.tid);
                        option.setAttribute('data-cost', counter.cost);
                        option.classList.add('chosenValue');
                        responseHolder.appendChild(option);
                    }
                }
            };
        })
        document.getElementById('addToList').addEventListener('click', (addToTestListEvt) => {
            const testOptions = document.getElementById('mainTestRef')
            const chosenTestObj = testOptions.options[testOptions.selectedIndex];
            const chosenTestName = chosenTestObj.text;
            const chosenTestCost = chosenTestObj.getAttribute("data-cost");
            const chosenTestRef = chosenTestObj.getAttribute('data-ref');
            if (chosenTestRef == null) return false;
            const param = {
                chosenTestName: chosenTestName,
                chosenTestCost: chosenTestCost,
                chosenTestRef: chosenTestRef
            };

            let tableBodyRef = document.getElementById('testListTable').getElementsByTagName('tbody')[0];

            var rowOne = tableBodyRef.insertRow(-1); //new tr
            var rowOneColumnOne = rowOne.insertCell(0)
            const testName = document.createTextNode(chosenTestName);
            rowOneColumnOne.appendChild(testName);
            var rowOneColumnTwo = rowOne.insertCell(1);
            const testAmount = document.createTextNode(chosenTestCost);
            rowOneColumnTwo.appendChild(testAmount);
            var rowOneColumnThree = rowOne.insertCell(2);
            let btn = document.createElement("button");
            btn.setAttribute("type", "button");
            btn.innerHTML = "<i class='fas fa-times-circle'></i> Remove";
            btn.classList.add("rmvBox");
            btn.setAttribute("data-testrf", chosenTestRef)
            btn.setAttribute("data-scdljload", JSON.stringify(param))
            btn.setAttribute("onclick", "rmvBoxEvt(this)");
            rowOneColumnThree.appendChild(btn);
            let finalTestCost = document.getElementById('finalTestCost');
            const newValue = Number(chosenTestCost) + Number(finalTestCost.textContent);
            finalTestCost.innerHTML = newValue;
        });

        document.getElementById("newLabTestForm").addEventListener('submit', (finalEvent) => {
            let formElem = finalEvent.currentTarget;
            finalEvent.preventDefault();
            swal({
                    title: "Are you sure?",
                    text: "Once Submitted, You Cannot Edit this Operation",
                    //text: "You are running out of Resources and needs to Upgrade Your Account",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                .then((proceed) => {
                    if (proceed) {
                        let buildString = "";
                        const scdlPayloads = document.getElementsByClassName("rmvBox");
                        const scdlPayloadsLen = scdlPayloads.length;
                        for (let index = 0; index < scdlPayloadsLen; index++) {
                            const eachPayload = scdlPayloads[index].getAttribute("data-scdljload");
                            buildString += eachPayload + "_"
                        }
                        //make AJAX REQUEST 
                        const payload = {
                            scdljload: buildString,
                            referral: document.getElementById('referrals').value,
                            userRef: document.getElementById("userRef").value,
                            customToken: document.getElementsByTagName("meta")["csrf_token"]
                                .getAttribute("content")
                        }
                        const btn = document.getElementById("finalSubmitTest");
                        const data = new URLSearchParams(payload).toString();
                        btn.disabled = true;
                        btn.innerHTML = `<progress></progress>`;
                        //make ajax request to remove tx
                        let xhr = new XMLHttpRequest();
                        xhr.open('POST', 'request/xmlHttpClientTest.php');
                        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                        xhr.send(data);
                        xhr.onload = function() {
                            if (xhr.status != 200) {
                                console.log(`Error ${xhr.status}: ${xhr.statusText}`);
                            } else {
                                btn.disabled = false;
                                btn.innerHTML =
                                    `Submit Transaction <i class='fas fa-arrow-right'></i> `;
                                const detail = JSON.parse(xhr.responseText);
                                if (detail.status) {
                                    formElem.reset();
                                    swal({
                                        title: "Alert",
                                        text: detail.message,
                                        icon: 'success',
                                        timer: 2000
                                    });
                                    window.location.href =
                                        "/client_profile.php?refx=" +
                                        payload.userRef;
                                } else {
                                    swal("Error", detail.message, 'error');
                                }
                            }
                        };
                    } else {
                        console.log("Operation Cancelled")
                    }
                });

        })

    };

    function rmvBoxEvt(evt) {
        const payloadTemp = JSON.parse(evt.getAttribute("data-scdljload"));
        let finalTestCost = document.getElementById('finalTestCost');
        const newValue = Number(finalTestCost.textContent) - Number(payloadTemp.chosenTestCost);
        finalTestCost.innerHTML = newValue;
        evt.parentNode.parentNode.remove();
    }
    </script>
</body>

</html>