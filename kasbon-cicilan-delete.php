<?php 
include 'aksi/functions.php';

$id = base64_decode($_GET["id"]);
$page = $_GET['page'];

if( hapusCicilanKasbon($id) > 0) {
	echo "
		<script>
			document.location.href = 'kasbon-cicilan?id=".$page."';
		</script>
	";
} else {
	echo "
		<script>
			alert('Data gagal dihapus');
			document.location.href = 'kasbon-cicilan?id=".$page."';
		</script>
	";
}

?>