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
  if( editBarangNonFisik($_POST) > 0 ) {
    echo "
      <script>
        document.location.href = 'barang-non-fisik';
      </script>
    ";
  } elseif( editBarangNonFisik($_POST) == null ) {
    echo "
      <script>
        alert('Anda Belum Melakukan Perubahan Data !!');
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

$id = abs((int)base64_decode($_GET['id']));
$data = query("SELECT * FROM barang_non_fisik WHERE bnf_id = $id")[0];
?>


  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-8">
            <h1>Edit Data Barang Non Fisik</h1>
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
                      <input type="hidden" name="bnf_id" value="<?= $id; ?>">
                        <div class="form-group">
                          <label for="bnf_kode">Kode</label>
                          <input type="text" name="bnf_kode" class="form-control" id="bnf_kode" placeholder="Enter Kode Produk" value="<?= $data['bnf_kode']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="bnf_nama">Nama Produk</label>
                            <input type="text" name="bnf_nama" class="form-control" id="bnf_nama" placeholder="Enter Nama Produk" value="<?= $data['bnf_nama']; ?>" required>
                        </div>
                         <div class="form-group">
                          <label for="bnf_deskripsi">Deskripsi</label>
                          <textarea name="bnf_deskripsi" class="form-control" id="bnf_deskripsi" placeholder="Deskripsi Lengkap" rows="4" required><?= $data['bnf_deskripsi']; ?></textarea>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-6">
                        <div class="form-group">
                            <label for="bnf_status">Status</label>
                            <div class="">
                              <?php  
                                if ( $data['bnf_status'] === '1' ) {
                                  $status = "Active";
                                } else {
                                  $status = "Not Active";
                                }
                              ?>
                                <select name="bnf_status" required="" class="form-control ">
                                  <option value="<?= $data['bnf_status']; ?>"><?= $status; ?></option>
                                  <?php  
                                    if ( $data['bnf_status'] === '1' ) {
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
