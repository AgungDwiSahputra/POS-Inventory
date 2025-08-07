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
          <div class="col-md-12">
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
                    <div class="col-md-6 col-lg-6">
                        <div class="form-group">
                          <label for="customer_nama">Provider</label>
                          <?php  
                            $rs_provider_id = $saldo['rs_provider_id'];
                            $namaSaldo = mysqli_query($conn, "SELECT provider_nama FROM provider WHERE provider_id = $rs_provider_id ");
                            $namaSaldo = mysqli_fetch_array($namaSaldo);
                          ?>

                          <input type="text" name="customer_nama" class="form-control" id="customer_nama" value="<?= $namaSaldo['provider_nama']; ?>" readonly>
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

                    <div class="col-md-6 col-lg-6">
                      <div class="form-group">
                          <label for="customer_create">User ACC</label>
                          <?php  
                            $rs_user_id_acc = $saldo['rs_user_id_acc'];

                            if ( $rs_user_id_acc > 0 ) {
                              $namaUserAcc = mysqli_query($conn, "SELECT user_nama FROM user WHERE user_id = $rs_user_id_acc ");
                              $namaUserAcc = mysqli_fetch_array($namaUserAcc);
                              $namaUserAcc = $namaUserAcc['user_nama'];

                            } else {
                              $namaUserAcc = "-";
                            }
                          ?>

                          <input type="text" name="rs_user_id_acc" class="form-control" id="rs_user_id_acc" value="<?= $namaUserAcc; ?>" readonly>
                        </div>

                        <div class="form-group">
                            <label for="rs_datetime_acc">Waktu ACC</label>
                            <input type="text" name="rs_datetime_acc" class="form-control" id="rs_datetime_acc" value="<?= $saldo['rs_datetime_acc']; ?>" readonly>
                        </div>

                        <div class="form-group">
                            <label for="rs_datetime_acc">Status</label>
                            <?php  
                              $rs_status = $saldo['rs_status'];
                              if ( $rs_status < 1 ) {
                                $rs_status_view = "Belum ACC";

                              } else {
                                $rs_status_view = "ACC";
                              }
                            ?>

                            <input type="text" name="rs_status" class="form-control" id="rs_status" value="<?= $rs_status_view; ?>" readonly>
                        </div>
                    </div>
                </div>
                <!-- /.card-body -->

                <div class="card-footer text-right">
                   <a href="#!" class="btn btn-success float-right" onclick="self.close()" style="margin-right: 5px;"> Kembali</a>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </section>


  </div>


<?php include '_footer.php'; ?>