<?php 
  include '_header.php';
  include '_nav.php';
  include '_sidebar.php'; 
?>
<?php  
  if ( $levelLogin === "kasir") {
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
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="tanggal_awal">Tanggal Awal</label>
                        <input type="date" name="tanggal_awal" class="form-control" id="tanggal_awal" value="<?= isset($_POST['tanggal_awal']) ? $_POST['tanggal_awal'] : date('Y-m-d') ?>" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="tanggal_akhir">Tanggal Akhir</label>
                        <input type="date" name="tanggal_akhir" class="form-control" id="tanggal_akhir" value="<?= isset($_POST['tanggal_akhir']) ? $_POST['tanggal_akhir'] : date('Y-m-d') ?>" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="tanggal_akhir">Nama Kasir</label>
                        <select class="form-control select2bs4" required="" name="user_id">
                            <option selected="selected" value="">-- Pilih Kasir --</option>
                            <?php  
                              $user = query("SELECT * FROM user WHERE user_cabang = $sessionCabang && user_status = '1' && user_level IS NOT NULL AND user_level != '' ORDER BY user_id DESC ");
                            ?>
                            <?php foreach ( $user as $ctr ) : ?>
                              <?php if ( $ctr['user_id'] != 0 && $ctr['user_level'] !== "kurir" ) { ?>
                              <option value="<?= $ctr['user_id'] ?>"><?= $ctr['user_nama'] ?></option>
                              <?php } ?>
                            <?php endforeach; ?>
                          </select>
                    </div>
                </div>
              </div>
              <div class="card-footer text-right">
                  <button type="submit" name="submit" class="btn btn-primary">
                    <i class="fa fa-filter"></i> Filter
                  </button>
                  <!-- add button print dengan url parameter laporan-kasir-print?tanggal_awal=2025-07-01&tanggal_akhir=2025-07-31&kasir=3 -->
                  <?php if( isset($_POST["submit"]) ){ ?>
                    <a href="laporan-kasir-print?tanggal_awal=<?= $_POST['tanggal_awal'] ?>&tanggal_akhir=<?= $_POST['tanggal_akhir'] ?>&user_id=<?= $_POST['user_id'] ?>" class="btn btn-success">
                      <i class="fa fa-print"></i> Print
                    </a>
                  <?php } ?>
              </div>
            </div>
          </form>
      </div>
    </section>


    <?php if( isset($_POST["submit"]) ){ ?>
        <?php  
          $tanggal_awal  = $_POST['tanggal_awal'];
          $tanggal_akhir = $_POST['tanggal_akhir'];
          $user_id       = $_POST['user_id'];
        ?>
   
   <section class="content">
      <div class="row">
        <div class="col-12">

          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Laporan Transaksi Kasir</h3>
            </div>

            <div class="card-body">
              <h5>Laporan Tarik Tunai</h5>
              <div class="table-auto">
                <table id="Laporan-Kasir-Pribadi" class="table table-bordered table-striped tableExport">
                  <thead>
                  <tr>
                    <th style="width: 6%;">No.</th>
                    <th style="width: 13%;">Invoice</th>
                    <th>Tanggal</th>
                    <th>Kasir</th>
                    <th>Harga Modal</th>
                    <th>Harga Jual</th>
                    <th>Total</th>
                  </tr>
                  </thead>
                  <tbody>

                  <?php 
                    $i = 1; 
                    $totalTarikTunai = 0;
                    $totalModal   = 0;
                    $totalJual    = 0;
                    $queryInvoice = $conn->query("SELECT invoice.invoice_id ,invoice.penjualan_invoice, invoice.invoice_tgl, invoice.invoice_cabang, user.user_id, user.user_nama, invoice.invoice_sub_total, invoice.invoice_total_beli_non_fisik
                               FROM invoice 
                               JOIN user ON invoice.invoice_kasir = user.user_id
                               WHERE invoice_cabang = '".$sessionCabang."' && invoice_date BETWEEN '".$tanggal_awal."' AND '".$tanggal_akhir."' && invoice_piutang = 0 && invoice_piutang_lunas = 0 && invoice_draft = 0 && invoice_tipe_tarik_tunai = 1
                               ORDER BY invoice_id DESC
                               ");
                    while ($rowProduct = mysqli_fetch_array($queryInvoice)) {
                    if ( $rowProduct['user_id'] === $user_id ) {
                      $totalTarikTunai += ($rowProduct['invoice_sub_total'] - $rowProduct['invoice_total_beli_non_fisik']);
                      $totalModal += $rowProduct['invoice_total_beli_non_fisik'];
                      $totalJual += $rowProduct['invoice_sub_total'];
                  ?>
                  <tr>
                      <td><?= $i; ?></td>
                      <td><?= $rowProduct['penjualan_invoice']; ?></td>
                      <td><?= date('j F Y \a\\t g:i A', strtotime($rowProduct['invoice_tgl'])); ?></td>
                      <td><?= $rowProduct['user_nama']; ?></td>
                      <td>Rp. <?= number_format($rowProduct['invoice_total_beli_non_fisik'], 0, ',', '.'); ?></td>
                      <td>Rp. <?= number_format($rowProduct['invoice_sub_total'], 0, ',', '.'); ?></td>
                      <td>
                          <?php  
                            $invoice_sub_total = $rowProduct['invoice_sub_total'] - $rowProduct['invoice_total_beli_non_fisik'];
                          ?>
                          Rp. <?= number_format($invoice_sub_total, 0, ',', '.'); ?> 
                      </td>
                  </tr>
                  <?php $i++; ?>
                  <?php } ?>
                  <?php } ?>
                  <tr>
                      <td colspan="4">
                        <b>Total</b>
                      </td>
                      <td>
                        Rp. <?php echo number_format($totalModal, 0, ',', '.'); ?>
                      </td>
                      <td>
                        Rp. <?php echo number_format($totalJual, 0, ',', '.'); ?>
                      </td>
                      <td>
                        Rp. <?php echo number_format($totalTarikTunai, 0, ',', '.'); ?>
                      </td>
                  </tr>
                 </tbody>
                </table>
              </div>
            </div>

            <div class="card-body">
              <h5>Laporan Transaksi Lainnya</h5>
              <div class="table-auto">
                <table id="Laporan-Kasir-Pribadi" class="table table-bordered table-striped tableExport">
                  <thead>
                  <tr>
                    <th style="width: 6%;">No.</th>
                    <th style="width: 13%;">Invoice</th>
                    <th>Tanggal</th>
                    <th>Kasir</th>
                    <th>Total</th>
                  </tr>
                  </thead>
                  <tbody>

                  <?php 
                    $i = 1; 
                    $total = 0;
                    $queryInvoice = $conn->query("SELECT invoice.invoice_id ,invoice.penjualan_invoice, invoice.invoice_tgl, invoice.invoice_cabang, user.user_id, user.user_nama, invoice.invoice_sub_total
                               FROM invoice 
                               JOIN user ON invoice.invoice_kasir = user.user_id
                               WHERE invoice_cabang = '".$sessionCabang."' && invoice_date BETWEEN '".$tanggal_awal."' AND '".$tanggal_akhir."' && invoice_piutang = 0 && invoice_piutang_lunas = 0 && invoice_draft = 0 && invoice_tipe_tarik_tunai = 0
                               ORDER BY invoice_id DESC
                               ");
                    while ($rowProduct = mysqli_fetch_array($queryInvoice)) {
                    if ( $rowProduct['user_id'] === $user_id ) {
                      $total += $rowProduct['invoice_sub_total'];
                  ?>
                  <tr>
                      <td><?= $i; ?></td>
                      <td><?= $rowProduct['penjualan_invoice']; ?></td>
                      <td><?= $rowProduct['invoice_tgl']; ?></td>
                      <td><?= $rowProduct['user_nama']; ?></td>
                      <td>Rp. <?= number_format($rowProduct['invoice_sub_total'], 0, ',', '.'); ?></td>
                  </tr>
                  <?php $i++; ?>
                  <?php } ?>
                  <?php } ?>
                  <tr>
                      <td colspan="4">
                        <b>Total</b>
                      </td>
                      <td>
                        Rp. <?php echo number_format($total, 0, ',', '.'); ?>
                      </td>
                  </tr>
                 </tbody>
                </table>
              </div>
            </div>

            <div class="card-body">
              <h5>Laporan DP Penjualan Kasir</h5>
              <div class="table-auto">
                <table id="Laporan-Kasir-Pribadi" class="table table-bordered table-striped tableExport">
                  <thead>
                  <tr>
                    <th style="width: 6%;">No.</th>
                    <th style="width: 13%;">Invoice</th>
                    <th>Tanggal</th>
                    <th>Kasir</th>
                    <th>Total</th>
                  </tr>
                  </thead>
                  <tbody>

                  <?php 
                    $i = 1; 
                    $totalDp = 0;
                    $queryInvoice = $conn->query("SELECT invoice.invoice_id ,invoice.penjualan_invoice, invoice.invoice_tgl, invoice.invoice_cabang, user.user_id, user.user_nama, invoice.invoice_sub_total, invoice.invoice_piutang_dp
                               FROM invoice 
                               JOIN user ON invoice.invoice_kasir = user.user_id
                               WHERE invoice_cabang = '".$sessionCabang."' && invoice_date BETWEEN '".$tanggal_awal."' AND '".$tanggal_akhir."' && invoice_piutang_dp > 0  
                               ORDER BY invoice_id DESC
                               ");
                    while ($rowProduct = mysqli_fetch_array($queryInvoice)) {
                    if ( $rowProduct['user_id'] === $user_id ) {
                      $totalDp += $rowProduct['invoice_piutang_dp'];
                  ?>
                  <tr>
                      <td><?= $i; ?></td>
                      <td><?= $rowProduct['penjualan_invoice']; ?></td>
                      <td><?= $rowProduct['invoice_tgl']; ?></td>
                      <td><?= $rowProduct['user_nama']; ?></td>
                      <td>Rp. <?= number_format($rowProduct['invoice_piutang_dp'], 0, ',', '.'); ?></td>
                  </tr>
                  <?php $i++; ?>
                  <?php } ?>
                  <?php } ?>
                  <tr>
                      <td colspan="4">
                        <b>Total</b>
                      </td>
                      <td>
                        Rp. <?php echo number_format($totalDp, 0, ',', '.'); ?>
                      </td>
                  </tr>
                 </tbody>
                </table>
              </div>
            </div>

            <div class="card-body">
              <h5>Laporan Cicilan Piutang Penjualan Kasir</h5>
              <div class="table-auto">
                <table id="Laporan-Kasir-Pribadi" class="table table-bordered table-striped tableExport">
                  <thead>
                  <tr>
                    <th style="width: 6%;">No.</th>
                    <th style="width: 13%;">Invoice</th>
                    <th>Tanggal</th>
                    <th>Kasir</th>
                    <th>Total</th>
                  </tr>
                  </thead>
                  <tbody>

                  <?php 
                    $i = 1; 
                    $totalCicilan = 0;
                    $queryInvoice = $conn->query("SELECT piutang.piutang_id, piutang.piutang_invoice, piutang.piutang_date, piutang.piutang_date_time, piutang.piutang_nominal, piutang.piutang_kasir, piutang.piutang_cabang, user.user_id, user.user_nama
                               FROM piutang 
                               JOIN user ON piutang.piutang_kasir = user.user_id
                               WHERE piutang_cabang = '".$sessionCabang."' && piutang_date BETWEEN '".$tanggal_awal."' AND '".$tanggal_akhir."' 
                               ORDER BY piutang_id DESC
                               ");
                    while ($rowProduct = mysqli_fetch_array($queryInvoice)) {
                    if ( $rowProduct['user_id'] === $user_id ) {
                      $totalCicilan += $rowProduct['piutang_nominal'];
                  ?>
                  <tr>
                      <td><?= $i; ?></td>
                      <td><?= $rowProduct['piutang_invoice']; ?></td>
                      <td><?= $rowProduct['piutang_date_time']; ?></td>
                      <td><?= $rowProduct['user_nama']; ?></td>
                      <td>Rp. <?= number_format($rowProduct['piutang_nominal'], 0, ',', '.'); ?></td>
                  </tr>
                  <?php $i++; ?>
                  <?php } ?>
                  <?php } ?>
                  <tr>
                      <td colspan="4">
                        <b>Total</b>
                      </td>
                      <td>
                        Rp. <?php echo number_format($totalCicilan, 0, ',', '.'); ?>
                      </td>
                  </tr>
                 </tbody>
                </table>
              </div>
            </div>

            <div class="card-body">
              <?php $totalAll = $total + $totalTarikTunai + $totalDp + $totalCicilan; ?>
              <h5>- Total Keseluruhan Rp. <?php echo number_format($totalAll, 0, ',', '.'); ?></h5>

              <?php $totalBersih = $totalAll - $totalModal; ?>
              <h3>- Total Bersih Rp. <?php echo number_format($totalBersih, 0, ',', '.'); ?></h3>
              <small><b>Total Bersih</b> = Total Keseluruhan - Total Harga Modal Tarik Tunai</small>
            </div>
          </div>
          <!-- /.card -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>
    <?php /*
    <section class="content">
      <div class="row">
        <div class="col-12">

          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Laporan Transaksi Kasir</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <div class="table-auto">
                <table id="laporan-transaksi-kasir" class="table table-bordered table-striped table-laporan">
                  <thead>
                  <tr>
                    <th style="width: 6%;">No.</th>
                    <th style="width: 13%;">Invoice</th>
                    <th>Tanggal</th>
                    <th>Kasir</th>
                    <th>Tipe</th>
                    <th>Total</th>
                  </tr>
                  </thead>
                  <tbody>

                  <?php 
                    $i = 1; 
                    $total = 0;
                    $queryInvoice = $conn->query("SELECT invoice.invoice_id ,invoice.penjualan_invoice, invoice.invoice_tgl, invoice.invoice_tipe_tarik_tunai, user.user_id, user.user_nama, invoice.invoice_total, invoice.invoice_sub_total
                               FROM invoice 
                               JOIN user ON invoice.invoice_kasir = user.user_id
                               WHERE invoice_date BETWEEN '".$tanggal_awal."' AND '".$tanggal_akhir."' && invoice_piutang = 0 && invoice_piutang_lunas = 0 && invoice_cabang = $sessionCabang && invoice_draft = 0
                               ORDER BY invoice_id DESC
                               ");
                    while ($rowProduct = mysqli_fetch_array($queryInvoice)) {
                    if ( $rowProduct['user_id'] === $user_id ) {
                      $total += $rowProduct['invoice_sub_total'];
                  ?>
                  <tr>
                    	<td><?= $i; ?></td>
                      <td><?= $rowProduct['penjualan_invoice']; ?></td>
                      <td><?= $rowProduct['invoice_tgl']; ?></td>
                      <td><?= $rowProduct['user_nama']; ?></td>
                      <td>
                        <?php  
                          if ( $rowProduct['invoice_tipe_tarik_tunai'] == 1 ) {
                            echo "<b style='color: blue';>Tarik Tunai</b>";
                          } else {
                            echo "Lainnya";
                          }
                        ?>
                      </td>
                      <td>Rp. <?= number_format($rowProduct['invoice_sub_total'], 0, ',', '.'); ?></td>
                  </tr>
                  <?php $i++; ?>
                  <?php } ?>
                  <?php } ?>
                  <tr>
                      <td colspan="5">
                        <b>Total</b>
                      </td>
                      <td>
                        Rp. <?php echo number_format($total, 0, ',', '.'); ?>
                      </td>
                  </tr>
                 </tbody>
                </table>
              </div>
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>
    */ ?>
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
    $("#laporan-transaksi-kasir").DataTable();
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