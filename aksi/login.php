<?php
include 'functions.php';

// Cek apakah form sudah di-submit
if (!isset($_POST['user_email']) || !isset($_POST['user_password'])) {
    echo"
        <script>
            alert('Email dan Password harus diisi!');
            window.location='../';
        </script>";
    exit;
}

// Cek apakah email dan password tidak kosong
if (empty($_POST['user_email']) || empty($_POST['user_password'])) {
    echo"
        <script>
            alert('Email dan Password tidak boleh kosong!');
            window.location='../';
        </script>";
    exit;
}

setcookie("emailPos", base64_encode($_POST['user_email']), time() + 31536000, "/");
setcookie("passPos", base64_encode($_POST['user_password']), time() + 31536000, "/");

$email    = mysqli_real_escape_string($conn, $_POST['user_email']);
$password = md5(md5(mysqli_real_escape_string($conn, $_POST['user_password'])));

// Debug: Log data yang diterima
error_log("Login attempt - Email: " . $email . ", Password: " . substr($password, 0, 10) . "...");

$cek = $conn->query("SELECT * FROM user WHERE user_email='$email' AND user_password='$password'");

// Debug: Log hasil query
error_log("Query result - Rows: " . $cek->num_rows);

if($cek->num_rows > 0)
{
	session_start();

	$r = $cek->fetch_array();
	$_SESSION['user_nama']      = $r['user_nama'];
	$_SESSION['user_email']     = $r['user_email'];
	$_SESSION['user_password']  = $r['user_password'];
	$_SESSION['user_status']    = $r['user_status'];
	$_SESSION['user_id']        = $r['user_id'];
	$_SESSION['user_level']     = $r['user_level'];
	$_SESSION['user_cabang']    = $r['user_cabang'];
	
	// Debug: Log session data
	error_log("Login successful - User ID: " . $r['user_id'] . ", Level: " . $r['user_level'] . ", Cabang: " . $r['user_cabang']);
	
	echo"<script>
			window.location='../bo';
		</script>";
}else{
	// Debug: Log failed login
	error_log("Login failed - Email: " . $email);
	
	echo"
		<script>
			alert('Email & Password Salah !!');
			window.location='../';
		</script>";
}
?>