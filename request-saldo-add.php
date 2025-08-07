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
  if( tambahRequestSaldo($_POST) > 0 ) {
    echo "
      <script>
        document.location.href = 'request-saldo';
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
            <h1>Tambah Data Request Saldo</h1>
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
                      <input type="hidden" name="rs_user_id_request" value="<?= $_SESSION['user_id']; ?>">
                      <input type="hidden" name="rs_cabang" value="<?= $sessionCabang; ?>">
                      <div class="form-group ">
                           <label for="rs_provider_id">Provider</label>
                           <div class="">
                               <?php 
                                  $data2 = query("SELECT * FROM provider WHERE provider_status > 0 && provider_cabang = $sessionCabang ORDER BY provider_id DESC"); 
                               ?>
                                 <select name="rs_provider_id" required="" class="form-control ">
                                  <?php  
                                    $countProvider = mysqli_query($conn, "SELECT * FROM provider WHERE provider_status > 0 && provider_cabang = $sessionCabang");
                                    $countProvider = mysqli_num_rows($countProvider);
                                  ?>
                                  <?php if ( $countProvider < 1 ) : ?>
                                    <option value="">Belum Ada Provider</option>
                                  <?php else : ?>
                                    <option value="">-- Pilih Provider --</option>
                                    <?php foreach ( $data2 as $row ) : ?>
                                        <option value="<?= $row['provider_id']; ?>">
                                           <?= $row['provider_nama']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                  <?php endif; ?>
                                </select>
                          </div>
                        </div>
                        <div class="form-group">
                            <label for="rs_nominal">Nominal Saldo</label>
                            <input type="number" name="rs_nominal" class="form-control" id="rs_nominal" placeholder="Contoh: 600000" required onkeypress="return hanyaAngka(event)">
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
<script>
    function hanyaAngka(evt) {
      var charCode = (evt.which) ? evt.which : event.keyCode
       if (charCode > 31 && (charCode < 48 || charCode > 57))
 
        return false;
      return true;
    }
</script>