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
$id = abs((int)$_GET['id']);


// query data mahasiswa berdasarkan id
$kategori = query("SELECT * FROM kategori WHERE kategori_id = $id ")[0];
$sub_kategori = query("SELECT * FROM sub_kategori WHERE kategori_id = ?", [$id]);

// cek apakah tombol submit sudah ditekan atau belum
if( isset($_POST["submit"]) ){
  // var_dump($_POST);

  // cek apakah data berhasil di tambahkan atau tidak
  if( editKategori($_POST) > 0 ) {
    echo "
      <script>
        document.location.href = 'kategori';
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
// cek apakah tombol submit sudah ditekan atau belum
if( isset($_POST["sub_submit"]) ){
  // cek apakah data berhasil di tambahkan atau tidak
  $result = tambahSubKategori($_POST);
  if( $result['success'] ) {
    echo "
      <script>
        document.location.href = 'kategori-edit?id=$id';
      </script>
    ";
  } else {
    echo "
      <script>
        alert('".$result['error']."');
      </script>
    ";
  }
}

// cek apakah tombol submit edit sub kategori sudah ditekan atau belum
if( isset($_POST["edit_sub_kategori_submit"]) )
{
  $result = editSubKategori($_POST);
  if( $result['success'] ) {
    echo "
      <script>
        document.location.href = 'kategori-edit?id=$id';
      </script>
    ";
  } else {
    echo "
      <script>
        alert('".$result['error']."');
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
            <h1>Edit Data Kategori</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="bo">Home</a></li>
              <li class="breadcrumb-item active">Data Kategori</li>
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
                <h3 class="card-title">Kategori</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form role="form" action="" method="post">
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-12 col-lg-12">
                        <div class="form-group">
                          <input type="hidden" name="kategori_id" value="<?= $kategori['kategori_id']; ?>">
                          <label for="kategori_nama">Nama Kategori</label>
                          <input type="text" name="kategori_nama" class="form-control" id="kategori_nama" value="<?= $kategori['kategori_nama']; ?>" required>
                        </div>
                        <div class="form-group ">
                            <label for="kategori_status">Status</label>
                            <div class="">
                              <?php  
                                if ( $kategori['kategori_status'] === '1' ) {
                                  $status = "Active";
                                } else {
                                  $status = " Not Active";
                                }
                              ?>
                                <select name="kategori_status" required="" class="form-control ">
                                  <option value="<?= $kategori['kategori_status']; ?>"><?= $status; ?></option>
                                  <?php  
                                    if ( $kategori['kategori_status'] === '1' ) {
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

          <!-- Right column -->
          <div class="col-md-6">
            <!-- general form elements -->
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">Sub Kategori</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form role="form" action="" method="post">
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-12 col-lg-12">
                        <div class="form-group">
                          <input type="hidden" name="kategori_id" value="<?= (isset($kategori['kategori_id'])) ? $kategori['kategori_id'] : ''; ?>">
                          <label for="sub_kategori_nama">Nama Sub Kategori</label>
                          <input type="text" name="sub_kategori_nama" class="form-control" id="sub_kategori_nama" value="<?= (isset($sub_kategori['sub_kategori_nama'])) ? $sub_kategori['sub_kategori_nama'] : ''; ?>" required>
                        </div>
                        <div class="form-group ">
                            <label for="sub_kategori_status">Status</label>
                            <div class="">
                              <?php  
                                if ( isset($sub_kategori['sub_kategori_status']) && $sub_kategori['sub_kategori_status'] === '1' ) {
                                  $status = "Active";
                                } else {
                                  $status = " Not Active";
                                }
                              ?>
                                <select name="sub_kategori_status" required="" class="form-control ">'
                                    <option value="0">Not Active</option>
                                    <option value="1">Active</option>
                                </select>
                            </div>
                        </div>
                    </div>
                  </div>
                </div>
                <!-- /.card-body -->

                <div class="card-footer text-right">
                  <button type="submit" name="sub_submit" class="btn btn-primary">Submit</button>
                </div>
              </form>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-12">

            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Data Sub Kategori</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <div class="table-auto">
                  <table id="example1" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                      <th style="width: 5%;">No.</th>
                      <th>Sub Kategori</th>
                      <th style="text-align: center; width: 20%;">Status</th>
                      <th style="text-align: center; width: 10%;">Aksi</th>
                    </tr>
                    </thead>
                    <tbody>

                    <?php foreach ( $sub_kategori as $index => $row ) : ?>
                        <tr>
                            <form method="POST" action="kategori-edit?id=<?= $id; ?>">
                                <td><?= $index + 1; ?></td>
                                <td style="text-align: center;">
                                    <input type="text" name="sub_kategori_nama" class="form-control" value="<?= $row['sub_kategori_nama']; ?>" required>		
                                </td>
                                <td>
                                    <select name="sub_kategori_status" class="form-control" required>
                                        <option value="1" <?= $row['sub_kategori_status'] === "1" ? "selected" : ""; ?>>Aktif</option>
                                        <option value="0" <?= $row['sub_kategori_status'] === "0" ? "selected" : ""; ?>>Tidak Aktif</option>
                                    </select>
                                </td>
                                <td class="orderan-online-button">
                                    <input type="hidden" name="id" value="<?= (isset($row['id'])) ? $row['id'] : ''; ?>">
                                    <button class="btn btn-primary" type="submit" name="edit_sub_kategori_submit">
                                        Ubah
                                    </button>

                                    <?php if ( $levelLogin === "super admin" ) { ?>
                                        <a href="sub-kategori-delete?id=<?= $row['id']; ?>&kategori_id=<?= $row["kategori_id"]; ?>" onclick="return confirm('Yakin dihapus ?')" title="Delete Data">
                                            <button class="btn btn-danger" type="button" name="hapus">
                                                <i class="fa fa-trash-o"></i>
                                            </button>
                                        </a>
                                    <?php } ?>
                                </td>
                            </form>
                        </tr>
                    <?php endforeach; ?>
                  </tbody>
                  </table>
                </div>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
        </div>
      </div>
    </section>


  </div>


<?php include '_footer.php'; ?>