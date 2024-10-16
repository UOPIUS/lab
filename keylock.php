<?php include_once 'functions/Functions.php'; 
$class = new Functions();
include_once 'functions/validate_session.php';
if(!($_SESSION['user_id'] && $_SESSION['role_id'])) header('location: logout.php');

$ref = (filter_has_var(INPUT_GET,'ref')) ? htmlentities(filter_input(INPUT_GET,'ref')) : $_SESSION['user_id'];
$profile = $class->fetch('users_tbl'," WHERE user_id = '$ref'");
$config = $class->fetch('settings');
$clients = $class->rawQuery("
   SELECT fname,lname,oname,ref,phone,gender,blood_group,dob,
   status,created_at FROM clients_tbl WHERE created_by = '{$_SESSION['user_id']}'
   ORDER BY created_at DESC
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
    <title>Clients history | <?= $config->name ?></title>
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
                    <div class="row">
                        <div class="col-lg-6 mx-auto">
                            <div class="card mb-4 mt-2">
                                <div class="card-header">
                                    <h4 class="mt-4">Change Your Password</h4>
                                </div>
                                <div class="card-body">
                                    <form action="<?=htmlentities($_SERVER['PHP_SELF']) ?>" method="POST"
                                        id="changeLock">
                                        <div class="form-group">
                                            <label for="oldPassword">Old Password <sup
                                                    class="text-danger">*</sup></label>
                                            <input type="password" class="form-control password-field" id="oldPassword">
                                        </div>
                                        <div class="form-group">
                                            <label for="pwd">New Password <sup class="text-danger">*</sup></label>
                                            <input type="password" class="form-control password-field" id="pwd">
                                        </div>

                                        <div class="form-group">
                                            <label for="npwd">Confirm Password <sup class="text-danger">*</sup></label>
                                            <input type="password" class="form-control password-field" id="npwd">
                                        </div>
                                        <div class="checkbox">
                                            <label><input type="checkbox" onclick="showPlainText()"> Show
                                                Password</label>
                                        </div>
                                        <div class="text-center" id="keyLock-Response"></div>
                                        <div class="text-center">
                                            <button type="submit" class="btn btn-success">Submit</button>
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
    <script src="js/jquery.dataTables.min.js"></script>
    <script src="js/dataTables.bootstrap4.min.js"></script>
    <script src="assets/demo/datatables-demo.js"></script>
    <script src="js/scripts.js"></script>
    <script src="js/tejiri.js"></script>
    <script>
    window.onload = () => {
        document.getElementById('changeLock').addEventListener('submit',keyLock);
    };

    function keyLock(param) {
        const formerLock = document.getElementById('oldPassword').value,
            newLock = document.getElementById('pwd').value,
            confirmLock = document.getElementById('npwd').value,
            responseDiv = document.getElementById('keyLock-Response');
console.log(newLock+ " "+confirmLock+" "+formerLock)
        if (newLock !== confirmLock) {
            setTimeout(() => {
                responseDiv.innerHTML = "<p class='text-danger text-center p-2 text-white'>Password Typed and Confirmed Does Not Match!";
                return false;
            }, 5000);
        }

        makeXHR(param, responseDiv, 'request/save_user.php', {
            former_lock: formerLock,
            new_lock: newLock,
            confirm_lock: confirmLock,
            tt: 'tlock'
        });
    }
    </script>
</body>

</html>