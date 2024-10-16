<?php include_once 'functions/Functions.php';
    if ($_SESSION['user_id'] && $_SESSION['role_id']) {
        switch($_SESSION['role_id']):
            case '101':
                header('location: admin_dashboard.php'); //admin
              exit;
            break;
            case '102':
              header('location: adhoc_dashboard.php'); //account
            break;
            case '103':
              header('location: acct_dashboard.php'); //front desk
            break;
            case '105':
                header('location: lab_dashboard.php'); //account
            break;
            case '106':
                header('location: admin_dashboard.php'); //doktr
              break;
              case '107':
                header('location: dashboard.php'); //referral
              exit;
        endswitch; 
    }
$class = new Functions;
$config = $class->fetch('settings');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <link rel="icon" type="image/png" href="assets/img/favicon.png" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Signin | <?=$config->name ?></title>
    <link href="css/styles.css" rel="stylesheet" />
    <script src="js/all.min.js" crossorigin="anonymous">
    </script>
</head>

<body style="background-image: url('assets/img/lab.jpg');  background-repeat: no-repeat, repeat;  background-size: cover;">
    <div id="layoutAuthentication">
        <div id="layoutAuthentication_content">
            <main>
                <div class="container" id="signin-container">
                    <div class="row justify-content-center">
                        <div class="col-lg-5">
                            <div class="card shadow-lg border-0 rounded-lg mt-5">
                                <div class="card-header">
                                    <h3 class="text-center font-weight-light my-4"><img src="assets/img/logo.png"
                                            alt=""></h3>
                                </div>
                                <div class="card-body">
                                    <form id="signinForm">
                                        <div class="form-group"><label class="small mb-1"
                                                for="inputUsername">Username</label><input class="form-control py-4" autofocus 
                                                id="inputUsername" type="text" placeholder="Enter Username">
                                        </div>
                                        <div class="form-group"><label class="small mb-1"
                                                for="inputPassword">Password</label><input class="form-control py-4"
                                                name="password" id="inputPassword" type="password"
                                                placeholder="Enter password" />
                                            <div class="text-center p-2" id="response"></div>
                                        </div>
                                        <div class="text-center">
                                            <button class="btn btn-primary" type="submit" 
                                                id="login">Get Me In <i class="fas fa-arrow-right"></i></button>
                                        </div>
                                    </form>
                                </div>
                                <div class="card-footer text-center">
                                    <div class="form-group d-flex align-items-center justify-content-between mt-4 mb-0">
                                        <a class="small" href="#">Forgot Password?</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
        <div id="layoutAuthentication_footer">
            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid">
                    <div class="d-flex align-items-center justify-content-center small">
                        <div class="text-muted">Copyright &copy; <?=date('Y') ?>&nbsp;</div>
                        <div>
                            <a href="#">Privacy Policy</a>
                            &middot;
                            <a href="#">Terms &amp; Conditions</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <script src="js/jquery-3.4.1.min.js"></script>
    <script src="js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
    <script src="js/tejiri.js"></script>

    <script>
    window.onload = (() => {
        document.getElementById("signinForm").addEventListener("submit", e => {
            e.preventDefault();
            let t = document.getElementById("response");
            if (!document.getElementById("inputUsername").value || !document.getElementById(
                    "inputPassword").value) return t.innerHTML =
                "<p class='bg-danger text-center p-2 text-white'>Please Provide a Valid username and Password</p>",
                setTimeout(function() {
                    t.innerHTML = ""
                }, 5e3), !1;
            makeXHR(e, t, "request/auth.php", {
                username: document.getElementById("inputUsername").value,
                password: document.getElementById("inputPassword").value,
                tt: 't1',
                action: 'login'
            })
        })
    });
    </script>
</body>

</html>