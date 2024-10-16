<?php include_once 'functions/Functions.php'; 
if(!(106 == $_SESSION['role_id'] || 103 == $_SESSION['role_id'] || 101 == $_SESSION['role_id'])) header('location: login.php');
$class = new Functions();
$config = $class->fetch('settings');
$condition = '';

//if($_GET['date_to'] && $_GET['date_from']){
  //date interval
  $from = (filter_has_var(INPUT_GET, 'date_from')) ? htmlentities(filter_input(INPUT_GET, 'date_from'),ENT_QUOTES) . ' 00:00:00' : date("Y-m-d")." 00:00:59";
  $to = (filter_has_var(INPUT_GET, 'date_to')) ? htmlentities(filter_input(INPUT_GET, 'date_to'),ENT_QUOTES).' 23:59:59' :  date("Y-m-d")." 23:59:59";
  $condition .= " AND (t.created_at BETWEEN '$from' AND '$to') ";
//}
if($_GET['trans_type']){
  $gateway = htmlentities(filter_input(INPUT_GET, 'trans_type'),ENT_QUOTES);
  $condition .= " AND (t.payment_method = '$gateway') ";
}
$sales = $class->rawQuery("
    SELECT t.amount,t.tranx_ref,t.client_id,DATE_FORMAT(t.created_at, '%d/%m/%Y') AS created_at,p.pay_method AS payment_method,
    CONCAT(c.fname,' ',c.lname,' ',c.oname) AS name,
    c.phone AS cphone,c.ref AS client FROM outstanding_tbl AS t LEFT JOIN
     clients_tbl AS c ON t.client_id = c.ref LEFT JOIN payment_types AS p ON
    t.payment_method = p.id WHERE (1 = 1) $condition ORDER BY t.created_at DESC
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
    <title>All time Transactions | <?= $config->name ?></title>

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
                        <h4 class="mt-4">Transaction History</h4>
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class='m-2 text-danger'><i class='fas fa-bell'></i> NOTE: ONLY TRANSACTIONS FOR TODAY ARE SHOWN BY DEFAULT. USE THE DATE FILTERS TO GET TRANSACTIONS FOR SPECIFIC DATES.</h5>
                            <button class="btn btn-pill btn-primary float-right" id="exportToExcel">
                                <i class="fa fa-file"></i>&nbsp;Export to Excel
                            </button>
                              
                                

                            </div>
                            <div class="card-body">
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
                                            <label for="sel1">Method</label>
                                            <select name="trans_type" id="" class="custom-select">
                                                <option value="">Choose...</option>
                                                <?php $paymentTypes = $class->fetchAll("payment_types"," WHERE status = 1");
                                                foreach($paymentTypes as $pt): ?>
                                                <option value="<?=$pt->id?>" <?php if($pt->id == filter_input(INPUT_GET,'trans_type')) echo 'selected' ?>><?=$pt->pay_method ?></option>
                                                <?php endforeach ?>
                                            </select>
                                        </div>


                                        <div class="form-group col-md-3">
                                            <label>Search Record</label>
                                            <input type="submit" class="form-control btn btn-success btn-sm"
                                                value="Search Records">
                                        </div>
                                    </div>
                                </form>
                                <div class="table-responsive mt-4">
                                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Ref.</th>
                                                <th>Client ID</th>
                                                <th>Name</th>
                                                <th>Mobile</th>
                                                <th>Amount</th>
                                                <!--<th>Discount</th>-->
                                                <th>Method</th>
                                                <th>Date</th>
                                                <th></th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php 
                                            $i=1;
                                            $total = 0;
                                            foreach($sales as $x): 
                                            $idx = $class->simple_encrypt($x->client,'e');
                                            $remoteRef = $class->simple_encrypt($x->tranx_ref);
                                            $discount += $x->discount;
                                            $total += $x->amount;
                                            ?>
                                            <tr>
                                                <td><?=$i++ ?></td>
                                                <td><?=$x->tranx_ref ?></td>
                                                <td><a href="client_profile.php?refx=<?=$idx ?>"
                                                        class="btn btn-link"><?=$x->client ?></a></td>
                                                <td><?=$x->name ?></td>
                                                <td><?=$x->cphone ?></td>
                                                <td><?=$x->amount ?></td>
                                                
                                                <td><?=$x->payment_method ?></td>
                                                
                                                <td><?=$x->created_at ?></td>
                                                
                                                <td>
                                                <?php if(103 == $_SESSION['role_id']){ ?>
                                                       <a href="receipt.php?ref=<?=$remoteRef ?>" target="__blank" class="btn btn-sm btn-link">
                                                        <i class="fa fa-print"></i>&nbsp;
                                                    </a
                                                    <?php } else { ?>
                                                    <span>NA</span>
                                                    <?php } ?>
                                                    
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan='2'></td>
                                                <th colspan='2' class="bg-primary text-white">Gross Total: ₦<?=number_format($total,0) ?></th>
                                                <td></td>
                                                <th colspan="4" class="bg-warning text-white">Net: ₦<?=number_format(($total - $discount),0) ?></th>
                                            </tr>
                                        </tfoot>
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
    <!--
    <script src="assets/demo/datatables-demo.js"></script>
    -->
    <script src="js/xlsx.full.min.js"></script>
    <script src="js/scripts.js"></script>
    <script src="js/tejiri.js"></script>
    <script>
        window.onload=()=>{
            document.getElementById("exportToExcel").addEventListener('click', (event) => {
            event.preventDefault();
            event.stopImmediatePropagation();
            const d = new Date();
            const table = document.getElementById("dataTable");
            let workbook = XLSX.utils.table_to_book(table, {
                sheet: "sheet1"
            });
            return XLSX.writeFile(workbook, "transactions_"+d.getTime() + ".xlsx")
        })
        }
    </script>
</body>

</html>