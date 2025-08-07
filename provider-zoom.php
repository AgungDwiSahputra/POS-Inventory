<?php 
  include '_header.php';
  include '_nav.php';
  include '_sidebar.php'; 
?>
<?php  
  if ( $levelLogin === "kasir" || $levelLogin === "kurir" ) {
    echo "
      <script>
        document.location.href = 'bo';
      </script>
    ";
  }  
 
// ambil data di URL
$id = abs((int)base64_decode($_GET['id']));


// query data mahasiswa berdasarkan id
$data = query("SELECT * FROM provider WHERE provider_id = $id ")[0];
?>


  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Tambah Data Provider</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="bo">Home</a></li>
              <li class="breadcrumb-item active">Data Provider</li>
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
                <h3 class="card-title">Provider</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form role="form" action="" method="post">
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-6 col-lg-6">
                        <div class="form-group">
                          <label for="provider_nama">Nama Provider</label>
                          <input type="text" name="provider_nama" class="form-control" id="provider_nama" placeholder="Input Nama Provider" value="<?= $data['provider_nama']; ?>" readonly>
                        </div>

                        <div class="form-group">
                              <label for="provider_desc">Deskripsi</label>
                              <textarea name="provider_desc" id="provider_desc" class="form-control" rows="5" readonly="readonly" placeholder="Deskripsi Lengkap"><?= $data['provider_desc']; ?></textarea>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-6">
                        <div class="form-group">
                          <label for="provider_saldo">Saldo</label>
                          <input type="number" name="provider_saldo" class="form-control" id="provider_saldo" placeholder="Input Nominal Saldo" readonly value="<?= $data['provider_saldo']; ?>">
                        </div>
                        <div class="form-group ">
                            <label for="provider_status">Status</label>
                              <?php  
                                if ( $data['provider_status'] === '1' ) {
                                  $status = "Active";
                                } else {
                                  $status = " Not Active";
                                }
                              ?>
                            <input type="text" name="provider_saldo" class="form-control" id="provider_saldo" placeholder="Input Nominal Saldo" readonly value="<?= $status; ?>">
                        </div>
                        <div class="form-group">
                          <label for="provider_terjual">Terjual</label>
                          <input type="text" name="provider_terjual" class="form-control" id="provider_terjual" placeholder="Input Nominal Saldo" readonly value="<?= $data['provider_terjual']; ?>x">
                        </div>
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