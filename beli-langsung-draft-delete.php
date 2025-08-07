<?php 
include 'aksi/functions.php';

$invoice  = $_GET["invoice"];
$customer = $_GET["customer"];
$cabang   = $_GET["cabang"];
$page     = base64_decode($_GET['page']);

if ( $page === "nonfisik" ) :
	$link = "beli-langsung-non-fisik?customer=".$customer;
else :
	$link = "beli-langsung?customer=".$customer;
endif;


if( hapusDraft($invoice, $cabang, $page) > 0) {
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