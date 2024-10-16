<?php include_once 'functions/Functions.php'; 
//if(!($_SESSION['role_id'] && $_SESSION['user_id'])) header('location: login.php');
$class = new Functions();
$condition = '';

//if($_GET['date_to'] && $_GET['date_from']){
  //date interval
  $from = (filter_has_var(INPUT_GET, 'date_from')) ? htmlentities(filter_input(INPUT_GET, 'date_from'),ENT_QUOTES) . ' 00:00:00' : date("Y-m-d")." 00:00:59";
  $to = (filter_has_var(INPUT_GET, 'date_to')) ? htmlentities(filter_input(INPUT_GET, 'date_to'),ENT_QUOTES).' 23:59:59' : date("Y-m-d")." 23:59:59";
  $condition .= " AND (t.created_at BETWEEN '$from' AND '$to') ";
//}

$sales = $class->rawQuery("SELECT t.id,t.client_id,DATE_FORMAT(t.created_at, '%d/%m/%Y') AS created_at,CONCAT(c.fname,' ',c.lname,' ',c.oname) AS name, c.phone,c.dob
FROM transactions t JOIN clients_tbl c ON t.client_id = c.ref
WHERE t.status = 1 $condition ORDER BY t.created_at DESC");

/**
<?php $tests = $class->fetchAll("tests_taken"," WHERE tranx_id = '{$x->id}'");
                                                    $k = 1;
                                                    foreach($tests as $t): ?>
                                                    <?php if($t->test_result) echo "<i class='fa fa-check-circle text-success'></i>";
                                                        else echo "<i class='fa fa-times text-danger'></i>"; ?>
                                                    <strong class="text-danger"><?=$k++.'.' .$class->fetchColumn('sub_labtest_tbl','name','id',$t->test_id) ?>
                                                    </strong><br>

                                                    <?php endforeach; ?>
 */
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Pending Transactions</title>

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
                        <h4 class="mt-4"><i class="fas fa-stethoscope mr-1"></i>Pending Laboratory Tests </h4>
                        <div class="card mb-4">
                            <div class="card-header">

                                <form class="" action="<?= htmlentities($_SERVER['PHP_SELF']) ?>" method="get">
                                    <div class="form-row">
                                        <div class="form-group col-md-3">
                                            <label for="sel1">Date From</label>
                                            <input type="date" class="custom-select mr-sm-2" data-provide="datepicker"
                                                placeholder="Date From" data-date-format="yyyy-mm-dd" name="date_from"
                                                value="<?php echo ($_GET['date_from']) ?? '' ?>">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label for="sel1">Date To</label>
                                            <input type="date" class="custom-select mr-sm-2" data-provide="datepicker"
                                                placeholder="Date To" data-date-format="yyyy-mm-dd" name="date_to"
                                                value="<?= filter_input(INPUT_GET, 'date_to') ?>">
                                        </div>
                            

                                        <div class="form-group col-md-3">
                                            <label>Search Record</label>
                                            <input type="submit" class="form-control btn btn-success btn-sm"
                                                value="Search Records">
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="card-body">
                               
                                <div class="table-responsive mt-4">
                                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Client ID</th>
                                                <th>Client</th>
                                                <th>Age</th>
                                                <!--<th>Tests</th>-->
                                                <th>Date</th>
                                                <th></th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php 
                                            $i=1;
                                            foreach($sales as $x):
                                            $ref = $class->simple_encrypt($x->client_id,'e');
                                            $txref = $class->simple_encrypt($x->id,'e');
                                            ?>
                                            <tr>
                                                <td><?=$i++ ?></td>
                                                <td><a href="client_profile.php?refx=<?=$ref ?>"
                                                        class="btn btn-link"><?=$x->client_id ?></a></td>
                                                <td>
                                                    <!--<span data-href="<?=$x->client_id?>" class='clienta' onclick="clientToolTip(this)">preview</span>-->
                                                    <?=$x->name?>
                                                </td>
                                                <td><?=$x->dob ?></td>
                                                <!--<td>-->
                                                    
                                                <!--</td>-->
                                                <td><?=$x->created_at ?></td>
                                                <td>
                                                    <a href="jodit.php?refx=<?=$txref?>"
                                                        class="btn-info btn-sm btn">Report Test</a>
                                                </td>
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
/*        window.onload=()=>{
            const buttonList = document.querySelectorAll(".clienta");
            const buttonLen = buttonList.length;
            for(var i=0;i<buttonLen;i++){
                clientToolTip(buttonList[i]);
            }
        }*/
        function clientToolTip(param){
            const t = new URLSearchParams({ href:param.getAttribute("data-href") }).toString();
            let a = new XMLHttpRequest();
            a.open("POST", "functions/fetch_client.php"),
                a.setRequestHeader("Content-type", "application/x-www-form-urlencoded"),
                //a.setRequestHeader("X-CSRF-TOKEN", document.getElementsByTagName("meta")["csrf-token"].getAttribute("content"));
                a.send(t);
                a.onload = function() {
                        if (a.status != 200) { // analyze HTTP status of the response
                            console.log(`Error ${o.status}: ${o.statusText}`);
                        } else { // show the result
                            const json = JSON.parse(a.response);
                            if (json.status) {
                                param.innerHTML = json.name + "("+json.age+")";
                            } else {
                                console.log(json.message)
                            }
                        }

                    };
        }
    </script>
</body>

</html>