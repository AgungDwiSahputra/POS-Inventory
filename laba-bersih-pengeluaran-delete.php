<?php 
include 'aksi/functions.php';

$id = base64_decode($_GET["id"]);

if( hapuspengeluaran($id) > 0) {
	echo "
		<script>
			document.location.href = 'laba-bersih-pengeluaran';
		</script>
	";
} else {
	echo "
		<script>
			alert('Data gagal dihapus');
			document.location.href = 'laba-bersih-pengeluaran';
		</script>
	";
}

?>