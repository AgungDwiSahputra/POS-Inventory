<?php 
include 'aksi/functions.php';

$id = abs((int)base64_decode($_GET['id']));

if( hapusProvider($id) > 0) {
	echo "
		<script>
			document.location.href = 'provider';
		</script>
	";
} else {
	echo "
		<script>
			alert('Data gagal dihapus');
			document.location.href = 'provider';
		</script>
	";
}

?>