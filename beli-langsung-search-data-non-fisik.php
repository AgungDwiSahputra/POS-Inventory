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
$table = 'barang_non_fisik'; 
 
// Table's primary key 
$primaryKey = 'bnf_id'; 
 
// Array of database columns which should be read and sent back to DataTables. 
// The `db` parameter represents the column name in the database.  
// The `dt` parameter represents the DataTables column identifier. 
$columns = array( 
    array( 'db' => 'bnf_id', 'dt'              => 0 ),
    array( 'db' => 'bnf_kode', 'dt'            => 1 ), 
    array( 'db' => 'bnf_nama', 'dt'            => 2 ), 
    array( 'db' => 'bnf_harga_jual',      'dt'      => 3 )
); 

// Include SQL query processing class 
require 'aksi/ssp.php'; 

// require('ssp.class.php');

// Output data as json format 
echo json_encode( 
    SSP::simple( $_GET, $dbDetails, $table, $primaryKey, $columns, null, "bnf_status > 0 &&  bnf_cabang = $cabang " )
    // SSP::simple( $_GET, $dbDetails, $table, $primaryKey, $columns)

);

