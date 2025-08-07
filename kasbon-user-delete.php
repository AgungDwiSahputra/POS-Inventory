<?php 
include 'aksi/functions.php';

$id = abs((int)base64_decode($_GET["id"]));
$page = base64_decode($_GET['page']);

$data = query("SELECT * FROM user WHERE user_id = $id ")[0];
$user_tipe_login = $data['user_tipe_login'];

// user_tipe_login = 1 aksi delete
// Jika user_tipe_login = 0 aksi edit

if( hapusUserKasbon($id, $user_tipe_login) > 0) {
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