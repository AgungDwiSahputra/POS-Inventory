<?php 
  include '_header.php';
  include '_nav.php';
  include '_sidebar.php'; 
?>

<?php  
// ambil data di URL
$id = abs((int)base64_decode($_GET['id']));


// query data mahasiswa berdasarkan id
$saldo = query("SELECT * FROM request_saldo WHERE rs_id = $id ")[0];

// cek apakah tombol submit sudah ditekan atau belum
if( isset($_POST["submit"]) ){
  // var_dump($_POST);

  // cek apakah data berhasil di tambahkan atau tidak
  if( editRequestSaldo($_POST) > 0 ) {
    echo "
      <script>
        document.location.href = 'request-saldo';
      </script>
    ";
  } else if( editRequestSaldo($_POST) == null ) {
    echo "
      <script>
        alert('Anda belum melakukan perubahan data !!');
        document.location.href = '';
      </script>
    ";
  } else {
    echo "
      <script>
        alert('data gagal ditambahkan');
      </script>
    ";
  }
  
}
?>


  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Lihat Data Request Saldo</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="bo">Home</a></li>
              <li class="breadcrumb-item active">Data Request Saldo</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <!-- left column -->
          <div class="col-md-6">
            <!-- general form elements -->
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">Data Request Saldo</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form role="form" action="" method="post">
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-12 col-lg-12">
                      <input type="hidden" name="rs_id" value="<?= $saldo['rs_id']; ?>">
                      <input type="hidden" name="rs_user_id_acc" value="<?= $_SESSION['user_id']; ?>">
                      <input type="hidden" name="rs_provider_id" value="<?= $saldo['rs_provider_id']; ?>">
                        <div class="form-group">
                          <label for="">Provider</label>
                          <?php  
                            $rs_provider_id = $saldo['rs_provider_id'];
                            $namaSaldo = mysqli_query($conn, "SELECT provider_nama FROM provider WHERE provider_id = $rs_provider_id ");
                            $namaSaldo = mysqli_fetch_array($namaSaldo);
                          ?>

                          <input type="text" name="" class="form-control" id="" value="<?= $namaSaldo['provider_nama']; ?>" readonly>
                        </div>

                        <div class="form-group">
                            <label for="rs_nominal">Nominal Saldo</label>
                            <input type="number" name="rs_nominal" class="form-control" id="rs_nominal" value="<?= $saldo['rs_nominal']; ?>" readonly>
                        </div>

                        <div class="form-group">
                          <label for="customer_create">User Request</label>
                          <?php  
                            $rs_user_id_request = $saldo['rs_user_id_request'];
                            $namaUser = mysqli_query($conn, "SELECT user_nama FROM user WHERE user_id = $rs_user_id_request ");
                            $namaUser = mysqli_fetch_array($namaUser);
                          ?>

                          <input type="text" name="rs_user_id_request" class="form-control" id="rs_user_id_request" value="<?= $namaUser['user_nama']; ?>" readonly>
                        </div>

                        <div class="form-group">
                            <label for="rs_datetime_permintaan">Waktu Request</label>
                            <input type="text" name="rs_datetime_permintaan" class="form-control" id="rs_datetime_permintaan" value="<?= $saldo['rs_datetime_permintaan']; ?>" readonly>
                        </div>
                    </div>
                </div>
                <!-- /.card-body -->

                <div class="card-footer text-right">
                  <?php if ( $saldo['rs_status'] < 1 ) : ?>
                   <button type="submit" name="submit" class="btn btn-primary">Setujui</button>
                  <?php else : ?>
                    <a href="request-saldo" class="btn btn-default float-right" style="margin-right: 5px;">Sudah Disetujui</a>
                  <?php endif; ?>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </section>


  </div>


<?php include '_footer.php'; ?>