<?php include_once "Database.php";
if ($_GET["request"] || $_REQUEST["uf"]) {
    try {
        if (!$_SESSION["user_id"]) {
            echo json_encode([
                "response" => ["color" => [], "data" => [], "label" => []],
            ]);
            die(0);
        }
        //code...
        $db = new Database();
        $cnx = $db->connect();
        $data = [];
        $label = [];
        $color = [];
        $reg = [];
        $idx = $_SESSION["user_id"];
        //$user = $cnx->prepare(" SELECT ref FROM clients WHERE id = ?");
        //$user->execute([$idx]);
        //$mfx_customer_ref = $user->fetchColumn();

        $year = date("Y-");
        $months = [
            "Jan" => "01",
            "Feb" => "02",
            "Mar" => "03",
            "Apr" => "04",
            "May" => "05",
            "Jun" => "06",
            "Jul" => "07",
            "Aug" => "08",
            "Sep" => "09",
            "Oct" => "10",
            "Nov" => "11",
            "Dec" => "12",
        ];
        foreach ($months as $key => $value) {
            $selectedMonth = $year . $value;
            $query = $cnx->query(
                "SELECT SUM(amount) FROM transactions WHERE status = 1 AND created_at LIKE '$selectedMonth%'"
            );
            $col = $query->fetchColumn();
            $sum = $col ? $col : 0;
            $data[] = $sum;
            $label[] = $key;
            $color[] =
                "#" .
                str_pad(dechex(mt_rand(0, 0xffffff)), 6, "0", STR_PAD_LEFT);
        }

        //registration for each month throughout the year
        foreach ($months as $key => $value) {
            $selectedMonth = $year . $value;
            $query = $cnx->query(
                "SELECT SUM(amount) FROM transactions WHERE status = 1 AND created_at LIKE '$selectedMonth%'"
            );
            $sum = $query->fetchColumn() ?? 0;
            $reg[] = $sum;
        }

        //expenses for each month throughout the year
        $expenses = [];
        foreach ($months as $key => $value) {
            $selectedMonth = $year . $value;
            $query = $cnx->query(
                "SELECT SUM(amount_approved) FROM expenses WHERE approved_at LIKE '$selectedMonth%'"
            );
            $sum = $query->fetchColumn() ?? 0;
            $expenses[] = $sum;
        }

        //calculate the days of the current month and their sales for each day
        $daysofMonth = [];
        $daysColor = [];
        $dailySale = [];
        $date = date("Y-m-") . "01";
        $end = date("Y-m-") . date("t", strtotime($date)); //get end date of month
        while (strtotime($date) <= strtotime($end)) {
            $day_num = date("d", strtotime($date));
            $day_name = date("D", strtotime($date));
            $daily = date("Y-m-") . $day_num;
            $vw = $cnx->query(
                "SELECT SUM(amount) FROM transactions WHERE (status = 1) AND (created_at LIKE '%$daily%')"
            );

            $amt = $vw->fetchColumn() ?? 0;
            $dailySale[] = $amt;
            $date = date("Y-m-d", strtotime("+1 day", strtotime($date)));
            $day = $day_num . " " . $day_name;
            $daysofMonth[] = $day;
            $daysColor[] =
                "#" .
                str_pad(dechex(mt_rand(0, 0xffffff)), 6, "0", STR_PAD_LEFT);
        }
        //count customers
        $customers = $cnx
            ->query("SELECT COUNT(ref) FROM clients_tbl")
            ->fetchColumn();

        //payment by payment method 1-CAsh,2-Transfer,3-POS,4-Check,5-Paylater,6-Paystack
        $month = date("Y-m");
        $pos = $cnx
            ->query(
                "SELECT SUM(amount) FROM outstanding_tbl WHERE (amount>0) AND (payment_method = 3) AND (created_at LIKE '%$month%')"
            )
            ->fetchColumn();
        $check = $cnx
            ->query(
                "SELECT SUM(amount) FROM outstanding_tbl WHERE (amount>0) AND (payment_method = 4) AND (created_at LIKE '%$month%')"
            )
            ->fetchColumn();
        $cash = $cnx
            ->query(
                "SELECT SUM(amount) FROM outstanding_tbl WHERE (amount>0) AND (payment_method = 1) AND (created_at LIKE '%$month%')"
            )
            ->fetchColumn();
        $transfer = $cnx
            ->query(
                "SELECT SUM(amount) FROM outstanding_tbl WHERE (amount>0) AND (payment_method = 2) AND (created_at LIKE '%$month%')"
            )
            ->fetchColumn();
        $paystack = $cnx
            ->query(
                "SELECT SUM(amount) FROM outstanding_tbl WHERE (amount>0) AND (payment_method = 6)  AND (created_at LIKE '%$month%')"
            )
            ->fetchColumn();

        //grand total sales
        $totalSales = $cnx
            ->query("SELECT SUM(amount) FROM transactions WHERE (status = 1)")
            ->fetchColumn();
        $totalExpenses = $cnx
            ->query("SELECT SUM(amount_requested) FROM expenses")
            ->fetchColumn();

        //outstanding
        $balance = $cnx
            ->query(
                "SELECT SUM(amount) paid, SUM(payable_amount) expected_amount  FROM transactions t WHERE (t.status <= 1) AND (ABS(t.payable_amount) > t.amount)"
            )
            ->fetch(PDO::FETCH_NUM);
        $outstanding = abs($balance[1]) - $balance[0];
        //sale for current month
        $monthSale = $cnx
            ->query(
                "SELECT SUM(amount) FROM transactions WHERE (status = 1) AND (created_at LIKE '%$month-%')"
            )
            ->fetchColumn();
        $monthExpense = $cnx
            ->query(
                "SELECT SUM(amount_approved) FROM expenses WHERE (approved_at LIKE '%$month-%')"
            )
            ->fetchColumn();
            
        //group test to category and get their statistics
        $testCategoriesX = $cnx->query("SELECT id,name FROM test_categories WHERE status = 1");
        $testCategories = $testCategoriesX->fetchAll(PDO::FETCH_NUM);
        $testCategoryArray = [];
        $testPerformed = [];
        foreach($testCategories as $tt):
            $testCategoryArray[] = $tt[1];
            //for each of this category, retrieve their test statistics
            $eachCategoryTestX = $cnx->query("SELECT COUNT(test_id) FROM tests_taken WHERE category_id = '".$tt[0]."'");
            $eachCategoryTest = $eachCategoryTestX->fetchColumn() ?? 0;
            $testPerformed[] = $eachCategoryTest;
        endforeach;
        
        echo json_encode([
            "response" => [
                "color" => $color,
                "data" => $data,
                "label" => $label,
                "dailySale" => $dailySale,
                "reg" => $reg,
                "rduration" => $daysofMonth,
                "daysColor" => $daysColor,
                "expenses" => $expenses,
            ],
            "methods" => [
                "pos" => number_format($pos, 0),
                "cheque" => number_format($check, 0),
                "cash" => number_format($cash, 0),
                "transfers" => number_format($transfer, 0),
                "paystack" => number_format($paystack, 0),
            ],
            "customersKount" => number_format($customers, 0),
            "mtotalSales" => number_format($monthSale, 0),
            "mTotalExpenses" => number_format($monthExpense, 0),
            "totalSales" => number_format($totalSales, 0),
            "totalExpenses" => number_format($totalExpenses, 0),
            "outstanding" => number_format($outstanding, 0),
            "categoryOfTest" => $testCategoryArray,
            "testCarriedOut" => $testPerformed
        ]);
        die(0);
    } catch (\Throwable $th) {
        echo json_encode(["status" => 404, "message" => $th->getMessage()]);
        exit(0);
    }
}
