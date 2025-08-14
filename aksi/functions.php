<?php 

// koneksi ke database
include 'koneksi.php';


function query($query, $params = []) {
	global $conn;
	if ($conn == null) {
		throw new Exception('Koneksi ke database gagal.');
	}
	$stmt = mysqli_stmt_init($conn);
	if (!mysqli_stmt_prepare($stmt, $query)) {
		throw new Exception(mysqli_stmt_error($stmt));
	}
	if (count($params) > 0) {
		$types = '';
		foreach ($params as $param) {
			$types .= gettype($param) == 'string' ? 's' : 'i';
		}
		mysqli_stmt_bind_param($stmt, $types, ...$params);
	}
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	if (!$result) {
		throw new Exception(mysqli_error($conn));
	}
	$rows = [];
	while ( $row = mysqli_fetch_assoc($result) ) {
		$rows[] = $row;
	}
	mysqli_stmt_close($stmt);
	return $rows;
}
function tanggal_indo($tanggal){
    $bulan = array (1 =>   'Januari',
                'Februari',
                'Maret',
                'April',
                'Mei',
                'Juni',
                'Juli',
                'Agustus',
                'September',
                'Oktober',
                'November',
                'Desember'
            );
    $split = explode('-', $tanggal);
    return $split[2] . ' ' . $bulan[ (int)$split[1] ] . ' ' . $split[0];
}

function singkat_angka($n, $presisi=1) {
	if ($n < 900) {
		$format_angka = number_format($n, $presisi);
		$simbol = '';
	} else if ($n < 900000) {
		$format_angka = number_format($n / 1000, $presisi);
		$simbol = ' rb';
	} else if ($n < 900000000) {
		$format_angka = number_format($n / 1000000, $presisi);
		$simbol = ' jt';
	} else if ($n < 900000000000) {
		$format_angka = number_format($n / 1000000000, $presisi);
		$simbol = ' M';
	} else {
		$format_angka = number_format($n / 1000000000000, $presisi);
		$simbol = ' T';
	}
 
	if ( $presisi > 0 ) {
		$pisah = '.' . str_repeat( '0', $presisi );
		$format_angka = str_replace( $pisah, '', $format_angka );
	}
	
	return $format_angka . $simbol;
}

// ================================================ USER ====================================== //
 
function tambahUser($data) {
	global $conn;
	// ambil data dari tiap elemen dalam form
	$user_nama = htmlspecialchars($data["user_nama"]);
	$user_no_hp = htmlspecialchars($data["user_no_hp"]);
	$user_alamat = htmlspecialchars($data["user_alamat"]);
	$user_email = htmlspecialchars($data["user_email"]);
	$user_password = md5(md5(htmlspecialchars($data["user_password"])));
	$user_create = date("d F Y g:i:s a");
	$user_level = htmlspecialchars($data["user_level"]);
	$user_status = htmlspecialchars($data["user_status"]);
	$user_cabang = htmlspecialchars($data["user_cabang"]);

	$tipe       = htmlspecialchars($data["tipe"]);

	if ( $tipe < 1 ) {
		// Cek Email
		$email_user_cek = mysqli_num_rows(mysqli_query($conn, "select * from user where user_email = '$user_email' "));

		if ( $email_user_cek > 0 ) {
			echo "
				<script>
					alert('Email Sudah Terdaftar');
				</script>
			";
		} else {
			// query insert data
			$query = "INSERT INTO user VALUES ('', '$user_nama', '$user_no_hp', '$user_alamat', '$user_email', '$user_password', '$user_create', '$user_level' , '$user_status', 0, 0, 0, '$user_cabang')";
			mysqli_query($conn, $query);

			return mysqli_affected_rows($conn);
		}
	} else {
		// query insert data
			$query = "INSERT INTO user VALUES ('', '$user_nama', '$user_no_hp', '$user_alamat', '$user_email', '$user_password', '$user_create', '$user_level' , '$user_status', 1, 1, 1, '$user_cabang')";
			mysqli_query($conn, $query);

			return mysqli_affected_rows($conn);
	}
}

function editUser($data){
	global $conn;
	$id = $data["user_id"];


	// ambil data dari tiap elemen dalam form
	$user_nama = htmlspecialchars($data["user_nama"]);
	$user_no_hp = htmlspecialchars($data["user_no_hp"]);
	$user_email = htmlspecialchars($data["user_email"]);
	$user_alamat = htmlspecialchars($data["user_alamat"]);
	$user_password = md5(md5(htmlspecialchars($data["user_password"])));
	$user_level = htmlspecialchars($data["user_level"]);
	$user_status = htmlspecialchars($data["user_status"]);

		// query update data
		$query = "UPDATE user SET 
						user_nama      = '$user_nama',
						user_no_hp     = '$user_no_hp',
						user_alamat    = '$user_alamat',
						user_email     = '$user_email',
						user_password  = '$user_password',
						user_level     = '$user_level',
						user_status    = '$user_status'
						WHERE user_id  = $id
				";
		// var_dump($query); die();
		mysqli_query($conn, $query);

		return mysqli_affected_rows($conn);

}

function editUserKasbon($data){
	global $conn;
	$id = $data["user_id"];


	// ambil data dari tiap elemen dalam form
	$user_nama = htmlspecialchars($data["user_nama"]);
	$user_no_hp = htmlspecialchars($data["user_no_hp"]);
	$user_alamat = htmlspecialchars($data["user_alamat"]);
	$user_kasbon_status = htmlspecialchars($data["user_kasbon_status"]);

		// query update data
		$query = "UPDATE user SET 
						user_nama      		  	= '$user_nama',
						user_no_hp     			= '$user_no_hp',
						user_alamat    			= '$user_alamat',
						user_kasbon_status    	= '$user_kasbon_status'
						WHERE user_id  			= $id
				";
		// var_dump($query); die();
		mysqli_query($conn, $query);

		return mysqli_affected_rows($conn);
}

function hapusUser($id) {
	global $conn;
	mysqli_query( $conn, "DELETE FROM user WHERE user_id = $id");

	return mysqli_affected_rows($conn);
}

function hapusUserKasbon($id, $user_tipe_login) {
	global $conn;

    // user_tipe_login = 1 aksi delete
    // Jika user_tipe_login = 0 aksi edit

	if ( $user_tipe_login == 1 ) {
		mysqli_query( $conn, "DELETE FROM user WHERE user_id = $id");

	} else {
		// query update data
		$query = "UPDATE user SET 
						user_kasbon      		= 0,
						user_kasbon_status     	= 0
						WHERE user_id  			= $id
				";
		mysqli_query($conn, $query);
	}
	

	return mysqli_affected_rows($conn);
}
// ========================================= Toko ======================================== //
function tambahToko($data) {
	global $conn;
	// ambil data dari tiap elemen dalam form
	$toko_nama      = htmlspecialchars($data["toko_nama"]);
	$toko_kota      = htmlspecialchars($data["toko_kota"]);
	$toko_alamat    = htmlspecialchars($data["toko_alamat"]);
	$toko_tlpn      = htmlspecialchars($data["toko_tlpn"]);
	$toko_wa        = htmlspecialchars($data["toko_wa"]);
	$toko_email     = htmlspecialchars($data["toko_email"]);
	$toko_print     = htmlspecialchars($data["toko_print"]);
	$toko_status    = htmlspecialchars($data["toko_status"]);
	$toko_ongkir    = htmlspecialchars($data["toko_ongkir"]);
	$toko_cabang    = htmlspecialchars($data["toko_cabang"]);

	
	// query insert data toko
	$query = "INSERT INTO toko VALUES ('', '$toko_nama', '$toko_kota', '$toko_alamat', '$toko_tlpn', '$toko_wa', '$toko_email', '$toko_print' ,'$toko_status', '$toko_ongkir', '$toko_cabang')";
	mysqli_query($conn, $query);

	// query insert data laba bersih
	$query2 = "INSERT INTO laba_bersih VALUES ('', '', '', '', '', '', '', '' ,'', '', '$toko_cabang')";
	mysqli_query($conn, $query2);


	return mysqli_affected_rows($conn);
}

function editToko($data) {
	global $conn;
	$id = $data["toko_id"];

	// ambil data dari tiap elemen dalam form
	$toko_nama      = htmlspecialchars($data["toko_nama"]);
	$toko_kota      = htmlspecialchars($data["toko_kota"]);
	$toko_alamat    = htmlspecialchars($data["toko_alamat"]);
	$toko_tlpn      = htmlspecialchars($data["toko_tlpn"]);
	$toko_wa        = htmlspecialchars($data["toko_wa"]);
	$toko_email     = htmlspecialchars($data["toko_email"]);
	$toko_print     = htmlspecialchars($data["toko_print"]);
	$toko_status    = htmlspecialchars($data["toko_status"]);
	$toko_ongkir    = htmlspecialchars($data["toko_ongkir"]);

	// query update data
	$query = "UPDATE toko SET 
				toko_nama       = '$toko_nama',
				toko_kota       = '$toko_kota',
				toko_alamat     = '$toko_alamat',
				toko_tlpn       = '$toko_tlpn',
				toko_wa         = '$toko_wa',
				toko_email      = '$toko_email',
				toko_print      = '$toko_print',
				toko_status     = '$toko_status',
				toko_ongkir		= '$toko_ongkir'
				WHERE toko_id   = $id
				";
	mysqli_query($conn, $query);
	return mysqli_affected_rows($conn);
}
function hapusToko($id) {
	global $conn;

	$cabang = mysqli_query($conn, "select toko_cabang from toko where toko_id = ".$id." ");
	$cabang = mysqli_fetch_array($cabang);
	$toko_cabang = $cabang['toko_cabang'];

	mysqli_query( $conn, "DELETE FROM toko WHERE toko_id = $id");
	mysqli_query( $conn, "DELETE FROM laba_bersih WHERE lb_cabang = $toko_cabang");

	mysqli_query( $conn, "DELETE FROM supplier WHERE supplier_cabang = $toko_cabang");
	mysqli_query( $conn, "DELETE FROM kategori WHERE kategori_cabang = $toko_cabang");
	mysqli_query( $conn, "DELETE FROM satuan WHERE satuan_cabang = $toko_cabang");
	mysqli_query( $conn, "DELETE FROM barang WHERE barang_cabang = $toko_cabang");
	mysqli_query( $conn, "DELETE FROM barang_sn WHERE barang_sn_cabang = $toko_cabang");
	mysqli_query( $conn, "DELETE FROM invoice_pembelian WHERE invoice_pembelian_cabang = $toko_cabang");
	mysqli_query( $conn, "DELETE FROM pembelian WHERE pembelian_cabang = $toko_cabang");
	mysqli_query( $conn, "DELETE FROM transfer WHERE transfer_cabang = $toko_cabang");
	mysqli_query( $conn, "DELETE FROM transfer_produk_keluar WHERE tpk_cabang = $toko_cabang");
	mysqli_query( $conn, "DELETE FROM transfer_produk_masuk WHERE tpm_cabang = $toko_cabang");
	mysqli_query( $conn, "DELETE FROM user WHERE user_cabang = $toko_cabang");

	return mysqli_affected_rows($conn);
}

// ========================================= Kategori ======================================= //
function tambahKategori($data) {
	global $conn;
	// ambil data dari tiap elemen dalam form
	$kategori_nama = htmlspecialchars($data['kategori_nama']);
	$kategori_status = $data['kategori_status'];
	$kategori_cabang = $data['kategori_cabang'];

	// query insert data
	$query = "INSERT INTO kategori VALUES ('', '$kategori_nama', '$kategori_status', '$kategori_cabang')";
	mysqli_query($conn, $query);

	return mysqli_affected_rows($conn);
}

function editKategori($data) {
	global $conn;
	$id = $data["kategori_id"];

	// ambil data dari tiap elemen dalam form
	$kategori_nama = htmlspecialchars($data['kategori_nama']);
	$kategori_status = $data['kategori_status'];

	// query update data
	$query = "UPDATE kategori SET 
				kategori_nama   = '$kategori_nama',
				kategori_status = '$kategori_status'
				WHERE kategori_id = $id
				";
	mysqli_query($conn, $query);
	return mysqli_affected_rows($conn);
}

function hapusKategori($id) {
	global $conn;
	mysqli_query( $conn, "DELETE FROM kategori WHERE kategori_id = $id");

	return mysqli_affected_rows($conn);
}

// ========================================= Sub Kategori ======================================= //
function tambahSubKategori($data) {
	global $conn;
	// ambil data dari tiap elemen dalam form
	$id = $data["kategori_id"];
	$sub_kategori_nama = htmlspecialchars($data['sub_kategori_nama']);
	// $sub_kategori_kode = strtoupper(substr($sub_kategori_nama, 0, 4));
	$sub_kategori_kode = htmlspecialchars($data['sub_kategori_kode']);
	$sub_kategori_status = $data['sub_kategori_status'];

	// query insert data
	$query = "INSERT INTO sub_kategori (kategori_id, sub_kategori_kode, sub_kategori_nama, sub_kategori_status) VALUES ($id, '$sub_kategori_kode', '$sub_kategori_nama', '$sub_kategori_status')";
	if (!mysqli_query($conn, $query)) {
		return [
			'success' => false,
			'error' => mysqli_error($conn)
		];
	}

	return [
		'success' => true,
		'affected_rows' => mysqli_affected_rows($conn)
	];
}

function hapusSubKategori($id) {
	global $conn;
	mysqli_query( $conn, "DELETE FROM sub_kategori WHERE id = $id");

	return mysqli_affected_rows($conn);
}

function editSubKategori($data) {
	global $conn;
	// ambil data dari tiap elemen dalam form
	$id = $data["id"];
	$sub_kategori_nama = htmlspecialchars($data['sub_kategori_nama']);
	// $sub_kategori_kode = strtoupper(substr($sub_kategori_nama, 0, 4));
	$sub_kategori_kode = htmlspecialchars($data['sub_kategori_kode']);
	$sub_kategori_status = $data['sub_kategori_status'];

	// query update data
	$query = "UPDATE sub_kategori SET sub_kategori_kode = '$sub_kategori_kode', sub_kategori_nama = '$sub_kategori_nama', sub_kategori_status = '$sub_kategori_status' WHERE id = $id";
	if (!mysqli_query($conn, $query)) {
		return [
			'success' => false,
			'error' => mysqli_error($conn)
		];
	}

	return [
		'success' => true,
		'affected_rows' => mysqli_affected_rows($conn)
	];
}


// ======================================= Satuan ========================================= //
function tambahSatuan($data) {
	global $conn;
	// ambil data dari tiap elemen dalam form
	$satuan_nama = htmlspecialchars($data['satuan_nama']);
	$satuan_status = $data['satuan_status'];
	$satuan_cabang = $data['satuan_cabang'];

	// query insert data
	$query = "INSERT INTO satuan VALUES ('', '$satuan_nama', '$satuan_status', '$satuan_cabang')";
	mysqli_query($conn, $query);

	return mysqli_affected_rows($conn);
}

function editSatuan($data) {
	global $conn;
	$id = $data["satuan_id"];

	// ambil data dari tiap elemen dalam form
	$satuan_nama = htmlspecialchars($data['satuan_nama']);
	$satuan_status = $data['satuan_status'];

	// query update data
	$query = "UPDATE satuan SET 
				satuan_nama   = '$satuan_nama',
				satuan_status = '$satuan_status'
				WHERE satuan_id = $id
				";
	mysqli_query($conn, $query);
	return mysqli_affected_rows($conn);
}

function hapusSatuan($id) {
	global $conn;
	mysqli_query( $conn, "DELETE FROM satuan WHERE satuan_id = $id");

	return mysqli_affected_rows($conn);
}


// ===================================== ekspedisi ========================================= //
function tambahEkspedisi($data) {
	global $conn;
	// ambil data dari tiap elemen dalam form
	$ekspedisi_nama = htmlspecialchars($data['ekspedisi_nama']);
	$ekspedisi_status = $data['ekspedisi_status'];
	$ekspedisi_cabang = $data['ekspedisi_cabang'];

	// query insert data
	$query = "INSERT INTO ekspedisi VALUES ('', '$ekspedisi_nama', '$ekspedisi_status', '$ekspedisi_cabang')";
	mysqli_query($conn, $query);

	return mysqli_affected_rows($conn);
}

function editEkspedisi($data) {
	global $conn;
	$id = $data["ekspedisi_id"];

	// ambil data dari tiap elemen dalam form
	$ekspedisi_nama = htmlspecialchars($data['ekspedisi_nama']);
	$ekspedisi_status = $data['ekspedisi_status'];

	// query update data
	$query = "UPDATE ekspedisi SET 
				ekspedisi_nama   = '$ekspedisi_nama',
				ekspedisi_status = '$ekspedisi_status'
				WHERE ekspedisi_id = $id
				";
	mysqli_query($conn, $query);
	return mysqli_affected_rows($conn);
}

function hapusEkspedisi($id) {
	global $conn;
	mysqli_query( $conn, "DELETE FROM ekspedisi WHERE ekspedisi_id = $id");

	return mysqli_affected_rows($conn);
}


// ======================================== Barang =============================== //
function tambahBarang($data) {
	global $conn;
	// ambil data dari tiap elemen dalam form
	$barang_kode      			= htmlspecialchars($data["barang_kode"] ?? '');
	$barang_kode_slug			= str_replace(" ", "-", $barang_kode);
	$barang_kode_count  		= htmlspecialchars($data["barang_kode_count"] ?? '');
	$barang_nama      			= htmlspecialchars($data["barang_nama"] ?? '');
	$barang_deskripsi 			= htmlspecialchars($data["barang_deskripsi"] ?? '');

	$barang_harga     			= htmlspecialchars($data["barang_harga"] ?? '');
	$barang_harga_grosir_1     	= htmlspecialchars($data["barang_harga_grosir_1"] ?? '');
	$barang_harga_grosir_2     	= htmlspecialchars($data["barang_harga_grosir_2"] ?? '');

	$barang_harga_s2     		= htmlspecialchars($data["barang_harga_s2"] ?? '');
	$barang_harga_grosir_1_s2   = htmlspecialchars($data["barang_harga_grosir_1_s2"] ?? '');
	$barang_harga_grosir_2_s2   = htmlspecialchars($data["barang_harga_grosir_2_s2"] ?? '');

	$barang_harga_s3     		= htmlspecialchars($data["barang_harga_s3"] ?? '');
	$barang_harga_grosir_1_s3   = htmlspecialchars($data["barang_harga_grosir_1_s3"] ?? '');
	$barang_harga_grosir_2_s3   = htmlspecialchars($data["barang_harga_grosir_2_s3"] ?? '');

	$kategori_id      			= $data["kategori_id"] ?? 0;
	$sub_kategori_id      		= isset($data["sub_kategori_id"]) ? (int)$data["sub_kategori_id"] : null;


	$satuan_id        			= $data["satuan_id"] ?? 0;
	$satuan_id_2        		= (int)$data["satuan_id_2"] ?? 0;
	$satuan_id_3        		= (int)$data["satuan_id_3"] ?? 0;

	$satuan_isi_1 				= 1;
	$satuan_isi_2        		= (int)$data["satuan_isi_2"] ?? 0;
	$satuan_isi_3        		= (int)$data["satuan_isi_3"] ?? 0;


	$barang_tanggal   			= date("d F Y g:i:s a");
	$barang_stock     			= htmlspecialchars($data["barang_stock"] ?? '');
	$barang_option_sn 			= $data["barang_option_sn"] ?? 0;
	$barang_cabang				= $data["barang_cabang"] ?? 0;

	// Cek Email
	$barang_kode_cek = mysqli_num_rows(mysqli_query($conn, "select * from barang where barang_kode = '".$barang_kode."' && barang_cabang = ".$barang_cabang." "));

	if ( $barang_kode_cek > 0 ) {
		echo "
			<script>
				alert('Kode Barang Sudah Ada Coba Kode yang Lain !!!');
			</script>
		";
	} else {
		// query insert data
		$query = "INSERT INTO barang (
			barang_kode, barang_kode_slug, barang_kode_count, barang_nama, 
			barang_harga_beli, barang_harga, barang_harga_grosir_1, barang_harga_grosir_2, 
			barang_harga_s2, barang_harga_grosir_1_s2, barang_harga_grosir_2_s2, 
			barang_harga_s3, barang_harga_grosir_1_s3, barang_harga_grosir_2_s3, 
			barang_stock, barang_tanggal, barang_kategori_id, barang_sub_kategori_id, 
			kategori_id, barang_satuan_id, satuan_id, satuan_id_2, satuan_id_3, 
			satuan_isi_1, satuan_isi_2, satuan_isi_3, barang_deskripsi, 
			barang_option_sn, barang_terjual, barang_cabang
		) VALUES (
			'$barang_kode', '$barang_kode_slug', '$barang_kode_count', '$barang_nama', 
			'', '$barang_harga', '$barang_harga_grosir_1', '$barang_harga_grosir_2', 
			'$barang_harga_s2', '$barang_harga_grosir_1_s2', '$barang_harga_grosir_2_s2', 
			'$barang_harga_s3', '$barang_harga_grosir_1_s3', '$barang_harga_grosir_2_s3', 
			'$barang_stock', '$barang_tanggal', '$kategori_id', $sub_kategori_id, 
			'$kategori_id', '$satuan_id', '$satuan_id', '$satuan_id_2', '$satuan_id_3', 
			'$satuan_isi_1', '$satuan_isi_2', '$satuan_isi_3', '$barang_deskripsi', 
			'$barang_option_sn', 0, '$barang_cabang'
		)";
		mysqli_query($conn, $query);

		if (mysqli_errno($conn)) {
			echo "Error: " . mysqli_error($conn);
			exit;
		}

		return mysqli_affected_rows($conn);
	}
}

function editBarang($data) {
	global $conn;
	$id = $data["barang_id"];

	// ambil data dari tiap elemen dalam form
	$barang_kode      			= htmlspecialchars($data["barang_kode"]);
	$barang_nama      			= htmlspecialchars($data["barang_nama"]);
	$barang_deskripsi 			= htmlspecialchars($data["barang_deskripsi"]);

	$barang_harga_beli 			= htmlspecialchars($data["barang_harga_beli"]);
	$barang_harga     			= htmlspecialchars($data["barang_harga"]);
	$barang_harga_grosir_1     	= htmlspecialchars($data["barang_harga_grosir_1"]);
	$barang_harga_grosir_2     	= htmlspecialchars($data["barang_harga_grosir_2"]);

	$barang_harga_s2     		= htmlspecialchars($data["barang_harga_s2"]);
	$barang_harga_grosir_1_s2   = htmlspecialchars($data["barang_harga_grosir_1_s2"]);
	$barang_harga_grosir_2_s2   = htmlspecialchars($data["barang_harga_grosir_2_s2"]);

	$barang_harga_s3     		= htmlspecialchars($data["barang_harga_s3"]);
	$barang_harga_grosir_1_s3   = htmlspecialchars($data["barang_harga_grosir_1_s3"]);
	$barang_harga_grosir_2_s3   = htmlspecialchars($data["barang_harga_grosir_2_s3"]);

	$kategori_id      			= $data["kategori_id"];
	$sub_kategori_id      		= isset($data["sub_kategori_id"]) ? (int)$data["sub_kategori_id"] : 0;

	$satuan_id        			= $data["satuan_id"];
	$satuan_id_2        		= $data["satuan_id_2"];
	$satuan_id_3        		= $data["satuan_id_3"];

	$satuan_isi_2        		= $data["satuan_isi_2"];
	$satuan_isi_3        		= $data["satuan_isi_3"];

	$level 	= htmlspecialchars($data['level']);
	if ( $level === "super admin" ) {
		$barang_stock     			= htmlspecialchars($data["barang_stock"]);
	} elseif ( $level === "admin" ) {
		$barang_stock 				= htmlspecialchars(base64_decode($data['barang_stock']));
	}
	
	$barang_option_sn 			= $data["barang_option_sn"];

	// query update data
	$query = "UPDATE barang SET 
				barang_kode       		= '$barang_kode',
				barang_nama       		= '$barang_nama',
				barang_harga_beli       = '$barang_harga_beli',
				barang_harga      		= '$barang_harga',
				barang_harga_grosir_1   = '$barang_harga_grosir_1',
				barang_harga_grosir_2   = '$barang_harga_grosir_2',
				barang_harga_s2      	= '$barang_harga_s2',
				barang_harga_grosir_1_s2= '$barang_harga_grosir_1_s2',
				barang_harga_grosir_2_s2= '$barang_harga_grosir_2_s2',
				barang_harga_s3      	= '$barang_harga_s3',
				barang_harga_grosir_1_s3= '$barang_harga_grosir_1_s3',
				barang_harga_grosir_2_s3= '$barang_harga_grosir_2_s3',
				barang_stock      		= '$barang_stock',
				barang_kategori_id      = '$kategori_id',
				barang_sub_kategori_id	= '$sub_kategori_id',
				kategori_id       		= '$kategori_id',
				satuan_id         		= '$satuan_id',
				satuan_id_2         	= '$satuan_id_2',
				satuan_id_3         	= '$satuan_id_3',
				satuan_isi_2         	= '$satuan_isi_2',
				satuan_isi_3         	= '$satuan_isi_3',
				barang_deskripsi  		= '$barang_deskripsi',
				barang_option_sn  		= '$barang_option_sn'
				WHERE barang_id   		= $id
				";
	mysqli_query($conn, $query);

	if (mysqli_errno($conn)) {
		echo "Error: " . mysqli_error($conn);
		exit;
	}
	
	return mysqli_affected_rows($conn);
}

function hapusBarang($id) {
	global $conn;

	// Ambil ID produk
	$data_id = $id;

	// Mencari No. Invoice
	$sn = mysqli_query( $conn, "select barang_option_sn from barang where barang_id = '".$data_id."'");
    $sn = mysqli_fetch_array($sn); 
    $sn = $sn["barang_option_sn"];

    $barang = mysqli_query($conn, "select barang_kode_slug, barang_cabang from barang where barang_id = ".$data_id." ");
    $barang = mysqli_fetch_array($barang);
    $barang_kode_slug 	= $barang['barang_kode_slug'];
    $barang_cabang 		= $barang['barang_cabang'];

    $countBarangSn = mysqli_query($conn, "select * from barang_sn where barang_kode_slug = '".$barang_kode_slug."' && barang_sn_status > 0 && barang_sn_cabang = ".$barang_cabang." ");
    $countBarangSn = mysqli_num_rows($countBarangSn);

    if ( $sn < 1 ) {
    	mysqli_query( $conn, "DELETE FROM barang WHERE barang_id = $id");
    	return mysqli_affected_rows($conn);
    } else {
    	mysqli_query( $conn, "DELETE FROM barang WHERE barang_id = $id");
    	
    	if ( $countBarangSn > 0 ) {
    		mysqli_query( $conn, "DELETE FROM barang_sn WHERE barang_kode_slug = '".$barang_kode_slug."' && barang_sn_status > 0 && barang_sn_cabang = $barang_cabang ");
    	}
    	return mysqli_affected_rows($conn);
    }

	
}

// ===================================== Barang SN ========================================= //
function tambahBarangSn($data) {
	global $conn;
	// ambil data dari tiap elemen dalam form
	$barang_sn_desc 			= $data['barang_sn_desc'];
	$barang_kode_slug 			= $data['barang_kode_slug'];
	$barang_sn_status 			= $data['barang_sn_status'];
	$barang_sn_cabang 			= $data['barang_sn_cabang'];

	$barang_sn_barang_id 		= $data['barang_sn_barang_id'];

	$jumlah = count($barang_kode_slug);

	
	if (count($barang_sn_desc) !== count(array_unique($barang_sn_desc))) {
	    echo '
	    	<script>
	    	    alert("Nomor SN tidak boleh ada yang sama !!");
	    		document.location.href ="";
	    	</script>
	    ';
	} else {
	    // query insert data jika tidak ada duplikat
	    for ($x = 0; $x < $jumlah; $x++) {
	    	$countSnDb = mysqli_query($conn, "SELECT * FROM barang_sn WHERE barang_sn_desc = '$barang_sn_desc[$x]' && barang_sn_cabang = $barang_sn_cabang[$x] ");
	    	$countSnDb = mysqli_num_rows($countSnDb);

	    	if ( $countSnDb > 0 ) {
	    		echo '
			    	<script>
			    	    alert("Nomor SN Sudah pernah diinputkan ke Database coba cek kembali semua produk SN !!");
			    		document.location.href ="";
			    	</script>
			    '; exit();
	    	} else {
	    		$query = "INSERT INTO barang_sn VALUES ('', '$barang_sn_desc[$x]', '$barang_sn_barang_id', '$barang_kode_slug[$x]', '$barang_sn_status[$x]', '$barang_sn_cabang[$x]')";
	        	mysqli_query($conn, $query);
	    	}
	    }

	    return mysqli_affected_rows($conn);
	}
	

	/*
	// Cek apakah ada duplikat di $barang_sn_desc
	if (count($barang_sn_desc) !== count(array_unique($barang_sn_desc))) {
	    echo '
	        <script>
	            alert("Nomor SN tidak boleh ada yang sama di form !!");
	            document.location.href = "";
	        </script>
	    ';
	} else {
	    $duplicateFound = false; // Flag untuk menandai jika ada SN yang sudah ada di database

	    foreach ($barang_sn_desc as $sn) {

		    // Cek duplikasi di database
		    $query_check = "SELECT COUNT(*) AS count FROM barang_sn WHERE barang_sn_desc = '$sn' AND barang_sn_cabang = '$barang_sn_cabang'";
		    $result_check = mysqli_query($conn, $query_check);

		    // Periksa apakah query berhasil dijalankan
		    if (!$result_check) {
		        echo '
		            <script>
		                alert("Error: Query gagal dijalankan.");
		                document.location.href = "";
		            </script>
		        ';
		        exit;
		    }

		    $row = mysqli_fetch_assoc($result_check);

		    // Jika ditemukan duplikat
		    if ($row['count'] > 0) {
		        echo '
		            <script>
		                alert("Nomor SN '.$sn.' sudah ada di database !!");
		                document.location.href = "";
		            </script>
		        ';
		        exit(); // Keluar dari loop jika ditemukan duplikat di database
		    }
		}



	    if (!$duplicateFound) {
	        // Jika tidak ada duplikat di database, lanjutkan insert data
	        for ($x = 0; $x < $jumlah; $x++) {
	            $query = "INSERT INTO barang_sn VALUES ('', '$barang_sn_desc[$x]', '$barang_sn_barang_id', '$barang_kode_slug[$x]', '$barang_sn_status[$x]', '$barang_sn_cabang[$x]')";
	            mysqli_query($conn, $query);
	        }

	        return mysqli_affected_rows($conn);
	    }
	}
	*/

}

function editBarangSn($data) {
	global $conn;
	$id = $data["barang_sn_id"];

	// ambil data dari tiap elemen dalam form
	$barang_sn_desc 	= htmlspecialchars($data['barang_sn_desc']);
	$barang_sn_status 	= $data['barang_sn_status'];

	// query update data
	$query = "UPDATE barang_sn SET 
				barang_sn_desc    = '$barang_sn_desc',
				barang_sn_status  = '$barang_sn_status'
				WHERE barang_sn_id = $id
				";
	mysqli_query($conn, $query);
	return mysqli_affected_rows($conn);
}

function hapusBarangSn($id) {
	global $conn;
	mysqli_query( $conn, "DELETE FROM barang_sn WHERE barang_sn_id = $id");

	return mysqli_affected_rows($conn);
}

// ===================================== Keranjang ========================================= //
function tambahKeranjang($keranjang_cabang, 
	$barang_id, 
	$barang_kode_slug, 
	$keranjang_nama, 
	$keranjang_harga_beli, 
	$keranjang_harga, 
	$keranjang_satuan, 
	$keranjang_id_kasir, 
	$keranjang_qty, 
	$keranjang_konversi_isi, 
	$keranjang_barang_sn_id, 
	$keranjang_barang_option_sn, 
	$keranjang_sn, 
	$keranjang_id_cek, 
	$customer) {
	global $conn;


	// Cek STOCK
	$barang_id_cek = mysqli_num_rows(mysqli_query($conn, "select * from keranjang where keranjang_id_cek = '$keranjang_id_cek' "));
		
	if ( $barang_id_cek > 0 && $keranjang_barang_option_sn < 1 ) {
		$keranjangParent = mysqli_query( $conn, "select keranjang_qty, keranjang_qty_view, keranjang_konversi_isi from keranjang where keranjang_id_cek = '".$keranjang_id_cek."'");
        $kp = mysqli_fetch_array($keranjangParent); 
        // $kp += $keranjang_qty;
        $keranjang_qty_view_keranjang 		= $kp['keranjang_qty_view'];
        $keranjang_qty_keranjang 			= $kp['keranjang_qty'];
        $keranjang_konversi_isi_keranjang 	= $kp['keranjang_konversi_isi'];

        $kqvk = $keranjang_qty_view_keranjang + $keranjang_qty;
        $kqkk = $keranjang_qty_keranjang + $keranjang_konversi_isi_keranjang;

        $query = "UPDATE keranjang SET 
					keranjang_qty   	= '$kqkk',
					keranjang_qty_view  = '$kqvk'
					WHERE keranjang_id_cek = $keranjang_id_cek
					";
		mysqli_query($conn, $query);
		return mysqli_affected_rows($conn);

	} else {
		// query insert data
		$query = "INSERT INTO keranjang VALUES ('', 
		'$keranjang_nama', 
		'$keranjang_harga_beli', 
		'$keranjang_harga',
		'$keranjang_harga', 
		'0',
		'$keranjang_satuan', 
		'$barang_id', 
		'$barang_kode_slug', 
		'$keranjang_qty', 
		'$keranjang_qty', 
		'$keranjang_konversi_isi', 
		'$keranjang_barang_sn_id', 
		'$keranjang_barang_option_sn', 
		'$keranjang_sn', 
		'$keranjang_id_kasir', 
		'$keranjang_id_cek', 
		'$customer', 
		'$keranjang_cabang')";
		
		mysqli_query($conn, $query);

		return mysqli_affected_rows($conn);
	}
}

function tambahKeranjangDraft($keranjang_cabang, 
	$barang_id, 
	$barang_kode_slug, 
	$keranjang_nama, 
	$keranjang_harga_beli, 
	$keranjang_harga, 
	$keranjang_satuan, 
	$keranjang_id_kasir, 
	$keranjang_qty, 
	$keranjang_konversi_isi, 
	$keranjang_barang_sn_id, 
	$keranjang_barang_option_sn, 
	$keranjang_sn, 
	$keranjang_id_cek, 
	$invoice,
	$customer) {
	global $conn;


	// Cek STOCK
	$barang_id_cek = mysqli_num_rows(mysqli_query($conn, "select * from keranjang_draft where barang_id = ".$barang_id." && keranjang_invoice = ".$invoice." && keranjang_cabang = ".$keranjang_cabang." "));

	if ( $barang_id_cek > 0 && $keranjang_barang_option_sn < 1 ) {
		$keranjangParent = mysqli_query( $conn, "select keranjang_qty, keranjang_qty_view, keranjang_konversi_isi from keranjang_draft where keranjang_id_cek = '".$keranjang_id_cek."'");
        $kp = mysqli_fetch_array($keranjangParent); 
        // $kp += $keranjang_qty;
        $keranjang_qty_view_keranjang 		= $kp['keranjang_qty_view'];
        $keranjang_qty_keranjang 			= $kp['keranjang_qty'];
        $keranjang_konversi_isi_keranjang 	= $kp['keranjang_konversi_isi'];

        $kqvk = $keranjang_qty_view_keranjang + $keranjang_qty;
        $kqkk = $keranjang_qty_keranjang + $keranjang_konversi_isi_keranjang;

        $query = "UPDATE keranjang_draft SET 
					keranjang_qty   	= '$kqkk',
					keranjang_qty_view  = '$kqvk'
					WHERE keranjang_id_cek = $keranjang_id_cek
					";

		mysqli_query($conn, $query);
		return mysqli_affected_rows($conn);

	} else {
		// query insert data
		$query = "INSERT INTO keranjang_draft VALUES ('', 
		'$keranjang_nama', 
		'$keranjang_harga_beli', 
		'$keranjang_harga',
		'$keranjang_harga', 
		'0', 
		'$keranjang_satuan', 
		'$barang_id', 
		'$barang_kode_slug', 
		'$keranjang_qty', 
		'$keranjang_qty', 
		'$keranjang_konversi_isi', 
		'$keranjang_barang_sn_id', 
		'$keranjang_barang_option_sn', 
		'$keranjang_sn', 
		'$keranjang_id_kasir', 
		'$keranjang_id_cek', 
		'$customer', 
		'1',
		'$invoice',
		'$keranjang_cabang')";
		mysqli_query($conn, $query);

		return mysqli_affected_rows($conn);
	}
}

function tambahKeranjangBarcode($data) {
	global $conn;

	$barang_kode 		= htmlspecialchars($data['inputbarcode']);
	$keranjang_id_kasir = $data['keranjang_id_kasir'];
	$tipe_harga 		= $data['tipe_harga'];
	$keranjang_cabang   = $data['keranjang_cabang'];

	// Ambil Data Barang berdasarkan Kode Barang 
	$barang 	= mysqli_query( $conn, "select barang_id, 
		barang_nama, 
		barang_harga_beli, 
		barang_harga, 
		barang_harga_grosir_1, 
		barang_harga_grosir_2, 
		barang_stock, 
		barang_kode_slug, 
		satuan_id,
		satuan_isi_1,
		barang_option_sn from barang where barang_kode = '".$barang_kode."' && barang_cabang = ".$keranjang_cabang." ");
    $br 		= mysqli_fetch_array($barang);

    $barang_id  				= $br["barang_id"];
    $keranjang_nama  			= $br["barang_nama"];
    $keranjang_harga_beli  		= $br["barang_harga_beli"];
    $keranjang_satuan           = $br["satuan_id"];
    $keranjang_konversi_isi     = $br["satuan_isi_1"];

    if ( $tipe_harga == 1 ) {
      	$keranjang_harga  = $br["barang_harga_grosir_1"];
  	} elseif ( $tipe_harga == 2 ) {
      	$keranjang_harga  = $br["barang_harga_grosir_2"];
  	} else {
      	$keranjang_harga  = $br["barang_harga"];
  	}
    
    $barang_stock 				= $br["barang_stock"];
    $barang_kode_slug 			= $br["barang_kode_slug"];
    $keranjang_barang_option_sn = $br["barang_option_sn"];
    $keranjang_qty      		= 1;
    $keranjang_konversi_isi     = $br['satuan_isi_1'];
	$keranjang_barang_sn_id     = 0;
	$keranjang_sn       		= 0;
	$keranjang_tipe_customer    = $tipe_harga;
	$keranjang_id_cek   		= $barang_id.$keranjang_id_kasir.$keranjang_cabang;


	// Kondisi jika scan Barcode Tidak sesuai
	if ( $barang_id != null ) {

		// Cek apakah data barang sudah sesuai dengan jumlah stok saat Insert Ke Keranjang dan jika melebihi stok maka akan dikembalikan
		$idBarang = mysqli_query($conn, "select keranjang_qty, keranjang_konversi_isi, keranjang_tipe_customer from keranjang where barang_id = ".$barang_id." ");
    	$idBarang = mysqli_fetch_array($idBarang);
   		$keranjang_qty_stock = $idBarang['keranjang_qty'] * $idBarang['keranjang_konversi_isi'];

   		if ( $keranjang_qty_stock >= $barang_stock ) {
	   		echo '
				<script>
					alert("Produk TIDAK BISA DITAMBAHKAN Karena Jumlah QTY Melebihi Stock yang Ada di Semua Transaksi Kasir & Mohon di Cek Kembali !!!");
					document.location.href = "";
				</script>
			';
	   	} else {
	   		// Cek STOCK
			$barang_id_cek = mysqli_num_rows(mysqli_query($conn, "select * from keranjang where keranjang_id_cek = ".$keranjang_id_cek." "));
				
			if ( $barang_id_cek > 0 && $keranjang_barang_option_sn < 1 ) {
				$keranjangParent = mysqli_query( $conn, "select keranjang_qty, keranjang_qty_view, keranjang_konversi_isi from keranjang where keranjang_id_cek = '".$keranjang_id_cek."'");
		        $kp = mysqli_fetch_array($keranjangParent); 
		        // $kp += $keranjang_qty;
		        $keranjang_qty_view_keranjang 		= $kp['keranjang_qty_view'];
		        $keranjang_qty_keranjang 			= $kp['keranjang_qty'];
		        $keranjang_konversi_isi_keranjang 	= $kp['keranjang_konversi_isi'];

		        $kqvk = $keranjang_qty_view_keranjang + $keranjang_qty;
		        $kqkk = $keranjang_qty_keranjang + $keranjang_konversi_isi_keranjang;

		        $query = "UPDATE keranjang SET 
							keranjang_qty   	= '$kqkk',
							keranjang_qty_view  = '$kqvk'
							WHERE keranjang_id_cek = $keranjang_id_cek
							";
				mysqli_query($conn, $query);
				return mysqli_affected_rows($conn);

			} else {
				// query insert data
				$query = "INSERT INTO keranjang VALUES ('', 
				'$keranjang_nama', 
				'$keranjang_harga_beli', 
				'$keranjang_harga',
				'$keranjang_harga', 
				'0',
				'$keranjang_satuan',
				'$barang_id', 
				'$barang_kode_slug', 
				'$keranjang_qty', 
				'$keranjang_qty',
				'$keranjang_konversi_isi',
				'$keranjang_barang_sn_id', 
				'$keranjang_barang_option_sn', 
				'$keranjang_sn', 
				'$keranjang_id_kasir', 
				'$keranjang_id_cek', 
				'$keranjang_tipe_customer',
				'$keranjang_cabang')";
				mysqli_query($conn, $query);

				return mysqli_affected_rows($conn);
			}
	   	}
	} else {
		// Menghitung Keranjang No. SN
		$countSnKeranjang = mysqli_query($conn, "SELECT * FROM keranjang WHERE keranjang_sn = '".$barang_kode."' && keranjang_cabang = $keranjang_cabang");
		$countSnKeranjang = mysqli_num_rows($countSnKeranjang);

		if ( $countSnKeranjang > 0 ) {
			echo '
				<script>
					alert("Data No SN sudah ada di keranjang Penjualan Coba Cek Kembali !!");
					document.location.href = "";
				</script>
			'; exit();
		} else {
			// Menghitung tabel barang SN
			$countSn = mysqli_query($conn, "SELECT * FROM barang_sn WHERE barang_sn_desc = '".$barang_kode."' && barang_sn_cabang = $keranjang_cabang && barang_sn_status > 0 ");
			$countSn = mysqli_num_rows($countSn);

			if ( $countSn < 1 ) {
				echo '
					<script>
						alert("Data Barcode Tidak Tersedia Coba Cek Kembali di Kode Produk dan No. SN !!");
						document.location.href = "";
					</script>
				'; exit();
			} else {
					$dataSnInput = mysqli_query($conn, "SELECT barang_sn_id, barang_kode_slug FROM barang_sn WHERE barang_sn_desc = '".$barang_kode."' && barang_sn_cabang = $keranjang_cabang ");
					$dataSnInput = mysqli_fetch_array($dataSnInput);

					$barang_sn_id 			= $dataSnInput['barang_sn_id'];
					$barang_kode_slug 		= $dataSnInput['barang_kode_slug'];

					// Ambil Data Barang berdasarkan Kode Barang 
					$barang 	= mysqli_query( $conn, "select barang_id, 
						barang_nama, 
						barang_harga_beli, 
						barang_harga, 
						barang_harga_grosir_1, 
						barang_harga_grosir_2, 
						barang_stock, 
						barang_kode_slug, 
						satuan_id,
						satuan_isi_1,
						barang_option_sn from barang where barang_kode = '".$barang_kode_slug."' && barang_cabang = ".$keranjang_cabang." ");
				    $br 		= mysqli_fetch_array($barang);

				    $barang_id  				= $br["barang_id"];
				    $keranjang_nama  			= $br["barang_nama"];
				    $keranjang_harga_beli  		= $br["barang_harga_beli"];
				    $keranjang_satuan           = $br["satuan_id"];
				    $keranjang_konversi_isi     = $br["satuan_isi_1"];

				    if ( $tipe_harga == 1 ) {
				      	$keranjang_harga  = $br["barang_harga_grosir_1"];
				  	} elseif ( $tipe_harga == 2 ) {
				      	$keranjang_harga  = $br["barang_harga_grosir_2"];
				  	} else {
				      	$keranjang_harga  = $br["barang_harga"];
				  	}
				    
				    $barang_stock 				= $br["barang_stock"];
				    $barang_kode_slug 			= $br["barang_kode_slug"];
				    $keranjang_barang_option_sn = $br["barang_option_sn"];
				    $keranjang_qty      		= 1;
				    $keranjang_konversi_isi     = $br['satuan_isi_1'];
					$keranjang_sn       		= 0;
					$keranjang_tipe_customer    = $tipe_harga;
					$keranjang_id_cek   		= $barang_id.$keranjang_id_kasir.$keranjang_cabang;

					// query insert data
					$query = "INSERT INTO keranjang VALUES ('', 
					'$keranjang_nama', 
					'$keranjang_harga_beli', 
					'$keranjang_harga',
					'$keranjang_harga', 
					'0',
					'$keranjang_satuan',
					'$barang_id', 
					'$barang_kode_slug', 
					'$keranjang_qty', 
					'$keranjang_qty',
					'$keranjang_konversi_isi',
					'$barang_sn_id', 
					'$keranjang_barang_option_sn', 
					'$barang_kode', 
					'$keranjang_id_kasir', 
					'$keranjang_id_cek', 
					'$keranjang_tipe_customer',
					'$keranjang_cabang')";
					mysqli_query($conn, $query);

					$query2 = "UPDATE barang_sn SET 
						barang_sn_status     = 0
						WHERE barang_sn_id = $barang_sn_id ";
					mysqli_query($conn, $query2);

					return mysqli_affected_rows($conn);
			}
		}
	}
}

function tambahKeranjangBarcodeDraft($data) {
	global $conn;

	$barang_kode 		= htmlspecialchars($data['inputbarcodeDraft']);
	$keranjang_id_kasir = $data['keranjang_id_kasir'];
	$tipe_harga 		= $data['tipe_harga'];
	$keranjang_invoice  = $data['keranjang_invoice'];
	$keranjang_cabang   = $data['keranjang_cabang'];

	// Ambil Data Barang berdasarkan Kode Barang 
	$barang 	= mysqli_query( $conn, "select barang_id, 
		barang_nama, 
		barang_harga_beli, 
		barang_harga, 
		barang_harga_grosir_1, 
		barang_harga_grosir_2, 
		barang_stock, 
		barang_kode_slug, 
		satuan_id,
		satuan_isi_1,
		barang_option_sn from barang where barang_kode = '".$barang_kode."' && barang_cabang = ".$keranjang_cabang." ");
    $br 		= mysqli_fetch_array($barang);

    $barang_id  				= $br["barang_id"];
    $keranjang_nama  			= $br["barang_nama"];
    $keranjang_harga_beli  		= $br["barang_harga_beli"];
    $keranjang_satuan           = $br["satuan_id"];
    $keranjang_konversi_isi     = $br["satuan_isi_1"];

    if ( $tipe_harga == 1 ) {
      	$keranjang_harga  = $br["barang_harga_grosir_1"];
  	} elseif ( $tipe_harga == 2 ) {
      	$keranjang_harga  = $br["barang_harga_grosir_2"];
  	} else {
      	$keranjang_harga  = $br["barang_harga"];
  	}
    
    $barang_stock 				= $br["barang_stock"];
    $barang_kode_slug 			= $br["barang_kode_slug"];
    $keranjang_barang_option_sn = $br["barang_option_sn"];
    $keranjang_qty      		= 1;
    $keranjang_konversi_isi     = $br['satuan_isi_1'];
	$keranjang_barang_sn_id     = 0;
	$keranjang_sn       		= 0;
	$keranjang_tipe_customer    = $tipe_harga;
	$keranjang_id_cek   		= $barang_id.$keranjang_id_kasir.$keranjang_cabang;


	// Kondisi jika scan Barcode Tidak sesuai
	if ( $barang_id != null ) {

		// Cek apakah data barang sudah sesuai dengan jumlah stok saat Insert Ke Keranjang dan jika melebihi stok maka akan dikembalikan
		$idBarang = mysqli_query($conn, "select keranjang_qty, keranjang_konversi_isi, keranjang_tipe_customer from keranjang_draft where barang_id = ".$barang_id." ");
    	$idBarang = mysqli_fetch_array($idBarang);
   		$keranjang_qty_stock = $idBarang['keranjang_qty'] + $idBarang['keranjang_konversi_isi'];

   		if ( $keranjang_qty_stock >= $barang_stock ) {
	   		echo '
				<script>
					alert("Produk TIDAK BISA DITAMBAHKAN Karena Jumlah QTY Melebihi Stock yang Ada di Semua Transaksi Kasir & Mohon di Cek Kembali !!!");
					document.location.href = "";
				</script>
			';
	   	} else {
	   		// Cek STOCK
			$barang_id_cek = mysqli_num_rows(mysqli_query($conn, "select * from keranjang_draft where barang_id = ".$barang_id." && keranjang_invoice = ".$keranjang_invoice." && keranjang_cabang = ".$keranjang_cabang." "));
				
			if ( $barang_id_cek > 0 && $keranjang_barang_option_sn < 1 ) {
				$keranjangParent = mysqli_query( $conn, "select keranjang_qty, keranjang_qty_view, keranjang_konversi_isi from keranjang_draft where keranjang_id_cek = '".$keranjang_id_cek."'");
		        $kp = mysqli_fetch_array($keranjangParent); 
		        // $kp += $keranjang_qty;
		        $keranjang_qty_view_keranjang 		= $kp['keranjang_qty_view'];
		        $keranjang_qty_keranjang 			= $kp['keranjang_qty'];
		        $keranjang_konversi_isi_keranjang 	= $kp['keranjang_konversi_isi'];

		        $kqvk = $keranjang_qty_view_keranjang + $keranjang_qty;
		        $kqkk = $keranjang_qty_keranjang + $keranjang_konversi_isi_keranjang;

		        $query = "UPDATE keranjang_draft SET 
							keranjang_qty   	= '$kqkk',
							keranjang_qty_view  = '$kqvk'
							WHERE keranjang_id_cek = $keranjang_id_cek
							";
				mysqli_query($conn, $query);
				return mysqli_affected_rows($conn);

			} else {
				// query insert data
				$query = "INSERT INTO keranjang_draft VALUES ('', 
				'$keranjang_nama', 
				'$keranjang_harga_beli', 
				'$keranjang_harga', 
				'$keranjang_harga', 
				'0',
				'$keranjang_satuan',
				'$barang_id', 
				'$barang_kode_slug', 
				'$keranjang_qty', 
				'$keranjang_qty',
				'$keranjang_konversi_isi',
				'$keranjang_barang_sn_id', 
				'$keranjang_barang_option_sn', 
				'$keranjang_sn', 
				'$keranjang_id_kasir', 
				'$keranjang_id_cek', 
				'$keranjang_tipe_customer',
				'1',
				'$keranjang_invoice',
				'$keranjang_cabang')";
				mysqli_query($conn, $query);

				return mysqli_affected_rows($conn);
			}
	   	}
	} else {
		// Menghitung Keranjang No. SN
		$countSnKeranjang = mysqli_query($conn, "SELECT * FROM keranjang_draft WHERE keranjang_sn = '".$barang_kode."' && keranjang_cabang = $keranjang_cabang");
		$countSnKeranjang = mysqli_num_rows($countSnKeranjang);

		// var_dump($countSnKeranjang); die();
		if ( $countSnKeranjang > 0 ) {
			echo '
				<script>
					alert("Data No SN sudah ada di keranjang Penjualan Coba Cek Kembali !!");
					document.location.href = "";
				</script>
			'; exit();
		} else {
			// Menghitung tabel barang SN
			$countSn = mysqli_query($conn, "SELECT * FROM barang_sn WHERE barang_sn_desc = '".$barang_kode."' && barang_sn_cabang = $keranjang_cabang && barang_sn_status > 0 ");
			$countSn = mysqli_num_rows($countSn);

			if ( $countSn < 1 ) {
				echo '
					<script>
						alert("Data Barcode Tidak Tersedia Coba Cek Kembali di Kode Produk dan No. SN !!");
						document.location.href = "";
					</script>
				'; exit();
			} else {
					$dataSnInput = mysqli_query($conn, "SELECT barang_sn_id, barang_kode_slug FROM barang_sn WHERE barang_sn_desc = '".$barang_kode."' && barang_sn_cabang = $keranjang_cabang ");
					$dataSnInput = mysqli_fetch_array($dataSnInput);

					$barang_sn_id 			= $dataSnInput['barang_sn_id'];
					$barang_kode_slug 		= $dataSnInput['barang_kode_slug'];

					// Ambil Data Barang berdasarkan Kode Barang 
					$barang 	= mysqli_query( $conn, "select barang_id, 
						barang_nama, 
						barang_harga_beli, 
						barang_harga, 
						barang_harga_grosir_1, 
						barang_harga_grosir_2, 
						barang_stock, 
						barang_kode_slug, 
						satuan_id,
						satuan_isi_1,
						barang_option_sn from barang where barang_kode = '".$barang_kode_slug."' && barang_cabang = ".$keranjang_cabang." ");
				    $br 		= mysqli_fetch_array($barang);

				    $barang_id  				= $br["barang_id"];
				    $keranjang_nama  			= $br["barang_nama"];
				    $keranjang_harga_beli  		= $br["barang_harga_beli"];
				    $keranjang_satuan           = $br["satuan_id"];
				    $keranjang_konversi_isi     = $br["satuan_isi_1"];

				    if ( $tipe_harga == 1 ) {
				      	$keranjang_harga  = $br["barang_harga_grosir_1"];
				  	} elseif ( $tipe_harga == 2 ) {
				      	$keranjang_harga  = $br["barang_harga_grosir_2"];
				  	} else {
				      	$keranjang_harga  = $br["barang_harga"];
				  	}
				    
				    $barang_stock 				= $br["barang_stock"];
				    $barang_kode_slug 			= $br["barang_kode_slug"];
				    $keranjang_barang_option_sn = $br["barang_option_sn"];
				    $keranjang_qty      		= 1;
				    $keranjang_konversi_isi     = $br['satuan_isi_1'];
					$keranjang_sn       		= 0;
					$keranjang_tipe_customer    = $tipe_harga;
					$keranjang_id_cek   		= $barang_id.$keranjang_id_kasir.$keranjang_cabang;

					// query insert data
					$query = "INSERT INTO keranjang_draft VALUES ('', 
					'$keranjang_nama', 
					'$keranjang_harga_beli', 
					'$keranjang_harga', 
					'$keranjang_harga', 
					'0',
					'$keranjang_satuan',
					'$barang_id', 
					'$barang_kode_slug', 
					'$keranjang_qty', 
					'$keranjang_qty',
					'$keranjang_konversi_isi',
					'$barang_sn_id', 
					'$keranjang_barang_option_sn', 
					'$barang_kode', 
					'$keranjang_id_kasir', 
					'$keranjang_id_cek', 
					'$keranjang_tipe_customer',
					'1',
					'$keranjang_invoice',
					'$keranjang_cabang')";
					mysqli_query($conn, $query);

					$query2 = "UPDATE barang_sn SET 
						barang_sn_status     = 0
						WHERE barang_sn_id = $barang_sn_id ";
					mysqli_query($conn, $query2);

					return mysqli_affected_rows($conn);
			}
		}
	}
}

function updateSn($data){
	global $conn;
	$id = $data["keranjang_id"];


	// ambil data dari tiap elemen dalam form
	$barang_sn_id  = $data["barang_sn_id"];


	$barang_sn_desc = mysqli_query( $conn, "select barang_sn_desc from barang_sn where barang_sn_id = '".$barang_sn_id."'");
    $barang_sn_desc = mysqli_fetch_array($barang_sn_desc); 
    $barang_sn_desc = $barang_sn_desc['barang_sn_desc'];

	// query update data
	$query = "UPDATE keranjang SET 
						keranjang_barang_sn_id  = '$barang_sn_id',
						keranjang_sn            = '$barang_sn_desc'
						WHERE keranjang_id      = $id
				";

	$query2 = "UPDATE barang_sn SET 
						barang_sn_status     = 0
						WHERE barang_sn_id = $barang_sn_id
				";

	mysqli_query($conn, $query);
	mysqli_query($conn, $query2);

	return mysqli_affected_rows($conn);

}

function updateSnDrfat($data){
	global $conn;
	$id = $data["keranjang_draf_id"];


	// ambil data dari tiap elemen dalam form
	$barang_sn_id  = $data["barang_sn_id"];


	$barang_sn_desc = mysqli_query( $conn, "select barang_sn_desc from barang_sn where barang_sn_id = '".$barang_sn_id."'");
    $barang_sn_desc = mysqli_fetch_array($barang_sn_desc); 
    $barang_sn_desc = $barang_sn_desc['barang_sn_desc'];

	// query update data
	$query = "UPDATE keranjang_draft SET 
						keranjang_barang_sn_id  = '$barang_sn_id',
						keranjang_sn            = '$barang_sn_desc'
						WHERE keranjang_draf_id      = $id
				";

	$query2 = "UPDATE barang_sn SET 
						barang_sn_status     = 0
						WHERE barang_sn_id = $barang_sn_id
				";

	mysqli_query($conn, $query);
	mysqli_query($conn, $query2);

	return mysqli_affected_rows($conn);

}

function updateQTYHarga($data) {
	global $conn;
	$id = $data["keranjang_id"];

	// ambil data dari tiap elemen dalam form
	$keranjang_qty_view_parent 	= htmlspecialchars($data['keranjang_qty_view']);
	$keranjang_barang_option_sn = $data['keranjang_barang_option_sn'];

	$keranjang_satuan_end_isi   = $data['keranjang_satuan_end_isi'];
	$keranjang_satuan_old       = $data['keranjang_satuan_old'];
	$pecah_data 				= explode("-",$keranjang_satuan_end_isi);

	if ( $keranjang_barang_option_sn < 1 ) {
		$keranjang_satuan   		= $pecah_data[0];
		$keranjang_konversi_isi 	= $pecah_data[1];
		$checkboxHarga              = $data['checkbox-harga'];
		if ( $checkboxHarga > 0 ) {
			if ( $keranjang_satuan_old == $keranjang_satuan ) {
				$keranjang_harga 		= htmlspecialchars($data["keranjang_harga"]);
				$keranjang_qty_view     = $keranjang_qty_view_parent;

			} else {
				$keranjang_harga 	    = $pecah_data[2];
				$keranjang_qty_view     = 1;
			}
		} else {
			if ( $keranjang_satuan_old == $keranjang_satuan ) {
				$keranjang_qty_view     = $keranjang_qty_view_parent;

			} else {
				$keranjang_qty_view     = 1;
			}
			$keranjang_harga 	    = $pecah_data[2];
		}

	} else {
		$keranjang_satuan   		= $data['keranjang_satuan'];
		$keranjang_konversi_isi 	= $data['keranjang_konversi_isi'];
		$checkboxHarga              = $data['checkbox-harga'];
		$keranjang_harga 			= htmlspecialchars($data["keranjang_harga"]);
		$keranjang_qty_view         = $keranjang_qty_view_parent;
	}

	$stock_brg 			        = $data['stock_brg'];
	$keranjang_qty              = $keranjang_qty_view * $keranjang_konversi_isi;

	if ( $keranjang_qty > $stock_brg ) {
		echo"
			<script>
				alert('QTY Melebihi Stock Barang.. Coba Cek Lagi !!!');
				document.location.href = '';
			</script>
		";
	} else {
		// query update data
		$query = "UPDATE keranjang SET 
					keranjang_harga  		= '$keranjang_harga',
					keranjang_harga_edit  	= '$checkboxHarga',
					keranjang_satuan        = '$keranjang_satuan',
					keranjang_qty   		= '$keranjang_qty',
					keranjang_qty_view   	= '$keranjang_qty_view',
					keranjang_konversi_isi  = '$keranjang_konversi_isi'
					WHERE keranjang_id 		= $id
					";
		mysqli_query($conn, $query);
		return mysqli_affected_rows($conn);
	}
}

function updateQTYHargaDraft($data) {
	global $conn;
	$id = $data["keranjang_draf_id"];

	// ambil data dari tiap elemen dalam form
	$keranjang_qty_view_parent 	= htmlspecialchars($data['keranjang_qty_view']);
	$keranjang_barang_option_sn = $data['keranjang_barang_option_sn'];

	$keranjang_satuan_end_isi   = $data['keranjang_satuan_end_isi'];
	$keranjang_satuan_old       = $data['keranjang_satuan_old'];
	$pecah_data 				= explode("-",$keranjang_satuan_end_isi);

	if ( $keranjang_barang_option_sn < 1 ) {
		$keranjang_satuan   		= $pecah_data[0];
		$keranjang_konversi_isi 	= $pecah_data[1];
		$checkboxHarga              = $data['checkbox-harga'];
		if ( $checkboxHarga > 0 ) {
			if ( $keranjang_satuan_old == $keranjang_satuan ) {
				$keranjang_harga 		= htmlspecialchars($data["keranjang_harga"]);
				$keranjang_qty_view     = $keranjang_qty_view_parent;

			} else {
				$keranjang_harga 	    = $pecah_data[2];
				$keranjang_qty_view     = 1;
			}
		} else {
			if ( $keranjang_satuan_old == $keranjang_satuan ) {
				$keranjang_qty_view     = $keranjang_qty_view_parent;

			} else {
				$keranjang_qty_view     = 1;
			}
			$keranjang_harga 	    = $pecah_data[2];
		}

	} else {
		$keranjang_satuan   		= $data['keranjang_satuan'];
		$keranjang_konversi_isi 	= $data['keranjang_konversi_isi'];
		$checkboxHarga              = $data['checkbox-harga'];
		$keranjang_harga 			= htmlspecialchars($data["keranjang_harga"]);
		$keranjang_qty_view         = $keranjang_qty_view_parent;
	}

	$stock_brg 			        = $data['stock_brg'];
	$keranjang_qty              = $keranjang_qty_view * $keranjang_konversi_isi;

	if ( $keranjang_qty > $stock_brg ) {
		echo"
			<script>
				alert('QTY Melebihi Stock Barang.. Coba Cek Lagi !!!');
				document.location.href = '';
			</script>
		";
	} else {
		// query update data
		$query = "UPDATE keranjang_draft SET 
					keranjang_harga  		= '$keranjang_harga',
					keranjang_harga_edit  	= '$checkboxHarga',
					keranjang_satuan        = '$keranjang_satuan',
					keranjang_qty   		= '$keranjang_qty',
					keranjang_qty_view   	= '$keranjang_qty_view',
					keranjang_konversi_isi  = '$keranjang_konversi_isi'
					WHERE keranjang_draf_id 		= $id
					";
		mysqli_query($conn, $query);
		return mysqli_affected_rows($conn);
	}
}


function hapusKeranjang($id) {
	global $conn;


	// Ambil ID produk
	$data_id = $id;

	// Mencari keranjang_barang_sn_id
	$keranjang_barang_sn_id = mysqli_query( $conn, "select keranjang_barang_sn_id from keranjang where keranjang_id = '".$data_id."'");
    $keranjang_barang_sn_id = mysqli_fetch_array($keranjang_barang_sn_id); 
    $keranjang_barang_sn_id = $keranjang_barang_sn_id["keranjang_barang_sn_id"];


    
    if ( $keranjang_barang_sn_id > 0 ) {
    	$query2 = "UPDATE barang_sn SET 
					barang_sn_status    = 1
					WHERE barang_sn_id  = $keranjang_barang_sn_id
					";
		mysqli_query($conn, $query2);
    }
    
	mysqli_query( $conn, "DELETE FROM keranjang WHERE keranjang_id = $id");

	return mysqli_affected_rows($conn);
}

function hapusKeranjangDraft($id) {
	global $conn;
	// Ambil ID produk
	$data_id = $id;

	// Mencari keranjang_barang_sn_id
	$keranjang_barang_sn_id = mysqli_query( $conn, "select keranjang_barang_sn_id from keranjang_draft where keranjang_draf_id = '".$data_id."'");
    $keranjang_barang_sn_id = mysqli_fetch_array($keranjang_barang_sn_id); 
    $keranjang_barang_sn_id = $keranjang_barang_sn_id["keranjang_barang_sn_id"];

    
    if ( $keranjang_barang_sn_id > 0 ) {
    	$query2 = "UPDATE barang_sn SET 
					barang_sn_status    = 1
					WHERE barang_sn_id  = $keranjang_barang_sn_id
					";
		mysqli_query($conn, $query2);
    }
    
	mysqli_query( $conn, "DELETE FROM keranjang_draft WHERE keranjang_draf_id = $id");

	return mysqli_affected_rows($conn);
}

// ================================================= Keranjang Non SN ============================ //
function tambahKeranjangNonFisik($knf_cabang, $knf_barang_id, $knf_barang_nama, $knf_barang_kode, $knf_nama, $knf_harga_beli, $knf_harga_jual, $knf_id_kasir, $knf_qty, $knf_id_cek, $linkBack) {
	global $conn;


	// Cek STOCK
	$barang_id_cek = mysqli_num_rows(mysqli_query($conn, "select * from keranjang_non_fisik where knf_id_cek = '$knf_id_cek' "));
	
		
	if ( $barang_id_cek > 0 ) {
		$keranjangParent = mysqli_query( $conn, "select knf_qty from keranjang_non_fisik where knf_id_cek = '".$knf_id_cek."'");
        $kp = mysqli_fetch_array($keranjangParent); 
        $kp = $kp['knf_qty'];
        $kp += $knf_qty;

        $query = "UPDATE keranjang_non_fisik SET 
					knf_qty   = '$kp'
					WHERE knf_id_cek = $knf_id_cek
					";
		mysqli_query($conn, $query);
		return mysqli_affected_rows($conn);

	} else {
		// Memeriksa apakah kata "tarik tunai" ada di dalam inputan
		if (strpos(strtolower($knf_barang_nama), 'tarik tunai') !== false) {
		    $countTarikTunai = mysqli_query($conn, "SELECT * FROM keranjang_non_fisik WHERE knf_id_kasir = $knf_id_kasir && knf_cabang = $knf_cabang");
            $countTarikTunai = mysqli_num_rows($countTarikTunai);

            if ( $countTarikTunai < 1 ) {
            	$query = "INSERT INTO keranjang_non_fisik VALUES ('', '$knf_nama', 0, '$knf_harga_beli', '$knf_harga_jual', '$knf_barang_id', '$knf_barang_nama', '$knf_barang_kode', '$knf_qty', '$knf_id_kasir', '-', '$knf_id_cek', '$knf_cabang')";
            } else {
            	echo '
            		<script>
            			alert("Transaksi yang berhubungan dengan TARIK TUNAI Wajib 1 Invoice itu 1 Transaksi !! ");
            			document.location.href = "'.$linkBack.'";
            		</script>
            	'; exit();
            }

		} else {
		    // query insert data
			$query = "INSERT INTO keranjang_non_fisik VALUES ('', '$knf_nama', 0, '$knf_harga_beli', '$knf_harga_jual', '$knf_barang_id', '$knf_barang_nama', '$knf_barang_kode', '$knf_qty', '$knf_id_kasir', '-', '$knf_id_cek', '$knf_cabang')";
		}

		
		
		mysqli_query($conn, $query);

		return mysqli_affected_rows($conn);
	}
}

function tambahKeranjangBarcodeNonFisik($data) {
	global $conn;

	$knf_barang_kode 			= htmlspecialchars($data['inputbarcodeNonFisik']);
	$knf_id_kasir 				= $data['knf_id_kasir'];
	$knf_cabang   				= $data['knf_cabang'];

	// Ambil Data Barang berdasarkan Kode Barang 
	$jasa 	= mysqli_query( $conn, "select bnf_id, bnf_kode, bnf_nama, bnf_harga_beli, bnf_harga_jual, bnf_status  from barang_non_fisik where bnf_kode = '".$knf_barang_kode."' && bnf_cabang = ".$knf_cabang." ");
    $jasa 		= mysqli_fetch_array($jasa);

    $knf_barang_id   	= $jasa['bnf_id'];
    $namaProdukNonFisik = mysqli_query($conn, "SELECT bnf_nama FROM barang_non_fisik WHERE bnf_id = $knf_barang_id ");
    $namaProdukNonFisik = mysqli_fetch_array($namaProdukNonFisik);
    $knf_barang_nama    = strtolower($namaProdukNonFisik['bnf_nama']);

	$knf_barang_kode   	= $jasa['bnf_kode'];
	$knf_nama     		= $jasa['bnf_nama'];
	$knf_harga_beli    	= $jasa['bnf_harga_beli'];
	$knf_harga_jual    	= $jasa['bnf_harga_jual'];
    $knf_qty      		= 1;
	$bnf_status 		= $jasa["bnf_status"];
	$knf_id_cek   		= $knf_barang_id.$knf_id_kasir.$knf_cabang;


	// Kondisi jika scan Barcode Tidak sesuai
	if ( $knf_barang_id != null ) {

	   		// Kondisi barang berdasarkan status
	   		if ( $bnf_status == 0 ) {
				echo '
					<script>
							alert("Produk Non Fisik TIDAK BISA DITAMBAHKAN Karena Status bukan untuk dijual!! Coba cek kembali di data status produk tersebut");
							document.location.href = "";
					</script>
				'; exit();
			}

	   		// Cek STOCK
			$barang_id_cek = mysqli_num_rows(mysqli_query($conn, "select * from keranjang_non_fisik where knf_id_cek = ".$knf_id_cek." "));
				
			if ( $barang_id_cek > 0 ) {
				$keranjangParent = mysqli_query( $conn, "select knf_qty from keranjang_non_fisik where knf_id_cek = '".$knf_id_cek."'");
		        $kp = mysqli_fetch_array($keranjangParent); 
		        $kp = $kp['knf_qty'];
		        $kp += $knf_qty;

		        $query = "UPDATE keranjang_non_fisik SET 
							knf_qty   		 = '$kp'
							WHERE knf_id_cek = $knf_id_cek
							";
				mysqli_query($conn, $query);
				return mysqli_affected_rows($conn);

			} else {
				// Memeriksa apakah kata "tarik tunai" ada di dalam inputan
				if (strpos(strtolower($knf_barang_nama), 'tarik tunai') !== false) {
				    $countTarikTunai = mysqli_query($conn, "SELECT * FROM keranjang_non_fisik WHERE knf_id_kasir = $knf_id_kasir && knf_cabang = $knf_cabang");
		            $countTarikTunai = mysqli_num_rows($countTarikTunai);

		            if ( $countTarikTunai < 1 ) {
		            	$query = "INSERT INTO keranjang_non_fisik VALUES ('', '$knf_nama', 0, '$knf_harga_beli', '$knf_harga_jual', '$knf_barang_id', '$knf_barang_nama', '$knf_barang_kode', '$knf_qty', '$knf_id_kasir', '-', '$knf_id_cek', '$knf_cabang')";
		            } else {
		            	echo '
		            		<script>
		            			alert("Transaksi yang berhubungan dengan TARIK TUNAI Wajib 1 Invoice itu 1 Transaksi !! ");
		            			document.location.href = "";
		            		</script>
		            	'; exit();
		            }

				} else {
				    // query insert data
					$query = "INSERT INTO keranjang_non_fisik VALUES ('', '$knf_nama', 0, '$knf_harga_beli', '$knf_harga_jual', '$knf_barang_id', '$knf_barang_nama', '$knf_barang_kode', '$knf_qty', '$knf_id_kasir', '-', '$knf_id_cek', '$knf_cabang')";
				}

				mysqli_query($conn, $query);

				return mysqli_affected_rows($conn);
			}
	   	
	} else {
		echo '
			<script>
				alert("Kode Produk Tidak ada di Data Master Jasa dan Coba Cek Kembali !! ");
				document.location.href = "";
			</script>
		';
	}
}

function updateQTYHargaNonFisik($data) {
	global $conn;
	$id = $data["knf_id"];

	// ambil data dari tiap elemen dalam form
	$knf_qty 			= htmlspecialchars($data['knf_qty']);
	$knf_provider 		= htmlspecialchars($data["knf_provider"]);
	$knf_harga_beli 	= htmlspecialchars($data["knf_harga_beli"]);
	$knf_harga_jual 	= htmlspecialchars($data["knf_harga_jual"]);
	$knf_catatan 		= htmlspecialchars($data["knf_catatan"]);

		// query update data
		$query = "UPDATE keranjang_non_fisik SET 
					knf_provider    = '$knf_provider',
					knf_harga_beli  = '$knf_harga_beli',
					knf_harga_jual  = '$knf_harga_jual',
					knf_qty   		= '$knf_qty',
					knf_catatan   	= '$knf_catatan'
					WHERE knf_id 	= $id
					";
		mysqli_query($conn, $query);
		return mysqli_affected_rows($conn);	
}

function hapusKeranjangNonFisik($id) {
	global $conn;
    
	mysqli_query( $conn, "DELETE FROM keranjang_non_fisik WHERE knf_id = $id");

	return mysqli_affected_rows($conn);
}

function updateStockNonFisik($data) {
	global $conn;
	$pbnf_barang_id   			= $data['knf_barang_id'];
	$pbnf_barang_nama 			= $data['pbnf_barang_nama'];
	$pbnf_provider 				= $data['knf_provider'];
	$pbnf_provider_sisa_saldo   = $data['pbnf_provider_sisa_saldo'];
	$pbnf_qty   				= $data['knf_qty'];
	$pbnf_harga_beli   			= $data['knf_harga_beli'];
	$pbnf_harga_jual   			= $data['knf_harga_jual'];
	$pbnf_catatan   			= $data['knf_catatan'];
	$pbnf_id_kasir      		= $data['knf_id_kasir'];

	$kik                 		= $data['kik'];
	$penjualan_invoice2  		= $data['penjualan_invoice2'];
	$invoice_tgl         		= date("d F Y g:i:s a");
	$invoice_total_beli       	= 0;
	$invoice_total_beli_non_fisik = $data['invoice_total_beli_non_fisik'];

	$invoice_total       		= $data['invoice_total'];
	$invoice_ongkir      		= 0;
	$invoice_diskon      		= htmlspecialchars($data['invoice_diskon']);
	
	$invoice_sub_total   		= $invoice_total + $invoice_ongkir;
	$invoice_sub_total   		= $invoice_sub_total - $invoice_diskon;
	$invoice_bayar       		= htmlspecialchars($data['angka1']);
	if ( $invoice_bayar == null ) {
		echo"
			<script>
				alert('Anda Belum Input Nominal BAYAR !!!');
				document.location.href = '';
			</script>
		"; exit();
	} 

	$invoice_kembali     		= $invoice_bayar - $invoice_sub_total;
	$invoice_date        		= date("Y-m-d");
	$invoice_date_year_month    = date("Y-m");
	$invoice_customer    		= $data['invoice_customer'];
	$invoice_customer_category  = $data['invoice_customer_category'];
	$invoice_kurir    	 		= $data['invoice_kurir'];
	$invoice_tipe_transaksi  	= $data['invoice_tipe_transaksi'];
	$penjualan_invoice_count 	= $data['penjualan_invoice_count'];
	$invoice_piutang			= $data['invoice_piutang'];
	if ( $invoice_piutang == 1 ) {
		$invoice_piutang_dp = $invoice_bayar;
	} else {
		$invoice_piutang_dp = 0;
	}
	$invoice_piutang_jatuh_tempo= $data['invoice_piutang_jatuh_tempo'];
	$invoice_piutang_lunas		= $data['invoice_piutang_lunas'];
	
	$invoice_tipe_tarik_tunai   = htmlspecialchars($data['invoice_tipe_tarik_tunai']);

/*	
	// Jika Tipe Tarik Tunai Total hanya margin keuntungan saja
	if ( $invoice_tipe_tarik_tunai == 1 ) {
		$invoice_sub_total -= $invoice_total_beli_non_fisik;
	} else {
		$invoice_sub_total = $invoice_sub_total;
	}
*/

	$invoice_cabang             = $data['invoice_cabang'];
	

	if ( $invoice_customer == 1 ) {
		$invoice_marketplace = htmlspecialchars($data['invoice_marketplace']);
		$invoice_ekspedisi   = htmlspecialchars($data['invoice_ekspedisi']);
		$invoice_no_resi     = htmlspecialchars($data['invoice_no_resi']);
	} else {
		$invoice_marketplace = "";
		$invoice_ekspedisi   = 0;
		$invoice_no_resi     = "-";
	}
	$jumlah = count($pbnf_id_kasir);

	if ( $invoice_piutang == 0 && $invoice_bayar < $invoice_sub_total ) {
		echo"
			<script>
				alert('Transaksi TIDAK BISA Dilanjutakn !!! Nominal Pembayaran LEBIH KECIL dari Total Pembayaran.. Silahkan Melakukan Transaksi PIUTANG jika Nominal Kurang Dari Total Pembayaran');
				document.location.href = '';
			</script>
		";
	} elseif ( $invoice_piutang == 1 && $invoice_bayar >= $invoice_sub_total ) {
		echo"
			<script>
				alert('Transaksi TIDAK BISA Dilanjutakn !!! Nominal DP LEBIH BESAR / SAMA dari Total Piutang.. Silahkan Melakukan Transaksi CASH jika Nominal Lebih Besar / Sama Dari Total Pembayaran');
				document.location.href = '';
			</script>
		";
	} else {
		// query insert invoice
		$query1 = "INSERT INTO invoice VALUES ('', '$penjualan_invoice2', '$penjualan_invoice_count', '$invoice_tgl', '$invoice_customer', '$invoice_customer_category', '$invoice_kurir', '1', '$invoice_tipe_transaksi', '$invoice_total_beli', '$invoice_total_beli_non_fisik', '$invoice_total', '$invoice_ongkir', '$invoice_diskon', '$invoice_sub_total', '$invoice_bayar', '$invoice_kembali', '$kik', '$invoice_date', '$invoice_date_year_month', ' ', ' ', '$invoice_total_beli', '$invoice_total', '$invoice_ongkir', '$invoice_sub_total', '$invoice_bayar', '$invoice_kembali', '$invoice_marketplace', '$invoice_ekspedisi', '$invoice_no_resi', '-', '$invoice_piutang', '$invoice_piutang_dp', '$invoice_piutang_jatuh_tempo', '$invoice_piutang_lunas', ' ', ' ', 0, 1, '$invoice_tipe_tarik_tunai', '$invoice_cabang')";
		// var_dump($query1); die();
		mysqli_query($conn, $query1);

		for( $x=0; $x<$jumlah; $x++ ){
				$query3 = "INSERT INTO penjualan_barang_non_fisik VALUES ('', '$pbnf_barang_id[$x]', '$pbnf_barang_nama[$x]', '$pbnf_provider[$x]', '$pbnf_provider_sisa_saldo[$x]', '$pbnf_qty[$x]', '$pbnf_harga_beli[$x]', '$pbnf_harga_jual[$x]', '$pbnf_catatan[$x]', '$kik', '$penjualan_invoice2', '$invoice_date', '$invoice_tgl', '$invoice_cabang')";

				mysqli_query($conn, $query3);
		}
		mysqli_query( $conn, "DELETE FROM keranjang_non_fisik WHERE knf_id_kasir = $kik");
	}
}

function updateStockNonFisikDraft($data) {
	global $conn;

	$knf_nama 					= $data['knf_nama'];
	$knf_barang_kode 			= $data['knf_barang_kode'];
	$knf_id_cek 				= $data['knf_id_cek'];
	$pbnf_barang_id   			= $data['knf_barang_id'];
	$pbnf_barang_nama 			= $data['pbnf_barang_nama'];
	$pbnf_provider 				= $data['knf_provider'];
	$pbnf_qty   				= $data['knf_qty'];
	$pbnf_harga_beli   			= $data['knf_harga_beli'];
	$pbnf_harga_jual   			= $data['knf_harga_jual'];
	$pbnf_catatan   			= $data['knf_catatan'];
	$pbnf_id_kasir      		= $data['knf_id_kasir'];

	$kik                 		= $data['kik'];
	$penjualan_invoice2  		= $data['penjualan_invoice2'];
	$invoice_tgl         		= date("d F Y g:i:s a");
	$invoice_total_beli       	= 0;
	$invoice_total_beli_non_fisik = $data['invoice_total_beli_non_fisik'];
	$invoice_total       		= $data['invoice_total'];
	$invoice_ongkir      		= 0;
	$invoice_diskon      		= htmlspecialchars($data['invoice_diskon']);
	$invoice_count_non_fisik    = htmlspecialchars($data['invoice_count_non_fisik']);
	
	$invoice_sub_total   		= $invoice_total + $invoice_ongkir;
	$invoice_sub_total   		= $invoice_sub_total - $invoice_diskon;
	$invoice_bayar       		= htmlspecialchars($data['angka1']);
	

	$invoice_kembali     		= $invoice_bayar - $invoice_sub_total;
	$invoice_date        		= date("Y-m-d");
	$invoice_date_year_month    = date("Y-m");
	$invoice_customer    		= $data['invoice_customer'];
	$invoice_customer_category  = $data['invoice_customer_category'];
	$invoice_kurir    	 		= $data['invoice_kurir'];
	$invoice_tipe_transaksi  	= $data['invoice_tipe_transaksi'];
	$penjualan_invoice_count 	= $data['penjualan_invoice_count'];
	$invoice_piutang			= $data['invoice_piutang'];
	if ( $invoice_piutang == 1 ) {
		$invoice_piutang_dp = $invoice_bayar;
	} else {
		$invoice_piutang_dp = 0;
	}
	$invoice_piutang_jatuh_tempo= $data['invoice_piutang_jatuh_tempo'];
	$invoice_piutang_lunas		= $data['invoice_piutang_lunas'];

	$invoice_tipe_tarik_tunai   = htmlspecialchars($data['invoice_tipe_tarik_tunai']);

/*
	// Jika Tipe Tarik Tunai Total hanya margin keuntungan saja
	if ( $invoice_tipe_tarik_tunai == 1 ) {
		$invoice_sub_total -= $invoice_total_beli_non_fisik;
	} else {
		$invoice_sub_total = $invoice_sub_total;
	}
*/
	$invoice_cabang             = $data['invoice_cabang'];
	

	if ( $invoice_customer == 1 ) {
		$invoice_marketplace = htmlspecialchars($data['invoice_marketplace']);
		$invoice_ekspedisi   = htmlspecialchars($data['invoice_ekspedisi']);
		$invoice_no_resi     = htmlspecialchars($data['invoice_no_resi']);
	} else {
		$invoice_marketplace = "";
		$invoice_ekspedisi   = 0;
		$invoice_no_resi     = "-";
	}
	$jumlah = count($pbnf_id_kasir);

	// query insert invoice
	$query1 = "INSERT INTO invoice VALUES ('', '$penjualan_invoice2', '$penjualan_invoice_count', '$invoice_tgl', '$invoice_customer', '$invoice_customer_category', '$invoice_kurir', '1', '$invoice_tipe_transaksi', '$invoice_total_beli', '$invoice_total_beli_non_fisik', '$invoice_total', '$invoice_ongkir', '$invoice_diskon', '$invoice_sub_total', '$invoice_bayar', '$invoice_kembali', '$kik', '$invoice_date', '$invoice_date_year_month', ' ', ' ', '$invoice_total_beli', '$invoice_total', '$invoice_ongkir', '$invoice_sub_total', '$invoice_bayar', '$invoice_kembali', '$invoice_marketplace', '$invoice_ekspedisi', '$invoice_no_resi', '-', '$invoice_piutang', '$invoice_piutang_dp', '$invoice_piutang_jatuh_tempo', '$invoice_piutang_lunas', ' ', ' ', 1, 1, '$invoice_tipe_tarik_tunai', '$invoice_cabang')";
		// var_dump($query1); die();
		mysqli_query($conn, $query1);

		for( $x=0; $x<$jumlah; $x++ ){
				$query3 = "INSERT INTO keranjang_non_fisik_draft VALUES ('', '$knf_nama[$x]', '$pbnf_provider[$x]', '$pbnf_harga_beli[$x]', '$pbnf_harga_jual[$x]', '$pbnf_barang_id[$x]', '$pbnf_barang_nama[$x]', '$knf_barang_kode[$x]', '$pbnf_qty[$x]', '$kik', '$pbnf_catatan[$x]', '$knf_id_cek[$x]', 1, '$penjualan_invoice2', '$invoice_cabang')";

			mysqli_query($conn, $query3);
		}
		mysqli_query( $conn, "DELETE FROM keranjang_non_fisik WHERE knf_id_kasir = $kik");
	
	return mysqli_affected_rows($conn);
}


function updateStockSaveNonFisikDraft($data) {
	global $conn;

	$knf_nama 					= $data['knfd_nama'];
	$knf_barang_kode 			= $data['knfd_barang_kode'];
	$knf_id_cek 				= $data['knfd_id_cek'];
	$pbnf_barang_id   			= $data['knfd_barang_id'];
	$pbnf_barang_nama 			= $data['pbnf_barang_nama'];
	$pbnf_provider 				= $data['knf_provider'];
	$pbnf_provider_sisa_saldo   = $data['pbnf_provider_sisa_saldo'];
	$pbnf_qty   				= $data['knfd_qty'];
	$pbnf_harga_beli   			= $data['knfd_harga_beli'];
	$pbnf_harga_jual   			= $data['knfd_harga_jual'];
	$pbnf_catatan   			= $data['knfd_catatan'];
	$pbnf_id_kasir      		= $data['knfd_id_kasir'];

	$invoice_id 				= $data['invoice_id'];
	$kik                 		= $data['kik'];
	$penjualan_invoice2  		= $data['penjualan_invoice2'];
	$invoice_tgl         		= date("d F Y g:i:s a");
	$invoice_total       		= $data['invoice_total'];
	$invoice_ongkir      		= 0;
	$invoice_diskon      		= htmlspecialchars($data['invoice_diskon']);
	
	$invoice_total_beli_non_fisik = $data['invoice_total_beli_non_fisik'];
	$invoice_sub_total   		= $invoice_total + $invoice_ongkir;
	$invoice_sub_total   		= $invoice_sub_total - $invoice_diskon;
	
	$invoice_bayar       		= htmlspecialchars($data['angka1']);
	

	$invoice_kembali     		= $invoice_bayar - $invoice_sub_total;
	$invoice_date        		= date("Y-m-d");
	$invoice_date_year_month    = date("Y-m");
	$invoice_customer    		= $data['invoice_customer'];
	$invoice_customer_category  = $data['invoice_customer_category'];
	$invoice_kurir    	 		= $data['invoice_kurir'];
	$invoice_tipe_transaksi  	= $data['invoice_tipe_transaksi'];
	$penjualan_invoice_count 	= $data['penjualan_invoice_count'];
	$invoice_piutang			= $data['invoice_piutang'];
	if ( $invoice_piutang == 1 ) {
		$invoice_piutang_dp = $invoice_bayar;
	} else {
		$invoice_piutang_dp = 0;
	}
	$invoice_piutang_jatuh_tempo= $data['invoice_piutang_jatuh_tempo'];
	$invoice_piutang_lunas		= $data['invoice_piutang_lunas'];
	
	$invoice_tipe_tarik_tunai 	= $data['invoice_tipe_tarik_tunai'];

/*
	// Jika Tipe Tarik Tunai Total hanya margin keuntungan saja
	if ( $invoice_tipe_tarik_tunai == 1 ) {
		$invoice_sub_total -= $invoice_total_beli_non_fisik;
	} else {
		$invoice_sub_total = $invoice_sub_total;
	}
*/

	$invoice_cabang             = $data['invoice_cabang'];
	

	if ( $invoice_customer == 1 ) {
		$invoice_marketplace = htmlspecialchars($data['invoice_marketplace']);
		$invoice_ekspedisi   = htmlspecialchars($data['invoice_ekspedisi']);
		$invoice_no_resi     = htmlspecialchars($data['invoice_no_resi']);
	} else {
		$invoice_marketplace = "";
		$invoice_ekspedisi   = 0;
		$invoice_no_resi     = "-";
	}
	$jumlah = count($pbnf_id_kasir);


	if ( $invoice_bayar == null ) {
		echo"
			<script>
				alert('Anda Belum Input Nominal BAYAR !!!');
				document.location.href = '';
			</script>
		";
	} else {
		// query Update invoice
		$query1 = "UPDATE invoice SET  
				invoice_tgl 				= '$invoice_tgl', 
				invoice_customer 			= '$invoice_customer', 
				invoice_tipe_transaksi 		= '$invoice_tipe_transaksi', 
				invoice_total_beli_non_fisik = '$invoice_total_beli_non_fisik', 
				invoice_total 				= '$invoice_total', 
				invoice_ongkir 				= '$invoice_ongkir', 
				invoice_diskon 				= '$invoice_diskon', 
				invoice_sub_total 			= '$invoice_sub_total', 
				invoice_bayar 				= '$invoice_bayar', 
				invoice_kembali 			= '$invoice_kembali', 
				invoice_kasir 				= '$kik', 
				invoice_date 				= '$invoice_date', 
				invoice_date_year_month 	= '$invoice_date_year_month', 
				invoice_total_lama 			= '$invoice_total', 
				invoice_ongkir_lama 		= '$invoice_ongkir', 
				invoice_sub_total_lama 		= '$invoice_sub_total', 
				invoice_bayar_lama 			= '$invoice_bayar', 
				invoice_kembali_lama 		= '$invoice_kembali',  
				invoice_piutang 			= '$invoice_piutang', 
				invoice_piutang_dp 			= '$invoice_piutang_dp', 
				invoice_piutang_jatuh_tempo = '$invoice_piutang_jatuh_tempo', 
				invoice_piutang_lunas 		= '$invoice_piutang_lunas', 
				invoice_draft 				= 0,
				invoice_tipe_tarik_tunai    = '$invoice_tipe_tarik_tunai'
				WHERE invoice_id 			= $invoice_id
		";
		// var_dump($query1); die();
		mysqli_query($conn, $query1);
		
		for( $x=0; $x<$jumlah; $x++ ){
				$query3 = "INSERT INTO penjualan_barang_non_fisik VALUES ('', '$pbnf_barang_id[$x]', '$pbnf_barang_nama[$x]', '$pbnf_provider[$x]', '$pbnf_provider_sisa_saldo[$x]', '$pbnf_qty[$x]', '$pbnf_harga_beli[$x]', '$pbnf_harga_jual[$x]', '$pbnf_catatan[$x]', '$kik', '$penjualan_invoice2', '$invoice_date', '$invoice_tgl', '$invoice_cabang')";

				mysqli_query($conn, $query3);
		}
		mysqli_query( $conn, "DELETE FROM keranjang_non_fisik_draft WHERE knfd_id_kasir = $kik");
		return mysqli_affected_rows($conn);
	}
}

function tambahKeranjangNonFisikDraft($knf_cabang, $knf_barang_id, $knf_barang_nama, $knf_barang_kode, $knf_nama, $knf_harga_beli, $knf_harga_jual, $knf_id_kasir, $knf_qty, $knf_id_cek, $invoice, $linkBack) {
	global $conn;


	// Cek STOCK
	$barang_id_cek = mysqli_num_rows(mysqli_query($conn, "select * from keranjang_non_fisik_draft where knfd_id_cek = '$knf_id_cek' "));
	
		
	if ( $barang_id_cek > 0 ) {
		$keranjangParent = mysqli_query( $conn, "select knfd_qty from keranjang_non_fisik_draft where knfd_id_cek = '".$knf_id_cek."'");
        $kp = mysqli_fetch_array($keranjangParent); 
        $kp = $kp['knfd_qty'];
        $kp += $knf_qty;

        $query = "UPDATE keranjang_non_fisik_draft SET 
					knfd_qty   = '$kp'
					WHERE knfd_id_cek = $knf_id_cek
					";
		mysqli_query($conn, $query);
		return mysqli_affected_rows($conn);

	} else {
		// Memeriksa apakah kata "tarik tunai" ada di dalam inputan
		if (strpos(strtolower($knf_barang_nama), 'tarik tunai') !== false) {
		    $countTarikTunai = mysqli_query($conn, "SELECT * FROM keranjang_non_fisik_draft WHERE knfd_id_kasir = $knf_id_kasir && knfd_cabang = $knf_cabang");
            $countTarikTunai = mysqli_num_rows($countTarikTunai);

            if ( $countTarikTunai < 1 ) {
            	$query = "INSERT INTO keranjang_non_fisik_draft VALUES ('', '$knf_nama', 0, '$knf_harga_beli', '$knf_harga_jual', '$knf_barang_id', '$knf_barang_nama', '$knf_barang_kode', '$knf_qty', '$knf_id_kasir', '-', '$knf_id_cek', '1', '$invoice', '$knf_cabang')";
            } else {
            	echo '
            		<script>
            			alert("Transaksi yang berhubungan dengan TARIK TUNAI Wajib 1 Invoice itu 1 Transaksi !! ");
            			document.location.href = "'.$linkBack.'";
            		</script>
            	'; exit();
            }

		} else {
		    // query insert data
			$query = "INSERT INTO keranjang_non_fisik_draft VALUES ('', '$knf_nama', 0, '$knf_harga_beli', '$knf_harga_jual', '$knf_barang_id', '$knf_barang_nama', '$knf_barang_kode', '$knf_qty', '$knf_id_kasir', '-', '$knf_id_cek', '1', '$invoice', '$knf_cabang')";
		}
		
		mysqli_query($conn, $query);

		return mysqli_affected_rows($conn);
	}
}


function tambahKeranjangBarcodeNonFisikDraft($data) {
	global $conn;

	$knf_barang_kode 			= htmlspecialchars($data['inputbarcodeNonFisik']);
	$knf_id_kasir 				= $data['knf_id_kasir'];
	$knfd_invoice 				= $data['knfd_invoice'];
	$knf_cabang   				= $data['knf_cabang'];

	// Ambil Data Barang berdasarkan Kode Barang 
	$jasa 	= mysqli_query( $conn, "select bnf_id, bnf_kode, bnf_nama, bnf_harga_beli, bnf_harga_jual, bnf_status  from barang_non_fisik where bnf_kode = '".$knf_barang_kode."' && bnf_cabang = ".$knf_cabang." ");
    $jasa 		= mysqli_fetch_array($jasa);

    $knf_barang_id   	= $jasa['bnf_id'];
    $namaProdukNonFisik = mysqli_query($conn, "SELECT bnf_nama FROM barang_non_fisik WHERE bnf_id = $knf_barang_id ");
    $namaProdukNonFisik = mysqli_fetch_array($namaProdukNonFisik);
    $knf_barang_nama    = strtolower($namaProdukNonFisik['bnf_nama']);

	$knf_barang_kode   	= $jasa['bnf_kode'];
	$knf_nama     		= $jasa['bnf_nama'];
	$knf_harga_beli    	= $jasa['bnf_harga_beli'];
	$knf_harga_jual    	= $jasa['bnf_harga_jual'];
    $knf_qty      		= 1;
	$bnf_status 		= $jasa["bnf_status"];
	$knf_id_cek   		= $knf_barang_id.$knf_id_kasir.$knf_cabang;


	// Kondisi jika scan Barcode Tidak sesuai
	if ( $knf_barang_id != null ) {

	   		// Kondisi barang berdasarkan status
	   		if ( $bnf_status == 0 ) {
				echo '
					<script>
							alert("Produk Non Fisik TIDAK BISA DITAMBAHKAN Karena Status bukan untuk dijual!! Coba cek kembali di data status produk tersebut");
							document.location.href = "";
					</script>
				'; exit();
			}

	   		// Cek STOCK
			$barang_id_cek = mysqli_num_rows(mysqli_query($conn, "select * from keranjang_non_fisik_draft where knfd_id_cek = ".$knf_id_cek." "));
				
			if ( $barang_id_cek > 0 ) {
				$keranjangParent = mysqli_query( $conn, "select knfd_qty from keranjang_non_fisik_draft where knfd_id_cek = '".$knf_id_cek."'");
		        $kp = mysqli_fetch_array($keranjangParent); 
		        $kp = $kp['knfd_qty'];
		        $kp += $knf_qty;

		        $query = "UPDATE keranjang_non_fisik_draft SET 
							knfd_qty   		 = '$kp'
							WHERE knfd_id_cek = $knf_id_cek
							";
				mysqli_query($conn, $query);
				return mysqli_affected_rows($conn);

			} else {
				// Memeriksa apakah kata "tarik tunai" ada di dalam inputan
				if (strpos(strtolower($knf_barang_nama), 'tarik tunai') !== false) {
				    $countTarikTunai = mysqli_query($conn, "SELECT * FROM keranjang_non_fisik_draft WHERE knfd_id_kasir = $knf_id_kasir && knfd_cabang = $knf_cabang");
		            $countTarikTunai = mysqli_num_rows($countTarikTunai);

		            if ( $countTarikTunai < 1 ) {
		            	$query = "INSERT INTO keranjang_non_fisik_draft VALUES ('', '$knf_nama', 0, '$knf_harga_beli', '$knf_harga_jual', '$knf_barang_id', '$knf_barang_nama', '$knf_barang_kode', '$knf_qty', '$knf_id_kasir', '-', '$knf_id_cek', '1', '$invoice', '$knf_cabang')";
		            } else {
		            	echo '
		            		<script>
		            			alert("Transaksi yang berhubungan dengan TARIK TUNAI Wajib 1 Invoice itu 1 Transaksi !! ");
		            			document.location.href = "";
		            		</script>
		            	'; exit();
		            }

				} else {
				    // query insert data
					$query = "INSERT INTO keranjang_non_fisik_draft VALUES ('', '$knf_nama', 0, '$knf_harga_beli', '$knf_harga_jual', '$knf_barang_id', '$knf_barang_nama', '$knf_barang_kode', '$knf_qty', '$knf_id_kasir', '-', '$knf_id_cek', '1', '$invoice', '$knf_cabang')";
				}
				mysqli_query($conn, $query);

				return mysqli_affected_rows($conn);
			}
	   	
	} else {
		echo '
			<script>
				alert("Kode Produk Tidak ada di Data Master Jasa dan Coba Cek Kembali !! ");
				document.location.href = "";
			</script>
		';
	}
}

function updateQTYHargaNonFisikDraft($data) {
	global $conn;
	$id = $data["knfd_id"];

	// ambil data dari tiap elemen dalam form
	$knf_provider 		= htmlspecialchars($data["knf_provider"]);
	$knf_harga_beli 	= htmlspecialchars($data["knfd_harga_beli"]);
	$knf_harga_jual 	= htmlspecialchars($data["knfd_harga_jual"]);
	$knf_qty 			= htmlspecialchars($data['knfd_qty']);
	$knf_catatan 		= htmlspecialchars($data["knfd_catatan"]);

		// query update data
		$query = "UPDATE keranjang_non_fisik_draft SET 
					knf_provider 	 = '$knf_provider',
					knfd_harga_beli  = '$knf_harga_beli',
					knfd_harga_jual  = '$knf_harga_jual',
					knfd_qty   		= '$knf_qty',
					knfd_catatan   	= '$knf_catatan'
					WHERE knfd_id 	= $id
					";
		mysqli_query($conn, $query);
		return mysqli_affected_rows($conn);	
}

function hapusKeranjangNonFisikDraft($id) {
	global $conn;
    
	mysqli_query( $conn, "DELETE FROM keranjang_non_fisik_draft WHERE knfd_id = $id");

	return mysqli_affected_rows($conn);
}




function updateStock($data) {
	global $conn;
	$id                  		= $data['barang_ids'];
	$keranjang_qty       		= $data['keranjang_qty'];
	$keranjang_qty_view       	= $data['keranjang_qty_view'];
	$keranjang_konversi_isi     = $data['keranjang_konversi_isi'];
	$keranjang_satuan           = $data['keranjang_satuan'];
	$keranjang_harga_beli       = $data['keranjang_harga_beli'];
	$keranjang_harga			= $data['keranjang_harga'];
	$keranjang_harga_parent		= $data['keranjang_harga_parent'];
	$keranjang_harga_edit		= $data['keranjang_harga_edit'];
	$keranjang_id_kasir  		= $data['keranjang_id_kasir'];
	$penjualan_invoice   		= $data['penjualan_invoice'];
	$keranjang_barang_option_sn = $data['keranjang_barang_option_sn'];
	$keranjang_barang_sn_id     = $data['keranjang_barang_sn_id'];
	$keranjang_sn               = $data['keranjang_sn'];
	$invoice_customer_category2 = $data['invoice_customer_category2'];
	$penjualan_cabang        	= $data['penjualan_cabang'];

	$kik                 		= $data['kik'];
	$penjualan_invoice2  		= $data['penjualan_invoice2'];
	$invoice_tgl         		= date("d F Y g:i:s a");
	$invoice_total_beli       	= $data['invoice_total_beli'];
	$invoice_total       		= $data['invoice_total'];
	$invoice_ongkir      		= htmlspecialchars($data['invoice_ongkir']);
	$invoice_diskon      		= htmlspecialchars($data['invoice_diskon']);
	
	$invoice_sub_total   		= $invoice_total + $invoice_ongkir;
	$invoice_sub_total   		= $invoice_sub_total - $invoice_diskon;
	$invoice_bayar       		= htmlspecialchars($data['angka1']);
	if ( $invoice_bayar == null ) {
		echo"
			<script>
				alert('Anda Belum Input Nominal BAYAR !!!');
				document.location.href = '';
			</script>
		"; exit();
	} 

	$invoice_kembali     		= $invoice_bayar - $invoice_sub_total;
	$invoice_date        		= date("Y-m-d");
	$invoice_date_year_month    = date("Y-m");
	$penjualan_date      		= $data['penjualan_date'];
	$invoice_customer    		= $data['invoice_customer'];
	$invoice_customer_category  = $data['invoice_customer_category'];
	$invoice_kurir    	 		= $data['invoice_kurir'];
	$invoice_tipe_transaksi  	= $data['invoice_tipe_transaksi'];
	$penjualan_invoice_count 	= $data['penjualan_invoice_count'];
	$invoice_piutang			= $data['invoice_piutang'];
	if ( $invoice_piutang == 1 ) {
		$invoice_piutang_dp = $invoice_bayar;
	} else {
		$invoice_piutang_dp = 0;
	}
	$invoice_piutang_jatuh_tempo= $data['invoice_piutang_jatuh_tempo'];
	$invoice_piutang_lunas		= $data['invoice_piutang_lunas'];
	$invoice_cabang             = $data['invoice_cabang'];
	

	if ( $invoice_customer == 1 ) {
		$invoice_marketplace = htmlspecialchars($data['invoice_marketplace']);
		$invoice_ekspedisi   = htmlspecialchars($data['invoice_ekspedisi']);
		$invoice_no_resi     = htmlspecialchars($data['invoice_no_resi']);
	} else {
		$invoice_marketplace = "";
		$invoice_ekspedisi   = 0;
		$invoice_no_resi     = "-";
	}
	$jumlah = count($keranjang_id_kasir);

	if ( $invoice_piutang == 0 && $invoice_bayar < $invoice_sub_total ) {
		echo"
			<script>
				alert('Transaksi TIDAK BISA Dilanjutakn !!! Nominal Pembayaran LEBIH KECIL dari Total Pembayaran.. Silahkan Melakukan Transaksi PIUTANG jika Nominal Kurang Dari Total Pembayaran');
				document.location.href = '';
			</script>
		";
	} elseif ( $invoice_piutang == 1 && $invoice_bayar >= $invoice_sub_total ) {
		echo"
			<script>
				alert('Transaksi TIDAK BISA Dilanjutakn !!! Nominal DP LEBIH BESAR / SAMA dari Total Piutang.. Silahkan Melakukan Transaksi CASH jika Nominal Lebih Besar / Sama Dari Total Pembayaran');
				document.location.href = '';
			</script>
		";
	} else {
		// query insert invoice
		$query1 = "INSERT INTO invoice VALUES ('', '$penjualan_invoice2', '$penjualan_invoice_count', '$invoice_tgl', '$invoice_customer', '$invoice_customer_category', '$invoice_kurir', '1', '$invoice_tipe_transaksi', '$invoice_total_beli', 0, '$invoice_total', '$invoice_ongkir', '$invoice_diskon', '$invoice_sub_total', '$invoice_bayar', '$invoice_kembali', '$kik', '$invoice_date', '$invoice_date_year_month', ' ', ' ', '$invoice_total_beli', '$invoice_total', '$invoice_ongkir', '$invoice_sub_total', '$invoice_bayar', '$invoice_kembali', '$invoice_marketplace', '$invoice_ekspedisi', '$invoice_no_resi', '-', '$invoice_piutang', '$invoice_piutang_dp', '$invoice_piutang_jatuh_tempo', '$invoice_piutang_lunas', ' ', ' ', 0, 0, 0, '$invoice_cabang')";
		// var_dump($query1); die();
		mysqli_query($conn, $query1);

		for( $x=0; $x<$jumlah; $x++ ){
			$query = "INSERT INTO penjualan VALUES ('', '$id[$x]', '$id[$x]', '$keranjang_qty_view[$x]', '$keranjang_qty[$x]', '$keranjang_konversi_isi[$x]', '$keranjang_satuan[$x]','$keranjang_harga_beli[$x]', '$keranjang_harga[$x]', '$keranjang_harga_parent[$x]', '$keranjang_harga_edit[$x]', '$keranjang_id_kasir[$x]', '$penjualan_invoice[$x]' , '$penjualan_date[$x]', '$invoice_date_year_month', '$keranjang_qty_view[$x]', '$keranjang_qty_view[$x]', '$keranjang_barang_option_sn[$x]', '$keranjang_barang_sn_id[$x]', '$keranjang_sn[$x]', '$invoice_customer_category2[$x]', '$penjualan_cabang[$x]')";
			$query2 = "INSERT INTO terlaris VALUES ('', '$id[$x]', '$keranjang_qty[$x]')";

			mysqli_query($conn, $query);
			mysqli_query($conn, $query2);
		}
		

		mysqli_query( $conn, "DELETE FROM keranjang WHERE keranjang_id_kasir = $kik");
		return mysqli_affected_rows($conn);
	}
}

function updateStockDraft($data) {
	global $conn;
	$id                  		= $data['barang_ids'];
	$keranjang_qty       		= $data['keranjang_qty'];
	$keranjang_qty_view       	= $data['keranjang_qty_view'];
	$keranjang_konversi_isi     = $data['keranjang_konversi_isi'];
	$keranjang_satuan           = $data['keranjang_satuan'];
	$keranjang_harga_beli       = $data['keranjang_harga_beli'];
	$keranjang_harga			= $data['keranjang_harga'];
	$keranjang_harga_parent		= $data['keranjang_harga_parent'];
	$keranjang_harga_edit		= $data['keranjang_harga_edit'];
	$keranjang_id_kasir  		= $data['keranjang_id_kasir'];
	$penjualan_invoice   		= $data['penjualan_invoice'];
	$keranjang_barang_option_sn = $data['keranjang_barang_option_sn'];
	$keranjang_barang_sn_id     = $data['keranjang_barang_sn_id'];
	$keranjang_sn               = $data['keranjang_sn'];
	$invoice_customer_category2 = $data['invoice_customer_category2'];
	$keranjang_nama 			= $data['keranjang_nama'];
	$barang_kode_slug 			= $data['barang_kode_slug'];
	$keranjang_id_cek 			= $data['keranjang_id_cek'];
	$penjualan_cabang        	= $data['penjualan_cabang'];

	$kik                 		= $data['kik'];
	$penjualan_invoice2  		= $data['penjualan_invoice2'];
	$invoice_tgl         		= date("d F Y g:i:s a");
	$invoice_total_beli       	= $data['invoice_total_beli'];
	$invoice_total       		= $data['invoice_total'];
	$invoice_ongkir      		= htmlspecialchars($data['invoice_ongkir']);
	$invoice_diskon      		= htmlspecialchars($data['invoice_diskon']);
	
	$invoice_sub_total   		= $invoice_total + $invoice_ongkir;
	$invoice_sub_total   		= $invoice_sub_total - $invoice_diskon;
	$invoice_bayar       		= htmlspecialchars($data['angka1']);
	

	$invoice_kembali     		= $invoice_bayar - $invoice_sub_total;
	$invoice_date        		= date("Y-m-d");
	$invoice_date_year_month    = date("Y-m");
	$penjualan_date      		= $data['penjualan_date'];
	$invoice_customer    		= $data['invoice_customer'];
	$invoice_customer_category  = $data['invoice_customer_category'];
	$invoice_kurir    	 		= $data['invoice_kurir'];
	$invoice_tipe_transaksi  	= $data['invoice_tipe_transaksi'];
	$penjualan_invoice_count 	= $data['penjualan_invoice_count'];
	$invoice_piutang			= $data['invoice_piutang'];
	if ( $invoice_piutang == 1 ) {
		$invoice_piutang_dp = $invoice_bayar;
	} else {
		$invoice_piutang_dp = 0;
	}
	$invoice_piutang_jatuh_tempo= $data['invoice_piutang_jatuh_tempo'];
	$invoice_piutang_lunas		= $data['invoice_piutang_lunas'];
	$invoice_cabang             = $data['invoice_cabang'];
	

	if ( $invoice_customer == 1 ) {
		$invoice_marketplace = htmlspecialchars($data['invoice_marketplace']);
		$invoice_ekspedisi   = htmlspecialchars($data['invoice_ekspedisi']);
		$invoice_no_resi     = htmlspecialchars($data['invoice_no_resi']);
	} else {
		$invoice_marketplace = "";
		$invoice_ekspedisi   = 0;
		$invoice_no_resi     = "-";
	}
	$jumlah = count($keranjang_id_kasir);


	// query insert invoice
	$query1 = "INSERT INTO invoice VALUES ('', '$penjualan_invoice2', '$penjualan_invoice_count', '$invoice_tgl', '$invoice_customer', '$invoice_customer_category', '$invoice_kurir', '1', '$invoice_tipe_transaksi', '$invoice_total_beli', 0, '$invoice_total', '$invoice_ongkir', '$invoice_diskon', '$invoice_sub_total', '$invoice_bayar', '$invoice_kembali', '$kik', '$invoice_date', '$invoice_date_year_month', ' ', ' ', '$invoice_total_beli', '$invoice_total', '$invoice_ongkir', '$invoice_sub_total', '$invoice_bayar', '$invoice_kembali', '$invoice_marketplace', '$invoice_ekspedisi', '$invoice_no_resi', '-', '$invoice_piutang', '$invoice_piutang_dp', '$invoice_piutang_jatuh_tempo', '$invoice_piutang_lunas', ' ', ' ', 1, 0, 0, '$invoice_cabang')";
		// var_dump($query1); die();
		mysqli_query($conn, $query1);

	for( $x=0; $x<$jumlah; $x++ ){

		$query = "INSERT INTO keranjang_draft VALUES ('', '$keranjang_nama[$x]', '$keranjang_harga_beli[$x]', '$keranjang_harga[$x]', '$keranjang_harga_parent[$x]', '$keranjang_harga_edit[$x]', '$keranjang_satuan[$x]', '$id[$x]', '$barang_kode_slug[$x]', '$keranjang_qty[$x]', '$keranjang_qty_view[$x]', '$keranjang_konversi_isi[$x]', '$keranjang_barang_sn_id[$x]', '$keranjang_barang_option_sn[$x]', '$keranjang_sn[$x]', '$keranjang_id_kasir[$x]', '$keranjang_id_cek[$x]', '$invoice_customer_category2[$x]', 1, '$penjualan_invoice[$x]', '$penjualan_cabang[$x]')";
		mysqli_query($conn, $query);
	}
		

	mysqli_query( $conn, "DELETE FROM keranjang WHERE keranjang_id_kasir = $kik");
	return mysqli_affected_rows($conn);
}


function updateStockSaveDraft($data) {
	global $conn;
	$id                  		= $data['barang_ids'];
	$keranjang_qty       		= $data['keranjang_qty'];
	$keranjang_qty_view       	= $data['keranjang_qty_view'];
	$keranjang_konversi_isi     = $data['keranjang_konversi_isi'];
	$keranjang_satuan           = $data['keranjang_satuan'];
	$keranjang_harga_beli       = $data['keranjang_harga_beli'];
	$keranjang_harga			= $data['keranjang_harga'];
	$keranjang_harga_parent		= $data['keranjang_harga_parent'];
	$keranjang_harga_edit		= $data['keranjang_harga_edit'];
	$keranjang_id_kasir  		= $data['keranjang_id_kasir'];
	$penjualan_invoice   		= $data['penjualan_invoice'];
	$keranjang_barang_option_sn = $data['keranjang_barang_option_sn'];
	$keranjang_barang_sn_id     = $data['keranjang_barang_sn_id'];
	$keranjang_sn               = $data['keranjang_sn'];
	$invoice_customer_category2 = $data['invoice_customer_category2'];
	$penjualan_cabang        	= $data['penjualan_cabang'];

	$invoice_id 				= $data['invoice_id'];
	$kik                 		= $data['kik'];
	$penjualan_invoice2  		= $data['penjualan_invoice2'];
	$invoice_tgl         		= date("d F Y g:i:s a");
	$invoice_total_beli       	= $data['invoice_total_beli'];
	$invoice_total       		= $data['invoice_total'];
	$invoice_ongkir      		= htmlspecialchars($data['invoice_ongkir']);
	$invoice_diskon      		= htmlspecialchars($data['invoice_diskon']);
	
	$invoice_sub_total   		= $invoice_total + $invoice_ongkir;
	$invoice_sub_total   		= $invoice_sub_total - $invoice_diskon;
	$invoice_bayar       		= htmlspecialchars($data['angka1']);
	

	$invoice_kembali     		= $invoice_bayar - $invoice_sub_total;
	$invoice_date        		= date("Y-m-d");
	$invoice_date_year_month    = date("Y-m");
	$penjualan_date      		= $data['penjualan_date'];
	$invoice_customer    		= $data['invoice_customer'];
	$invoice_customer_category  = $data['invoice_customer_category'];
	$invoice_kurir    	 		= $data['invoice_kurir'];
	$invoice_tipe_transaksi  	= $data['invoice_tipe_transaksi'];
	$penjualan_invoice_count 	= $data['penjualan_invoice_count'];
	$invoice_piutang			= $data['invoice_piutang'];
	if ( $invoice_piutang == 1 ) {
		$invoice_piutang_dp = $invoice_bayar;
	} else {
		$invoice_piutang_dp = 0;
	}
	$invoice_piutang_jatuh_tempo= $data['invoice_piutang_jatuh_tempo'];
	$invoice_piutang_lunas		= $data['invoice_piutang_lunas'];
	$invoice_cabang             = $data['invoice_cabang'];
	

	if ( $invoice_customer == 1 ) {
		$invoice_marketplace = htmlspecialchars($data['invoice_marketplace']);
		$invoice_ekspedisi   = htmlspecialchars($data['invoice_ekspedisi']);
		$invoice_no_resi     = htmlspecialchars($data['invoice_no_resi']);
	} else {
		$invoice_marketplace = "";
		$invoice_ekspedisi   = 0;
		$invoice_no_resi     = "-";
	}
	$jumlah = count($keranjang_id_kasir);


	if ( $invoice_bayar == null ) {
		echo"
			<script>
				alert('Anda Belum Input Nominal BAYAR !!!');
				document.location.href = '';
			</script>
		";
	} else {
		// query Update invoice
		$query1 = "UPDATE invoice SET  
				invoice_tgl 				= '$invoice_tgl', 
				invoice_customer 			= '$invoice_customer', 
				invoice_customer_category 	= '$invoice_customer_category', 
				invoice_tipe_transaksi 		= '$invoice_tipe_transaksi', 
				invoice_total_beli 			= '$invoice_total_beli', 
				invoice_total 				= '$invoice_total', 
				invoice_ongkir 				= '$invoice_ongkir', 
				invoice_diskon 				= '$invoice_diskon', 
				invoice_sub_total 			= '$invoice_sub_total', 
				invoice_bayar 				= '$invoice_bayar', 
				invoice_kembali 			= '$invoice_kembali', 
				invoice_kasir 				= '$kik', 
				invoice_date 				= '$invoice_date', 
				invoice_date_year_month 	= '$invoice_date_year_month', 
				invoice_total_beli_lama 	= '$invoice_total_beli', 
				invoice_total_lama 			= '$invoice_total', 
				invoice_ongkir_lama 		= '$invoice_ongkir', 
				invoice_sub_total_lama 		= '$invoice_sub_total', 
				invoice_bayar_lama 			= '$invoice_bayar', 
				invoice_kembali_lama 		= '$invoice_kembali',  
				invoice_piutang 			= '$invoice_piutang', 
				invoice_piutang_dp 			= '$invoice_piutang_dp', 
				invoice_piutang_jatuh_tempo = '$invoice_piutang_jatuh_tempo', 
				invoice_piutang_lunas 		= '$invoice_piutang_lunas', 
				invoice_draft 				= 0, 
				invoice_cabang 				= '$invoice_cabang'
				WHERE invoice_id 			= $invoice_id
		";
		// var_dump($query1); die();
		mysqli_query($conn, $query1);

		for( $x=0; $x<$jumlah; $x++ ){
			$query = "INSERT INTO penjualan VALUES ('', '$id[$x]', '$id[$x]', '$keranjang_qty_view[$x]', '$keranjang_qty[$x]', '$keranjang_konversi_isi[$x]', '$keranjang_satuan[$x]','$keranjang_harga_beli[$x]', '$keranjang_harga[$x]', '$keranjang_harga_parent[$x]', '$keranjang_harga_edit[$x]', '$keranjang_id_kasir[$x]', '$penjualan_invoice[$x]' , '$penjualan_date[$x]', '$invoice_date_year_month', '$keranjang_qty_view[$x]', '$keranjang_qty_view[$x]', '$keranjang_barang_option_sn[$x]', '$keranjang_barang_sn_id[$x]', '$keranjang_sn[$x]', '$invoice_customer_category2[$x]', '$penjualan_cabang[$x]')";
			$query2 = "INSERT INTO terlaris VALUES ('', '$id[$x]', '$keranjang_qty[$x]')";
			// var_dump($query); die();
			mysqli_query($conn, $query);
			mysqli_query($conn, $query2);
		}
		

		mysqli_query( $conn, "DELETE FROM keranjang_draft WHERE keranjang_invoice = $penjualan_invoice2 && keranjang_cabang = $invoice_cabang ");
		return mysqli_affected_rows($conn);
	}
}

function hapusDraft($invoice, $cabang, $page) {
	global $conn;

	if ( $page === "nonfisik" ) :
		$countDraft = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM keranjang_non_fisik_draft WHERE knfd_invoice = $invoice && knfd_cabang = $cabang"));

		if ( $countDraft > 0 ) {
			mysqli_query( $conn, "DELETE FROM invoice WHERE penjualan_invoice = $invoice && invoice_cabang = $cabang");

			mysqli_query( $conn, "DELETE FROM keranjang_non_fisik_draft WHERE knfd_invoice = $invoice && knfd_cabang = $cabang");
			return mysqli_affected_rows($conn);
		} else {
			mysqli_query( $conn, "DELETE FROM invoice WHERE penjualan_invoice = $invoice && invoice_cabang = $cabang");
			return mysqli_affected_rows($conn);
		}	

	else :
		$countDraft = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM keranjang_draft WHERE keranjang_invoice = $invoice && keranjang_cabang = $cabang"));

		if ( $countDraft > 0 ) {
			mysqli_query( $conn, "DELETE FROM invoice WHERE penjualan_invoice = $invoice && invoice_cabang = $cabang");

			mysqli_query( $conn, "DELETE FROM keranjang_draft WHERE keranjang_invoice = $invoice && keranjang_cabang = $cabang");
			return mysqli_affected_rows($conn);
		} else {
			mysqli_query( $conn, "DELETE FROM invoice WHERE penjualan_invoice = $invoice && invoice_cabang = $cabang");
			return mysqli_affected_rows($conn);
		}	
	endif;
}

// =========================================== CUSTOMER ====================================== //
 
function tambahCustomer($data) {
	global $conn;
	// ambil data dari tiap elemen dalam form
	$customer_nama     = htmlspecialchars($data["customer_nama"]);
	$customer_tlpn     = htmlspecialchars($data["customer_tlpn"]);
	$customer_email    = htmlspecialchars($data["customer_email"]);
	$customer_alamat   = htmlspecialchars($data["customer_alamat"]);
	$customer_create   = date("d F Y g:i:s a");
	$customer_status   = htmlspecialchars($data["customer_status"]);
	$customer_category = $data["customer_category"];
	$customer_cabang   = htmlspecialchars($data["customer_cabang"]);

	// Cek Email
	$customer_tlpn_cek = mysqli_num_rows(mysqli_query($conn, "select * from customer where customer_tlpn = '$customer_tlpn' "));

	if ( $customer_tlpn_cek > 0 ) {
		echo "
			<script>
				alert('Customer Sudah Terdaftar');
			</script>
		";
	} else {
		// query insert data
		$query = "INSERT INTO customer VALUES ('', '$customer_nama', '$customer_tlpn', '$customer_email', '$customer_alamat', '$customer_create', '$customer_status', '$customer_category', '$customer_cabang')";
		mysqli_query($conn, $query);

		return mysqli_affected_rows($conn);
	}
}

function editCustomer($data){
	global $conn;
	$id = $data["customer_id"];


	// ambil data dari tiap elemen dalam form
	$customer_nama     = htmlspecialchars($data["customer_nama"]);
	$customer_tlpn     = htmlspecialchars($data["customer_tlpn"]);
	$customer_email    = htmlspecialchars($data["customer_email"]);
	$customer_alamat   = htmlspecialchars($data["customer_alamat"]);
	$customer_status   = htmlspecialchars($data["customer_status"]);
	$customer_category = $data["customer_category"];

		// query update data
		$query = "UPDATE customer SET 
						customer_nama     = '$customer_nama',
						customer_tlpn     = '$customer_tlpn',
						customer_email    = '$customer_email',
						customer_alamat   = '$customer_alamat',
						customer_status   = '$customer_status',
						customer_category = '$customer_category'
						WHERE customer_id = $id
				";
		// var_dump($query); die();
		mysqli_query($conn, $query);

		return mysqli_affected_rows($conn);

}


function hapusCustomer($id) {
	global $conn;
	mysqli_query( $conn, "DELETE FROM customer WHERE customer_id = $id");

	return mysqli_affected_rows($conn);
}


// =========================================== Panjualan ===================================== //
function hapusPenjualan($id) {
	global $conn;
    
	mysqli_query( $conn, "DELETE FROM penjualan WHERE penjualan_id = $id");

	return mysqli_affected_rows($conn);
}

function hapusPenjualanInvoice($id) {
	global $conn;

	// Mencari Invoive Penjualan dan cabang
	$invoiceTbl = mysqli_query( $conn, "select penjualan_invoice, invoice_tipe_non_fisik, invoice_cabang from invoice where invoice_id = '".$id."'");

    $ivc = mysqli_fetch_array($invoiceTbl); 
    $penjualan_invoice  	= $ivc["penjualan_invoice"];
    $invoice_tipe_non_fisik = $ivc["invoice_tipe_non_fisik"];
    $invoice_cabang  		= $ivc["invoice_cabang"];


	// Mencari banyak barang SN
	$barang_option_sn = mysqli_query( $conn, "select barang_option_sn from penjualan where penjualan_invoice = '".$penjualan_invoice."' && barang_option_sn > 0 && penjualan_cabang = '".$invoice_cabang."' ");
	$barang_option_sn = mysqli_num_rows($barang_option_sn);

	// Menghitung data di tabel piutang sesuai No. Invoice
	$piutang = mysqli_query($conn,"select * from piutang where piutang_invoice = '".$penjualan_invoice."' && piutang_cabang = '".$invoice_cabang."' ");
    $jmlPiutang = mysqli_num_rows($piutang);

    
	// Mencari ID SN
	if ( $barang_option_sn > 0 ) {
		$barang_sn_id = query("SELECT * FROM penjualan WHERE penjualan_invoice = $penjualan_invoice && barang_option_sn > 0 && penjualan_cabang = $invoice_cabang ");

		foreach ( $barang_sn_id as $row ) :
		 	$barang_sn_id = $row['barang_sn_id'];

		 	$barang = count($barang_sn_id);
		 	for ( $i = 0; $i < $barang; $i++ ) {
		 		$query = "UPDATE barang_sn SET 
						barang_sn_status     = 3
						WHERE barang_sn_id = $barang_sn_id
				";
		 	}
		 	mysqli_query($conn, $query);
		endforeach;
	}

	// Kondisi Hapus jika terdapat cicilan di tabel Piutang
	if ( $jmlPiutang > 0 ) {
		mysqli_query( $conn, "DELETE FROM piutang WHERE piutang_invoice = $penjualan_invoice && piutang_cabang = $invoice_cabang ");

		if ( $invoice_tipe_non_fisik < 1 ) :
			mysqli_query( $conn, "DELETE FROM penjualan WHERE penjualan_invoice = $penjualan_invoice && penjualan_cabang = $invoice_cabang ");
		else :
			mysqli_query( $conn, "DELETE FROM penjualan_barang_non_fisik WHERE pbnf_invoice = '".$penjualan_invoice."' && pbnf_cabang = $invoice_cabang ");
		endif;

		mysqli_query( $conn, "DELETE FROM invoice WHERE invoice_id = $id");
	} else {
	// Kondisi Hapus jika Tanpa cicilan di tabel Piutang
		if ( $invoice_tipe_non_fisik < 1 ) :
			mysqli_query( $conn, "DELETE FROM penjualan WHERE penjualan_invoice = $penjualan_invoice && penjualan_cabang = $invoice_cabang ");
		else :
			mysqli_query( $conn, "DELETE FROM penjualan_barang_non_fisik WHERE pbnf_invoice = '".$penjualan_invoice."' && pbnf_cabang = $invoice_cabang ");
		endif;

		mysqli_query( $conn, "DELETE FROM invoice WHERE invoice_id = $id");
	}



	return mysqli_affected_rows($conn);
}

function hapusPenjualanInvoiceTarikTunai($id) {
	global $conn;

	// Mencari Invoive Penjualan dan cabang
	$invoiceTbl = mysqli_query( $conn, "select penjualan_invoice, invoice_tipe_non_fisik, invoice_cabang from invoice where invoice_id = '".$id."'");

    $ivc = mysqli_fetch_array($invoiceTbl); 
    $penjualan_invoice  	= $ivc["penjualan_invoice"];
    $invoice_tipe_non_fisik = $ivc["invoice_tipe_non_fisik"];
    $invoice_cabang  		= $ivc["invoice_cabang"];


	// Menghitung data di tabel piutang sesuai No. Invoice
	$piutang = mysqli_query($conn,"select * from piutang where piutang_invoice = '".$penjualan_invoice."' && piutang_cabang = '".$invoice_cabang."' ");
    $jmlPiutang = mysqli_num_rows($piutang);


    // Mengembalikan saldo dari transaksi tarik tunai dengan cara mengurangi dalso sebelumnya
    $tarikTunai = mysqli_query($conn, "SELECT pbnf_provider, pbnf_harga_beli FROM penjualan_barang_non_fisik WHERE pbnf_invoice = '".$penjualan_invoice."' && pbnf_cabang = $invoice_cabang ");
    $tarikTunai = mysqli_fetch_array($tarikTunai);
    $pbnf_provider = $tarikTunai['pbnf_provider'];
    $pbnf_harga_beli = $tarikTunai['pbnf_harga_beli'];

    // Mencari Saldo saat ini
    $saldoSaatIni = mysqli_query($conn, "SELECT provider_saldo FROM provider WHERE provider_id = $pbnf_provider ");
    $saldoSaatIni = mysqli_fetch_array($saldoSaatIni);
    $provider_saldo = $saldoSaatIni['provider_saldo'];

    $provider_saldo -= $pbnf_harga_beli;

    	$query = "UPDATE provider SET 
					provider_saldo 		 = '$provider_saldo'
					WHERE provider_id    = $pbnf_provider
					";
		mysqli_query($conn, $query);


	// Kondisi Hapus jika terdapat cicilan di tabel Piutang
	if ( $jmlPiutang > 0 ) {
		mysqli_query( $conn, "DELETE FROM piutang WHERE piutang_invoice = $penjualan_invoice && piutang_cabang = $invoice_cabang ");

		if ( $invoice_tipe_non_fisik < 1 ) :
			mysqli_query( $conn, "DELETE FROM penjualan WHERE penjualan_invoice = $penjualan_invoice && penjualan_cabang = $invoice_cabang ");
		else :
			mysqli_query( $conn, "DELETE FROM penjualan_barang_non_fisik WHERE pbnf_invoice = '".$penjualan_invoice."' && pbnf_cabang = $invoice_cabang ");
		endif;

		mysqli_query( $conn, "DELETE FROM invoice WHERE invoice_id = $id");
	} else {
	// Kondisi Hapus jika Tanpa cicilan di tabel Piutang
		if ( $invoice_tipe_non_fisik < 1 ) :
			mysqli_query( $conn, "DELETE FROM penjualan WHERE penjualan_invoice = $penjualan_invoice && penjualan_cabang = $invoice_cabang ");
		else :
			mysqli_query( $conn, "DELETE FROM penjualan_barang_non_fisik WHERE pbnf_invoice = '".$penjualan_invoice."' && pbnf_cabang = $invoice_cabang ");
		endif;

		mysqli_query( $conn, "DELETE FROM invoice WHERE invoice_id = $id");
	}



	return mysqli_affected_rows($conn);
}

function hapusPenjualanInvoiceNonFisik($id) {
	global $conn;

	// Mencari Invoive Penjualan dan cabang
	$invoiceTbl = mysqli_query( $conn, "select penjualan_invoice, invoice_tipe_non_fisik, invoice_cabang from invoice where invoice_id = '".$id."'");

    $ivc = mysqli_fetch_array($invoiceTbl); 
    $penjualan_invoice  	= $ivc["penjualan_invoice"];
    $invoice_tipe_non_fisik = $ivc["invoice_tipe_non_fisik"];
    $invoice_cabang  		= $ivc["invoice_cabang"];


	// Menghitung data di tabel piutang sesuai No. Invoice
	$piutang = mysqli_query($conn,"select * from piutang where piutang_invoice = '".$penjualan_invoice."' && piutang_cabang = '".$invoice_cabang."' ");
    $jmlPiutang = mysqli_num_rows($piutang);


    // Mengembalikan saldo dari transaksi tarik tunai dengan cara menambah saldo sebelumnya
    $tarikTunai = query("SELECT * FROM penjualan_barang_non_fisik WHERE pbnf_invoice = '".$penjualan_invoice."' && pbnf_cabang = $invoice_cabang");

    foreach ( $tarikTunai as $row ) :
    	$pbnf_provider = $row['pbnf_provider'];
    	$pbnf_harga_beli = $row['pbnf_harga_beli'];

    	$saldoSaatIni = mysqli_query($conn, "SELECT provider_saldo FROM provider WHERE provider_id = $pbnf_provider ");
    	$saldoSaatIni = mysqli_fetch_array($saldoSaatIni);
    	$provider_saldo = $saldoSaatIni['provider_saldo'];

    	$provider_saldo += $pbnf_harga_beli;

    	// Edit Data
			$query = "UPDATE provider SET 
					provider_saldo 		 = '$provider_saldo'
					WHERE provider_id    = $pbnf_provider
					";
			mysqli_query($conn, $query);
    endforeach;

	// Kondisi Hapus jika terdapat cicilan di tabel Piutang
	if ( $jmlPiutang > 0 ) {
		mysqli_query( $conn, "DELETE FROM piutang WHERE piutang_invoice = $penjualan_invoice && piutang_cabang = $invoice_cabang ");

		if ( $invoice_tipe_non_fisik < 1 ) :
			mysqli_query( $conn, "DELETE FROM penjualan WHERE penjualan_invoice = $penjualan_invoice && penjualan_cabang = $invoice_cabang ");
		else :
			mysqli_query( $conn, "DELETE FROM penjualan_barang_non_fisik WHERE pbnf_invoice = '".$penjualan_invoice."' && pbnf_cabang = $invoice_cabang ");
		endif;

		mysqli_query( $conn, "DELETE FROM invoice WHERE invoice_id = $id");
	} else {
	// Kondisi Hapus jika Tanpa cicilan di tabel Piutang
		if ( $invoice_tipe_non_fisik < 1 ) :
			mysqli_query( $conn, "DELETE FROM penjualan WHERE penjualan_invoice = $penjualan_invoice && penjualan_cabang = $invoice_cabang ");
		else :
			mysqli_query( $conn, "DELETE FROM penjualan_barang_non_fisik WHERE pbnf_invoice = '".$penjualan_invoice."' && pbnf_cabang = $invoice_cabang ");
		endif;

		mysqli_query( $conn, "DELETE FROM invoice WHERE invoice_id = $id");
	}



	return mysqli_affected_rows($conn);
}

function updateQTY2($data) {
	global $conn;
	$id = $data["penjualan_id"];
	$bid = $data["barang_id"];

	// ambil data dari tiap elemen dalam form
	$barang_qty      			= htmlspecialchars($data['barang_qty']);
	$barang_qty_lama 			= $data['barang_qty_lama'];
	$barang_terjual  			= $data['barang_terjual'];
	$barang_qty_konversi_isi 	= $data['barang_qty_konversi_isi'];

	// Edit No SN Jika Produk Menggunakan SN
	$barang_option_sn 			= $data['barang_option_sn'];
	$barang_sn_id     			= $data['barang_sn_id'];

	// retur
	$barang_stock           	= $data['barang_stock'];
	$barang_stock_kurang    	= $barang_qty_lama - $barang_qty;
	$barang_stock_kurang       *= $barang_qty_konversi_isi;

	$barang_stock_hasil     	= $barang_stock + $barang_stock_kurang;
	$barang_terjual         	= $barang_terjual - $barang_stock_kurang;
	// var_dump($barang_stock_hasil); die();

	if ( $barang_qty > $barang_qty_lama ) {
		echo"
			<script>
				alert('Jika Anda Ingin Menambahkan QTY Barang.. Lakukan Transaksi Invoice Baru !!!');
			</script>
		";
	} else {
		// query update data

		$query = "UPDATE penjualan SET 
					barang_qty       = '$barang_qty'
					WHERE penjualan_id = $id
					";
		$query1 = "UPDATE barang SET 
					barang_stock   = '$barang_stock_hasil',
					barang_terjual = '$barang_terjual'
					WHERE barang_id = $bid
					";
		if ( $barang_option_sn > 0 ) {
			$query2 = "UPDATE barang_sn SET 
					barang_sn_status = 2
					WHERE barang_sn_id = $barang_sn_id
				";
			mysqli_query($conn, $query2);
		}

		mysqli_query($conn, $query);
		mysqli_query($conn, $query1);
		
		return mysqli_affected_rows($conn);
		// $query1 = "INSERT INTO retur VALUES ('', '$retur_barang_id', '$retur_invoice', '$retur_admin_id', '$retur_date', ' ', '$barang_stock')";
		// mysqli_query($conn, $query1);
		
	} 
}

function updateInvoice($data) {
	global $conn;
	$id = $data["invoice_id"];

	// ambil data dari tiap elemen dalam form
	$invoice_total_beli   = htmlspecialchars($data['invoice_total_beli']);
	$invoice_total        = htmlspecialchars($data['invoice_total']);
	$invoice_ongkir       = $data['invoice_ongkir'];
	$invoice_sub_total    = $data['invoice_sub_total'];
	$invoice_bayar        = htmlspecialchars($data['angka1']);
	$invoice_kembali      = $invoice_bayar - $invoice_sub_total;
	$invoice_kasir_edit   = $data['invoice_kasir_edit'];
	$invoice_date_edit    = date('Y-m-d');

		// query update data
		$query = "UPDATE invoice SET 
					invoice_total_beli = '$invoice_total_beli',
					invoice_total      = '$invoice_total',
					invoice_ongkir     = '$invoice_ongkir',
					invoice_sub_total  = '$invoice_sub_total',
					invoice_bayar      = '$invoice_bayar',
					invoice_kembali    = '$invoice_kembali',
					invoice_date_edit  = '$invoice_date_edit',
					invoice_kasir_edit = '$invoice_kasir_edit'
					WHERE invoice_id = $id
					";
		mysqli_query($conn, $query);
		return mysqli_affected_rows($conn);
}

function editInvoiceEkspedisi($data) {
	global $conn;
	$id = $data["invoice_id"];

	// ambil data dari tiap elemen dalam form
	$invoice_marketplace        = htmlspecialchars($data['invoice_marketplace']);
	$invoice_ekspedisi          = htmlspecialchars($data['invoice_ekspedisi']);
	$invoice_no_resi            = htmlspecialchars($data['invoice_no_resi']);
	$invoice_total              = $data['invoice_total'];
	$invoice_ongkir             = htmlspecialchars($data['invoice_ongkir']);
	$invoice_sub_total          = $invoice_total + $invoice_ongkir;
	$invoice_bayar              = $data['invoice_bayar'];
	$invoice_kembali            = $invoice_bayar - $invoice_sub_total;

		// query update data
		$query = "UPDATE invoice SET 
					invoice_total          = '$invoice_total',
					invoice_ongkir         = '$invoice_ongkir',
					invoice_sub_total      = '$invoice_sub_total',
					invoice_bayar          = '$invoice_bayar',
					invoice_kembali        = '$invoice_kembali',
					invoice_marketplace    = '$invoice_marketplace',
					invoice_ekspedisi      = '$invoice_ekspedisi',
					invoice_no_resi        = '$invoice_no_resi'
					WHERE invoice_id = $id
					";
		mysqli_query($conn, $query);
		return mysqli_affected_rows($conn);
}

function editInvoiceKurir($data) {
	global $conn;
	$id = $data["invoice_id"];

	// ambil data dari tiap elemen dalam form
	$invoice_total              = $data['invoice_total'];
	$invoice_ongkir             = htmlspecialchars($data['invoice_ongkir']);
	$invoice_sub_total          = $invoice_total + $invoice_ongkir - $data['invoice_diskon'];
	$invoice_bayar              = $data['invoice_bayar'];
	$invoice_kembali            = $invoice_bayar - $invoice_sub_total;
	$invoice_kurir              = htmlspecialchars($data['invoice_kurir']);
	$invoice_status_kurir       = htmlspecialchars($data['invoice_status_kurir']);

		// query update data
		$query = "UPDATE invoice SET 
					invoice_kurir 		   = '$invoice_kurir',
					invoice_status_kurir   = '$invoice_status_kurir',
					invoice_total          = '$invoice_total',
					invoice_ongkir         = '$invoice_ongkir',
					invoice_sub_total      = '$invoice_sub_total',
					invoice_bayar          = '$invoice_bayar',
					invoice_kembali        = '$invoice_kembali'
					WHERE invoice_id = $id
					";
		mysqli_query($conn, $query);
		return mysqli_affected_rows($conn);
}

// ============================================ Supplier ====================================== // 
function tambahSupplier($data) {
	global $conn;
	// ambil data dari tiap elemen dalam form
	$supplier_nama      = htmlspecialchars($data["supplier_nama"]);
	$supplier_wa 		= htmlspecialchars($data["supplier_wa"]);
	$supplier_alamat    = htmlspecialchars($data["supplier_alamat"]);
	$supplier_company   = htmlspecialchars($data["supplier_company"]);
	$supplier_status    = htmlspecialchars($data["supplier_status"]);
	$supplier_create    = date("d F Y g:i:s a");
	$supplier_cabang    = htmlspecialchars($data["supplier_cabang"]);

	// Cek Email
	$supplier_wa_cek = mysqli_num_rows(mysqli_query($conn, "select * from supplier where supplier_wa = '$supplier_wa' "));

	if ( $supplier_wa_cek > 0 ) {
		echo "
			<script>
				alert('No. WhatsApp Sudah Terdaftar');
			</script>
		";
	} else {
		// query insert data
		$query = "INSERT INTO supplier VALUES ('', '$supplier_nama', '$supplier_wa', '$supplier_alamat', '$supplier_company', '$supplier_status', '$supplier_create', '$supplier_cabang')";
		mysqli_query($conn, $query);

		return mysqli_affected_rows($conn);
	}
}

function editSupplier($data){
	global $conn;
	$id = $data["supplier_id"];


	// ambil data dari tiap elemen dalam form
	$supplier_nama      = htmlspecialchars($data["supplier_nama"]);
	$supplier_wa 		= htmlspecialchars($data["supplier_wa"]);
	$supplier_alamat    = htmlspecialchars($data["supplier_alamat"]);
	$supplier_company   = htmlspecialchars($data["supplier_company"]);
	$supplier_status    = htmlspecialchars($data["supplier_status"]);

		// query update data
		$query = "UPDATE supplier SET 
						supplier_nama      = '$supplier_nama',
						supplier_wa        = '$supplier_wa',
						supplier_alamat    = '$supplier_alamat',
						supplier_company   = '$supplier_company',
						supplier_status    = '$supplier_status'
						WHERE supplier_id  = $id
				";
		// var_dump($query); die();
		mysqli_query($conn, $query);

		return mysqli_affected_rows($conn);

}

function hapusSupplier($id) {
	global $conn;
	mysqli_query( $conn, "DELETE FROM supplier WHERE supplier_id = $id");

	return mysqli_affected_rows($conn);
}

// ===================================== Keranjang Pembelian =============================== //
function tambahKeranjangPembelian($barang_id, $keranjang_nama, $keranjang_harga, $keranjang_id_kasir, $keranjang_qty, $keranjang_cabang, $keranjang_id_cek) {
	global $conn;
	
	// Cek STOCK
	$barang_id_cek = mysqli_num_rows(mysqli_query($conn, "select * from keranjang_pembelian where keranjang_id_cek = '$keranjang_id_cek' "));
	
	// Kondisi jika scan Barcode Tidak sesuai
	if ( $barang_id != null ) {
		if ( $barang_id_cek > 0 ) {
			$keranjangParent = mysqli_query( $conn, "select keranjang_qty from keranjang_pembelian where keranjang_id_cek = '".$keranjang_id_cek."'");
		    $kp = mysqli_fetch_array($keranjangParent); 
		    $kp = $kp['keranjang_qty'];
		    $kp += $keranjang_qty;

		    $query = "UPDATE keranjang_pembelian SET 
							keranjang_qty   = '$kp'
							WHERE keranjang_id_cek = $keranjang_id_cek
							";
			mysqli_query($conn, $query);
			
			return mysqli_affected_rows($conn);
		} else {
			// query insert data
			$query = "INSERT INTO keranjang_pembelian (keranjang_nama, keranjang_harga, barang_id, keranjang_qty, keranjang_id_kasir, keranjang_id_cek, keranjang_cabang) VALUES ('$keranjang_nama', '$keranjang_harga', '$barang_id', '$keranjang_qty', '$keranjang_id_kasir', '$keranjang_id_cek', '$keranjang_cabang')";
			
			mysqli_query($conn, $query);

			return mysqli_affected_rows($conn);
		}
	} else {
		echo '
			<script>
				alert("Kode Produk Tidak ada di Data Master Barang dan Coba Cek Kembali !! ");
				document.location.href = "transaksi-pembelian";
			</script>
		';
	}
}

function tambahKeranjangPembelianBarcode($data) {
	global $conn;
	$barang_kode 		= htmlspecialchars($data['inputbarcode']);
	$keranjang_id_kasir = $data['keranjang_id_kasir'];
	$keranjang_cabang   = $data['keranjang_cabang'];

	// Ambil Data Barang berdasarkan Kode Barang 
	$barang 	= mysqli_query( $conn, "select barang_id, barang_nama from barang where barang_kode = '".$barang_kode."' && barang_cabang = '".$keranjang_cabang."' ");
    $br 		= mysqli_fetch_array($barang);

    $barang_id          = $br['barang_id'];
	$keranjang_nama     = $br['barang_nama'];
	$keranjang_harga    = 0;
	$keranjang_qty      = 1;
	$keranjang_id_cek   = $barang_id.$keranjang_id_kasir.$keranjang_cabang;

	// Cek STOCK
	$barang_id_cek = mysqli_num_rows(mysqli_query($conn, "select * from keranjang_pembelian where keranjang_id_cek = '$keranjang_id_cek' "));
	
	// Kondisi jika scan Barcode Tidak sesuai
	if ( $barang_id != null ) {
		if ( $barang_id_cek > 0 ) {
			$keranjangParent = mysqli_query( $conn, "select keranjang_qty from keranjang_pembelian where keranjang_id_cek = '".$keranjang_id_cek."'");
		    $kp = mysqli_fetch_array($keranjangParent); 
		    $kp = $kp['keranjang_qty'];
		    $kp += $keranjang_qty;

		    $query = "UPDATE keranjang_pembelian SET 
							keranjang_qty   = '$kp'
							WHERE keranjang_id_cek = $keranjang_id_cek
							";
			mysqli_query($conn, $query);
			return mysqli_affected_rows($conn);

		} else {
			// query insert data
			$query = "INSERT INTO keranjang_pembelian VALUES ('', '$keranjang_nama', '$keranjang_harga', '$barang_id', '$keranjang_qty', '$keranjang_id_kasir', '$keranjang_id_cek', '$keranjang_cabang')";
			
			mysqli_query($conn, $query);

			return mysqli_affected_rows($conn);
		}
	} else {
		echo '
			<script>
				alert("Kode Produk Tidak ada di Data Master Barang dan Coba Cek Kembali !! ");
				document.location.href = "transaksi-pembelian";
			</script>
		';
	}

}

function hapusKeranjangPembelian($id) {
	global $conn;

	mysqli_query( $conn, "DELETE FROM keranjang_pembelian WHERE keranjang_id = $id");

	return mysqli_affected_rows($conn);
}

function updateQTYpembelian($data) {
	global $conn;
	$id = $data["keranjang_id"];

	// ambil data dari tiap elemen dalam form
	$keranjang_qty = htmlspecialchars($data['keranjang_qty']);
	$stock_brg = $data['stock_brg'];


	// query update data
	$query = "UPDATE keranjang_pembelian SET 
				keranjang_qty   = '$keranjang_qty'
				WHERE keranjang_id = $id
			";
	mysqli_query($conn, $query);
	return mysqli_affected_rows($conn);
	
}

// ============================================== Transaksi Pembelian ======================== //
function updateStockPembelian($data) {
	global $conn;
	$barangIds                = $data["barang_ids"];
	$keranjangQtys            = $data["keranjang_qty"];
	$keranjangIdKasirs        = $data['keranjang_id_kasir'];
	$pembelianInvoices        = $data['pembelian_invoice'];
	$kiks                     = $data['kik'];
	$barangHargaBelis         = $data['barang_harga_beli'];
	$pembelianInvoiceParents  = $data['pembelian_invoice_parent'];
	$invoicePembelianCabangs  = $data['invoice_pembelian_cabang'];

	$pembelianInvoice2        = $data['pembelian_invoice2'];
	$invoiceTgl               = date("d F Y g:i:s a");
	$invoiceSuppliers         = $data['invoice_supplier'];
	$invoiceTotals            = $data['invoice_total'];
	$invoiceBayars            = $data['angka1'];
	$invoiceKembalis          = $invoiceBayars - $invoiceTotals;
	$invoiceDates             = date("Y-m-d");
	$pembelianDates           = $data['pembelian_date'];
	$invoicePembelianNumberDeletes = $data['invoice_pembelian_number_delete'];
	$pembelianInvoiceParent2s = $data['pembelian_invoice_parent2'];
	$invoiceHutangs           = $data['invoice_hutang'];
	if ( $invoiceHutangs == 1 ) {
		$invoiceHutangDps = $invoiceBayars;
	} else {
		$invoiceHutangDps = 0;
	}
	$invoiceHutangJatuhTempos = $data['invoice_hutang_jatuh_tempo'];
	$invoiceHutangLunases     = $data['invoice_hutang_lunas'];
	$pembelianCabangs         = $data['pembelian_cabang'];

	$jumlah = count($keranjangIdKasirs);

	// Cek No. Invoice
	$invoiceCek = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM invoice_pembelian WHERE pembelian_invoice = '$pembelianInvoice2' AND invoice_pembelian_cabang = '$invoicePembelianCabangs'"));

	if ( $invoiceCek > 0 ) {
		echo "
			<script>
				alert('No. Invoice Pembelian Sudah Digunakan Sebelumnya !!');
			</script>
		";
	} else {
		// Query insert invoice
		$query1 = "INSERT INTO invoice_pembelian (pembelian_invoice, pembelian_invoice_parent, invoice_tgl, invoice_supplier, invoice_total, invoice_bayar, invoice_kembali, invoice_kasir, invoice_date, invoice_date_edit, invoice_kasir_edit, invoice_total_lama, invoice_bayar_lama, invoice_kembali_lama, invoice_hutang, invoice_hutang_dp, invoice_hutang_jatuh_tempo, invoice_hutang_lunas, invoice_pembelian_cabang) VALUES ('$pembelianInvoice2', '$pembelianInvoiceParent2s', '$invoiceTgl', '$invoiceSuppliers', '$invoiceTotals', '$invoiceBayars', '$invoiceKembalis', '$kiks', '$invoiceDates', '', '', '$invoiceTotals', '$invoiceBayars', '$invoiceKembalis', '$invoiceHutangs', '$invoiceHutangDps', '$invoiceHutangJatuhTempos', '$invoiceHutangLunases', '$invoicePembelianCabangs')";
		mysqli_query($conn, $query1);

		// Array untuk mengakumulasi qty dan data lain per barang_id unik
		$aggregatedData = [];
		for ($index = 0; $index < $jumlah; $index++) {
			$barangId = $barangIds[$index];
			if (!isset($aggregatedData[$barangId])) {
				$aggregatedData[$barangId] = [
					'qty' => 0,
					'keranjang_id_kasir' => $keranjangIdKasirs[$index],
					'pembelian_invoice' => $pembelianInvoices[$index],
					'pembelian_invoice_parent' => $pembelianInvoiceParents[$index],
					'pembelian_date' => $pembelianDates[$index],
					'barang_harga_beli' => $barangHargaBelis[$index],
					'pembelian_cabang' => $pembelianCabangs[$index],
					'inserts' => []
				];
			}
			$aggregatedData[$barangId]['qty'] += $keranjangQtys[$index];
			$aggregatedData[$barangId]['inserts'][] = [
				'qty' => $keranjangQtys[$index],
				'keranjang_id_kasir' => $keranjangIdKasirs[$index],
				'pembelian_invoice' => $pembelianInvoices[$index],
				'pembelian_invoice_parent' => $pembelianInvoiceParents[$index],
				'pembelian_date' => $pembelianDates[$index],
				'barang_harga_beli' => $barangHargaBelis[$index],
				'pembelian_cabang' => $pembelianCabangs[$index]
			];
		}

		// Insert ke tabel pembelian
		foreach ($aggregatedData as $barangId => $agg) {
			foreach ($agg['inserts'] as $insertData) {
				$query = "INSERT INTO pembelian (pembelian_barang_id, barang_id, barang_qty, keranjang_id_kasir, pembelian_invoice, pembelian_invoice_parent, pembelian_date, barang_qty_lama, barang_qty_lama_parent, barang_harga_beli, pembelian_cabang) VALUES ('$barangId', '$barangId', '{$insertData['qty']}', '{$insertData['keranjang_id_kasir']}', '{$insertData['pembelian_invoice']}', '{$insertData['pembelian_invoice_parent']}', '{$insertData['pembelian_date']}', '{$insertData['qty']}', '{$insertData['qty']}', '{$insertData['barang_harga_beli']}', '{$insertData['pembelian_cabang']}')";
				mysqli_query($conn, $query);
			}
		}

		// Update tabel barang setelah looping selesai
		foreach ($aggregatedData as $barangId => $agg) {
			// Get Data Barang
			$QueryBarang = "SELECT * FROM barang WHERE barang_id = $barangId";
			$QueryBarang = mysqli_query($conn, $QueryBarang);
			$getBarang = mysqli_fetch_assoc($QueryBarang);

			$oldStock = $getBarang['barang_stock'];
			$oldHPP = ( $getBarang['hpp'] == null ) ? 0 : $getBarang['hpp'];
			$oldBarangHargaBeli = ( $getBarang['barang_harga_beli'] == null ) ? 0 : $getBarang['barang_harga_beli'];

			$newStockBarang = $oldStock + $agg['qty'];
			$newHPP = ($oldBarangHargaBeli + ($agg['qty'] * $agg['barang_harga_beli'])) / $newStockBarang;

			// Update Stock
			$QueryUpdateStock = "UPDATE barang SET 
									barang_stock = $newStockBarang, 
									hpp = $newHPP,
									barang_harga_beli = ($oldBarangHargaBeli + ({$agg['qty']} * {$agg['barang_harga_beli']})), 
									barang_harga = {$agg['barang_harga_beli']}
								WHERE barang_id = $barangId";
			mysqli_query($conn, $QueryUpdateStock);
		}

		mysqli_query($conn, "DELETE FROM keranjang_pembelian WHERE keranjang_id_kasir = $kiks");
		mysqli_query($conn, "DELETE FROM invoice_pembelian_number WHERE invoice_pembelian_number_delete = $invoicePembelianNumberDeletes");
		return mysqli_affected_rows($conn);
	}
}

// ======================================== Pembelian Edit ================================ //
function updateQTY2pembelian($data) {
	global $conn;
	$id = $data["pembelian_id"];
	$bid = $data["barang_id"];

	// ambil data dari tiap elemen dalam form
	$barang_qty      = htmlspecialchars($data['barang_qty']);
	$barang_qty_lama = $data['barang_qty_lama'];

	// retur
	$barang_stock           = $data['barang_stock'];
	$barang_stock_kurang    = $barang_qty_lama - $barang_qty;
	$barang_stock_hasil     = $barang_stock - $barang_stock_kurang;
	// var_dump($barang_stock_hasil); die();

	if ( $barang_qty > $barang_qty_lama ) {
		echo"
			<script>
				alert('Jika Anda Ingin Menambahkan QTY Barang.. Lakukan Transaksi Invoice Baru !!!');
			</script>
		";
	} else {
		// query update data
		$query = "UPDATE pembelian SET 
					barang_qty       = '$barang_qty'
					WHERE pembelian_id = $id
					";
		$query1 = "UPDATE barang SET 
					barang_stock   = '$barang_stock_hasil'
					WHERE barang_id = $bid
					";
		mysqli_query($conn, $query1);
		mysqli_query($conn, $query);
		return mysqli_affected_rows($conn);
		// $query1 = "INSERT INTO retur VALUES ('', '$retur_barang_id', '$retur_invoice', '$retur_admin_id', '$retur_date', ' ', '$barang_stock')";
		// mysqli_query($conn, $query1);
		
	} 
}

function updateInvoicePembelian($data) {
	global $conn;
	$id = $data["invoice_pembelian_id"];

	// ambil data dari tiap elemen dalam form
	$invoice_total        = htmlspecialchars($data['invoice_total']);
	$invoice_bayar        = htmlspecialchars($data['angka1']);
	$invoice_kembali      = $invoice_bayar - $invoice_total;
	$invoice_kasir_edit   = $data['invoice_kasir_edit'];
	$invoice_date_edit    = date('Y-m-d');

		// query update data
		$query = "UPDATE invoice_pembelian SET 
					invoice_total      = '$invoice_total',
					invoice_bayar      = '$invoice_bayar',
					invoice_kembali    = '$invoice_kembali',
					invoice_date_edit  = '$invoice_date_edit',
					invoice_kasir_edit = '$invoice_kasir_edit'
					WHERE invoice_pembelian_id = $id
					";
		mysqli_query($conn, $query);
		return mysqli_affected_rows($conn);
}

function hapusPembelianInvoice($id) {
	global $conn;

	$id = $id;

	$pembelian_invoice_parent = mysqli_query( $conn, "select pembelian_invoice_parent, invoice_pembelian_cabang from invoice_pembelian where invoice_pembelian_id = '".$id."'");
    $pip = mysqli_fetch_array($pembelian_invoice_parent); 
    $pembelian_invoice_parent  = $pip["pembelian_invoice_parent"];
    $invoice_pembelian_cabang  = $pip["invoice_pembelian_cabang"];

    // Menghitung data di tabel HUtang sesuai No. Invoice Parent
	$hutang = mysqli_query($conn,"select * from hutang where hutang_invoice_parent = '".$pembelian_invoice_parent."' && hutang_cabang = '".$invoice_pembelian_cabang."' ");
    $jmlHutang = mysqli_num_rows($hutang);

    if ( $jmlHutang > 0 ) {
    	mysqli_query( $conn, "DELETE FROM hutang WHERE hutang_invoice_parent = $pembelian_invoice_parent && hutang_cabang = $invoice_pembelian_cabang");

    	mysqli_query( $conn, "DELETE FROM pembelian WHERE pembelian_invoice_parent = $pembelian_invoice_parent && pembelian_cabang = $invoice_pembelian_cabang")
    	;

		mysqli_query( $conn, "DELETE FROM invoice_pembelian WHERE pembelian_invoice_parent = $pembelian_invoice_parent && invoice_pembelian_cabang = $invoice_pembelian_cabang");
    } else {
    	mysqli_query( $conn, "DELETE FROM pembelian WHERE pembelian_invoice_parent = $pembelian_invoice_parent && pembelian_cabang = $invoice_pembelian_cabang")
    	;

		mysqli_query( $conn, "DELETE FROM invoice_pembelian WHERE pembelian_invoice_parent = $pembelian_invoice_parent && invoice_pembelian_cabang = $invoice_pembelian_cabang");
    }

	return mysqli_affected_rows($conn);
}

// ===================================== Pindah Cabang ===================================== //
function editLokasiCabang($data) {
	global $conn;
	$id = $data["user_id"];

	// ambil data dari tiap elemen dalam form
	$user_cabang = htmlspecialchars($data['user_cabang']);

	// query update data
	$query = "UPDATE user SET 
				user_cabang       = '$user_cabang'
				WHERE user_id     = $id
				";
	// var_dump($query); die();
	mysqli_query($conn, $query);
	return mysqli_affected_rows($conn);
}

// ======================================== Kurir ========================================== //
function editStatusKurir($data) {
	global $conn;
	$id = $data["invoice_id"];

	// ambil data dari tiap elemen dalam form
	$invoice_status_kurir       = $data['invoice_status_kurir'];
	$invoice_date_selesai_kurir = date("d F Y g:i:s a");

	if ( $invoice_status_kurir == 3 ) {
		// query update data
		$query = "UPDATE invoice SET 
				invoice_status_kurir 		= '$invoice_status_kurir',
				invoice_date_selesai_kurir	= '$invoice_date_selesai_kurir'
				WHERE invoice_id     = $id
		";
	} else {
		// query update data
		$query = "UPDATE invoice SET 
				invoice_status_kurir 		= '$invoice_status_kurir',
				invoice_date_selesai_kurir	= '-'
				WHERE invoice_id     = $id
		";
	}
	
	mysqli_query($conn, $query);
	return mysqli_affected_rows($conn);
}

// ======================================= Piutang ======================================= //
function tambahCicilanPiutang($data) {
	global $conn;
	$id = $data["invoice_id"];

	// ambil data dari tiap elemen dalam form
	$invoice_bayar_lama			= $data['invoice_bayar'];
	$piutang_nominal			= $data['piutang_nominal'];
	$invoice_bayar         		= $invoice_bayar_lama + $piutang_nominal;
	$invoice_sub_total			= $data['invoice_sub_total'];
	$invoice_kembali            = $invoice_bayar - $invoice_sub_total;

	$piutang_invoice			= $data['piutang_invoice'];
	$piutang_date				= date("Y-m-d");
	$piutang_date_time			= date("d F Y g:i:s a");
	$piutang_kasir				= $data['piutang_kasir'];
	$piutang_tipe_pembayaran	= $data['piutang_tipe_pembayaran'];
	$piutang_cabang				= $data['piutang_cabang'];

	if ( $invoice_bayar >= $invoice_sub_total ) {
		// query update data
		$query = "UPDATE invoice SET 
					invoice_bayar          = '$invoice_bayar',
					invoice_kembali        = '$invoice_kembali',
					invoice_piutang        = 0,
					invoice_piutang_lunas  = 1,
					invoice_date_lunas     = '$piutang_date',
					invoice_datetime_lunas = '$piutang_date_time'
					WHERE invoice_id = $id
				";
		mysqli_query($conn, $query);

		// Insert Tabel kembalian Piutang Cicilan
		$kembalian_piutang = $invoice_bayar - $invoice_sub_total;
		$query3 = "INSERT INTO piutang_kembalian VALUES ('', '$piutang_invoice', '$piutang_date', '$piutang_date_time', '$kembalian_piutang', '$piutang_cabang')";
		mysqli_query($conn, $query3);

	} else {
		// query update data
		$query = "UPDATE invoice SET 
					invoice_bayar          = '$invoice_bayar',
					invoice_kembali        = '$invoice_kembali'
					WHERE invoice_id = $id
				";
		mysqli_query($conn, $query);
	} 
	
	

	// query insert data
	$query2 = "INSERT INTO piutang VALUES ('', '$piutang_invoice', '$piutang_date', '$piutang_date_time', '$piutang_kasir', '$piutang_nominal', '$piutang_tipe_pembayaran', '$piutang_cabang')";
	mysqli_query($conn, $query2);

	return mysqli_affected_rows($conn);
}

function hapusCicilanPiutang($id) {
	global $conn;


	// Ambil ID produk
	$data_id = $id;

	// Mencari No. Invoice
	$noInvoice = mysqli_query( $conn, "select piutang_invoice, piutang_nominal, piutang_cabang from piutang where piutang_id = '".$data_id."'");
    $noInvoice = mysqli_fetch_array($noInvoice); 
    $piutangInvoice = $noInvoice["piutang_invoice"];
    $nominal 		= $noInvoice["piutang_nominal"];
    $cabangInvoice 	= $noInvoice["piutang_cabang"];

    // Mencari Nilai Bayar di Tabel Invoive
    $bayarInvoice = mysqli_query ( $conn, "select invoice_id, invoice_bayar, invoice_sub_total from invoice where penjualan_invoice = '".$piutangInvoice."' && invoice_cabang = '".$cabangInvoice."' ");
    $bayarInvoice = mysqli_fetch_array($bayarInvoice);
    $invoice_id         = $bayarInvoice['invoice_id'];
    $bayar       		= $bayarInvoice['invoice_bayar'];
    $subTotalInvoice 	= $bayarInvoice['invoice_sub_total'];

    // Proses
    $invoice_bayar         		= $bayar - $nominal;
	$invoice_kembali            = $invoice_bayar - $subTotalInvoice;

	if ( $invoice_bayar >= $subTotalInvoice ) {
		// query update data
		$query2 = "UPDATE invoice SET 
					invoice_bayar          = '$invoice_bayar',
					invoice_kembali        = '$invoice_kembali',
					invoice_piutang        = 0,
					invoice_piutang_lunas  = 1
					WHERE invoice_id = $invoice_id
				";
	} else {
		// query update data
		$query2 = "UPDATE invoice SET 
					invoice_bayar          = '$invoice_bayar',
					invoice_kembali        = '$invoice_kembali',
					invoice_piutang        = 1,
					invoice_piutang_lunas  = 0
					WHERE invoice_id = $invoice_id
				";
	} 
	mysqli_query($conn, $query2);
   
    
	mysqli_query( $conn, "DELETE FROM piutang WHERE piutang_id = $id");

	return mysqli_affected_rows($conn);
}

function updateInvoicePiutang($data) {
	global $conn;
	$id = $data["invoice_id"];

	// ambil data dari tiap elemen dalam form
	$invoice_total        = htmlspecialchars($data['invoice_total']);
	$invoice_ongkir       = $data['invoice_ongkir'];
	$invoice_sub_total    = $data['invoice_sub_total'];
	$invoice_bayar        = htmlspecialchars($data['angka1']);
	$invoice_kembali      = $invoice_bayar - $invoice_sub_total;
	$invoice_kasir_edit   = $data['invoice_kasir_edit'];
	$invoice_date_edit    = date('Y-m-d');



	if ( $invoice_bayar >= $invoice_sub_total ) {
		// query update data
		$query = "UPDATE invoice SET 
					invoice_total      		= '$invoice_total',
					invoice_ongkir     		= '$invoice_ongkir',
					invoice_sub_total  		= '$invoice_sub_total',
					invoice_bayar      		= '$invoice_bayar',
					invoice_kembali    		= '$invoice_kembali',
					invoice_date_edit  		= '$invoice_date_edit',
					invoice_kasir_edit 		= '$invoice_kasir_edit',
					invoice_piutang        	= 0,
					invoice_piutang_lunas 	= 1
					WHERE invoice_id = $id
				";
	} else {
		// query update data
		$query = "UPDATE invoice SET 
					invoice_total      		= '$invoice_total',
					invoice_ongkir     		= '$invoice_ongkir',
					invoice_sub_total  		= '$invoice_sub_total',
					invoice_bayar      		= '$invoice_bayar',
					invoice_kembali    		= '$invoice_kembali',
					invoice_date_edit  		= '$invoice_date_edit',
					invoice_kasir_edit 		= '$invoice_kasir_edit',
					invoice_piutang        	= 1,
					invoice_piutang_lunas 	= 0
					WHERE invoice_id = $id
				";
	} 
	mysqli_query($conn, $query);
	return mysqli_affected_rows($conn);
}

// ======================================= Hutang ======================================= //
function tambahCicilanhutang($data) {
	global $conn;
	$id = $data["invoice_pembelian_id"];

	// ambil data dari tiap elemen dalam form
	$invoice_bayar_lama			= $data['invoice_bayar'];
	$hutang_nominal				= $data['hutang_nominal'];
	$invoice_bayar         		= $invoice_bayar_lama + $hutang_nominal;
	$invoice_total				= $data['invoice_total'];
	$invoice_kembali            = $invoice_bayar - $invoice_total;

	$hutang_invoice				= $data['hutang_invoice'];
	$hutang_invoice_parent		= $data['hutang_invoice_parent'];
	$hutang_date				= date("Y-m-d");
	$hutang_date_time			= date("d F Y g:i:s a");
	$hutang_kasir				= $data['hutang_kasir'];
	$hutang_tipe_pembayaran		= $data['hutang_tipe_pembayaran'];
	$hutang_cabang				= $data['hutang_cabang'];

	if ( $invoice_bayar >= $invoice_total ) {
		// query update data
		$query = "UPDATE invoice_pembelian SET 
					invoice_bayar          = '$invoice_bayar',
					invoice_kembali        = '$invoice_kembali',
					invoice_hutang         = 0,
					invoice_hutang_lunas   = 1
					WHERE invoice_pembelian_id = $id
				";
		mysqli_query($conn, $query);

		// Insert Tabel kembalian Piutang Cicilan
		$kembalian_hutang = $invoice_bayar - $invoice_total;
		$query3 = "INSERT INTO hutang_kembalian VALUES ('', '$hutang_invoice', '$hutang_invoice_parent', '$hutang_date', '$hutang_date_time', '$kembalian_hutang', '$hutang_cabang')";
		mysqli_query($conn, $query3);
	} else {
		// query update data
		$query = "UPDATE invoice_pembelian SET 
					invoice_bayar          = '$invoice_bayar',
					invoice_kembali        = '$invoice_kembali'
					WHERE invoice_pembelian_id = $id
				";
		mysqli_query($conn, $query);
	} 
	
	

	// query insert data
	$query2 = "INSERT INTO hutang VALUES ('', '$hutang_invoice', '$hutang_invoice_parent', '$hutang_date', '$hutang_date_time', '$hutang_kasir', '$hutang_nominal', '$hutang_tipe_pembayaran', '$hutang_cabang')";
	mysqli_query($conn, $query2);

	return mysqli_affected_rows($conn);
}

function hapusCicilanHutang($id) {
	global $conn;


	// Ambil ID produk
	$data_id = $id;

	// Mencari No. Invoice
	$noInvoice = mysqli_query( $conn, "select hutang_invoice_parent, hutang_nominal, hutang_cabang from hutang where hutang_id = '".$data_id."'");
    $noInvoice = mysqli_fetch_array($noInvoice); 
    $invoiceParent 		 = $noInvoice["hutang_invoice_parent"];
    $nominal 			 = $noInvoice["hutang_nominal"];
    $cabangInvoice 	 	 = $noInvoice["hutang_cabang"];

    // Mencari Nilai Bayar di Tabel Invoive
    $bayarInvoicePembelian = mysqli_query ( $conn, "select invoice_pembelian_id, invoice_bayar, invoice_total from invoice_pembelian where pembelian_invoice_parent = '".$invoiceParent."' && invoice_pembelian_cabang = '".$cabangInvoice."' ");
    $bip 				  		  = mysqli_fetch_array($bayarInvoicePembelian);
    $invoice_pembelian_id         = $bip['invoice_pembelian_id'];
    $bayar       				  = $bip['invoice_bayar'];
    $totalInvoice 	              = $bip['invoice_total'];

    // Proses
    $invoice_bayar         		= $bayar - $nominal;
	$invoice_kembali            = $invoice_bayar - $totalInvoice;

	if ( $invoice_bayar >= $totalInvoice ) {
		// query update data
		$query2 = "UPDATE invoice_pembelian SET 
					invoice_bayar          = '$invoice_bayar',
					invoice_kembali        = '$invoice_kembali',
					invoice_hutang         = 0,
					invoice_hutang_lunas   = 1
					WHERE invoice_pembelian_id = $invoice_pembelian_id
				";
	} else {
		// query update data
		$query2 = "UPDATE invoice_pembelian SET 
					invoice_bayar          = '$invoice_bayar',
					invoice_kembali        = '$invoice_kembali',
					invoice_hutang         = 1,
					invoice_hutang_lunas   = 0
					WHERE invoice_pembelian_id = $invoice_pembelian_id
				";
	} 
	mysqli_query($conn, $query2);
   
    
	mysqli_query( $conn, "DELETE FROM hutang WHERE hutang_id = $id");

	return mysqli_affected_rows($conn);
}

function updateInvoicePembelianHutang($data) {
	global $conn;
	$id = $data["invoice_pembelian_id"];

	// ambil data dari tiap elemen dalam form
	$invoice_total        = htmlspecialchars($data['invoice_total']);
	$invoice_bayar        = htmlspecialchars($data['angka1']);
	$invoice_kembali      = $invoice_bayar - $invoice_total;
	$invoice_kasir_edit   = $data['invoice_kasir_edit'];
	$invoice_date_edit    = date('Y-m-d');

	if ( $invoice_bayar >= $invoice_total ) {
		// query update data
		$query = "UPDATE invoice_pembelian SET 
					invoice_total      = '$invoice_total',
					invoice_bayar      = '$invoice_bayar',
					invoice_kembali    = '$invoice_kembali',
					invoice_date_edit  = '$invoice_date_edit',
					invoice_kasir_edit = '$invoice_kasir_edit',
					invoice_hutang        	= 0,
					invoice_hutang_lunas 	= 1
					WHERE invoice_pembelian_id = $id
				";
	} else {
		// query update data
		$query = "UPDATE invoice_pembelian SET 
					invoice_total      = '$invoice_total',
					invoice_bayar      = '$invoice_bayar',
					invoice_kembali    = '$invoice_kembali',
					invoice_date_edit  = '$invoice_date_edit',
					invoice_kasir_edit = '$invoice_kasir_edit',
					invoice_hutang        	= 1,
					invoice_hutang_lunas 	= 0
					WHERE invoice_pembelian_id = $id
				";
	}
		
	mysqli_query($conn, $query);
	return mysqli_affected_rows($conn);
}

// ================================ Tranfer Stock Cabang =================================== //
function tambahTransferSelectCabang($data) {
	global $conn;

	// ambil data dari tiap elemen dalam form
	$tsc_cabang_pusat 		= htmlspecialchars($data['tsc_cabang_pusat']);
	$tsc_cabang_penerima 	= htmlspecialchars($data['tsc_cabang_penerima']);
	$tsc_user_id 			= htmlspecialchars($data['tsc_user_id']);
	$tsc_cabang 			= htmlspecialchars($data['tsc_cabang']);


	$count = mysqli_query($conn, "select * from transfer_select_cabang where tsc_user_id = ".$tsc_user_id." && tsc_cabang = ".$tsc_cabang." ");
	$count = mysqli_num_rows($count);

	if ( $count < 1 ) {
		// query insert data
		$query = "INSERT INTO transfer_select_cabang (tsc_cabang_pusat, tsc_cabang_penerima, tsc_user_id, tsc_cabang) VALUES ('$tsc_cabang_pusat', '$tsc_cabang_penerima', '$tsc_user_id', '$tsc_cabang')";
		mysqli_query($conn, $query);
	} else {
		mysqli_query( $conn, "DELETE FROM transfer_select_cabang WHERE tsc_user_id = $tsc_user_id && tsc_cabang = $tsc_cabang");
	}

	return mysqli_affected_rows($conn);
}

function resetTransferSelectCabang($data) {
	global $conn;

	// ambil data dari tiap elemen dalam form
	$tsc_user_id 			= htmlspecialchars($data['tsc_user_id']);
	$tsc_cabang 			= htmlspecialchars($data['tsc_cabang']);
	$tsc_cabang_pusat		= htmlspecialchars($data['tsc_cabang_pusat']);

	$keranjang = mysqli_query($conn,"select * from keranjang_transfer where keranjang_transfer_id_kasir = ".$tsc_user_id." && keranjang_transfer_cabang = ".$tsc_cabang_pusat." ");
    $jmlkeranjang = mysqli_num_rows($keranjang);


    if ( $jmlkeranjang > 0 ) {
    	mysqli_query( $conn, "DELETE FROM keranjang_transfer WHERE keranjang_transfer_id_kasir = $tsc_user_id && keranjang_transfer_cabang = $tsc_cabang_pusat");
    } 

	mysqli_query( $conn, "DELETE FROM transfer_select_cabang WHERE tsc_user_id = $tsc_user_id && tsc_cabang = $tsc_cabang");

	return mysqli_affected_rows($conn);
}

function tambahkeranjangtransfer($data) {
	global $conn;
	// ambil data dari tiap elemen dalam form
	$keranjang_nama     			= $data['keranjang_nama'];
	$barang_id          			= $data['barang_id'];
	$keranjang_qty      			= 1;
	$keranjang_barang_sn_id     	= 0;
	$keranjang_barang_option_sn 	= $data['keranjang_barang_option_sn'];
	$keranjang_sn       			= 0;
	$keranjang_id_kasir 			= $data['keranjang_id_kasir'];
	$keranjang_cabang   			= $data['keranjang_cabang'];
	
	$keranjang_id_cek   			= $barang_id.$keranjang_id_kasir.$keranjang_cabang;
	
	$keranjang_cabang_pengirim 		= $data['keranjang_cabang_pengirim'];
	$keranjang_cabang_tujuan		= $data['keranjang_cabang_tujuan'];
	$barang_kode_slug				= $data['barang_kode_slug'];
	$barang_kode 					= $data['barang_kode'];
	$cabang_penerima_stock			= $data['cabang_penerima_stock'];

	// Mencari Data Barang berdasarkan Kode Slug dan cabang
	$barangTujuan 		= mysqli_query($conn,"select * from barang where barang_kode_slug = '".$barang_kode_slug."' && barang_cabang = ".$keranjang_cabang_tujuan." ");
    $jmlBarangTujuan 	= mysqli_num_rows($barangTujuan);

  	// Kondisi Jika Cabang Penerima tidak memiliki Produk terkait
  	if ( $jmlBarangTujuan < 1 ) {
  		echo "
  			<script>
  				alert('Maaf Kode Produk ".$barang_kode." Tidak Ada di Toko ".$cabang_penerima_stock." dan Coba Cek Kembali !!');
  			</script>
  		";
  	} else {
  		// Cek STOCK
		$barang_id_cek = mysqli_num_rows(mysqli_query($conn, "select * from keranjang_transfer where keranjang_id_cek = '$keranjang_id_cek' "));
		
		if ( $barang_id_cek > 0 && $keranjang_barang_option_sn < 1 ) {
			$keranjangParent = mysqli_query( $conn, "select keranjang_transfer_qty from keranjang_transfer where keranjang_id_cek = '".$keranjang_id_cek."'");
	        $kp = mysqli_fetch_array($keranjangParent); 
	        $kp = $kp['keranjang_transfer_qty'];
	        $kp += $keranjang_qty;

	        $query = "UPDATE keranjang_transfer SET 
						keranjang_transfer_qty   = '$kp'
						WHERE keranjang_id_cek = $keranjang_id_cek
						";
			mysqli_query($conn, $query);
			return mysqli_affected_rows($conn);

		} else {
			// query insert data
			$query = "INSERT INTO keranjang_transfer (
				keranjang_transfer_nama,
				barang_id,
				barang_kode_slug,
				keranjang_transfer_qty,
				keranjang_barang_sn_id,
				keranjang_barang_option_sn,
				keranjang_sn,
				keranjang_transfer_id_kasir,
				keranjang_id_cek,
				keranjang_pengirim_cabang,
				keranjang_penerima_cabang,
				keranjang_transfer_cabang
			) VALUES (
				'$keranjang_nama',
				'$barang_id',
				'$barang_kode_slug',
				'$keranjang_qty',
				'$keranjang_barang_sn_id',
				'$keranjang_barang_option_sn',
				'$keranjang_sn',
				'$keranjang_id_kasir',
				'$keranjang_id_cek',
				'$keranjang_cabang_pengirim',
				'$keranjang_cabang_tujuan',
				'$keranjang_cabang'
			)";
			
			mysqli_query($conn, $query);

			return mysqli_affected_rows($conn);
		}
  	}
}

function tambahKeranjangBarcodeTransfer($data) {
	global $conn;

	$barang_kode 					= htmlspecialchars($data['inputbarcode']);
	$barang_kode_slug   			= str_replace(" ", "-", $barang_kode);
	$keranjang_cabang_pengirim 		= $data['keranjang_cabang_pengirim'];
	$keranjang_cabang_tujuan		= $data['keranjang_cabang_tujuan'];
	$keranjang_id_kasir 			= $data['keranjang_id_kasir'];
	$keranjang_cabang   			= $data['keranjang_cabang'];

	// Ambil Data Barang berdasarkan Kode Barang 
	$barang 	= mysqli_query( $conn, "select barang_id, barang_nama, barang_harga, barang_option_sn from barang where barang_kode = '".$barang_kode."' && barang_cabang = '".$keranjang_cabang."' ");
    $br 		= mysqli_fetch_array($barang);

    $barang_id  				= $br["barang_id"];
    $keranjang_nama  			= $br["barang_nama"];
    $keranjang_barang_option_sn = $br["barang_option_sn"];
    $keranjang_qty      		= 1;
	$keranjang_barang_sn_id     = 0;
	$keranjang_sn       		= 0;
	$keranjang_id_cek   		= $barang_id.$keranjang_id_kasir.$keranjang_cabang;

	// Kondisi jika scan Barcode Tidak sesuai
	if ( $barang_id != null ) {

		// Cek STOCK
		$barang_id_cek = mysqli_num_rows(mysqli_query($conn, "select * from keranjang_transfer where keranjang_id_cek = '$keranjang_id_cek' "));
			
		if ( $barang_id_cek > 0 && $keranjang_barang_option_sn < 1 ) {
			$keranjangParent = mysqli_query( $conn, "select keranjang_transfer_qty from keranjang_transfer where keranjang_id_cek = '".$keranjang_id_cek."'");
	        $kp = mysqli_fetch_array($keranjangParent); 
	        $kp = $kp['keranjang_transfer_qty'];
	        $kp += $keranjang_qty;

	        $query = "UPDATE keranjang_transfer SET 
						keranjang_transfer_qty   = '$kp'
						WHERE keranjang_id_cek = $keranjang_id_cek
						";
			mysqli_query($conn, $query);
			return mysqli_affected_rows($conn);

		} else {
			// query insert data
			$query = "INSERT INTO keranjang_transfer (
				keranjang_transfer_nama,
				barang_id,
				barang_kode_slug,
				keranjang_transfer_qty,
				keranjang_barang_sn_id,
				keranjang_barang_option_sn,
				keranjang_sn,
				keranjang_transfer_id_kasir,
				keranjang_id_cek,
				keranjang_pengirim_cabang,
				keranjang_penerima_cabang,
				keranjang_transfer_cabang
			) VALUES (
				'$keranjang_nama',
				'$barang_id',
				'$barang_kode_slug',
				'$keranjang_qty',
				'$keranjang_barang_sn_id',
				'$keranjang_barang_option_sn',
				'$keranjang_sn',
				'$keranjang_id_kasir',
				'$keranjang_id_cek',
				'$keranjang_cabang_pengirim',
				'$keranjang_cabang_tujuan',
				'$keranjang_cabang'
			)";
			
			mysqli_query($conn, $query);

			return mysqli_affected_rows($conn);
		}
	} else {
		echo '
			<script>
				alert("Kode Produk Tidak ada di Data Master Barang dan Coba Cek Kembali !! ");
				document.location.href = "";
			</script>
		';
	}
}

function updateSnTransfer($data){
	global $conn;
	$id = $data["keranjang_id"];


	// ambil data dari tiap elemen dalam form
	$barang_sn_id  				= $data["barang_sn_id"];
	$keranjang_transfer_cabang 	= $data['keranjang_transfer_cabang'];


	$barang_sn_desc = mysqli_query( $conn, "select barang_sn_desc from barang_sn where barang_sn_id = '".$barang_sn_id."'");
    $barang_sn_desc = mysqli_fetch_array($barang_sn_desc); 
    $barang_sn_desc = $barang_sn_desc['barang_sn_desc'];

    // Menghitung jumlah No SN berdasarkan cabang jika ada maka di tolak
    $barang_sn_count = mysqli_query($conn, "select * from keranjang_transfer where keranjang_sn = '".$barang_sn_desc."' && keranjang_transfer_cabang = '".$keranjang_transfer_cabang."' ");
    $barang_sn_count = mysqli_num_rows($barang_sn_count);

    if ( $barang_sn_count > 0 ) {
    	echo "
    		<script>
    			alert('Data No.SN ".$barang_sn_desc." Sudah ada di daftar transfer coba pilih yang lain !!');
    			document.location.href = 'transfer-stock-cabang';
    		</script>
    	";
    } else {
    	// query update data
		$query = "UPDATE keranjang_transfer SET 
							keranjang_barang_sn_id  			= '$barang_sn_id',
							keranjang_sn            			= '$barang_sn_desc'
							WHERE keranjang_transfer_id      	= $id
					";

		mysqli_query($conn, $query);
    }

	return mysqli_affected_rows($conn);

}


function updateQtyTransfer($data) {
	global $conn;
	$id = $data["keranjang_id"];

	// ambil data dari tiap elemen dalam form
	$keranjang_qty 		= htmlspecialchars($data['keranjang_qty']);
	$stock_brg 			= $data['stock_brg'];

	if ( $keranjang_qty > $stock_brg ) {
		echo"
			<script>
				alert('QTY Melebihi Stock Barang.. Coba Cek Lagi !!!');
				document.location.href = '';
			</script>
		";
	} else {
		// query update data
		$query = "UPDATE keranjang_transfer SET 
					keranjang_transfer_qty   		= '$keranjang_qty'
					WHERE keranjang_transfer_id 	= $id
					";
		mysqli_query($conn, $query);
		return mysqli_affected_rows($conn);
	}
}

function hapusKeranjangTransfer($id) {
	global $conn;

	mysqli_query( $conn, "DELETE FROM keranjang_transfer WHERE keranjang_transfer_id = $id");

	return mysqli_affected_rows($conn);
}

function prosesTransfer($data) {
	global $conn;
	
	// Data Input Tabel Transfer
	$transfer_ref 				= htmlspecialchars($data['transfer_ref']);
	$transfer_count				= htmlspecialchars($data['transfer_count']); 
	$transfer_date				= date("Y-m-d");
	$transfer_date_time			= date("d F Y g:i:s a");
	$transfer_note				= htmlspecialchars($data['transfer_note']);
	$transfer_pengirim_cabang   = $data['transfer_pengirim_cabang'];
	$transfer_penerima_cabang   = $data['transfer_penerima_cabang'];
	$transfer_id_tipe_keluar    = $data['transfer_id_tipe_keluar'];
	$transfer_id_tipe_masuk		= $data['transfer_id_tipe_masuk'];
		// Status Trnsfer Stock Antar Cabang
		// 1. Proses Kirim
		// 2. Selesai
		// 3. Dibatalkan 
	$transfer_status			= 1;
	$transfer_user				= htmlspecialchars($data['transfer_user']);
	$transfer_cabang 			= $data['transfer_cabang'];

	// ============================================================================= //
	// Data Input Tabel transfer_produk_keluar
	$tpk_transfer_barang_id		= $data['barang_id'];
	$tpk_barang_id				= $data['barang_id'];
	$tpk_kode_slug				= $data['tpk_kode_slug'];
	$tpk_qty					= $data['keranjang_transfer_qty'];
	$tpk_ref 					= $data['tpk_ref'];
	$tpk_date                   = $data['tpk_date'];
	$tpk_date_time              = $data['tpk_date_time'];
	$tpk_barang_option_sn       = $data['tpk_barang_option_sn'];
	$tpk_barang_sn_id           = $data['tpk_barang_sn_id'];
	$tpk_barang_sn_desc         = $data['tpk_barang_sn_desc'];
	$tpk_user                   = $data['keranjang_transfer_id_kasir'];
	$tpk_pengirim_cabang        = $data['tpk_pengirim_cabang'];
	$tpk_penerima_cabang        = $data['tpk_penerima_cabang'];
	$tpk_cabang                 = $data['tpk_cabang'];

	$jumlah = count($tpk_user);

	// query insert invoice
	$query1 = "INSERT INTO transfer (
							transfer_ref, 
							transfer_count, 
							transfer_date, 
							transfer_date_time, 
							transfer_terima_date, 
							transfer_terima_date_time, 
							transfer_note, 
							transfer_pengirim_cabang, 
							transfer_penerima_cabang, 
							transfer_id_tipe_keluar, 
							transfer_id_tipe_masuk, 
							transfer_status, 
							transfer_user, 
							transfer_user_penerima, 
							transfer_cabang
							) VALUES (
							'$transfer_ref', 
							'$transfer_count', 
							'$transfer_date', 
							'$transfer_date_time', 
							'-', 
							'-', 
							'$transfer_note', 
							'$transfer_pengirim_cabang', 
							'$transfer_penerima_cabang', 
							'$transfer_id_tipe_keluar', 
							'$transfer_id_tipe_masuk', 
							'$transfer_status', 
							'$transfer_user', 
							0, 
							'$transfer_cabang'
							)";
	mysqli_query($conn, $query1);

	for( $x=0; $x<$jumlah; $x++ ){
		$query = "INSERT INTO transfer_produk_keluar (
							tpk_transfer_barang_id, 
							tpk_barang_id, 
							tpk_kode_slug, 
							tpk_qty, 
							tpk_ref, 
							tpk_date, 
							tpk_date_time, 
							tpk_barang_option_sn, 
							tpk_barang_sn_id, 
							tpk_barang_sn_desc, 
							tpk_user, 
							tpk_pengirim_cabang, 
							tpk_penerima_cabang, 
							tpk_cabang
							) VALUES (
							'$tpk_transfer_barang_id[$x]', 
							'$tpk_barang_id[$x]', 
							'$tpk_kode_slug[$x]', 
							'$tpk_qty[$x]', 
							'$tpk_ref[$x]', 
							'$tpk_date[$x]', 
							'$tpk_date_time[$x]', 
							'$tpk_barang_option_sn[$x]', 
							'$tpk_barang_sn_id[$x]', 
							'$tpk_barang_sn_desc[$x]', 
							'$tpk_user[$x]', 
							'$tpk_pengirim_cabang[$x]', 
							'$tpk_penerima_cabang[$x]', 
							'$tpk_cabang[$x]'
							)";

		mysqli_query($conn, $query);
	}
	
	// Mencari banyak barang SN
	$barang_option_sn = mysqli_query( $conn, "select tpk_barang_option_sn from transfer_produk_keluar where tpk_ref = '".$transfer_ref."' && tpk_barang_option_sn > 0 && tpk_cabang = '".$transfer_cabang."' ");
	$barang_option_sn = mysqli_num_rows($barang_option_sn);
    
	// Mencari ID SN
	if ( $barang_option_sn > 0 ) {
		$barang_sn_id = query("SELECT * FROM transfer_produk_keluar WHERE tpk_ref = $transfer_ref && tpk_barang_option_sn > 0 && tpk_cabang = $transfer_cabang ");

		// var_dump($barang_sn_id); die();
		foreach ( $barang_sn_id as $row ) :
		 	$barang_sn_id = $row['tpk_barang_sn_id'];

		 	$barang = count($barang_sn_id);
		 	for ( $i = 0; $i < $barang; $i++ ) {
		 		$query5 = "UPDATE barang_sn SET 
						barang_sn_status     = 5
						WHERE barang_sn_id = $barang_sn_id
				";
		 	}
		 	mysqli_query($conn, $query5);
		endforeach;
	}

	mysqli_query( $conn, "DELETE FROM keranjang_transfer WHERE keranjang_transfer_id_kasir = $transfer_user");
	mysqli_query( $conn, "DELETE FROM transfer_select_cabang WHERE tsc_user_id = $transfer_user && tsc_cabang = $transfer_id_tipe_keluar");

	return mysqli_affected_rows($conn);
}

function hapusTransferStockCabang($id) {
	global $conn;
    
	mysqli_query( $conn, "DELETE FROM transfer WHERE transfer_ref = $id");
	mysqli_query( $conn, "DELETE FROM transfer_produk_keluar WHERE tpk_ref = $id");

	return mysqli_affected_rows($conn);
}

function prosesKonfirmasiTransfer($data) {
	global $conn;
	
	// Data Input Tabel Transfer
	$transfer_status 					= htmlspecialchars($data['transfer_status']); 
	$transfer_terima_date				= date("Y-m-d");
	$transfer_terima_date_time			= date("d F Y g:i:s a");
	$transfer_ref 						= $data['transfer_ref'];
	$transfer_user_penerima 			= $data['transfer_user_penerima'];
	$transfer_penerima_cabang			= $data['transfer_penerima_cabang'];
	// Status Trnsfer Stock Antar Cabang
	// 1. Proses Kirim
	// 2. Selesai
	// 3. Dibatalkan 

	// ============================================================================= //
	// Data Input Tabel transfer_produk_masuk
	$tpm_kode_slug			= $data['tpm_kode_slug'];
	$tpm_qty				= $data['tpm_qty'];
	$tpm_ref				= $data['tpm_ref'];
	$tpm_date				= $data['tpm_date'];
	$tpm_date_time 			= $data['tpm_date_time'];
	$tpm_barang_option_sn   = $data['tpm_barang_option_sn'];
	$tpm_barang_sn_id       = $data['tpm_barang_sn_id'];
	$tpm_barang_sn_desc     = $data['tpm_barang_sn_desc'];
	$tpm_user           	= $data['tpm_user'];
	$tpm_pengirim_cabang    = $data['tpm_pengirim_cabang'];
	$tpm_penerima_cabang    = $data['tpm_penerima_cabang'];
	$tpm_cabang        		= $data['tpm_cabang'];

	$tpm_barang_harga			= isset($data['tpm_barang_harga']) ? $data['tpm_barang_harga'] : [];
	$jumlah = count($tpm_user);

	// Mencari banyak barang SN di tabel transfer_produk_keluar
	$barang_option_sn_produk_keluar = mysqli_query( $conn, "select tpk_barang_option_sn from transfer_produk_keluar where tpk_ref = '".$transfer_ref."' && tpk_barang_option_sn > 0 && tpk_penerima_cabang = '".$transfer_penerima_cabang."' ");
	$barang_option_sn_produk_keluar = mysqli_num_rows($barang_option_sn_produk_keluar);

	if ( $barang_option_sn_produk_keluar > 0 ) {
		if ( $transfer_status > 0 ) {
			// query update data
			$query = "UPDATE transfer SET 
						transfer_terima_date   		= '$transfer_terima_date',
						transfer_terima_date_time   = '$transfer_terima_date_time',
						transfer_status 			= 2,
						transfer_user_penerima      = '$transfer_user_penerima'
						WHERE transfer_ref 			= $transfer_ref
						";
			mysqli_query($conn, $query);

			for( $x=0; $x<$jumlah; $x++ ){
				$query1 = "INSERT INTO transfer_produk_masuk (tpm_kode_slug, tpm_qty, tpm_ref, tpm_date, tpm_date_time, tpm_barang_option_sn, tpm_barang_sn_id, tpm_barang_sn_desc, tpm_user, tpm_pengirim_cabang, tpm_penerima_cabang, tpm_cabang) VALUES (
											'$tpm_kode_slug[$x]', 
											'$tpm_qty[$x]', 
											'$tpm_ref[$x]', 
											'$tpm_date[$x]', 
											'$tpm_date_time[$x]', 
											'$tpm_barang_option_sn[$x]', 
											'$tpm_barang_sn_id[$x]', 
											'$tpm_barang_sn_desc[$x]', 
											'$tpm_user[$x]', 
											'$tpm_pengirim_cabang[$x]', 
											'$tpm_penerima_cabang[$x]', 
											'$tpm_cabang[$x]')";
				mysqli_query($conn, $query1);

				// Update barang table
				// Get Data Barang
				$QueryBarang = "SELECT * FROM barang WHERE barang_kode_slug = '{$tpm_kode_slug[$x]}' AND barang_cabang = '{$tpm_penerima_cabang[$x]}'";
				$QueryBarang = mysqli_query($conn, $QueryBarang);
				$getBarang = mysqli_fetch_assoc($QueryBarang);

				if ($getBarang) {
					$oldStock = $getBarang['barang_stock'];
					$oldHPP = ($getBarang['hpp'] == null) ? 0 : $getBarang['hpp'];
					$oldBarangHargaBeli = ($getBarang['barang_harga_beli'] == null) ? 0 : $getBarang['barang_harga_beli'];
					$qty = $tpm_qty[$x];
					$harga_beli = isset($tpm_barang_harga[$x]) ? $tpm_barang_harga[$x] : $oldBarangHargaBeli;
					$newStockBarang = $oldStock + $qty;
					$newHPP = ($oldBarangHargaBeli + ($qty * $harga_beli)) / ($newStockBarang > 0 ? $newStockBarang : 1);

					$QueryUpdateStock = "UPDATE barang SET 
						barang_stock = $newStockBarang, 
						hpp = $newHPP,
						barang_harga_beli = ($oldBarangHargaBeli + ($qty * $harga_beli)), 
						barang_harga = $harga_beli
					WHERE barang_kode_slug = '{$tpm_kode_slug[$x]}' AND barang_cabang = '{$tpm_penerima_cabang[$x]}'";
					mysqli_query($conn, $QueryUpdateStock);
				}
			}

			// Mencari banyak barang SN
			$barang_option_sn = mysqli_query( $conn, "select tpm_barang_option_sn from transfer_produk_masuk where tpm_ref = '".$transfer_ref."' && tpm_barang_option_sn > 0 && tpm_penerima_cabang = '".$transfer_penerima_cabang."' ");
			$barang_option_sn = mysqli_num_rows($barang_option_sn);

			// Mencari ID SN
			if ( $barang_option_sn > 0 ) {
				$barang_sn_id = query("SELECT * FROM transfer_produk_masuk WHERE tpm_ref = $transfer_ref && tpm_barang_option_sn > 0 && tpm_penerima_cabang = $transfer_penerima_cabang ");
				foreach ( $barang_sn_id as $row ) :
					$barang_sn_id = $row['tpm_barang_sn_id'];
					$barang = count($barang_sn_id);
					for ( $i = 0; $i < $barang; $i++ ) {
						$query5 = "UPDATE barang_sn SET 
								barang_sn_status     = 1,
								barang_sn_cabang     = '$transfer_penerima_cabang'
								WHERE barang_sn_id = $barang_sn_id
						";
					}
					mysqli_query($conn, $query5);
				endforeach;
			}
		} else {
			// query update data
			$query = "UPDATE transfer SET 
							transfer_terima_date   		= '$transfer_terima_date',
							transfer_terima_date_time   = '$transfer_terima_date_time',
							transfer_status 			= 0,
							transfer_user_penerima      = '$transfer_user_penerima'
							WHERE transfer_ref 			= $transfer_ref
							";
			mysqli_query($conn, $query);
		}
	} else {
		if ( $transfer_status > 0 ) {
			// query update data
			$query = "UPDATE transfer SET 
						transfer_terima_date   		= '$transfer_terima_date',
						transfer_terima_date_time   = '$transfer_terima_date_time',
						transfer_status 			= 2,
						transfer_user_penerima      = '$transfer_user_penerima'
						WHERE transfer_ref 			= $transfer_ref
						";
			mysqli_query($conn, $query);

			for( $x=0; $x<$jumlah; $x++ ){
				$query1 = "INSERT INTO transfer_produk_masuk (tpm_kode_slug, tpm_qty, tpm_ref, tpm_date, tpm_date_time, tpm_barang_option_sn, tpm_barang_sn_id, tpm_barang_sn_desc, tpm_user, tpm_pengirim_cabang, tpm_penerima_cabang, tpm_cabang) VALUES ( 
											'$tpm_kode_slug[$x]', 
											'$tpm_qty[$x]', 
											'$tpm_ref[$x]', 
											'$tpm_date[$x]', 
											'$tpm_date_time[$x]', 
											'$tpm_barang_option_sn[$x]', 
											'$tpm_barang_sn_id[$x]', 
											'$tpm_barang_sn_desc[$x]', 
											'$tpm_user[$x]', 
											'$tpm_pengirim_cabang[$x]', 
											'$tpm_penerima_cabang[$x]', 
											'$tpm_cabang[$x]' )";
				mysqli_query($conn, $query1);

				// Update barang table
				$QueryBarang = "SELECT * FROM barang WHERE barang_kode_slug = '{$tpm_kode_slug[$x]}' AND barang_cabang = '{$tpm_penerima_cabang[$x]}'";
				$QueryBarang = mysqli_query($conn, $QueryBarang);
				$getBarang = mysqli_fetch_assoc($QueryBarang);

				if ($getBarang) {
					$oldStock = $getBarang['barang_stock'];
					$oldHPP = ($getBarang['hpp'] == null) ? 0 : $getBarang['hpp'];
					$oldBarangHargaBeli = ($getBarang['barang_harga_beli'] == null) ? 0 : $getBarang['barang_harga_beli'];
					$qty = $tpm_qty[$x];
					$harga_beli = isset($tpm_barang_harga[$x]) ? $tpm_barang_harga[$x] : $oldBarangHargaBeli;
					$newStockBarang = $oldStock + $qty;
					$newHPP = ($oldBarangHargaBeli + ($qty * $harga_beli)) / ($newStockBarang > 0 ? $newStockBarang : 1);

					$QueryUpdateStock = "UPDATE barang SET 
						barang_stock = $newStockBarang, 
						hpp = $newHPP,
						barang_harga_beli = ($oldBarangHargaBeli + ($qty * $harga_beli)), 
						barang_harga = $harga_beli
					WHERE barang_kode_slug = '{$tpm_kode_slug[$x]}' AND barang_cabang = '{$tpm_penerima_cabang[$x]}'";
					mysqli_query($conn, $QueryUpdateStock);
				}
			}
		} else {
			// query update data
			$query = "UPDATE transfer SET 
							transfer_terima_date   		= '$transfer_terima_date',
							transfer_terima_date_time   = '$transfer_terima_date_time',
							transfer_status 			= 0,
							transfer_user_penerima      = '$transfer_user_penerima'
							WHERE transfer_ref 			= $transfer_ref
							";
			mysqli_query($conn, $query);
		}
	}

	return mysqli_affected_rows($conn);
}


// ====================================== Laba Bersih ===================================== //
function editLabaBersih($data) {
	global $conn;
	$id = $data["lb_id"];

	// ambil data dari tiap elemen dalam form
	$lb_pendapatan_lain      			= $data["lb_pendapatan_lain"];
	$lb_pengeluaran_gaji      			= $data["lb_pengeluaran_gaji"];
	$lb_pengeluaran_listrik 			= $data["lb_pengeluaran_listrik"];
	$lb_pengeluaran_tlpn_internet     	= $data["lb_pengeluaran_tlpn_internet"];
	$lb_pengeluaran_perlengkapan_toko   = $data["lb_pengeluaran_perlengkapan_toko"];
	$lb_pengeluaran_biaya_penyusutan    = $data["lb_pengeluaran_biaya_penyusutan"];
	$lb_pengeluaran_bensin     			= $data["lb_pengeluaran_bensin"];
	$lb_pengeluaran_tak_terduga 		= $data["lb_pengeluaran_tak_terduga"];
	$lb_pengeluaran_lain        		= $data["lb_pengeluaran_lain"];
	$lb_cabang 							= $data["lb_cabang"];

	// query update data
	$query = "UPDATE laba_bersih SET 
				lb_pendapatan_lain       			= '$lb_pendapatan_lain',
				lb_pengeluaran_gaji       			= '$lb_pengeluaran_gaji',
				lb_pengeluaran_listrik      		= '$lb_pengeluaran_listrik',
				lb_pengeluaran_tlpn_internet      	= '$lb_pengeluaran_tlpn_internet',
				lb_pengeluaran_perlengkapan_toko    = '$lb_pengeluaran_perlengkapan_toko',
				lb_pengeluaran_biaya_penyusutan     = '$lb_pengeluaran_biaya_penyusutan',
				lb_pengeluaran_bensin  				= '$lb_pengeluaran_bensin',
				lb_pengeluaran_tak_terduga  		= '$lb_pengeluaran_tak_terduga',
				lb_pengeluaran_lain 				= '$lb_pengeluaran_lain'
				WHERE lb_id   = $id && lb_cabang = $lb_cabang
				";

	mysqli_query($conn, $query);
	return mysqli_affected_rows($conn);
}

// ============================= Stock Opname Keseluruhan ================================= //
function tambahStockOpname($data) {
	global $conn;
	// ambil data dari tiap elemen dalam form
	$stock_opname_date_create 		= date("Y-m-d");
	$stock_opname_datetime_create 	= date("d F Y g:i:s a");
	$stock_opname_date_proses 		= htmlspecialchars($data['stock_opname_date_proses']);
	$stock_opname_user_create 		= htmlspecialchars($data['stock_opname_user_create']);
	$stock_opname_user_eksekusi 	= htmlspecialchars($data['stock_opname_user_eksekusi']);
	// Status 0 = Proses || 1 = selesai
	$stock_opname_status 			= 0;
	$stock_opname_tipe 				= htmlspecialchars($data['stock_opname_tipe']);
	$stock_opname_cabang 			= htmlspecialchars($data['stock_opname_cabang']);

	// query insert data
	$query = "INSERT INTO stock_opname VALUES ('', '$stock_opname_date_create', '$stock_opname_datetime_create', '$stock_opname_date_proses', '$stock_opname_user_create', '$stock_opname_user_eksekusi', '$stock_opname_status', '', '', '', '$stock_opname_tipe', '$stock_opname_cabang')";
	mysqli_query($conn, $query);

	return mysqli_affected_rows($conn);
}

function hapusStockOpname($id, $sessionCabang) {
	global $conn;

	$stock_opname_hasil_count = mysqli_query($conn, "SELECT * FROM stock_opname_hasil WHERE soh_stock_opname_id = $id && soh_barang_cabang = $sessionCabang");
	$stock_opname_hasil_count = mysqli_num_rows($stock_opname_hasil_count);


	if ( $stock_opname_hasil_count > 0 ) {
		mysqli_query( $conn, "DELETE FROM stock_opname_hasil WHERE soh_stock_opname_id = $id && soh_barang_cabang = $sessionCabang");
	}

	mysqli_query( $conn, "DELETE FROM stock_opname WHERE stock_opname_id = $id");

	return mysqli_affected_rows($conn);
}

function tambahStockOpnamePerProduk($data) {
	global $conn;
	// ambil data dari tiap elemen dalam form

	$soh_stock_opname_id 		= htmlspecialchars($data['soh_stock_opname_id']);
	$soh_barang_kode 			= htmlspecialchars($data['soh_barang_kode']);
	$soh_stock_fisik 			= htmlspecialchars($data['soh_stock_fisik']);
	$soh_note 					= htmlspecialchars($data['soh_note']);
	$soh_date 					= date("Y-m-d");
	$soh_datetime 				= date("d F Y g:i:s a");
	$soh_tipe 					= htmlspecialchars($data['soh_tipe']);
	$soh_user 					= htmlspecialchars($data['soh_user']);
	$soh_barang_cabang 			= htmlspecialchars($data['soh_barang_cabang']);

	$soh_barang_kode_slug       = str_replace(" ", "-", $soh_barang_kode);

    $barang         = mysqli_query($conn, "SELECT barang_id, barang_stock FROM barang WHERE barang_cabang = $soh_barang_cabang && barang_kode_slug = '".$soh_barang_kode_slug."' ");
    $barang         = mysqli_fetch_array($barang);
    $barang_id      = $barang['barang_id'];
    $barang_stock   = $barang['barang_stock'];
    $soh_selisih            	= $soh_stock_fisik - $barang_stock;

    if ( $barang_id == null ) {
        echo '
            <script>
                alert("Kode Barang/Barcode '.$soh_barang_kode.' TIDAK ADA di DATA Barang !! Silahkan Sesuaikan & Cek Kembali dari penulisan Huruf Besar, Kecil, Spasi beserta KODE HARUS SESUAI !!");
                  document.location.href = "";
            </script>
        '; exit();
    } 
	
	// query insert data
	$query = "INSERT INTO stock_opname_hasil VALUES ('', 
            '$soh_stock_opname_id',
            '$barang_id', 
            '$soh_barang_kode', 
            '$barang_stock', 
            '$soh_stock_fisik',
            '$soh_selisih', 
            '$soh_note',
            '$soh_date',
            '$soh_datetime',
            '$soh_tipe',
            '$soh_user',
            '$soh_barang_cabang')";
	mysqli_query($conn, $query);

	return mysqli_affected_rows($conn);
}

function editStockOpname($data) {
	global $conn;
	$id = $data["stock_opname_id"];

	// ambil data dari tiap elemen dalam form
	$stock_opname_user_upload 		= htmlspecialchars($data['stock_opname_user_upload']);
	$stock_opname_status 			= htmlspecialchars($data['stock_opname_status']);
	$stock_opname_date_upload 		= date("Y-m-d");
	$stock_opname_datetime_upload 	= date("d F Y g:i:s a");
	$stock_opname_cabang			= htmlspecialchars($data['stock_opname_cabang']);

	$query = "UPDATE stock_opname SET 
            stock_opname_status           = '$stock_opname_status',
            stock_opname_user_upload      = '$stock_opname_user_upload',
            stock_opname_date_upload      = '$stock_opname_date_upload',
            stock_opname_datetime_upload  = '$stock_opname_datetime_upload'
            WHERE stock_opname_id         = $id && stock_opname_cabang = $stock_opname_cabang;
            ";
    mysqli_query($conn, $query);
	return mysqli_affected_rows($conn);
}

// ========================================= Kategori ======================================= //
function tambahProvider($data) {
	global $conn;
	// ambil data dari tiap elemen dalam form
	$provider_nama 		= htmlspecialchars($data['provider_nama']);
	$provider_desc 		= htmlspecialchars($data['provider_desc']);
	$provider_saldo 	= htmlspecialchars($data['provider_saldo']);
	$provider_status 	= htmlspecialchars($data['provider_status']);
	$provider_terjual 	= 0;
	$provider_cabang 	= htmlspecialchars($data['provider_cabang']);

	// query insert data
	$query = "INSERT INTO provider VALUES ('', '$provider_nama', '$provider_desc', '$provider_saldo', '$provider_status', '$provider_terjual', '$provider_cabang')";
	mysqli_query($conn, $query);

	return mysqli_affected_rows($conn);
}

function editProvider($data) {
	global $conn;
	$id = $data["provider_id"];

	// ambil data dari tiap elemen dalam form
	$provider_nama 		= htmlspecialchars($data['provider_nama']);
	$provider_desc 		= htmlspecialchars($data['provider_desc']);

	$level 	= htmlspecialchars($data['level']);
	if ( $level === "super admin" ) {
		$provider_saldo 	= htmlspecialchars($data['provider_saldo']);
	} elseif ( $level === "admin" ) {
		$provider_saldo 	= htmlspecialchars(base64_decode($data['provider_saldo']));
	}
	
	$provider_status 	= htmlspecialchars($data['provider_status']);

	// query update data
	$query = "UPDATE provider SET 
				provider_nama   	= '$provider_nama',
				provider_desc 		= '$provider_desc',
				provider_saldo   	= '$provider_saldo',
				provider_status 	= '$provider_status'
				WHERE provider_id 	= $id
				";
	mysqli_query($conn, $query);
	return mysqli_affected_rows($conn);
}

function hapusProvider($id) {
	global $conn;
	mysqli_query( $conn, "DELETE FROM provider WHERE provider_id = $id");

	return mysqli_affected_rows($conn);
}

// ===================================== Barang Non Fisik ========================================= //
function tambahBarangNonFisik($data) {
	global $conn;
	// ambil data dari tiap elemen dalam form
	$bnf_kode 				= htmlspecialchars($data['bnf_kode']);
	$bnf_nama 				= htmlspecialchars($data['bnf_nama']);
	$bnf_deskripsi 			= htmlspecialchars($data['bnf_deskripsi']);
	$bnf_harga_beli 		= 0;
	$bnf_harga_jual 		= 0;
	$bnf_create_date 		= date("Y-m-d");
	$bnf_create_datetime 	= date("d F Y g:i:s a");;
	$bnf_user_create 		= htmlspecialchars($data['bnf_user_create']);
	$bnf_status 			= htmlspecialchars($data['bnf_status']);
	$bnf_cabang 			= htmlspecialchars($data['bnf_cabang']);

	
	$bnf_kode_cek = mysqli_num_rows(mysqli_query($conn, "select * from barang_non_fisik where bnf_kode = '".$bnf_kode."'&& bnf_cabang = $bnf_cabang "));

	if ( $bnf_kode_cek > 0 ) {
		echo "
			<script>
				alert('Kode Produk Non Fisik Sudah Terdaftar');
				document.location.href='';
			</script>
		";exit();
	} else {
		// query insert data
		$query = "INSERT INTO barang_non_fisik VALUES ('', '$bnf_kode', '$bnf_nama', '$bnf_deskripsi', '$bnf_harga_beli', '$bnf_harga_jual', '$bnf_create_date', '$bnf_create_datetime', '$bnf_user_create', '$bnf_status', '', '$bnf_cabang')";
		mysqli_query($conn, $query);

		return mysqli_affected_rows($conn);
	}
}

function editBarangNonFisik($data) {
	global $conn;
	$id = $data["bnf_id"];

	// ambil data dari tiap elemen dalam form
	$bnf_kode 				= htmlspecialchars($data['bnf_kode']);
	$bnf_nama 				= htmlspecialchars($data['bnf_nama']);
	$bnf_deskripsi 			= htmlspecialchars($data['bnf_deskripsi']);
	$bnf_status 			= htmlspecialchars($data['bnf_status']);
	// query update data
	$query = "UPDATE barang_non_fisik SET 
				bnf_kode   			= '$bnf_kode',
				bnf_nama 			= '$bnf_nama',
				bnf_deskripsi   	= '$bnf_deskripsi',
				bnf_status   		= '$bnf_status'
				WHERE bnf_id 		= $id
				";
	mysqli_query($conn, $query);
	return mysqli_affected_rows($conn);
}

function hapusBarangNonFisik($id) {
	global $conn;
	mysqli_query( $conn, "DELETE FROM barang_non_fisik WHERE bnf_id = $id");

	return mysqli_affected_rows($conn);
}

// ========================================= Request Saldo ======================================= //
function tambahRequestSaldo($data) {
	global $conn;
	// ambil data dari tiap elemen dalam form
	$rs_provider_id 		= htmlspecialchars($data['rs_provider_id']);
	$rs_user_id_request 	= htmlspecialchars($data['rs_user_id_request']);
	$rs_nominal 			= htmlspecialchars($data['rs_nominal']);
	$rs_cabang 				= htmlspecialchars($data['rs_cabang']);

	$rs_date_permintaan 	= date("Y-m-d");
	$rs_datetime_permintaan = date("d F Y g:i:s a");

	// query insert data
	$query = "INSERT INTO request_saldo VALUES ('', '$rs_provider_id', '$rs_user_id_request', '$rs_nominal', '$rs_date_permintaan', '$rs_datetime_permintaan', '', '', '', 0, '$rs_cabang')";
	mysqli_query($conn, $query);

	return mysqli_affected_rows($conn);
}

function editRequestSaldo($data) {
	global $conn;
	$id = $data["rs_id"];

	// ambil data dari tiap elemen dalam form
	$rs_user_id_acc 	= htmlspecialchars($data['rs_user_id_acc']);
	$rs_provider_id 	= htmlspecialchars($data['rs_provider_id']);
	$rs_nominal 		= htmlspecialchars($data['rs_nominal']);

	$rs_date_acc 		= date("Y-m-d");
	$rs_datetime_acc 	= date("d F Y g:i:s a");

	// query update data
	$query = "UPDATE request_saldo SET 
				rs_user_id_acc   	= '$rs_user_id_acc',
				rs_date_acc 		= '$rs_date_acc',
				rs_datetime_acc 	= '$rs_datetime_acc',
				rs_status 			= 1
				WHERE rs_id 		= $id
				";
	mysqli_query($conn, $query);

	// Mencari data provider untuk update saldo
	$provider = mysqli_query($conn, "SELECT provider_saldo FROM provider WHERE provider_id = $rs_provider_id ");
	$provider = mysqli_fetch_array($provider);
	$provider_saldo = $provider['provider_saldo'] + $rs_nominal;

	$query1 = "UPDATE provider SET 
				provider_saldo   	= '$provider_saldo'
				WHERE provider_id   = $rs_provider_id
				";
	mysqli_query($conn, $query1);

	return mysqli_affected_rows($conn);
}

function hapusRequestSaldo($id) {
	global $conn;
	mysqli_query( $conn, "DELETE FROM request_saldo WHERE rs_id = $id");

	return mysqli_affected_rows($conn);
}

// ========================================= pengeluaran ======================================= //
function tambahpengeluaran($data) {
	global $conn;
	// ambil data dari tiap elemen dalam form
	$pengeluaran_name          = htmlspecialchars($data['pengeluaran_name']);
	$pengeluaran_penerima      = htmlspecialchars($data['pengeluaran_penerima']);
	$pengeluaran_date          = htmlspecialchars($data['pengeluaran_date']);
	$pengeluaran_create        = htmlspecialchars($data['pengeluaran_create']);
	$pengeluaran_desc          = htmlspecialchars($data['pengeluaran_desc']);
	$pengeluaran_metode 	   = htmlspecialchars($data['pengeluaran_metode']);
	$pengeluaran_total_dibayar = htmlspecialchars($data['pengeluaran_total_dibayar']);

	if ( empty($data['pengeluaran_lababersih']) ) {
		$pengeluaran_lababersih    = 0;
	} else {
		$pengeluaran_lababersih    = 1;
	}
	$pengeluaran_cabang        = htmlspecialchars($data['pengeluaran_cabang']);

	// query insert data
	$query = "INSERT INTO pengeluaran VALUES ('', '$pengeluaran_name', '$pengeluaran_penerima', '$pengeluaran_date', '$pengeluaran_create', '$pengeluaran_desc', '$pengeluaran_metode', '$pengeluaran_total_dibayar', '', '', '', '$pengeluaran_lababersih', '$pengeluaran_cabang' )";
	mysqli_query($conn, $query);

	return mysqli_affected_rows($conn);
}

function editpengeluaran($data) {
	global $conn;
	$id = $data["pengeluaran_id"];

	// ambil data dari tiap elemen dalam form
	$pengeluaran_name          = htmlspecialchars($data['pengeluaran_name']);
	$pengeluaran_penerima      = htmlspecialchars($data['pengeluaran_penerima']);
	$pengeluaran_desc          = htmlspecialchars($data['pengeluaran_desc']);
	$pengeluaran_metode 	   = htmlspecialchars($data['pengeluaran_metode']);
	$pengeluaran_total_dibayar = htmlspecialchars($data['pengeluaran_total_dibayar']);
	$pengeluaran_edit_user     = htmlspecialchars($data['pengeluaran_edit_user']);
	$pengeluaran_date_edit 	   = date("Y-m-d");
	$pengeluaran_datetime_edit = date("d F Y g:i:s a");

	if ( empty($data['pengeluaran_lababersih']) ) {
		$pengeluaran_lababersih = htmlspecialchars($data['pengeluaran_lababersih_old']);
	} else {
		$pengeluaran_lababersih    = htmlspecialchars($data['pengeluaran_lababersih']);
	}
	

	// query update data
	$query = "UPDATE pengeluaran SET 
				pengeluaran_name          	= '$pengeluaran_name',
				pengeluaran_penerima      	= '$pengeluaran_penerima',
				pengeluaran_desc 			= '$pengeluaran_desc',
				pengeluaran_metode 			= '$pengeluaran_metode',
				pengeluaran_total_dibayar 	= '$pengeluaran_total_dibayar',
				pengeluaran_edit_user 		= '$pengeluaran_edit_user',
				pengeluaran_date_edit 		= '$pengeluaran_date_edit',
				pengeluaran_datetime_edit 	= '$pengeluaran_datetime_edit',
				pengeluaran_lababersih 		= '$pengeluaran_lababersih'
				WHERE pengeluaran_id 		= $id
				";
	mysqli_query($conn, $query);
	return mysqli_affected_rows($conn);
}

function hapuspengeluaran($id) {
	global $conn;
	mysqli_query( $conn, "DELETE FROM pengeluaran WHERE pengeluaran_id = $id");

	return mysqli_affected_rows($conn);
}

// ======================================= Kasbon ========================================= //
function tambahKasbon($data) {
	global $conn;
	// ambil data dari tiap elemen dalam form
	$kasbon_nama = htmlspecialchars($data['kasbon_nama']);
	$kasbon_user_id_kasbon	 = htmlspecialchars($data['kasbon_user_id_kasbon']);
	$kasbon_desc = htmlspecialchars($data['kasbon_desc']);
	$kasbon_date = htmlspecialchars($data['kasbon_date']);
	$kasbon_user_create	 = htmlspecialchars($data['kasbon_user_create']);
	$kasbon_total = htmlspecialchars($data['kasbon_total']);
	$kasbon_cabang	 = htmlspecialchars($data['kasbon_cabang']);

	// query insert data
	$query = "INSERT INTO kasbon VALUES ('', '$kasbon_nama', '$kasbon_user_id_kasbon', '$kasbon_desc', '$kasbon_date', '$kasbon_user_create', '$kasbon_total', 0, 0, '$kasbon_cabang')";
	mysqli_query($conn, $query);

	return mysqli_affected_rows($conn);
}

function tambahCicilanKasbon($data) {
	global $conn;

	// ambil data dari tiap elemen dalam form
	$kasbon_total				= $data['kasbon_total'];
	$kasbon_total_cicilan		= $data['kasbon_total_cicilan'];
	$kb_nominal  				= htmlspecialchars($data['kb_nominal']);

	$kasbon_sisa 				= $data['kasbon_sisa'];
	if ( $kb_nominal > $kasbon_sisa ) {
		echo '
			<script>
				alert("Inputan nominal terlalu besar diwajibkan yang bayar harus sesuai sisa cicilan !!")
				document.location.href = "";
			</script>
		'; exit();
	}

	$invoice_bayar         		= $kasbon_total_cicilan + $kb_nominal;

	$kb_kasbon_id				= $data['kb_kasbon_id'];
	$kb_date					= date("Y-m-d");
	$kb_datetime				= date("d F Y g:i:s a");
	$kb_user_create				= $data['kb_user_create'];
	$kb_tipe_pembayaran			= 0;
	$kb_cabang					= $data['kb_cabang'];

	if ( $invoice_bayar >= $kasbon_total ) {
		// query update data
		$query = "UPDATE kasbon SET 
					kasbon_total_cicilan       = '$invoice_bayar',
					kasbon_status_lunas        = 1
					WHERE kasbon_id = $kb_kasbon_id
				";
		mysqli_query($conn, $query);

	} else {
		// query update data
		$query = "UPDATE kasbon SET 
					kasbon_total_cicilan       = '$invoice_bayar',
					kasbon_status_lunas        = 0
					WHERE kasbon_id = $kb_kasbon_id
				";
		mysqli_query($conn, $query);
	} 
	
	

	// query insert data
	$query2 = "INSERT INTO kasbon_cicilan VALUES ('', '$kb_kasbon_id', '$kb_date', '$kb_datetime', '$kb_user_create', '$kb_nominal', '$kb_tipe_pembayaran', '$kb_cabang')";
	mysqli_query($conn, $query2);

	return mysqli_affected_rows($conn);
}

function hapusCicilanKasbon($id) {
	global $conn;


	// Ambil ID produk
	$data_id = $id;

	// Mencari Cicilan Kasbon
	$kb = mysqli_query( $conn, "select kb_kasbon_id, kb_nominal, kb_cabang from kasbon_cicilan where kb_id = $data_id ");
    $kb = mysqli_fetch_array($kb); 
    $kb_kasbon_id 	= $kb["kb_kasbon_id"];
    $kb_nominal 	= $kb["kb_nominal"];
    $kb_cabang 		= $kb["kb_cabang"];

    // Mencari Nilai Bayar
    $bayar = mysqli_query ( $conn, "select kasbon_total, kasbon_total_cicilan from kasbon where kasbon_id = $kb_kasbon_id && kasbon_cabang = '".$kb_cabang."' ");
    $bayar = mysqli_fetch_array($bayar);
    $kasbon_total         	   = $bayar['kasbon_total'];
    $kasbon_total_cicilan      = $bayar['kasbon_total_cicilan'];

    // Proses
    $kasbon_total_cicilan -=  $kb_nominal;

	if ( $kasbon_total_cicilan >= $kasbon_total ) {
		// query update data
		$query = "UPDATE kasbon SET 
					kasbon_total_cicilan       = '$kasbon_total_cicilan',
					kasbon_status_lunas        = 1
					WHERE kasbon_id = $kb_kasbon_id
				";
		mysqli_query($conn, $query);

	} else {
		// query update data
		$query = "UPDATE kasbon SET 
					kasbon_total_cicilan       = '$kasbon_total_cicilan',
					kasbon_status_lunas        = 0
					WHERE kasbon_id = $kb_kasbon_id
				";
		mysqli_query($conn, $query);
	} 
   
    
	mysqli_query( $conn, "DELETE FROM kasbon_cicilan WHERE kb_id = $id");

	return mysqli_affected_rows($conn);
}

function hapusKasbon($id, $sessionCabang) {
	global $conn;

	// Mencari data cicilan
	$count = mysqli_query($conn, "SELECT * FROM kasbon_cicilan WHERE kb_kasbon_id = $id && kb_cabang = $sessionCabang ");
	$count = mysqli_num_rows($count);

	if ( $count > 0 ) {
		mysqli_query( $conn, "DELETE FROM kasbon_cicilan WHERE kb_kasbon_id = $id && kb_cabang = $sessionCabang  ");
		mysqli_query( $conn, "DELETE FROM kasbon WHERE kasbon_id = $id");
		
	} else {
		mysqli_query( $conn, "DELETE FROM kasbon WHERE kasbon_id = $id");
	}

	return mysqli_affected_rows($conn);
}

// ========================================= Kategori ======================================= //
function tambahKategoriPengeluaran($data) {
	global $conn;
	// ambil data dari tiap elemen dalam form
	$pm_nama = htmlspecialchars($data['pm_nama']);
	$pm_status = $data['pm_status'];
	$pm_cabang = $data['pm_cabang'];

	// Cek Email
	$email_user_cek = mysqli_num_rows(mysqli_query($conn, "select * from pengeluaran_master where pm_nama = '$pm_nama' && pm_cabang = $pm_cabang "));

	if ( $email_user_cek > 0 ) {
		echo "
			<script>
				alert('Kategori Sudah Terdaftar Inputkan Nama Lain !!');
				document.location.href='';
			</script>
		"; exit();
	} else {
		// query insert data
		$query = "INSERT INTO pengeluaran_master VALUES ('', '$pm_nama', '$pm_status', '$pm_cabang')";
		mysqli_query($conn, $query);
	}

	return mysqli_affected_rows($conn);
}

function editKategoriPengeluaran($data) {
	global $conn;
	$id = $data["pm_id"];

	// ambil data dari tiap elemen dalam form
	$pm_nama = htmlspecialchars($data['pm_nama']);
	$pm_status = $data['pm_status'];

	// query update data
	$query = "UPDATE pengeluaran_master SET 
				pm_nama   = '$pm_nama',
				pm_status = '$pm_status'
				WHERE pm_id = $id
				";
	mysqli_query($conn, $query);
	return mysqli_affected_rows($conn);
}

function hapusKategoriPengeluaran($id) {
	global $conn;
	mysqli_query( $conn, "DELETE FROM pengeluaran_master WHERE pm_id = $id");

	return mysqli_affected_rows($conn);
}


?>



