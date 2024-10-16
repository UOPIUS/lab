<?php include_once 'functions/Functions.php'; 
$role = $_SESSION['role_id'];
$class = new Functions();
$config = $class->fetch('settings');
if($role != 103) header('location: logout.php');
include_once 'functions/validate_session.php';
$reversal_response = '';
if (filter_has_var(INPUT_POST, 'inputRef')) {
    $ref = htmlentities(filter_input(INPUT_POST, 'inputRef'),ENT_QUOTES);
    $conn = $class->connect();
    // trans record 
    $tranx = $class->fetch("transactions"," WHERE id = '$ref' AND status = 1");
    if($tranx){
        //proceed to reverse
        $query = $conn->prepare("UPDATE transactions SET who_requested_reversal = ?,
        date_reversal_requested = ?,status = 2 WHERE id = ?");
        $attempt = $query->execute([$_SESSION['user_id'],date('Y-m-d H:m:s'),$ref]);
        if($attempt){
            $reversal_response .= "Request Submitted Successfully";
        }
    } else {
$script = <<<EOT
    <script>
        alert("Transaction Does Not Exist");
    </script>
EOT;
echo $script;
    }
}
$sales = $class->rawQuery("
    SELECT t.*,p.pay_method AS payment_method,c.fname,c.lname,c.phone AS cphone,c.ref AS client FROM transactions AS t LEFT JOIN
     clients_tbl AS c ON t.client_id = c.ref LEFT JOIN payment_types AS p ON
    t.payment_type = p.id  WHERE t.status > 1 ORDER BY t.created_at DESC
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
    <title>Request Reversal</title>

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
                        <h4 class="mt-4">Refund List</h4>
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class='text-danger'>TRANSACTIONS MARKED YELLOW ARE REQUEST AWAITING APPROVAL, GREEN ROWS SIGNIFIES APPROVED REVERSAL</h5>
                                <div class='float-right'>
                                    <form class="form-inline" action="<?= htmlentities($_SERVER['PHP_SELF']) ?>"
                                        method="POST" id="reversalMode" autocomplete="off">
                                        <div class="form-group mx-sm-3 mb-2">
                                            <label class="sr-only">Enter Transaction Ref.</label>
                                            <input type="text" class="form-control" name="inputRef" id="inputRef"
                                                value="<?=htmlentities(filter_input(INPUT_POST,'inputRef')) ?>"
                                                placeholder="Enter Transaction Ref.">
                                        </div>

                                        <button type="submit" name="submit" class="btn btn-danger mb-2"><i class="fa fa-times-circle"></i> Submit Refund Request</button>
                                    </form>
                                    <p class="bg-success text-white"><?=$reversal_response?></p>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive mt-4">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>Client ID</th>
                                                <th>Name</th>
                                                <th>Mobile</th>
                                                <th>Amount Paid</th>
                                                <th>Bal.</th>
                                                <th>Method</th>
                                                <th>Date</th>
                                                <th>Date Reversed</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            $i=1;
                                            foreach($sales as $x):
                                                $client_ref = $class->simple_encrypt($x->client_id,'e');
                                                $tranx_ref = $class->simple_encrypt($x->id,'e');
                                             ?>
                                            <tr <?php if($x->status == 3) {echo "class='bg-success'";} else {echo "class='bg-warning'";}  ?>>
                                                <td>
                                                    <a href="client_profile.php?refx=<?=$client_ref ?>"
                                                        class="btn btn-link text-white"><?=$x->client ?></a>
                                                </td>
                                                <td><?=$x->fname.' '.$x->lname. ''.$x->oname ?></td>
                                                <td><?=$x->cphone ?></td>
                                                <td><?=$x->amount ?></td>
                                                <td><?=$x->payable_amount + $x->amount;?></td>
                                                <td><?=$x->payment_method ?></td>
                                                <td><?=$x->created_at ?></td>
                                                <td><?=$x->reversed_at ?? 'NA' ?></td>
                                                

                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
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
    document.getElementById('reversalMode').addEventListener('submit',(t)=>{
        if(confirm("Please Confirm that You are about to Reverse "+document.getElementById('inputRef').value+".") === true){
            t.submit();
            return true;
        }
        t.preventDefault();
        return false;
    })
    </script>

</body>

</html>