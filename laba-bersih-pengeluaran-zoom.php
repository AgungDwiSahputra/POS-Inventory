<?php 
  include '_header.php';
  include '_nav.php';
  include '_sidebar.php'; 
?>
<?php  
  if ( $levelLogin === "teknisi") {
    echo "
      <script>
        document.location.href = 'bo';
      </script>
    ";
  }
    
?>
<?php  
// ambil data di URL
$id = abs((int)base64_decode($_GET['id']));


// query data mahasiswa berdasarkan id
$pengeluaran = query("SELECT * FROM pengeluaran WHERE pengeluaran_id = $id ")[0];

// cek apakah tombol submit sudah ditekan atau belum
if( isset($_POST["submit"]) ){
  // var_dump($_POST);

  // cek apakah data berhasil di tambahkan atau tidak
  if( editpengeluaran($_POST) > 0 ) {
    echo "
      <script>
        document.location.href = 'pengeluaran-data';
      </script>
    ";
  } elseif( editpengeluaran($_POST) == null ) {
    echo "
      <script>
        alert('Anda Belum melakukan perubahan data !!');
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
            <h1>Edit Data pengeluaran</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="bo">Home</a></li>
              <li class="breadcrumb-item active">Data pengeluaran</li>
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
                <h3 class="card-title">pengeluaran</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form role="form" action="" method="post">
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-6 col-lg-6">
                        <input type="hidden" name="pengeluaran_id" value="<?= $pengeluaran['pengeluaran_id']; ?>">
                        <input type="hidden" name="pengeluaran_user_edit" value="<?= $_SESSION['user_id']; ?>">
                        <div class="form-group">
                          <label for="pengeluaran_date">Tanggal Transaksi</label>
                          <input type="teks" name="pengeluaran_date" class="form-control" id="pengeluaran_date" value="<?= tanggal_indo($pengeluaran['pengeluaran_date']); ?>" readonly>
                        </div>
                        <div class="form-group">
                          <label for="pengeluaran_create">User Create</label>
                          <?php  
                            $pengeluaran_create = $pengeluaran['pengeluaran_create'];
                            $userId = mysqli_query($conn, "SELECT user_nama FROM user WHERE user_id = $pengeluaran_create ");
                            $userId = mysqli_fetch_array($userId);
                            $userId = $userId['user_nama'];
                          ?>
                          <input type="text" name="pengeluaran_create" class="form-control" id="pengeluaran_create" placeholder="Input pengeluaran" value="<?= $userId; ?>" readonly>
                        </div>
                        <div class="form-group">
                          <label for="pengeluaran_name">pengeluaran</label>
                          <input type="text" name="pengeluaran_name" class="form-control" id="pengeluaran_name" placeholder="Input pengeluaran" value="<?= $pengeluaran['pengeluaran_name']; ?>" readonly>
                        </div>
                        <div class="form-group">
                          <label for="pengeluaran_penerima">Penerima</label>
                          <input type="text" name="pengeluaran_penerima" class="form-control" id="pengeluaran_penerima" placeholder="Input Penerima" value="<?= $pengeluaran['pengeluaran_penerima']; ?>" readonly>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-6">
                        <div class="form-group">
                          <label for="pengeluaran_desc">Keterangan</label>
                          <textarea name="pengeluaran_desc" id="pengeluaran_desc" class="form-control" rows="3" readonly="readonly"><?= $pengeluaran['pengeluaran_desc']; ?></textarea>
                        </div>
                        <div class="form-group">
                          <label for="pengeluaran_total_dibayar">Total Dibayar</label>
                          <input type="number" name="pengeluaran_total_dibayar" class="form-control" id="pengeluaran_total_dibayar" value="<?= $pengeluaran['pengeluaran_total_dibayar']; ?>"  readonly>
                        </div>

                        <?php /*
                        <div class="form-group">
                          <label for="pengeluaran_metode">Metode Pembayaran</label>
                          <?php 
                              $pengeluaran_metode = $pengeluaran['pengeluaran_metode']; 
 
                            $cp_id  = $pengeluaran_metode;
                            if ( $cp_id == 1 ) {
                                $namaChannel = "Transfer";
                            } elseif ( $cp_id == 0 ) {
                                $namaChannel = "Cash";
                            } else {
                                $typePembayaran = query("SELECT * FROM payment WHERE payment_id = $cp_id && payment_cabang = $sessionCabang && payment_status > 0 ")[0];

                                $payment_type_id = $typePembayaran['payment_type_id'];
                                                  $payment_type_nama = mysqli_query($conn, "SELECT payment_type_nama FROM payment_type WHERE payment_type_id = $payment_type_id ");
                                                  $payment_type_nama = mysqli_fetch_array($payment_type_nama);
                                                  $payment_type_nama = $payment_type_nama['payment_type_nama'];

                                $namaChannel = $payment_type_nama." / ".$typePembayaran['payment_nama']." / ".$typePembayaran['payment_no'];
                            }
                        ?>  
                          <input type="text" name="pengeluaran_penerima" class="form-control" id="pengeluaran_penerima" placeholder="Input Penerima" value="<?= $namaChannel; ?>" readonly>
                        </div>
                        */ ?>

                    <div class="form-group">
                          <label for="pengeluaran_edit_user">User Edit</label>
                          <?php  
                            $pengeluaran_edit_user = $pengeluaran['pengeluaran_edit_user'];
                            $userIdPengeluaran = mysqli_query($conn, "SELECT user_nama FROM user WHERE user_id = $pengeluaran_edit_user ");
                            $userIdPengeluaran = mysqli_fetch_array($userIdPengeluaran);
                            $userIdPengeluaran = $userIdPengeluaran['user_nama'];
                          ?>
                          <input type="text" name="pengeluaran_edit_user" class="form-control" id="pengeluaran_edit_user" placeholder="" value="<?= $userIdPengeluaran; ?>" readonly>
                      </div>

                      <div class="form-group">
                          <label for="pengeluaran_datetime_edit">Waktu Edit</label>
                          <input type="text" name="pengeluaran_datetime_edit" class="form-control" id="pengeluaran_datetime_edit" placeholder="" value="<?= $pengeluaran['pengeluaran_datetime_edit']; ?>" readonly>
                        </div>

                      <?php if ( $levelLogin === "super admin" ) : ?>
                        <div class="form-group">
                          <label for="pengeluaran_lababersih">Tampil di Laba Bersih</label><br>
                          <input type="checkbox" value="1" class="" name="pengeluaran_lababersih" <?php echo ($pengeluaran['pengeluaran_lababersih'] === "1") ? 'checked' : ''; ?> disabled>
                        </div>
                        <?php else : ?>
                          <input type="hidden" value="<?= $pengeluaran['pengeluaran_lababersih']; ?>" class="" name="pengeluaran_lababersih">
                        <?php endif; ?>
                  </div>
                </div>
                <div class="row no-print">
                  <div class="col-12">
                    <a href="#!" class="btn btn-success float-right" onclick="self.close()" style="margin-right: 5px;"> Kembali</a>
                  </div>
                </div>
                <!-- /.card-body -->
              </form>
            </div>
          </div>
        </div>
      </div>
    </section>


  </div>


<?php include '_footer.php'; ?>