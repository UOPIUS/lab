<?php include_once 'functions/Functions.php';
$class = new Functions();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Validate Certificate </title>
    <link href="css/styles.css" rel="stylesheet" />
    <script src="js/all.min.js" crossorigin="anonymous">
    </script>
</head>

<body>
    <div id="layoutAuthentication">
        <div id="layoutAuthentication_content">
            <main>
                <div class="container" id="signin-container">
                    <div class="row justify-content-center">
                        <div class="col-lg-6">
                            <div class="card shadow-lg border-0 rounded-lg mt-5">
                                <div class="card-header">
                                    <h3 class="text-center font-weight-light my-4"><img src="assets/img/logo.png"
                                            alt=""></h3>
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title text-center text-primary mb-4">Validate Client ID</h5>
                <form action='<?php echo htmlentities($_SERVER['PHP_SELF']) ?>' method='post'>
                            <div class="form-row">
                                <div class="form-group col-md-10 mx-auto">
                                    <label>Enter Your Client ID:</label>
                                    <div class="input-group border">
                                        <input type="search" placeholder="Enter Certificate ID."value="<?=filter_input(INPUT_POST,'search_q') ?>"
                                            aria-describedby="button-addon3" name='search_q'
                                            class="form-control bg-none border-0">
                                        <div class="input-group-append border-0">
                                            <button type="submit" class="btn btn-danger text-white" name="submit"><i
                                                    class="fa fa-search"></i> Search</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <?php
                                        if(filter_has_var(INPUT_POST, 'search_q')){
                                            $id = trim(htmlentities(filter_input(INPUT_POST,'search_q'),ENT_QUOTES));
                                        $tranx = $class->connect()->prepare("SELECT * FROM clients_tbl WHERE ref = ?");
                                        $tranx->execute([$id]);
                                        $bix = $tranx->fetch(PDO::FETCH_OBJ);
                                        if($bix){ ?>
                                             <!-- Add icon library -->
                                        <div class="text-center mt-4">
                                            <table class="table table-bordered">
                                              <tbody>
                                                <tr>
                                                    <td colspan="2">
                                                        <h4 class="bg-success text-center text-white p-2">Client ID is Valid</h4>
                                                    </td>
                                                </td>
                                                </tr>
                                                <tr>
                                                  <th>Name</th>
                                                  <td><?=$bix->fname.' '.$bix->lname.' '.$bix->oname ?></td>
                                                </tr>
                                                <tr>
                                                  <th>Date created</th>
                                                  <td><?= $bix->created_at ?></td>
                                                </tr><tr>
                                               
                                              </tbody>
                                            </table>
                                        </div>
                                        <?php } else { ?>
                                            <div>
                                                <h4 class="bg-danger text-center text-white p-2">Client ID is Invalid</h4>
                                            </div>

                                    <?php    }
                                    } ?>
                                </div>
                                <div class="card-footer text-center">
                                    <div class="form-group d-flex align-items-center justify-content-between mt-4 mb-0">

                                        
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
</body>

</html>