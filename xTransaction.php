<?php include_once 'functions/Functions.php'; 
if(!(101==$_SESSION['role_id'] && $_SESSION['user_id'])) header('location: login.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Saltcity Diagnostic Lab | Remove Transaction</title>
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
                        <h4 class="mt-4">Remove Transaction.</h4>
                        <div class="card mb-4">
                            <div class="card-header text-danger">
                                <i class="fas fa-bell mr-1"></i> NOTE THAT ONCE A TRANSACTION IS DELETED, IT CANNOT BE REVERSED
                            </div>
                            <div class="card-body">
                                <form action="<?=htmlentities($_SERVER['PHP_SELF']) ?>" id="xTransForm" method="POST">
                                    <div class="mb-3">
                                        <label for="xTransRef" class="form-label">Transaction Reference</label>
                                        <input type="text" class="form-control" id="xTransRef" autofocus required
                                            placeholder="Enter The Transaction Ref.">
                                    </div>
                                    <div class="mb-3">
                                        <label for="xTransDesc" class="form-label">Why are You Removing This
                                            Transaction</label>
                                        <textarea class="form-control" id="xTransDesc" rows="3" maxlength="255" required></textarea>
                                    </div>
                                    <div class="text-center">
                                        <div id="response-div" class="text-center"></div>
                                        <button type="submit" class="btn btn-danger" id="xTransBtn">
                                            <i class="fas fa-times-circle"></i>&nbsp;Remove Transaction
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
    <script src="js/scripts.js"></script>
    <script src="js/tejiri.js"></script>
    <script src="assets/sweetalert/sweetalert.min.js"></script>
    <script>
    window.onload = () => {
        document.getElementById('xTransForm').addEventListener('submit', (f) => {
            f.preventDefault()
            
            const formElem = f.currentTarget;
            swal({
                    title: "Are you sure?",
                    text: "Once deleted, There is No Reverse",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                .then((proceed) => {
                    if (proceed) {
                        const btn = document.getElementById("xTransBtn");
                        const data = new URLSearchParams({
                            xTransRef: document.getElementById('xTransRef').value,
                            xTransDesc: document.getElementById("xTransDesc").value
                        }).toString();
                        btn.disabled = true;
                        btn.innerHTML = `<progress></progress>`;
                        //make ajax request to remove tx
                        let xhr = new XMLHttpRequest();
                        xhr.open('POST', 'request/xTranx.php');
                        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                        xhr.send(data);
                        xhr.onload = function() {
                            if (xhr.status != 200) {
                                console.log(
                                    `Error ${xhr.status}: ${xhr.statusText}`);
                            } else {
                                btn.disabled = false;
                                btn.innerHTML = `<i class='fas fa-times-circle'></i> Remove Transaction`;
                                const detail = JSON.parse(xhr.responseText);
                                if (detail.status) {
                                    formElem.reset();
                                    swal({title:"Alert", text:detail.message, icon:'success',timer: 2000});
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
    }
    </script>

</body>

</html>