<?php 
include 'aksi/functions.php';

$id = $_GET["id"];
$kategori_id = $_GET["kategori_id"];

if( hapusSubKategori($id) > 0) {
	echo "
		<script>
			document.location.href = 'kategori-edit?id=$kategori_id';
		</script>
	";
} else {
	echo "
		<script>
			alert('Data gagal dihapus');
			document.location.href = 'kategori';
		</script>
	";
}

?>