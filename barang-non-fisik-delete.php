<?php 
include 'aksi/functions.php';

$id = abs((int)base64_decode($_GET['id']));

	// if( hapusBarangNonFisik($id) > 0) {
	// 	echo "
	// 		<script>
	// 			document.location.href = 'barang-non-fisik';
	// 		</script>
	// 	";
	// } else {
	// 	echo "
	// 		<script>
	// 			alert('Data gagal dihapus');
	// 			document.location.href = 'barang-non-fisik';
	// 		</script>
	// 	";
	// }

	
$penjualan = mysqli_query($conn, "select * from penjualan_barang_non_fisik where pbnf_barang_id = $id");
$jmlPenjualan = mysqli_num_rows($penjualan);

if ( $jmlPenjualan < 1 ) {
	if( hapusBarangNonFisik($id) > 0) {
		echo "
			<script>
				document.location.href = 'barang-non-fisik';
			</script>
		";
	} else {
		echo "
			<script>
				alert('Data gagal dihapus');
				document.location.href = 'barang-non-fisik';
			</script>
		";
	}
} else {
	echo "
		<script>
			alert('Data tidak bisa dihapus karena masih ada di data Invoice Penjualan');
			document.location.href = 'barang-non-fisik';
		</script>
	";
}

?>