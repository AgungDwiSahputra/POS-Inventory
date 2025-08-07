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

// cek apakah tombol submit sudah ditekan atau belum
if( isset($_POST["submit"]) ){
  // var_dump($_POST);

  // cek apakah data berhasil di tambahkan atau tidak
  if( editProvider($_POST) > 0 ) {
    echo "
      <script>
        document.location.href = 'provider';
      </script>
    ";
  } elseif( editProvider($_POST) == null ) {
    echo "
      <script>
        alert('Anda belum melakukan perubahan Data !!');
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
                        <input type="hidden" name="provider_id" value="<?= $id; ?>">
                        <input type="hidden" name="level" value="<?= $_SESSION['user_level']; ?>">
                        <div class="form-group">
                          <label for="provider_nama">Nama Provider</label>
                          <input type="text" name="provider_nama" class="form-control" id="provider_nama" placeholder="Input Nama Provider" value="<?= $data['provider_nama']; ?>" required>
                        </div>

                        <div class="form-group">
                              <label for="provider_desc">Deskripsi</label>
                              <textarea name="provider_desc" id="provider_desc" class="form-control" rows="5" required="required" placeholder="Deskripsi Lengkap"><?= $data['provider_desc']; ?></textarea>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-6">
                        <div class="form-group">
                          <label for="provider_saldo">Saldo</label>

                          <?php if ( $levelLogin === "super admin" ) : ?>
                            <input type="number" name="provider_saldo" class="form-control" id="provider_saldo" placeholder="Input Nominal Saldo" required value="<?= $data['provider_saldo']; ?>">
                          <?php else : ?>
                            <input type="number" name="" class="form-control" id="provider_saldo" placeholder="Input Nominal Saldo" readonly="" value="<?= $data['provider_saldo']; ?>">
                            <input type="hidden" name="provider_saldo" value="<?= base64_encode($data['provider_saldo']); ?>" required>
                          <?php endif; ?>
                        </div>
                        <div class="form-group ">
                            <label for="provider_status">Status</label>
                            <div class="">
                              <?php  
                                if ( $data['provider_status'] === '1' ) {
                                  $status = "Active";
                                } else {
                                  $status = " Not Active";
                                }
                              ?>
                                <select name="provider_status" required="" class="form-control ">
                                  <option value="<?= $data['provider_status']; ?>"><?= $status; ?></option>
                                  <?php  
                                    if ( $data['provider_status'] === '1' ) {
                                      echo '
                                        <option value="0">Not Active</option>
                                      ';
                                    } else {
                                      echo '
                                        <option value="1">Active</option>
                                      ';
                                    }
                                  ?>
                                </select>
                            </div>
                        </div>
                    </div>
                  </div>
                </div>
                <!-- /.card-body -->

                <div class="card-footer text-right">
                  <button type="submit" name="submit" class="btn btn-primary">Submit</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </section>


  </div>


<?php include '_footer.php'; ?>