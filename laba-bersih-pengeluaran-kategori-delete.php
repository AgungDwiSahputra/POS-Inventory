<?php 
include 'aksi/functions.php';

$id = abs((int)base64_decode($_GET["id"]));

if( hapusKategoriPengeluaran($id) > 0) {
	echo "
		<script>
			document.location.href = 'laba-bersih-pengeluaran-kategori';
		</script>
	";
} else {
	echo "
		<script>
			alert('Data gagal dihapus');
			document.location.href = 'laba-bersih-pengeluaran-kategori';
		</script>
	";
}

?>