<?php include_once "../functions/Database.php";
switch ($_GET['sfx']) {
    case 'sf1':
        try {
            //code...
            $db = new Database;
            $cnx = $db->connect();
            $data = [];
            $label = [];
            $color = [];
        
            $year = date('Y-');
            $months = [
                'Jan' => '01', 'Feb' => '02', 'Mar' => '03', 'Apr' => '04', 'May' => '05', 'Jun' => '06',
                'Jul' => '07', 'Aug' => '08', 'Sep' => '09', 'Oct' => '10', 'Nov' => '11', 'Dec' => '12'
            ];
            foreach ($months as $key => $value) {
                $selectedMonth = $year . $value;
                $query = $cnx->query("SELECT COUNT(ref) FROM clients_tbl WHERE created_by = '{$_SESSION['user_id']}' AND created_at LIKE '$selectedMonth%'");
                $sum = $query->fetchColumn() ?? 0;
                $data[] = $sum;
                $label[] = $key;
                $color[] = '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
            }
            echo json_encode(array('response' => ['color' => $color, 'data' => $data, 'label' => $label]));
            return false;
        } catch (\Throwable $th) {
            echo json_encode(["status"=>404,"message"=>$th->getMessage()]);
            return false;
        }
        break;
    
    default:
        # code...
        
        try {
            //code...
            $db = new Database;
            $cnx = $db->connect();
            $data = [];
            $label = [];
            $color = [];
        
            $year = date('Y-');
            $months = [
                'Jan' => '01', 'Feb' => '02', 'Mar' => '03', 'Apr' => '04', 'May' => '05', 'Jun' => '06',
                'Jul' => '07', 'Aug' => '08', 'Sep' => '09', 'Oct' => '10', 'Nov' => '11', 'Dec' => '12'
            ];
            foreach ($months as $key => $value) {
                $selectedMonth = $year . $value;
                $query = $cnx->query("SELECT SUM(amount) FROM transactions WHERE (status = 1) AND (created_by = '{$_SESSION['user_id']}') AND (created_at LIKE '$selectedMonth%')");
                $sum = $query->fetchColumn() ?? 0;
                $data[] = $sum;
                $label[] = $key;
                $color[] = '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
                unset($sum);
            }
            echo json_encode(array('response' => ['color' => $color, 'data' => $data, 'label' => $label]));
            return false;
        } catch (\Throwable $th) {
            echo json_encode(["status"=>404,"message"=>$th->getMessage()]);
            return false;
        }

        break;
}