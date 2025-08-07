<?php 
include '_header-artibut.php';

$id = abs((int)base64_decode($_GET["id"]));
$page = $_GET['page'];

if ( $page === "lunas" ) {
	$link = "kasbon-lunas";
} else {
	$link = "kasbon";
}

if( hapusKasbon($id, $sessionCabang) > 0) {
	echo "
		<script>
			document.location.href = '".$link."';
		</script>
	";
} else {
	echo "
		<script>
			alert('Data gagal dihapus');
			document.location.href = '".$link."';
		</script>
	";
}

?>