<?php 
  include '_header.php';
  include '_nav.php';
  include '_sidebar.php'; 
?>
<?php  
  if ( $levelLogin === "teknisi" || $levelLogin === "kurir" ) {
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
  if( tambahpengeluaran($_POST) > 0 ) {
    echo "
      <script>
        document.location.href = 'laba-bersih-pengeluaran';
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
            <h1>Tambah Data Pengeluaran</h1>
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
                        <input type="hidden" name="pengeluaran_cabang" value="<?= $sessionCabang; ?>">
                        <input type="hidden" name="pengeluaran_create" value="<?= $_SESSION['user_id']; ?>">
                        <input type="hidden" name="pengeluaran_metode" value="0">
                        <div class="form-group">
                          <label for="pengeluaran_date">Tanggal Transaksi</label>
                          <input type="date" name="pengeluaran_date" class="form-control" id="pengeluaran_date" value="<?= date("Y-m-d"); ?>"  <?= $levelLogin === "kasir" ? 'readonly' : ''; ?> required>
                        </div>
                        <div class="form-group ">
                              <label for="satuan_id">Pengeluaran</label>
                              <div class="">
                                  <select name="pengeluaran_name" required="" class="form-control ">
                                    <option value="">-- Pilih --</option>

                                    <?php $data1 = query("SELECT * FROM pengeluaran_master WHERE pm_status > 0 && pm_cabang = $sessionCabang ORDER BY pm_id DESC"); ?>
                                    <?php foreach ( $data1 as $row ) : ?>
                                        <option value="<?= $row['pm_nama']; ?>">
                                          <?= $row['pm_nama']; ?> 
                                    <?php endforeach; ?>
                                </select>
                              </div>
                            <?php if ( $levelLogin === "super admin" ) { ?>
                              <small>
                                <a href="laba-bersih-pengeluaran-kategori-add?page=<?= base64_encode(1); ?>"><i class="fa fa-plus"></i> Tambah Nama</a>
                              </small>
                            <?php } ?>
                            </div>
                        <div class="form-group">
                          <label for="pengeluaran_penerima">Penerima</label>
                          <input type="text" name="pengeluaran_penerima" class="form-control" id="pengeluaran_penerima" placeholder="Contoh: PLN / PDAM / SPBU dll" required>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-6">
                        <div class="form-group">
                          <label for="pengeluaran_desc">Keterangan</label>
                          <textarea name="pengeluaran_desc" id="pengeluaran_desc" class="form-control" rows="3" required="required"></textarea>
                        </div>
                        <div class="form-group">
                          <label for="pengeluaran_total_dibayar">Total Dibayar</label>
                          <input type="number" name="pengeluaran_total_dibayar" class="form-control" id="pengeluaran_total_dibayar" placeholder="Contoh: 200000" required>
                        </div>

                        <?php /* if ( $levelLogin === "super admin" ) : ?>
                        <div class="form-group">
                          <label for="pengeluaran_lababersih">Tampil di Laba Bersih</label><br>
                          <input type="checkbox" value="1" class="" name="pengeluaran_lababersih">
                        </div>
                        <?php else : ?>
                          <input type="hidden" value="" class="" name="pengeluaran_lababersih">
                        <?php endif; */?>

                        <input type="hidden" value="1" class="" name="pengeluaran_lababersih">

                        <?php /* 
                          $typePembayaran = query("SELECT * FROM payment WHERE payment_cabang = $sessionCabang && payment_status > 0 ");
                        ?>  
                        <div class="form-group">
                            <label>Tipe Pembayaran</label>
                            <select class="form-control" required="" name="pengeluaran_metode">
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