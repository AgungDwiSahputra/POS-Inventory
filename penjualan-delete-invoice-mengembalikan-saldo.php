<?php 
include 'aksi/functions.php';

$id   = $_GET["id"];
$page = $_GET["page"];
$tipe = $_GET["tipe"];

if ( $page === "penjualan" ) {
	$link = "penjualan";
} elseif ( $page === "piutang" ) {
	$link = "piutang";
}

if ( $tipe > 0 ) {
	if( hapusPenjualanInvoiceTarikTunai($id) > 0) {
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

} else {
	if( hapusPenjualanInvoiceNonFisik($id) > 0) {
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
}


?>