<?php include_once 'functions/Functions.php'; 

if(!($_SESSION['role_id'] == 101 && $_SESSION['user_id'])) header('location: login.php');
$class = new Functions();

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
    <title>Configuration Settings - <?= $config->name ?></title>

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
                        <h4 class="mt-4">Edit Configuration</h4>
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-table mr-1"></i>System Settings
                               
                            </div>
                            <div class="card-body">
                            <form action="<?=htmlentities($_SERVER['PHP_SELF']) ?>" id="settingsForm" class="mb-4"
                                    method='POST'>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <label for="category">Name of Company: <strong
                                                    class="text-danger">*</strong></label>
                                            <input type="text" class="form-control" id='name' required
                                            value="<?=$config->name ?>">

                                        </div>
                                        <div class="col-lg-6">
                                            <label for="category">Acronymn: <strong
                                                    class="text-danger">*</strong></label>
                                            <input type="text" class="form-control" id='acronymn' required value="<?=$config->acronymn ?>" readonly>
                                            <input type="hidden" id='token' value="<?=$_SESSION['token'] ?>">
                                        </div>
                                    </div>
                                    <div class="row mb-4 mt-4">
                                        <div class="col-lg-6">
                                            <label for="category">Contact Phones: <strong
                                                    class="text-danger">*</strong></label>
                                            <input type="text" class="form-control" id='phones' required
                                            value="<?=$config->contact_phone ?>">

                                        </div>
                                        <div class="col-lg-6">
                                            <label for="category">Referral Percentage: <strong
                                                    class="text-danger">*</strong></label>
                                            <input type="text" class="form-control" id='percent' required value="<?=$config->referral ?>">
                                        </div>
                                    </div>
                                    <div class="row mt-4">
                                        <div class="col-lg-12">
                                            <label>Address: <strong class="text-danger">*</strong></label>
                                            <input type="text" class="form-control" id='address' required value="<?=$config->address ?>">
                                        </div>
                                        
                                    </div>
                
                                    <div class="text-center m-2" id="response"></div>
                                    <div class="m-4 text-center">
                                        <button type="submit" class="btn btn-primary" id="submit-user">Update Account <i
                                                class="fa fa-forward"></i></button>
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
    <script src="js/jquery.dataTables.min.js"></script>
    <script src="js/dataTables.bootstrap4.min.js"></script>
    <script src="assets/demo/datatables-demo.js"></script>
    <script src="js/scripts.js"></script>
    <script src="js/tejiri.js"></script>
    <script>
    window.onload = () => {
        document.getElementById('settingsForm').addEventListener('submit', (e) => {
            e.preventDefault();
            let responseDiv = document.getElementById('response');
            if (!(document.getElementById('name').value &&
                    document.getElementById('address').value && document.getElementById('acronymn').value)) {
                responseDiv.innerHTML =
                    "<p class='bg-danger text-center p-2 text-white'>All fields Marked * are Required</p>";
                setTimeout(() => {
                    responseDiv.innerHTML = '';
                }, 3000);
                return false;
            }
            makeXHR(e, responseDiv, 'request/config.php', {
                'name': document.getElementById('name').value,
                'address': document.getElementById('address').value,
                'phones': document.getElementById('phones').value,
                'percent': document.getElementById('percent').value,
                'acronymn': document.getElementById('acronymn').value,
                token: document.getElementById('token').value,
                tt: 't1'
            });
        });
    };
    </script>
</body>

</html>