<?php include '../functions/Functions.php';
$user = $_SESSION['user_id'];
if (101 != $_SESSION['role_id']) {
    echo json_encode(['status' => false, 'message' => 'You Have No Permission For this Operation']);
    die(0);
}

$xTransRef = htmlentities(filter_input(INPUT_POST, 'xTransRef'));
$xTransDesc = htmlentities(filter_input(INPUT_POST, 'xTransDesc'));

if (!$xTransDesc && !$xTransRef) {
    echo json_encode(['status' => false, 'message' => 'Missing Transaction Reference or Description']);
    die(9);
}

try {
    $class = new Functions;
    $fetchTranx = $class->fetch("transactions", " WHERE id = '$xTransRef'");
    if (!$fetchTranx) {
        echo json_encode(['status' => false, 'message' => $xTransRef . ' Does Not Exist']);
        die(9);
    }
    if ($fetchTranx->status == 1) {
        echo json_encode(['status' => false, 'message' => 'You Cannot Delete A Transaction That\'s Already Charged']);
        die(9);
    }
    if ($fetchTranx->status == 2) {
        echo json_encode(['status' => false, 'message' => 'You Cannot Delete A Reversed Transaction']);
        die(9);
    }
    if ($fetchTranx->status == 5) {
        echo json_encode(['status' => false, 'message' => $xTransRef . ' is Already Deleted']);
        die(9);
    }
    $tranx = $class->connect()->query("UPDATE transactions SET status = 5 WHERE id = '$xTransRef'");

    if ($tranx) {
        //remove the tests from client test
        $deleteTests = $class->connect()->query("DELETE FROM tests_taken WHERE tranx_id = '$xTransRef'");
        $class->connect()->query("INSERT INTO deletes(tranx_ref,reason,user_id) VALUES('$xTransRef','$xTransDesc','{$_SESSION['user_id']}')");
        //Log in a file
        $data = $_SESSION['user_id'] . " Deleted " . $xTransRef . " on " . date('Y-m-d H:i:s') . PHP_EOL;
        $fp = fopen('deletes.txt', 'a');
        fwrite($fp, $data);
        fclose($fp);
    }

    echo json_encode(['status' => true, 'message' => 'You Have Successfully Deleted ' . $xTransRef]);
    die(0);
} catch (\Throwable $th) {
    echo json_encode(['status' => false, 'message' => 'Something Happened so We Could Not Complete Operation ', 'reason' => $th->getMessage()]);
    die(0);
}