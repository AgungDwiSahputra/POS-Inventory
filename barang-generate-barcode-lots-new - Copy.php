<?php 
  include '_header-artibut.php';
?>
<?php  
  $status = $_SESSION['user_status'];
    if ( $status === '0') {
    echo"
          <script>
            alert('Akun Tidak Aktif');
            window.location='./';
          </script>";
  }
?>
<!DOCTYPE html>
<html>
<head>
	<title>Nota Cetak POS - SaaS Dekreatif.id</title>
	<meta charset=utf-8>
	<meta name=description content="">
	<meta name=viewport content="width=device-width, initial-scale=1">
	<!-- Tempusdominus Bootstrap 3 -->
    <link rel="stylesheet" type="text/css" href="dist/css/bootstrap-3.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="dist/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="dist/css/style.css">

    <!-- jQuery -->
    <script src="plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
</head>
<body>

<?php  
  if ( $levelLogin === "kasir") {
    echo "
      <script>
        document.location.href = 'bo';
      </script>
    ";
  }

  $panjang_kertas         = htmlspecialchars($_POST['panjang_kertas']);
  $panjang_per_kotak      = htmlspecialchars($_POST['panjang_per_kotak']);
  $tinggi_per_kotak       = htmlspecialchars($_POST['tinggi_per_kotak']);
  $magin_atas             = htmlspecialchars($_POST['magin_atas']);
  $magin_bawah            = htmlspecialchars($_POST['magin_bawah']);
  $margin_kanan_kiri      = htmlspecialchars($_POST['margin_kanan_kiri']);

  $input_barcode  = htmlspecialchars($_POST['input_barcode']);
  $input_kode     = htmlspecialchars($_POST['input_kode']);

  $barang = mysqli_query( $conn, "select barang_nama, barang_harga from barang where barang_kode = '".$input_kode."'");
  $ns = mysqli_fetch_array($barang); 
  $barang_nama  = $ns["barang_nama"];
  $barang_harga = number_format($ns["barang_harga"], 0, ',', '.');

?>
  
  <style>
    /*.barcode-cetak-new {
      width: 104mm;
      display: flex;
      flex-wrap: wrap;
    }
    .barcode-cetak-new-box {
      width: 33mm;
      height: 15mm;
      margin-top: 3mm;
      margin-left: 2mm;
      padding-left: 1mm;
      padding-right: 1mm;
      width: calc(33.33% - 10px); 
    }*/

    .barcode-cetak-new {
      /*display: block;
      margin: 0 auto;*/
      width: <?= $panjang_kertas."mm"; ?>;
      display: flex;
      flex-wrap: wrap;
      /*border: 1px solid #000;*/
    }
    .barcode-cetak-new-box {
      width: <?= $panjang_per_kotak."mm"; ?>;
      height: <?= $tinggi_per_kotak."mm"; ?>;
      margin-top: <?= $magin_atas."mm"; ?>;
      margin-bottom: <?= $magin_bawah."mm"; ?>;
      margin-right: <?= $margin_kanan_kiri."mm"; ?>;
      padding-left: 1mm;
      padding-right: 1mm;
      /*width: calc(33.33% - 10px);  Untuk membuat maksimal 3 kolom */
      border: 1px solid #000;
    }

    .barcode-cetak-new-box-title {
      font-size: 9px;
      font-weight: bold;
    }
    .barcode-cetak-new-box-desc {
      font-size: 10px;
      font-weight: bold;
    }
    .barcode-cetak-new-box-desc span {
      float: right;
    }
  </style>
  
  <?php  
    $widthImg = $panjang_per_kotak - 3;
  ?>
  <section class="barcode-cetak-new">
 <?php  
   for ( $i = 1; $i <= $input_barcode; $i++ ) {
   echo '
      <div class="barcode-cetak-new-box">
          <div class="barcode-cetak-new-box-title">
              '.$barang_nama.'
          </div>
          <div class="barcode-cetak-new-box-title">
              <img src="vendor/barcode/img/'.$input_kode.'-produk-'.$barang_nama.'-cabang-'.$sessionCabang.'.png" style="max-width: '.$widthImg.'mm; margin-bottom: 5px;">
          </div>
          <div class="barcode-cetak-new-box-desc">
              <div class="row">
                  <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                      Rp
                  </div>
                  <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
                      <span>'.$barang_harga.'</span>
                  </div>
              </div>
          </div>
      </div>
    ';
    }
  ?>
  </section>

</body>
</html>
<script>
  window.print();
</script>