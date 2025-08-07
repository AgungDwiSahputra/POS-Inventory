<?php 
include 'aksi/koneksi.php';
$cabang = $_GET['cabang'];
$status = $_GET['status'];

// Database connection info 
$dbDetails = array( 
    'host' => $servername, 
    'user' => $username, 
    'pass' => $password, 
    'db'   => $db
); 
 
// DB table to use 
// $table = 'members'; 
$table = <<<EOT
 (
    SELECT 
      a.kasbon_id, 
      a.kasbon_nama,
      a.kasbon_user_id_kasbon,
      a.kasbon_date, 
      a.kasbon_total, 
      a.kasbon_total_cicilan,
      a.kasbon_status_lunas,
      a.kasbon_cabang,
      b.user_id,
      b.user_nama
    FROM kasbon a
    LEFT JOIN user b ON a.kasbon_user_id_kasbon = b.user_id
 ) temp
EOT;
 
// Table's primary key 
$primaryKey = 'kasbon_id'; 
 
// Array of database columns which should be read and sent back to DataTables. 
// The `db` parameter represents the column name in the database.  
// The `dt` parameter represents the DataTables column identifier. 
$columns = array( 
    array( 'db' => 'kasbon_id', 'dt'              => 0 ),
    array( 'db' => 'user_nama', 'dt'            => 1 ), 
    array( 'db' => 'kasbon_nama', 'dt'            => 2 ), 
    array( 'db' => 'kasbon_date',  'dt'         => 3 ), 
    array( 'db' => 'kasbon_total',      'dt'      => 4 ),
    array( 'db' => 'kasbon_total_cicilan',      'dt'      => 5 )
); 

// Include SQL query processing class 
require 'aksi/ssp.php'; 

// require('ssp.class.php');

// Output data as json format 
echo json_encode( 
    SSP::simple( $_GET, $dbDetails, $table, $primaryKey, $columns, null, "kasbon_status_lunas = $status " )
    // SSP::simple( $_GET, $dbDetails, $table, $primaryKey, $columns)

);