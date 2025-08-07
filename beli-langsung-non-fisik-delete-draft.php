<?php 
include 'aksi/functions.php';

$id = $_GET["id"];
$r  = $_GET["r"];

$link = base64_encode($r);
// Kondisi jika link cash atau hutang
$page = "beli-langsung-non-fisik-draft?customer=".$_GET['customer']."&r=".$link."&invoice=".$_GET['invoice'];

if( hapusKeranjangNonFisikDraft($id) > 0) {
	echo "
		<script>
			document.location.href = '".$page."';
		</script>
	";
} else {
	echo "
		<script>
			alert('Data gagal dihapus');
			document.location.href = '".$page."';
		</script>
	";
}

?>