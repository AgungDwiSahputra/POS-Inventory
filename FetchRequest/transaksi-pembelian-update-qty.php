<?php
include '../aksi/functions.php';
$id              	= $_POST['keranjang_id'];
$keranjang_qty   	= $_POST['keranjang_qty'];
$stock_brg        	= $_POST['stock_brg'];
$barang_id         	= $_POST['barang_id'];

// if($keranjang_qty > $stock_brg){
// 	$data['message'] = "QTY Melebihi Stock Barang.. Coba Cek Lagi !!!";
// }else{
	if (empty($data['error'])) {
		// query update data
		$query = "UPDATE keranjang_pembelian SET 
						keranjang_qty   = $keranjang_qty
						WHERE keranjang_id = $id
						AND barang_id = $barang_id
					";
		if (mysqli_query($conn, $query)) {
			$data['hasil'] = 'sukses';
			$data['message'] = "Data Berhasil diupdate";
		} else {
			$data['hasil'] = 'gagal';
			$data['message'] = "Gagal Perintah SQL: " . mysqli_error($conn);
		}
	} else {
		$data['hasil'] = 'gagal';
		$data['message'] = "Data Gagal diupdate";
	}
// }

echo json_encode($data);

