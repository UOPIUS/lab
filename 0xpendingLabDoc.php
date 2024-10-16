<?php include_once 'functions/Functions.php'; 
//if(!($_SESSION['role_id'] && $_SESSION['user_id'])) header('location: login.php');
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
    <title>Pending Transactions</title>

    <link href="css/materialdesignicons.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css" rel="stylesheet" />
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
                        <h4 class="mt-4"><i class="fas fa-stethoscope mr-1"></i>Pending Laboratory Tests</h4>
                        <div class="card mb-4">
                            <div class="card-body">
                                <div class="table-responsive mt-4">
                                    <table class="table table-bordered" id="dataTablex" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>CMA Ref</th>
                                                <th>First Name</th>
                                                <th>Last Name</th>
                                                <th>Phone</th>
                                                <th>Tests</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tfoot>
                                            <tr>
                                                <th>CMA Ref</th>
                                                <th>First Name</th>
                                                <th>Last Name</th>
                                                <th>Phone</th>
                                                <th>Tests</th>
                                                <th>Date</th>
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
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="js/scripts.js"></script>
    <script src="js/tejiri.js"></script>
    <script type="text/javascript">
        var table = $('#dataTablex').DataTable({
    lengthChange: false,
    paging: false,
    search: {
        return: true
    },
    ajax: 'https://lab.capitalmedicares.com/functions/a5ikwbirnrcbf4raa39823r3mc9grd.php',
    dataSrc: 'data',
    processing: true,
    serverSide: true,
    "columnDefs": [
    {
        "targets": 0,
        "render": function(data, type, full, meta) {
            return '<a href=client_profile.php?refx=' + data + '>' + data + '</a>'
        }
    },
    {
        "targets": 4,
        "render": function(data, type, full, meta) {
            return '<a href=report_test.php?refx=' + full.tnx + '>' + data + '</a>'
        }
    }],
    columns: [{
        "data": "client_id"
    }, {
        "data": "fname"
    }, {
        "data": "lname"
    }, {
        "data": "phone"
    }, {
        "data": "tests"
    }, {
        "data": "created_at"
    }]
});
// 	    	$(function() {
// 			    var myTable = $('#dataTablex').DataTable({
// 			        processing: true,
// 			        "lengthChange": false,
// 			        serverSide: true,
// 			        ajax: "https://lab.capitalmedicares.com/functions/a5ikwbirnrcbf4raa39823r3mc9grd.php",
// 			        "columnDefs": [
// 			         {
// 			            "targets": 0,
// 			            "render": function ( data, type, full, meta ) {
// 			              return '<a href=client_profile.php?refx='+data+'>'+data+'</a>'
// 			            }
// 			         }
// 			        ],
// 			        columns: [
// 			            { "data": "client_id" },
//                         { "data": "fname" },
//                         { "data": "lname" },
//                         { "data": "phone" },
//                         { "data": "tests" },
//                         { "data": "created_at" }
// 			        ]
// 			    });
// 				$(".dataTables_filter input")
// 				.unbind()
// 				.bind('keyup change', function(e) {
// 					if (e.keyCode == 13 || this.value == "") {
// 						myTable.search(this.value).draw();
// 					}
// 				});
// 			});
	</script>
</body>

</html>