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
?>
<?php  

// cek apakah tombol submit sudah ditekan atau belum
if( isset($_POST["submit"]) ){
  // var_dump($_POST);

  // cek apakah data berhasil di tambahkan atau tidak
  if( tambahKasbon($_POST) > 0 ) {
    echo "
      <script>
        document.location.href = 'kasbon';
      </script>
    ";
  } else {
    echo "
      <script>
        alert('Data GAGAL Ditambahkan');
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
            <h1>Tambah Data Kasbon</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="bo">Home</a></li>
              <li class="breadcrumb-item active">Data Kasbon</li>
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
                <h3 class="card-title">Data Kasbon</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form role="form" action="" method="post">
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-6 col-lg-6">
                      <input type="hidden" name="kasbon_user_create" value="<?= $_SESSION['user_id']; ?>">
                      <input type="hidden" name="kasbon_cabang" value="<?= $sessionCabang; ?>">
                        <div class="form-group">
                          <label for="kasbon_date">Tanggal</label>
                          <input type="date" name="kasbon_date" class="form-control" id="kasbon_date" value="<?= date("Y-m-d"); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="tanggal_akhir">Nama</label>
                            <select class="form-control select2bs4" required="" name="kasbon_user_id_kasbon">
                                <option selected="selected" value="">-- Pilih --</option>
                                <?php  
                                  $user = query("SELECT * FROM user WHERE user_cabang = $sessionCabang && user_kasbon_status = '1' ORDER BY user_id DESC ");
                                ?>
                                <?php foreach ( $user as $ctr ) : ?>
                                  <?php if ( $ctr['user_id'] != 0 ) { ?>
                                  <option value="<?= $ctr['user_id'] ?>"><?= $ctr['user_nama'] ?></option>
                                  <?php } ?>
                                <?php endforeach; ?>
                              </select>
                              <small>
                                <a href="user-add?cabang=<?= base64_encode($sessionCabang); ?>&page=<?= base64_encode("kasbon"); ?>"><i class="fa fa-plus"></i> Tambah Nama</a>
                              </small>
                        </div>
                        <div class="form-group">
                          <label for="kasbon_nama">Kasbon</label>
                          <input type="text" name="kasbon_nama" class="form-control" id="kasbon_nama" placeholder="Contoh: Kasbon Cicilan Kendaraan" required>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-6">
                        <div class="form-group">
                            <label for="kasbon_desc">Keterangan Lengkap Kasbon</label>
                            <textarea name="kasbon_desc" id="kasbon_desc" class="form-control" required="required" placeholder="Keperluan Kasbon Secara Detail" style="height:123px;"></textarea>
                        </div>
                        <div class="form-group">
                          <label for="kasbon_total">Nominal</label>
                          <input type="number" name="kasbon_total" class="form-control" id="kasbon_total" placeholder="Input Nominal Kasbon" required>
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