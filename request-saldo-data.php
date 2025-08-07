<?php 
include 'aksi/koneksi.php';
$cabang = $_GET['cabang'];

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
      a.rs_id, 
      a.rs_provider_id,
      a.rs_user_id_request,
      a.rs_nominal, 
      a.rs_date_permintaan, 
      a.rs_status,
      a.rs_cabang,
      b.provider_id,
      b.provider_nama,
      c.user_id,
      c.user_nama
    FROM request_saldo a
    LEFT JOIN provider b ON a.rs_provider_id = b.provider_id
    LEFT JOIN user c ON a.rs_user_id_request = c.user_id
 ) temp
EOT;
 
// Table's primary key 
$primaryKey = 'rs_id'; 
 
// Array of database columns which should be read and sent back to DataTables. 
// The `db` parameter represents the column name in the database.  
// The `dt` parameter represents the DataTables column identifier. 
$columns = array( 
    array( 'db' => 'rs_id', 'dt'              => 0 ),
    array( 'db' => 'provider_nama', 'dt'      => 1 ), 
    array( 'db' => 'rs_date_permintaan', 'dt' => 2 ), 
    array( 'db' => 'user_nama', 'dt'          => 3 ), 
    array( 'db' => 'rs_nominal',  'dt'        => 4 ), 
    array( 
        'db'        => 'rs_status', 
        'dt'        => 5, 
        'formatter' => function( $d, $row ) { 
            return ($d == 1)?'<b style="color: blue;">ACC</b>':'<b style="color: red;">Belum ACC</b>'; 
        } 
    ) 
); 

// Include SQL query processing class 
require 'aksi/ssp.php'; 

// require('ssp.class.php');

// Output data as json format 
echo json_encode( 
    SSP::simple( $_GET, $dbDetails, $table, $primaryKey, $columns, null, "rs_cabang = $cabang " )
    // SSP::simple( $_GET, $dbDetails, $table, $primaryKey, $columns)

);