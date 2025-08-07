<?php 
  include '_header.php';
  include '_nav.php';
  include '_sidebar.php'; 
?>
<?php  
  if ( $levelLogin === "kurir") {
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
  if( tambahBarangNonFisik($_POST) > 0 ) {
    echo "
      <script>
        document.location.href = 'barang-non-fisik';
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
          <div class="col-sm-8">
            <h1>Tambah Data Barang Non Fisik</h1>
          </div>
          <div class="col-sm-4">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="bo">Home</a></li>
              <li class="breadcrumb-item active">Data Barang Non Fisik</li>
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
                <h3 class="card-title">Data Barang Non Fisik</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form role="form" action="" method="post">
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-6 col-lg-6">
                      <input type="hidden" name="bnf_cabang" value="<?= $sessionCabang; ?>">
                      <input type="hidden" name="bnf_user_create" value="<?= $_SESSION['user_id']; ?>">
                        <div class="form-group">
                          <label for="bnf_kode">Kode</label>
                          <input type="text" name="bnf_kode" class="form-control" id="bnf_kode" placeholder="Enter Kode Produk" required>
                        </div>
                        <div class="form-group">
                            <label for="bnf_nama">Nama Produk</label>
                            <input type="text" name="bnf_nama" class="form-control" id="bnf_nama" placeholder="Enter Nama Produk" required>
                        </div>
                         <div class="form-group">
                          <label for="bnf_deskripsi">Deskripsi</label>
                          <textarea name="bnf_deskripsi" class="form-control" id="bnf_deskripsi" placeholder="Deskripsi Lengkap" rows="4" required></textarea>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-6">
                        <div class="form-group ">
                          <label for="bnf_status">Status</label>
                          <div class="">
                              <select name="bnf_status" required="" class="form-control ">
                                  <option value="">-- Status --</option>
                                  <option value="1">Active</option>
                                  <option value="0">Not Active</option>
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
