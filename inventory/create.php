<?php include '../functions/Functions.php';
$class = new Functions();
if ($class->checkSession($_SESSION['user_id']) === false)
    header('location: ../logout.php');

$config = $class->fetchSettings();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Add Inventory Stock | <?= $config->name ?></title>
    <link href="../css/styles.css" rel="stylesheet" />
    <link href="../css/dataTables.bootstrap4.min.css" rel="stylesheet" />
    <script src="../js/all.min.js"></script>
</head>

<body class="sb-nav-fixed">
    <?php include_once '../header0.php' ?>
    <div id="layoutSidenav">
        <?php include '../menu.php' ?>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid">
                    <h2 class="mt-4">Add Inventory Stock</h2>
                    <div class="card mb-4">
                        <div class="card-header">

                            <button class="btn btn-pill btn-outline-dark btn-air-dark float-right" onclick="addRows()"
                                type="button">
                                <i class="fa fa-plus-circle"></i>&nbsp;Add Stock item
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <form action="<?php echo $_SERVER['PHP_SELF'] ?>" id="stockForm">
                                    <table class="table table-bordered" id="stockTable" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>Category</th>
                                                <th>Product</th>
                                                <th>Unit</th>
                                                <th>Quantity</th>
                                                <th>Cost</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody id="productTable">
                                            <tr>
                                                <td id="col0">
                                                    <select name="category[]" class="form-control"
                                                        onchange="browseProduct(this)">
                                                        <option value="">Choose...</option>
                                                        <?php
                                                        $categories = $class->rawQuery("SELECT id, name FROM inventory_categories WHERE status = 1");
                                                        foreach ($categories as $category) {
                                                            echo '<option value="' . $category->id . '">' . $category->name . '</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </td>
                                                <td id="col1">
                                                    <select name="product[]" class="form-control"
                                                        onchange="chooseProduct(this)">
                                                        <option value="">Choose...</option>
                                                    </select>
                                                </td>
                                                <td id="col2">
                                                    <input type="text" name="unit" class="form-control" readonly />
                                                </td>
                                                <td id="col3">
                                                    <input type="number" name="quantity[]" class="form-control">
                                                </td>
                                                <td id="col4">
                                                    <input type="number" name="cost[]" class="form-control" value="0">
                                                </td>
                                                <td id="col5">
                                                    <button class="btn-danger btn" onclick="removeRow(this)"
                                                        type="button">
                                                        Remove
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <div class="text-center">
                                        <button id="finalSubmitStockRequest" class="btn btn-primary">Submit</button>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </main>
            <?php include '../footer.php' ?>
        </div>
    </div>
    <script src="../js/jquery-3.4.1.min.js"></script>
    <script src="../js/bootstrap.bundle.min.js"></script>
    <script src="../js/scripts.js"></script>
    <script src="../js/jquery.dataTables.min.js"></script>
    <script src="../js/dataTables.bootstrap4.min.js"></script>
    <script src="../assets/demo/datatables-demo.js"></script>
    <script src="../assets/sweetalert/sweetalert.min.js"></script>
    <script src="../js/tejiri.js"></script>
    <script>
        window.onload = () => {
            document.getElementById("stockForm").addEventListener('submit', (finalEvent) => {
                let formElem = finalEvent.currentTarget;
                finalEvent.preventDefault();
                var payload = [];
                swal({
                    title: "Are you sure?",
                    text: "You are about to add stock to your store. This operation cannot be reversed or edited. Please be sure you know what you are doing before you continue.",
                    icon: "warning",
                    buttons: ["No, Cancel", "Yes Continue"],
                    dangerMode: true,
                })
                    .then((proceed) => {
                        if (proceed) {
                            const btn = document.getElementById("finalSubmitStockRequest");
                            btn.disabled = true;
                            btn.innerHTML = `<progress></progress>`;
                            let tableBodyRef = document.getElementById('stockTable').getElementsByTagName(
                                'tbody')[0];
                            const rows = tableBodyRef.querySelectorAll("tr");
                            //iterate and bring out values entered
                            payload.push({
                                "HTTP_REQUEST_ACTION": "HTTP_REQUEST_ADD_STOCK"
                            });
                            rows.forEach(function (row) {
                                var cols = row.querySelectorAll("td");
                                payload.push({
                                    "category": cols[0].getElementsByTagName("select")[0]
                                        .value,
                                    "product": cols[1].getElementsByTagName("select")[0]
                                        .value,
                                    "unit": cols[2].getElementsByTagName("input")[0].value,
                                    "quantity": cols[3].getElementsByTagName("input")[0]
                                        .value,
                                    "cost": cols[4].getElementsByTagName("input")[0].value
                                });
                            });
                            const data = JSON.stringify(payload);
                            console.log(data);
                            let xhr = new XMLHttpRequest();
                            xhr.open('POST', '../request/xmlHttp.php');
                            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                            xhr.send(data);
                            xhr.onload = function () {
                                if (xhr.status != 200) {
                                    console.log(`Error ${xhr.status}: ${xhr.statusText}`);
                                } else {
                                    btn.disabled = false;
                                    btn.innerHTML = "Submit";
                                    const detail = JSON.parse(xhr.responseText);
                                    if (detail.status) {
                                        formElem.reset();
                                        swal({
                                            title: "Alert",
                                            text: detail.message,
                                            icon: 'success',
                                            timer: 2000
                                        });
                                        window.location.href = "/inventory/stock.php";
                                    } else {
                                        swal("Error", detail.message, 'error');
                                    }
                                }
                            };
                        } else {
                            console.log("Operation Cancelled")
                        }
                    });
            })
        }
    </script>
</body>

</html>