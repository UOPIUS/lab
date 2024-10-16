<?php include_once 'functions/Functions.php'; 
$class = new Functions();

if(!($_SESSION['user_id'] && $_SESSION['role_id'])) header('location: logout.php');
include_once 'functions/validate_session.php';
$ref = $class->simple_encrypt(trim(htmlentities(filter_input(INPUT_GET,'refx'))),'d');

$profile = $class->fetch('clients_tbl'," WHERE ref = '$ref'");

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
    <title>Clients Edit</title>
<link rel="icon" type="image/png" href="assets/img/favicon.png" />
    <link href="css/materialdesignicons.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet" />
    <link type="text/css" rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jsgrid/1.5.3/jsgrid.min.css" />
    <link type="text/css" rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/jsgrid/1.5.3/jsgrid-theme.min.css" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <script src="js/all.min.js"></script>
        <style>
    .hide {
        display: none;
    }
    </style>
</head>

<body class="sb-nav-fixed">
    <?php include_once 'header.php'; ?>
    <div id="layoutSidenav">
        <?php include_once 'menu.php' ?>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid">
                    <div class="container-fluid">
                        <h4 class="mt-4">Edit Patients</h4>
                        <div class="card mb-4">
                           
                            <div class="card-body">
                               

                                <form id="divToHide" >
                                        <!-- Post -->
                                        <p class="text-left text-danger">All fields Marked * are required</p>
                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label for="fname">First Name <sup class="text-danger">*</sup></label>
                                                <input type="text" class="form-control" id="fname" value="<?=$profile->fname ?>">
                                                <input type="hidden" class="form-control" id="idx" value="<?=filter_input(INPUT_GET,'refx') ?>">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label>SurName <sup class="text-danger">*</sup></label>
                                                <input type="text" class="form-control" id="lname" value="<?=$profile->lname ?>">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="oname">Other Name <small class="text-danger">(Optional)</small></label>
                                                <input type="text" class="form-control" id="oname" value="<?=$profile->oname ?>">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="oname">Phone Number <sup class="text-danger">*</sup></label>
                                                <input type="text" class="form-control" id="phone" maxlength="11" value="<?=$profile->phone ?>">
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label>Gender <sup class="text-danger">*</sup></label>
                                                <?php $genders = ["Male" => "Male", "Female" => "Female"]; ?>
                                                <select id="gender" class="form-control" id="gender" disabled>
                                                    <?php foreach($genders as $k => $f){ ?>
                                                    <option value="<?=$k ?>" <?php if($k == $profile->gender) echo "selected"?>><?=$f ?></option>
                                                    <?php } ?>
                                                </select>
                                                
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="dob">Age <sup class="text-danger">*</sup></label>
                                                <input type="text" class="form-control" id="dob" value="<?=$profile->dob?>">
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label>Blood group <sup class="text-danger">*</sup></label>
                                                <?php $bgps = ["A" => "A", "B" => "B", "AB" => "AB", "O" => "O"]; ?>
                                                <select class="form-control" id="bloodgroup">
                                                    <?php foreach($bgps as $key => $value){ ?>
                                                    <option value="<?=$key?>" <?php if($key == $profile->blood_group) echo "selected"?>><?=$value ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                             <div class="form-group col-md-12">
                                                <label for="address">Address&nbsp;(<small class="text-warning">Optional</small>)</label>
                                                <textarea class="form-control" aria-label="address" id="address"><?=$profile->address ?></textarea>
                                            </div>
                                            <!--
                                             <div class="form-group col-md-6">
                                                <label>Rhesus factor&nbsp;(<small class="text-warning">Optional</small>)</label>
                                                <select class="form-control" id="rhesus">
                                                    <option value="">Choose...</option>
                                                    <option value="-">Negative(-)</option>
                                                    <option value="+">Positive(+)</option>
                                                </select>
                                            </div>
                                            -->
                                            <input type="hidden" id="_token" value="<?=$_SESSION['token']?>">
                                            <div class="col-md-12 m-2" id='s1response'></div>
                                        </div>
                                        <div class="text-center">
                                            <button type="submit" class="btn btn-success btn-lg">
                                                <i class="fas fa-save"></i> Save
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
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jsgrid/1.5.3/jsgrid.min.js"></script>
    <script src="js/scripts.js"></script>
    <script src="js/tejiri.js"></script>
    <script type="text/javascript">
    window.onload = () => {
        document.getElementById('divToHide').addEventListener('submit', (e) => {
            let t = document.getElementById("s1response");
            makeXHR(e, t, "request/add_client.php", {
                fname: document.getElementById("fname").value,
                lname: document.getElementById("lname").value,
                oname: document.getElementById("oname").value,
                bloodgroup: document.getElementById("bloodgroup").value,
                gender: document.getElementById("gender").value,
                phone: document.getElementById("phone").value,
                dob: document.getElementById("dob").value,
                token: document.getElementById('_token').value,
                address: document.getElementById('address').value,
                refx: document.getElementById('idx').value,
                sst: 's1fe'
            })
        });
    };
    </script>
</body>

</html>