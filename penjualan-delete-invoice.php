<?php 
include 'aksi/functions.php';

$id = $_GET["id"];
$page = $_GET["page"];

if ( $page === "penjualan" ) {
	$link = "penjualan";
} elseif ( $page === "piutang" ) {
	$link = "piutang";
}

$tipe = mysqli_query($conn, "SELECT invoice_tipe_non_fisik FROM invoice WHERE invoice_id = $id ");
$tipe = mysqli_fetch_array($tipe);
$invoice_tipe_non_fisik = $tipe['invoice_tipe_non_fisik'];

if ( $invoice_tipe_non_fisik < 1 ) {
	if( hapusPenjualanInvoice($id) > 0) {
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
	echo "
		<script>
			document.location.href = 'penjualan-delete-invoice-non-fisik?id=".base64_encode($id)."&link=".base64_encode($link)."';
		</script>
	";
}


?>