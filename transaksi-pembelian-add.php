<?php 
include '_header-artibut.php';

// Cek apakah ID sudah berupa integer atau perlu di-decode
$id_raw = $_GET["id"];
if (is_numeric($id_raw)) {
    $id = abs((int)$id_raw);
} else {
    $id = abs((int)base64_decode($id_raw));
}
$r  = $_GET["r"];
// Buat Url Sesuai variabel $r
if ( $r < 1 ) {
	$linkBack = "transaksi-pembelian";
} else {
	$linkBack = "transaksi-pembelian?r=".base64_encode($r);
}

if ( $id == null ) {
	echo '
		<script>
			document.location.href = "beli-langsung";
		</script>
	';
}

// Cek apakah barang ada di database
$barang_result = query("SELECT * FROM barang WHERE barang_id = ".$id." && barang_cabang = ".$sessionCabang." ");
if (empty($barang_result)) {
    echo '
        <script>
            alert("Produk tidak ditemukan atau tidak tersedia untuk cabang ini!");
            document.location.href = "'.$linkBack.'";
        </script>
    ';
    exit;
}
$barang = $barang_result[0];

	$barang_id          = $barang['barang_id'];
	$keranjang_nama     = $barang['barang_nama'];
	$keranjang_harga    = 0;
	$keranjang_id_kasir = $_SESSION['user_id'];
	$keranjang_qty      = 1;
	$keranjang_cabang   = $sessionCabang;
	$keranjang_id_cek   = $barang_id.$keranjang_id_kasir.$keranjang_cabang;
	
   	// Insert Data ke Table Keranjang dengan function tambahKeranjangPembelian() Lokasi di file aksi/function.php
	$result = tambahKeranjangPembelian($barang_id, $keranjang_nama, $keranjang_harga, $keranjang_id_kasir, $keranjang_qty, $keranjang_cabang, $keranjang_id_cek);
	
	if($result > 0)
	{
		echo "
			<script>
				document.location.href = '".$linkBack."';
			</script>
		";
	} else {
		echo "
			<script>
				alert('Data gagal di Insert. Silakan coba lagi atau hubungi administrator.');
				document.location.href = '".$linkBack."';
			</script>
		";
   	}

?>