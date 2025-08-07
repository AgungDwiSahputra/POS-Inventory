<?php 
  include '_header.php';
  include '_nav.php';
  include '_sidebar.php'; 
?>
<?php  
  if ( $levelLogin === "driver") {
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
        document.location.href = 'laba-bersih-pengeluaran';
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
            <h1>Edit Data Pengeluaran</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="bo">Home</a></li>
              <li class="breadcrumb-item active">Data Pengeluaran</li>
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
                <h3 class="card-title">Pengeluaran</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form role="form" action="" method="post">
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-6 col-lg-6">
                        <input type="hidden" name="pengeluaran_id" value="<?= $pengeluaran['pengeluaran_id']; ?>">
                        <input type="hidden" name="pengeluaran_edit_user" value="<?= $userIdLogin; ?>">
                        <input type="hidden" name="pengeluaran_metode" value="0">
                        <div class="form-group">
                          <label for="pengeluaran_name">Pengeluaran</label>
                          <input type="text" name="pengeluaran_name" class="form-control" id="pengeluaran_name" placeholder="Input pengeluaran" value="<?= $pengeluaran['pengeluaran_name']; ?>" required>
                        </div>
                        <div class="form-group">
                          <label for="pengeluaran_penerima">Penerima</label>
                          <input type="text" name="pengeluaran_penerima" class="form-control" id="pengeluaran_penerima" placeholder="Input Penerima" value="<?= $pengeluaran['pengeluaran_penerima']; ?>" required>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-6">
                        <div class="form-group">
                          <label for="pengeluaran_desc">Keterangan</label>
                          <textarea name="pengeluaran_desc" id="pengeluaran_desc" class="form-control" rows="3" required="required"><?= $pengeluaran['pengeluaran_desc']; ?></textarea>
                        </div>
                        <div class="form-group">
                          <label for="pengeluaran_total_dibayar">Total Tagihan</label>
                          <input type="number" name="pengeluaran_total_dibayar" class="form-control" id="pengeluaran_total_dibayar" placeholder="Contoh: 200000" value="<?= $pengeluaran['pengeluaran_total_dibayar']; ?>" required>
                        </div>

                        <?php if ( $levelLogin === "super admin" ) : ?>
                        <div class="form-group">
                          <label for="pengeluaran_lababersih">Tampil di Laba Bersih</label><br>
                          <input type="checkbox" value="1" class="" name="pengeluaran_lababersih" <?php echo ($pengeluaran['pengeluaran_lababersih'] === "1") ? 'checked' : ''; ?>>
                          <input type="hidden" name="pengeluaran_lababersih_old" value="<?= $pengeluaran['pengeluaran_lababersih']; ?>">
                        </div>
                        <?php else : ?>
                          <input type="hidden" value="<?= $pengeluaran['pengeluaran_lababersih']; ?>" class="" name="pengeluaran_lababersih">
                        <?php endif; ?>

                        <?php /* 
                          $typePembayaran = query("SELECT * FROM payment WHERE payment_cabang = $sessionCabang && payment_status > 0 ");
                        ?>  
                        <div class="form-group">
                          <label for="pengeluaran_metode">Metode Pembayaran</label>
                          <select name="pengeluaran_metode" required="" class="form-control ">
                          <?php $pengeluaran_metode = $pengeluaran['pengeluaran_metode']; ?>
                          <?php if ( $pengeluaran_metode == 0 ) : ?>
                            <option value="0">Cash</option>
                            <?php foreach ( $typePembayaran as $row ) : ?>
                              <?php  
                                  $payment_type_id = $row['payment_type_id'];
                                  $payment_type_nama = mysqli_query($conn, "SELECT payment_type_nama FROM payment_type WHERE payment_type_id = $payment_type_id ");
                                  $payment_type_nama = mysqli_fetch_array($payment_type_nama);
                                  $payment_type_nama = $payment_type_nama['payment_type_nama'];
                              ?>
                            <option value="<?= $row['payment_id']; ?>">
                                  <?= $payment_type_nama; ?> / <?= $row['payment_nama']; ?> / <?= $row['payment_no']; ?> 
                            </option>
                          <?php endforeach; ?>
                          <option value="1">Transfer</option>

                          <?php elseif ( $pengeluaran_metode == 1 ) : ?>
                            <option value="1">Transfer</option>
                            <option value="0">Cash</option>
                            <?php foreach ( $typePembayaran as $row ) : ?>
                              <?php  
                                  $payment_type_id = $row['payment_type_id'];
                                  $payment_type_nama = mysqli_query($conn, "SELECT payment_type_nama FROM payment_type WHERE payment_type_id = $payment_type_id ");
                                  $payment_type_nama = mysqli_fetch_array($payment_type_nama);
                                  $payment_type_nama = $payment_type_nama['payment_type_nama'];
                              ?>
                            <option value="<?= $row['payment_id']; ?>">
                                  <?= $payment_type_nama; ?> / <?= $row['payment_nama']; ?> / <?= $row['payment_no']; ?> 
                            </option>
                          <?php endforeach; ?>

                          <?php else : ?>
                            <?php  
                                $typePembayaran1 = query("SELECT * FROM payment WHERE payment_cabang = $sessionCabang && payment_id = $pengeluaran_metode ");
                            ?>

                            <?php foreach ( $typePembayaran1 as $row ) : ?>
                              <?php  
                                  $payment_type_id = $row['payment_type_id'];
                                  $payment_type_nama = mysqli_query($conn, "SELECT payment_type_nama FROM payment_type WHERE payment_type_id = $payment_type_id ");
                                  $payment_type_nama = mysqli_fetch_array($payment_type_nama);
                                  $payment_type_nama = $payment_type_nama['payment_type_nama'];
                              ?>
                            <option value="<?= $row['payment_id']; ?>">
                                  <?= $payment_type_nama; ?> / <?= $row['payment_nama']; ?> / <?= $row['payment_no']; ?> 
                            </option>
                            <?php endforeach; ?>

                            <?php  
                                $typePembayaran2 = query("SELECT * FROM payment WHERE payment_cabang = $sessionCabang && payment_id != $pengeluaran_metode ");
                            ?>

                            <?php foreach ( $typePembayaran2 as $row ) : ?>
                              <?php  
                                  $payment_type_id = $row['payment_type_id'];
                                  $payment_type_nama = mysqli_query($conn, "SELECT payment_type_nama FROM payment_type WHERE payment_type_id = $payment_type_id ");
                                  $payment_type_nama = mysqli_fetch_array($payment_type_nama);
                                  $payment_type_nama = $payment_type_nama['payment_type_nama'];
                              ?>
                            <option value="<?= $row['payment_id']; ?>">
                                  <?= $payment_type_nama; ?> / <?= $row['payment_nama']; ?> / <?= $row['payment_no']; ?> 
                            </option>
                            <?php endforeach; ?>

                            <option value="0">Cash</option>
                            <option value="1">Transfer</option>
                          <?php endif; ?>
                         </select>
                        </div>
                        */?>
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