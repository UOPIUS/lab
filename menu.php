<?php
$dashboard = '';
switch ($_SESSION['role_id']):
    case '101':
        $dashboard .= 'admin_dashboard.php'; //admin
        break;
    case '103':
        $dashboard .= 'acct_dashboard.php'; //cashier
        break;
    case '102':
        $dashboard .= 'adhoc_dashboard.php'; //front desk
        break;
    case '105':
        $dashboard .= 'lab_dashboard.php'; //Lab Scientist
        break;
    case '106':
        $dashboard .= 'doctor_dashboard.php'; //doctor
        break;
    case '107':
        $dashboard .= 'logistics_dashboard.php'; //logistics dashboard
        break;
    case '108':
        $dashboard .= 'labtechnician_dashboard.php'; //labtechnician dashboard
        break;
    default:
        $dashboard .= "404.html";
endswitch;

?>

<div id="layoutSidenav_nav">
    <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
        <div class="sb-sidenav-menu">
            <div class="nav">
                <div class="sb-sidenav-menu-heading">Home</div>
                <a class="nav-link" href="<?= $dashboard ?>">
                    <div class="sb-nav-link-icon"><i class="fas fa-home"></i></div>
                    Dashboard
                </a>
                <?php if ($_SESSION['role_id'] == 101): ?>
                <div class="sb-sidenav-menu-heading">Settings</div>
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseLayouts"
                    aria-expanded="false" aria-controls="collapseLayouts">
                    <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
                    Configuration
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>

                </a>
                <div class="collapse" id="collapseLayouts" aria-labelledby="headingOne" data-parent="#sidenavAccordion">
                    <nav class="sb-sidenav-menu-nested nav">
                        <a class="nav-link" href="expense_category.php">Expense Category</a>
                        <a class="nav-link" href="test_category.php">Test Category</a>
                        <a class="nav-link" href="main_test.php">Actual Test</a>
                        <a class="nav-link" href="settings.php">Settings</a>
                        <a class="nav-link" href="xTransaction.php">Remove Transaction</a>
                        <a class="nav-link" href="ptranx.php">Client Transactions</a>
                    </nav>
                </div>

                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePages"
                    aria-expanded="false" aria-controls="collapsePages">
                    <div class="sb-nav-link-icon"><i class="fas fa-book-open"></i></div>
                    Users
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse" id="collapsePages" aria-labelledby="headingTwo" data-parent="#sidenavAccordion">
                    <nav class="sb-sidenav-menu-nested nav accordion" id="sidenavAccordionPages">
                        <a class="nav-link " href="all_clients.php">Clients</a>
                        <a class="nav-link " href="users.php">System Admins</a>
                    </nav>
                </div>
                <a class="nav-link " href="all_tranx.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-arrow-up"></i></div>
                    Transactions
                </a>
                <a class="nav-link" href="ptranx.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-times"></i></div>
                    Editable Tx
                </a>

                <?php endif; ?>
                <?php if ($_SESSION['role_id'] == 102): ?>
                <div class="sb-sidenav-menu-heading">Patients Reg</div>
                <a class="nav-link" href="fd_clients.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                    Patients Reg
                </a>
                <a class="nav-link" href="print_test_result.php">
                    <div class="sb-nav-link-icon"><i class="fas fa fa-bath"></i></div>
                    Tests Result
                </a>
                <a class="nav-link" href="pendingLabDoc.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-stethoscope"></i></div>
                    Pending Lab Test
                </a>
                <a class="nav-link">
                    <div class="sb-nav-link-icon"></div>

                    <?php endif; ?>
                    <?php if ($_SESSION['role_id'] == 103): ?>
                    <div class="sb-sidenav-menu-heading">Lab Tests</div>
                    <a class="nav-link" href="ptranx.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-check-circle"></i></div>
                        Pending Cashout
                    </a>

                    <a class="nav-link" href="all_tranx.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-signal"></i></div>
                        All Transactions
                    </a>
                    <a class="nav-link" href="req_reverse.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-filter"></i></div>
                        Ask for Refund
                    </a>
                    <a class="nav-link" href="account_summary.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-bookmark"></i></div>
                        Account Summary
                    </a>


                    <a class="nav-link" href="request_fund.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-calendar"></i></div>
                        Expense Reports
                    </a>

                    <?php endif; ?>
                    <!-- Adhoc Roles Added to Cashier --
                <?php if ($_SESSION['role_id'] == 103): ?>
                <div class="sb-sidenav-menu-heading">Front Desk Menu</div>
                <!--
                <a class="nav-link" href="fd_clients.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                    Clients
                </a>
                --
                <a class="nav-link" href="fd_testreports.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-book"></i></div>
                    Lab
                </a>
                <a class="nav-link" href="fd_lab_test.php">
                    <div class="sb-nav-link-icon"><i class="fas fa fa-bath"></i></div>
                    Tests
                </a>
                <?php endif; ?>
                <!-- Adhoc Roles Added to Cashier Stops -->

                    <?php if ($_SESSION['role_id'] == 1071): ?>
                    <div class="sb-sidenav-menu-heading">Transactions</div>
                    <a class="nav-link" href="commission.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-building"></i></div>
                        View Commission
                    </a>

                    <?php endif; ?>


                    <?php if ($_SESSION['role_id'] == 106): ?>
                    <div class="sb-sidenav-menu-heading">Settings</div>
                    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseLayouts"
                        aria-expanded="false" aria-controls="collapseLayouts">
                        <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
                        Configuration
                        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>

                    </a>
                    <div class="collapse" id="collapseLayouts" aria-labelledby="headingOne"
                        data-parent="#sidenavAccordion">
                        <nav class="sb-sidenav-menu-nested nav">
                            <a class="nav-link" href="test_category.php">Test Category</a>
                            <a class="nav-link" href="main_test.php">Actual Test</a>
                            <a class="nav-link" href="testTemplate.php">Test Templates</a>
                            <a class="nav-link" href="specimen.php">Specimens</a>
                            <a class="nav-link" href="referrals.php">Referrals</a>
                        </nav>
                    </div>
                    <div class="sb-sidenav-menu-heading">Transactions</div>
                    <a class="nav-link" href="pendingLabDoc.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-hospital"></i></i></div>
                        Pending Lab Test
                    </a>

                    <a class="nav-link" href="account_summary.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-bookmark"></i></div>
                        Account Summary
                    </a>
                    <a class="nav-link" href="all_tranx.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-signal"></i></div>
                        All Account Transactions
                    </a>

                    <a class="nav-link" href="reversal.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-times-circle"></i></div>
                        Approve Refund
                    </a>
                    <a class="nav-link" href="request_fund.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-calendar"></i></div>
                        Make Expense
                    </a>
                    <a class="nav-link" href="approve_request.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-arrow-circle-right"></i></div>
                        Expense Mgt
                    </a>

                    <?php endif; ?>
                    <!-- Lab Scientist Menu -->

                    <?php if ($_SESSION['role_id'] == 105): ?>
                    <div class="sb-sidenav-menu-heading">Lab Scientist Menu</div>
                    <a class="nav-link" href="pendingLabDoc.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-building"></i></div>
                        Pending Lab Test
                    </a>
                    <a class="nav-link" href="fd_lab_test.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-check-circle"></i></div>
                        Completed Lab Test
                    </a>
                    <a class="nav-link" href="#">
                        <div class="sb-nav-link-icon"><i class="fas fa-check-circle"></i></div>
                        Patient Consult Profile
                    </a>
                    <a class="nav-link" href="testTemplate.php">
                        <div class="sb-nav-link-icon"><i class="fa fa-cog"></i></div>
                        Setup Templates
                    </a>

                    <?php endif; ?>
                    <!-- Lab Scientist Menu Ends -->
                    <?php if ('106' == $_SESSION['role_id'] || '103' == $_SESSION['role_id'] || '101' == $_SESSION['role_id']): ?>
                    <a class="nav-link" href="debtors.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-check-circle"></i></div>
                        CMA Debtors
                    </a>
                </a>
                <?php endif; ?>
                <?php if ($_SESSION['role_id'] == 107): ?>
                <div class="sb-sidenav-menu-heading">Inventory</div>
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseLayouts"
                    aria-expanded="false" aria-controls="collapseLayouts">
                    <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
                    Manage inventory
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>

                </a>
                <div class="collapse" id="collapseLayouts" aria-labelledby="headingOne" data-parent="#sidenavAccordion">
                    <nav class="sb-sidenav-menu-nested nav">
                        <a class="nav-link" href="/inventory_category/categories.php">Category</a>
                        <a class="nav-link" href="/inventory_category/unit.php">Units</a>
                        <a class="nav-link" href="/inventory/items.php">Items</a>
                        <a class="nav-link" href="/inventory/stock.php">Record Stock</a>
                    </nav>
                </div>
                <a class="nav-link" href="inventory_assigned.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-check-circle"></i></div>
                    Assigned Items
                </a>
                <?php endif ?>
            </div>
        </div>
        <div class="sb-sidenav-footer">
            <div class="small">Logged in as:</div>
            <?php echo $_SESSION['name'] ?>
        </div>
    </nav>
</div>
<?php if (102 == $_SESSION['role_id']): ?>
<script type="text/javascript">
window.addEventListener('load', (domEvent) => {
    setInterval(function() {
        if (null == document.getElementById('customOverlay'))
            fetchNotification();

    }, 25000)
});

function fetchNotification() {
    let xhr = new XMLHttpRequest();
    xhr.open('POST', 'request/notifi.php');
    var data = new URLSearchParams({
        NOTIFY_ACTION: 'HTTP_INFORM_ME'
    }).toString()
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.send(data);
    xhr.onload = function() {
        if (xhr.status != 200) {
            console.log(xhr.statusText);
        } else {
            var jsonData = JSON.parse(xhr.response);
            if (jsonData.status) {
                alertBox(jsonData.message);
                return;
            }
            console.log(jsonData.message)

        }
    };
}

function alertBox(param = 'Please Wait . . .') {
    const overLay = document.createElement('div');
    overLay.id = 'customOverlay';
    overLay.style.position = 'fixed';
    overLay.style.textAlign = 'center';
    overLay.style.display = 'block';
    overLay.style.width = 100 + '%';
    overLay.style.height = 100 + '%';
    overLay.style.top = 0;
    overLay.style.color = 2.5 + 'em';
    overLay.style.margin = 'auto';
    overLay.style.left = 0;
    overLay.style.right = 0;
    overLay.style.bottom = 0;

    overLay.style.backgroundColor = 'rgba(0,0,0,0.6)';
    overLay.style.zIndex = 999;
    overLay.style.cursor = 'pointer';

    let innerLay = document.createElement('div');
    innerLay.style.backgroundColor = "#CD0000";
    innerLay.style.marginTop = '60px'
    innerLay.style.paddingBottom = "15px"
    innerLay.style.paddingTop = '5px'
    innerLay.style.paddingRight = '2px'
    innerLay.style.paddingLeft = '2px'
    innerLay.style.position = 'absolute'
    innerLay.style.minWidth = '420px'
    innerLay.style.right = 0;

    let leftIcon = document.createElement("span");
    leftIcon.innerHTML = "<i class='fas fa-exclamation-circle></i>"
    let message = document.createElement("span");
    message.style.fontWeight = "bold";
    message.style.fontSize = '18px'
    message.style.color = "#fff"
    message.innerHTML = param;
    let rightIcon = document.createElement("span");
    rightIcon.innerHTML = "<i class='fas fa-times-circle></i>"
    innerLay.appendChild(leftIcon);
    innerLay.appendChild(message)
    innerLay.appendChild(rightIcon)
    overLay.appendChild(innerLay);
    document.body.appendChild(overLay);
    const music = new Audio('radio.mp3');
    music.play();
    music.loop = true;

    message.addEventListener('click', (t) => {
        overLay.style.display = "none";
        music.pause();
        overLay.remove();
        const url = new URL(window.location.href);
        if ("print_test_result.php" == url.pathname) {
            return;
        }
        window.open("print_test_result.php");
        //window.location.href = "print_test_result.php"
    })
}
</script>
<?php endif; ?>