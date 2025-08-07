<?php 
  include '_header.php';
  include '_nav.php';
  include '_sidebar.php'; 
?>
<?php  
  if ( $levelLogin !== "super admin") {
    echo "
      <script>
        document.location.href = 'bo';
      </script>
    ";
  }
    
?>

	<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Data Laporan Kasir</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="bo">Home</a></li>
              <li class="breadcrumb-item active">Laporan Kasir</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>


    <section class="content">
      <div class="container-fluid">
        <div class="card card-default">
          <div class="card-header">
            <h3 class="card-title">Filter Data Berdasrkan Tanggal</h3>

            <div class="card-tools">
              <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
              <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-remove"></i></button>
            </div>
          </div>
          <!-- /.card-header -->
          <form role="form" action="" method="POST">
            <div class="card-body">
              <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="tanggal_akhir">Nama</label>
                        <select class="form-control select2bs4" required="" name="user_id">
                            <option selected="selected" value="">-- Pilih --</option>
                            <?php  
                              $user = query("SELECT * FROM user WHERE user_cabang = $sessionCabang && user_status = '1' ORDER BY user_id DESC ");
                            ?>
                            <?php foreach ( $user as $ctr ) : ?>
                              <?php if ( $ctr['user_id'] != 0 && $ctr['user_level'] !== "kurir" ) { ?>
                              <option value="<?= $ctr['user_id'] ?>"><?= $ctr['user_nama'] ?></option>
                              <?php } ?>
                            <?php endforeach; ?>
                          </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group ">
                      <label for="user_status">Status</label>
                      <div class="">
                          <select name="user_status" required="" class="form-control ">
                              <option value="">-- Pilih --</option>
                              <option value="0">Belum Lunas</option>
                              <option value="1">Lunas</option>
                          </select>
                      </div>
                    </div>
                </div>
              </div>
              <div class="card-footer text-right">
                  <button type="submit" name="submit" class="btn btn-primary">
                    <i class="fa fa-filter"></i> Filter
                  </button>
              </div>
            </div>
          </form>
      </div>
    </section>


    <?php if( isset($_POST["submit"]) ){ ?>
        <?php  
          $user_status = $_POST['user_status'];
          $user_id       = $_POST['user_id'];

          $data = query("SELECT * FROM kasbon WHERE kasbon_user_id_kasbon = $user_id && kasbon_status_lunas = $user_status ORDER BY kasbon_id DESC ");
        ?>
    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-12">

          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Laporan</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <div class="table-auto">
                <table id="laporan-kasbon" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th style="width: 6%;">No.</th>
                    <th>Nama</th>
                    <th>Kasbon</th>
                    <th>Tanggal</th>
                    <th>Nominal</th>
                    <th>Total Cicilan</th>
                    <th style="text-align: center; width: 12%;">Aksi</th>
                  </tr>
                  </thead>
                  <tbody>

                  <?php 
                    $i = 1;
                    $kasbon_total = 0;
                    $kasbon_total_cicilan = 0;
                    foreach ( $data as $rowProduct ) : 

                    $kasbon_total += $rowProduct['kasbon_total'];
                    $kasbon_total_cicilan += $rowProduct['kasbon_total_cicilan'];
                  ?>
                  <tr>
                    	<td><?= $i; ?></td>
                      <td>
                          <?php  
                            $kasbon_user_id_kasbon = $rowProduct['kasbon_user_id_kasbon'];
                            $namaUser = mysqli_query($conn, "SELECT user_nama FROM user WHERE user_id = $kasbon_user_id_kasbon ");
                            $namaUser = mysqli_fetch_array($namaUser);
                            $namaUser = $namaUser['user_nama'];
                            echo $namaUser;
                          ?>      
                      </td>
                      <td><?= $rowProduct['kasbon_nama']; ?></td>
                      <td><?= tanggal_indo($rowProduct['kasbon_date']); ?></td>
                      <td>Rp. <?= number_format($rowProduct['kasbon_total'], 0, ',', '.'); ?></td>
                      <td>Rp. <?= number_format($rowProduct['kasbon_total_cicilan'], 0, ',', '.'); ?></td>
                      <td class="orderan-online-button">
                        <a href="kasbon-zoom?id=<?= base64_encode($rowProduct['kasbon_id']) ?>" target="_blank">
                          <button class='btn btn-primary tblZoom' title='Lihat Data'>
                            <i class='fa fa-eye'></i>
                          </button>&nbsp;
                        </a>

                        <a href="kasbon-cicilan?id=<?= base64_encode($rowProduct['kasbon_id']) ?>" target="_blank">
                          <button class='btn btn-success tblEdit' title="Cicilan">
                            <i class='fa fa-money'></i>
                          </button>&nbsp;
                        </a>
                      </td>
                  </tr>
                  <?php $i++; ?>
                  <?php endforeach; ?>
                  <tr>
                      <td colspan="4">
                        <b>Total</b>
                      </td>
                      <td>
                        Rp. <?php echo number_format($kasbon_total, 0, ',', '.'); ?>
                      </td>
                      <td>
                        Rp. <?php echo number_format($kasbon_total_cicilan, 0, ',', '.'); ?>
                      </td>
                      <td></td>
                  </tr>
                 </tbody>
                </table>
              </div>
              <br>
              <?php 
                $sisa = $kasbon_total - $kasbon_total_cicilan;
               ?>
              <h4>Sisa yang Harus Dibayar Rp. <?php echo number_format($sisa, 0, ',', '.'); ?></h4>
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->
    <?php  } ?>
  </div>
</div>



<?php include '_footer.php'; ?>

<!-- DataTables -->
<script src="plugins/datatables/jquery.dataTables.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.js"></script>
<script>
  $(function () {
    $("#laporan-kasbon").DataTable();
  });
</script>
<script>
  $(function () {

    //Initialize Select2 Elements
    $('.select2bs4').select2({
      theme: 'bootstrap4'
    })
  });
</script>
</body>
</html>