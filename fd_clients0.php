<?php include_once 'functions/Functions.php'; 
$class = new Functions();

if(!($_SESSION['user_id'] && $_SESSION['role_id'])) header('location: logout.php');
include_once 'functions/validate_session.php';
$ref = (filter_has_var(INPUT_GET,'ref')) ? htmlentities(filter_input(INPUT_GET,'ref')) : $_SESSION['user_id'];
$profile = $class->fetch('users_tbl'," WHERE user_id = '$ref'");
$config = $class->fetch('settings');
$clients = $class->rawQuery("
   SELECT fname,lname,oname,ref,phone,gender,blood_group,dob,rhesus,address,
   status,created_at FROM clients_tbl
   ORDER BY created_at DESC LIMIT 100
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
                    <div class="container-fluid" id="oldDiv">
                        <h4 class="mt-4">Registered Clients</h4>
                        <div class="card mb-4">
                            <div class="card-header">
                            <?php if($_SESSION['role_id'] == 102): ?>
                                <button class="btn btn-danger float-right mb-2" onclick="displayBlock(this)"
                                    data-name='Add New Client'>
                                    <i class="fa fa-plus-circle"></i>&nbsp;Add New Client
                                </button>
                            <?php endif; ?>
                                <i class="fas fa-table mr-1"></i>Clients
                            </div>
                            <div class="card-body">
                                <div class="table-responsive mt-4" id="divToShow">
                                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Client ID</th>
                                                <th>Name</th>
                                                <th>Mobile</th>
                                                <th>Address</th>
                                                <th>Gender</th>
                                                <th>Blood Group</th>
                                                <th>Age</th>
                                                <th>Date created</th>
                                            </tr>
                                        </thead>
                                      
                                        <tbody>
                                            <?php 
                                            $i=1;
                                            foreach($clients as $x):
                                            $id = $class->simple_encrypt($x->ref,'e');
                                            ?>
                                            <tr>
                                                <td><?=$i++ ?></td>
                                                <td><a href="client_profile.php?refx=<?=$id ?>"
                                                        class="btn btn-link"><?=$x->ref ?></a></td>
                                                <td><?=$x->fname.' '.$x->lname. ' '.$x->oname ?></td>
                                                <td><?=$x->phone ?></td>
                                                <td><?=$x->address?></td>
                                                <td><?=$x->gender ?></td>
                                                <td><?=$x->blood_group ?></td>
                                                <td>
                                                <?php ;
                                                 
                                                     $age = $x->dob;
                                                     //$age = floor((time() - strtotime($dob)) / 31556926);
                                                     echo $age."YRS";
                                                 ?>
                                                 </td>
                                                <td><?=$x->created_at ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>

                                <form id="divToHide" class="d-none">
                                        <!-- Post -->
                                        <p class="text-left text-danger">All fields Marked * are required</p>
                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label for="fname">First Name <sup class="text-danger">*</sup></label>
                                                <input type="text" class="form-control" id="fname">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label>Last Name <sup class="text-danger">*</sup></label>
                                                <input type="text" class="form-control" id="lname">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="oname">Other Name <small class="text-danger">(Optional)</small></label>
                                                <input type="text" class="form-control" id="oname">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="oname">Phone Number <sup class="text-danger">*</sup></label>
                                                <input type="text" class="form-control" id="phone" maxlength="11">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label>Gender <sup class="text-danger">*</sup></label>
                                                <select id="gender" class="form-control" id="gender">
                                                    <option value="">Choose...</option>
                                                    <option value="Male">Male</option>
                                                    <option value="Female">Female</option>
                                                </select>
                                                
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="dob">Age <sup class="text-danger">*</sup></label>
                                                <input type="text" class="form-control" id="dob">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label>Blood group <sup class="text-danger">*</sup></label>
                                                <select class="form-control" id="bloodgroup">
                                                    <option value="">Choose...</option>
                                                    <option value="A">A</option>
                                                    <option value="B">B</option>
                                                    <option value="AB">AB</option>
                                                    <option value="O">O</option>
                                                </select>
                                            </div>
                                             <div class="form-group col-md-12">
                                                <label for="address">Address&nbsp;(<small class="text-warning">Optional</small>)</label>
                                                <textarea class="form-control" aria-label="address" id="address"></textarea>
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
    <script src="js/jquery.dataTables.min.js"></script>
    <script src="js/dataTables.bootstrap4.min.js"></script>
    <script src="assets/demo/datatables-demo.js"></script>
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
                sst: 's1f'
            })
        });
    };
    </script>
</body>

</html>