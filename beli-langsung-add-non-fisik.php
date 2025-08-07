<?php 
include '_header-artibut.php';

$id 		= abs((int)base64_decode($_GET["id"]));
$customer   = base64_decode($_GET['customer']);
$r  		= $_GET["r"];
// Buat Url Sesuai variabel $r
if ( $r < 1 ) {
	$linkBack = "beli-langsung-non-fisik?customer=".$_GET['customer']." ";
} else {
	$linkBack = "beli-langsung-non-fisik?customer=".$_GET['customer']."&r=".base64_encode($r);
}

if ( $id == null ) {
	echo '
		<script>
			document.location.href = "beli-langsung?customer='.base64_encode($customer).'";
		</script>
	';
}

$jasa = query("SELECT * FROM barang_non_fisik WHERE bnf_id = ".$id." && bnf_cabang = ".$sessionCabang." ")[0];

	$knf_cabang   	 	= $sessionCabang;
	$knf_barang_id   	= $jasa['bnf_id'];
	$namaProdukNonFisik = mysqli_query($conn, "SELECT bnf_nama FROM barang_non_fisik WHERE bnf_id = $knf_barang_id ");
    $namaProdukNonFisik = mysqli_fetch_array($namaProdukNonFisik);
    $knf_barang_nama    = strtolower($namaProdukNonFisik['bnf_nama']);

	$knf_barang_kode   	= $jasa['bnf_kode'];
	$knf_nama     		= $jasa['bnf_nama'];
	$knf_harga_beli     = $jasa['bnf_harga_beli'];
	$knf_harga_jual    	= $jasa['bnf_harga_jual'];
	$knf_id_kasir 		= $_SESSION['user_id'];
	$knf_qty      		= 1;
	$knf_id_cek   		= $knf_barang_id.$knf_id_kasir.$knf_cabang;


   		// Insert Data ke Table Keranjang dengan function tambahKeranjang() Lokasi di file aksi/function.php
		if( tambahKeranjangNonFisik($knf_cabang, $knf_barang_id, $knf_barang_nama, $knf_barang_kode, $knf_nama, $knf_harga_beli, $knf_harga_jual, $knf_id_kasir, $knf_qty, $knf_id_cek, $linkBack) > 0) {
			echo "
				<script>
					document.location.href = '".$linkBack."';
				</script>
			";
		} else {
			echo "
				<script>
					alert('Data gagal di Insert');
					document.location.href = '".$linkBack."';
				</script>
			";
		}
   	

?>