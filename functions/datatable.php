<?php
$table = 'user';
$primaryKey = 'id';

    $condition = " AND (c.fname LIKE :fname) OR (c.lname LIKE :lname) OR (c.phone LIKE :phone OR c.ref LIKE :ref) ";
   
   $query = "SELECT tt.test_id, tt.client_id,c.fname,c.lname,slb.name test,c.phone, DATE_FORMAT(tt.created_at, '%d/%m/%Y') AS created_at, tt.tranx_id 
   FROM tests_taken tt JOIN clients_tbl c ON tt.client_id = c.ref JOIN sub_labtest_tbl slb ON tt.test_id = slb.id 
   WHERE 1 = 1 $condition
   ORDER BY tt.created_at DESC LIMIT 20";

$columns = array(
	array( 'db' => '`c`.`fname`', 'dt' => 0, 'field' => 'fname' ),
	array( 'db' => '`c`.`lname`',  'dt' => 1, 'field' => 'lname' ),
	array( 'db' => '`c`.`phone`',   'dt' => 2, 'field' => 'phone' ),
	array( 'db' => '`tt`.`test`',     'dt' => 3, 'field' => 'tests'),
	array( 'db' => '`tt`.`created_at`', 'dt' => 4, 'field' => 'created_at', 'formatter' => function( $d, $row ) {
																	return date( 'jS M y', strtotime($d));
																})
);
require('config.php');
$sql_details = array(
	'user' => $db_username,
	'pass' => $db_password,
	'db'   => $db_name,
	'host' => $db_host
);
require('ssp.customized.class.php' );

$joinQuery = "FROM `user` AS `u` JOIN `user_details` AS `ud` ON (`ud`.`user_id` = `u`.`id`)";
$extraWhere = "`u`.`salary` >= 90000";
$groupBy = "`u`.`office`";
$having = "`u`.`salary` >= 140000";

echo json_encode(
	SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere, $groupBy, $having )
);