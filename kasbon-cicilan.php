<?php 
  include '_header.php';
  include '_nav.php';
  include '_sidebar.php'; 
?>
<?php  
  if ( $levelLogin === "kurir" || $levelLogin === "kasir" ) {
    echo "
      <script>
        document.location.href = 'bo';
      </script>
    ";
  }  
 

// cek apakah tombol submit sudah ditekan atau belum
if( isset($_POST["submit"]) ){
  // var_dump($_POST);

  // cek apakah data berhasil di tambahkan atau tidak
  if( tambahCicilanKasbon($_POST) > 0 ) {
    echo "
      <script>
        document.location.href = '';
      </script>
    ";
  } else {
    echo "
      <script>
        alert('data gagal ditambahkan');
        document.location.href = 'kasbon';
      </script>
    ";
  }
  
}
?>

<?php  
  // ambil data di URL
  $id = abs((int)base64_decode($_GET['id']));

  // query data mahasiswa berdasarkan id
  $kasbon = query("SELECT * FROM kasbon WHERE kasbon_id = $id ")[0];
  $kasbon_nama          = $kasbon['kasbon_nama'];
  $kasbon_total         = $kasbon['kasbon_total'];
  $kasbon_status_lunas  = $kasbon['kasbon_status_lunas'];
?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-8">
            <h1>
                Cicilan Kasbon <b><?= $kasbon_nama; ?></b> 
                <?php if ( $kasbon_status_lunas > 0 ) { ?>
                <span class='badge badge-primary'>LUNAS</span>
                <?php } ?>
            </h1>
          </div>
          <div class="col-sm-4">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="bo">Home</a></li>
              <li class="breadcrumb-item active">Data Cicilan Kasbon</li>
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
                <h3 class="card-title">Cicilan</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form role="form" action="" method="post">
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-6 col-lg-6">
                        <div class="form-group">
                          <label for="">Nominal Kasbon</label>
                          <input type="text" name="" class="form-control" value="<?= number_format($kasbon['kasbon_total'], 0, ',', '.'); ?>" readonly="">
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-6">
                        <div class="form-group">
                          <label for="">Total Cicilan</label>
                          <input type="text" name="" class="form-control" value="<?= number_format($kasbon['kasbon_total_cicilan'], 0, ',', '.'); ?>" readonly="">
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-6">
                        <div class="form-group">
                          <label for="">Sisa Kasbon</label>
                          <?php  
                            $sisaPiutang = $kasbon['kasbon_total'] - $kasbon['kasbon_total_cicilan'];
                          ?>
                          <input type="text" name="" class="form-control" value="<?= number_format($sisaPiutang, 0, ',', '.'); ?>" readonly="">
                        </div>
                    </div>
                    
                    <?php if ( $kasbon_status_lunas < 1 ) { ?>
                    <div class="col-md-6 col-lg-6">
                        <div class="form-group">
                          <label for="">Nominal Cicilan</label>
                          <input type="number" name="kb_nominal" class="form-control" id="kb_nominal" value="0" max="<?= $sisaPiutang; ?>" required="">
                        </div>
                    </div>

                    <input type="hidden" name="kasbon_total" value="<?= $kasbon['kasbon_total']; ?>">
                    <input type="hidden" name="kasbon_sisa" value="<?= $sisaPiutang; ?>">
                    <input type="hidden" name="kasbon_total_cicilan" value="<?= $kasbon['kasbon_total_cicilan']; ?>">
                    <input type="hidden" name="kb_kasbon_id" value="<?= $id; ?>">
                    <input type="hidden" name="kb_user_create" value="<?= $_SESSION['user_id']; ?>">
                    <input type="hidden" name="kb_cabang" value="<?= $sessionCabang; ?>">
                    <?php } ?>
                  </div>
                </div>
                <!-- /.card-body -->
                <?php if (  $kasbon_status_lunas < 1 ) { ?>
                <div class="card-footer text-right">
                  <button type="submit" name="submit" class="btn btn-primary">Submit</button>
                </div>
                <?php } ?>
              </form>
            </div>

            <div class="card">
              <div class="card-header">
                <h3 class="card-title"><b>History Cicilan</b></h3>
              </div>
            <!-- /.card-header -->
              <div class="card-body">
                <div class="table-auto">
                  <table id="example1" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                      <th style="width: 6%;">No.</th>
                      <th>Tanggal</th>
                      <th>Nominal</th>
                      <!-- <th>Pembayaran</th> -->
                      <th>Kasir</th>
                      <?php if ( $levelLogin === "super admin" ) { ?>
                      <th style="text-align: center; width: 14%">Aksi</th>
                      <?php } ?>
                    </tr>
                    </thead>
                    <tbody>

                    <?php 
                      $i = 1; 
                      $queryProduct = $conn->query("SELECT kasbon_cicilan.kb_id, 
                        kasbon_cicilan.kb_kasbon_id, 
                        kasbon_cicilan.kb_datetime, 
                        kasbon_cicilan.kb_user_create, 
                        kasbon_cicilan.kb_nominal, 
                        kasbon_cicilan.kb_tipe_pembayaran, 
                        kasbon_cicilan.kb_cabang, 
                        user.user_id, 
                        user.user_nama
                                 FROM kasbon_cicilan 
                                 JOIN user ON kasbon_cicilan.kb_user_create = user.user_id
                                 WHERE kb_cabang = ".$sessionCabang." && kb_kasbon_id = ".$id." ORDER BY kb_id DESC
                                 ");
                      while ($rowProduct = mysqli_fetch_array($queryProduct)) {
                    ?>
                    <tr>
                        <td><?= $i; ?></td>
                        <td><?= $rowProduct['kb_datetime']; ?></td>
                        <td>Rp. <?= number_format($rowProduct['kb_nominal'], 0, ',', '.'); ?></td>
                      <?php /*
                        <td>
                          <?php  
                            $tipePembayaran = $rowProduct['kb_tipe_pembayaran'];
                            if ( $tipePembayaran == 1 ) {
                              echo "Transfer";
                            } elseif ( $tipePembayaran == 2 ) {
                              echo "Debit";
                            } elseif ( $tipePembayaran == 3 ) {
                              echo "Credit Card";
                            } else {
                              echo "Cash";
                            }
                          ?>
                        </td>
                      */ ?>
                        <td><?= $rowProduct['user_nama']; ?></td>
                        <?php if ( $levelLogin === "super admin" ) { ?>
                        <td class="text-center">
                          <?php 
                            $idPiutang = base64_encode($rowProduct["kb_id"]); 
                          ?>
                            <a href="kasbon-cicilan-delete?id=<?= $idPiutang; ?>&page=<?= $_GET['id']; ?>" onclick="return confirm('Yakin dihapus ?')" title="Delete Data">
                                <button class="btn btn-danger" type="submit" name="hapus">
                                    <i class="fa fa-trash-o"></i>
                                </button>
                            </a>
                        </td>
                        <?php } ?>
                    </tr>
                    <?php $i++; ?>
                    <?php } ?>
                    </tbody>
                  </table>
                </div>
              </div>
              <!-- /.card-body -->
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>


<?php include '_footer.php'; ?>
<!-- DataTables -->
<script src="plugins/datatables/jquery.dataTables.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.js"></script>
<script>
  $(function () {
    $("#example1").DataTable();
  });

</script>