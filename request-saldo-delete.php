<?php 
include 'aksi/functions.php';

$id = abs((int)base64_decode($_GET["id"]));

if( hapusRequestSaldo($id) > 0) {
	echo "
		<script>
			document.location.href = 'request-saldo';
		</script>
	";
} else {
	echo "
		<script>
			alert('Data gagal dihapus');
			document.location.href = 'request-saldo';
		</script>
	";
}

?>