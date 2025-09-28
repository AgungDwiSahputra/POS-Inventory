<?php 
	  include 'aksi/halau.php'; 
  	include 'aksi/functions.php';

    // Cek apakah session sudah ada
    if (!isset($_SESSION['user_level']) || !isset($_SESSION['user_status']) || !isset($_SESSION['user_id'])) {
        echo"
          <script>
            alert('Session tidak valid. Silakan login ulang.');
            window.location='./';
          </script>";
        exit;
    }

    $levelLogin = $_SESSION['user_level'];
    $status = $_SESSION['user_status'];
    if ( $status === '0') {
    echo"
          <script>
            alert('Akun Tidak Aktif');
            window.location='./';
          </script>";
    }
      	
  	// Membuat data user cabang dinamis 

    $userLoginCabang = mysqli_query( $conn, "select user_cabang from user where user_id = '".$_SESSION['user_id']."'");
    $sessionCabangData = mysqli_fetch_array($userLoginCabang); 
    $sessionCabang     = $sessionCabangData['user_cabang'];

    // Cek apakah sessionCabang valid
    if ($sessionCabang === null || $sessionCabang === '') {
        echo"
          <script>
            alert('Data cabang tidak valid. Silakan login ulang.');
            window.location='./';
          </script>";
        exit;
    }

    // $sessionCabang     = $_SESSION['user_cabang'];
    $dataTokoLogin = query("SELECT * FROM toko WHERE toko_cabang = $sessionCabang")[0];

  	// End Membuat data user cabang dinamis

    if ( $sessionCabang < 1 ) {
      $tipeToko = "Pusat";
    } else {
      $tipeToko = "Cabang ".$sessionCabang;
    }
?>