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
                        <div class="form-group">
                          <label for="bnf_kode">Kode</label>
                          <input type="text" name="bnf_kode" class="form-control" id="bnf_kode" placeholder="Enter Kode Produk" value="<?= $data['bnf_kode']; ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="bnf_nama">Nama Produk</label>
                            <input type="text" name="bnf_nama" class="form-control" id="bnf_nama" placeholder="Enter Nama Produk" value="<?= $data['bnf_nama']; ?>" readonly>
                        </div>
                         <div class="form-group">
                          <label for="bnf_deskripsi">Deskripsi</label>
                          <textarea name="bnf_deskripsi" class="form-control" id="bnf_deskripsi" placeholder="Deskripsi Lengkap" rows="5" readonly><?= $data['bnf_deskripsi']; ?></textarea>
                        </div>

                      <?php /*
                        <div class="form-group">
                            <label for="bnf_harga_beli">Harga Beli</label>
                            <input type="number" name="bnf_harga_beli" class="form-control" id="bnf_harga_beli" placeholder="Contoh: 12000" value="<?= $data['bnf_harga_beli']; ?>" readonly>
                        </div>
                      */ ?>
                    </div>

                    <div class="col-md-6 col-lg-6">
                      <?php /*
                        <div class="form-group">
                            <label for="bnf_harga_jual">Harga Jual</label>
                            <input type="number" name="bnf_harga_jual" class="form-control" id="bnf_harga_jual" placeholder="Contoh: 12000" value="<?= $data['bnf_harga_jual']; ?>" readonly>
                        </div>
                      */ ?>
                        <div class="form-group">
                            <label for="bnf_status">Status</label>
                              <?php  
                                if ( $data['bnf_status'] === '1' ) {
                                  $status = "Active";
                                } else {
                                  $status = "Not Active";
                                }
                              ?>
                              <input type="teks" name="jasa_harga" class="form-control" id="jasa_harga" value="<?= $status; ?>" readonly>
                        </div>
                        <div class="form-group">
                          <label for="bnf_create_datetime">Waktu Create</label>
                          <input type="text" name="bnf_create_datetime" class="form-control" id="bnf_create_datetime" placeholder="Enter Kode Jasa" value="<?= $data['bnf_create_datetime']; ?>" readonly>
                        </div>
                        <div class="form-group">
                          <label for="bnf_user_create">User Create</label>
                          <?php  
                            $bnf_user_create = $data['bnf_user_create'];
                            $namaUser = mysqli_query($conn, "SELECT user_nama FROM user WHERE user_id = $bnf_user_create");
                            $namaUser = mysqli_fetch_array($namaUser);
                            $namaUser = $namaUser['user_nama'];
                          ?>
                          <input type="text" name="bnf_user_create" class="form-control" id="bnf_user_create" placeholder="Enter Kode Jasa" value="<?= $namaUser; ?>" readonly>
                        </div>
                        <div class="form-group">
                          <label for="bnf_terjual">Terjual (x)</label>
                          <input type="number" name="bnf_terjual" class="form-control" id="bnf_terjual" placeholder="Enter Kode Jasa" value="<?= $data['bnf_terjual']; ?>" readonly>
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
