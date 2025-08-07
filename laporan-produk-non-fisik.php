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
          <div class="col-sm-8">
            <h1>Laporan Penjualan Per Produk Non Fisik</h1>
          </div>
          <div class="col-sm-4">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="bo">Home</a></li>
              <li class="breadcrumb-item active">Penjualan Per Produk Non Fisik</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>


    <section class="content">
      <div class="container-fluid">
        <div class="card card-default">
          <div class="card-header">
            <h3 class="card-title">Filter Data Berdasrkan Tanggal dan Produk</h3>

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
                        <input type="date" name="tanggal_awal" class="form-control" id="tanggal_awal" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="tanggal_akhir">Tanggal Akhir</label>
                        <input type="date" name="tanggal_akhir" class="form-control" id="tanggal_akhir" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="tanggal_akhir">Produk</label>
                        <select class="form-control select2bs4" required="" name="barang_id">
                            <option selected="selected" value="">-- Pilih Produk --</option>
                            <?php  
                              $produk = query("SELECT * FROM barang_non_fisik WHERE bnf_status > 0 && bnf_cabang = $sessionCabang ORDER BY bnf_id DESC ");
                            ?>
                            <?php foreach ( $produk as $row ) : ?>
                              <option value="<?= $row['bnf_id'] ?>"><?= $row['bnf_nama'] ?></option>
                            <?php endforeach; ?>
                          </select>
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
          $tanggal_awal  = $_POST['tanggal_awal'];
          $tanggal_akhir = $_POST['tanggal_akhir'];
          $barang_id     = $_POST['barang_id'];
        ?>
    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-12">

          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Laporan Per Produk</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <div class="table-auto">
                <table id="laporan-per-produk-non-fisik" class="table table-bordered table-striped table-laporan">
                  <thead>
                  <tr>
                    <th style="width: 6%;">No.</th>
                    <th style="width: 13%;">Invoice</th>
                    <th>Tanggal</th>
                    <th>Produk</th>
                    <th>Provider</th>
                    <th>QTY Terjual</th>
                  </tr>
                  </thead>
                  <tbody>

                  <?php 
                    $i = 1; 
                    $total = 0;
                    $queryPenjualan = $conn->query("SELECT penjualan_barang_non_fisik.pbnf_id, 
                      penjualan_barang_non_fisik.pbnf_barang_id, 
                      penjualan_barang_non_fisik.pbnf_invoice, 
                      penjualan_barang_non_fisik.pbnf_date, 
                      penjualan_barang_non_fisik.pbnf_provider, 
                      penjualan_barang_non_fisik.pbnf_qty, 
                      penjualan_barang_non_fisik.pbnf_cabang, 
                      barang_non_fisik.bnf_id, 
                      barang_non_fisik.bnf_nama
                               FROM penjualan_barang_non_fisik 
                               JOIN barang_non_fisik ON penjualan_barang_non_fisik.pbnf_barang_id = barang_non_fisik.bnf_id
                               WHERE pbnf_cabang = '".$sessionCabang."' && pbnf_barang_id = '".$barang_id."' && pbnf_date BETWEEN '".$tanggal_awal."' AND '".$tanggal_akhir."' 
                               ORDER BY pbnf_id DESC
                               ");
                    while ($rowProduct = mysqli_fetch_array($queryPenjualan)) {
                    $total += $rowProduct['pbnf_qty'];
                  ?>
                  <tr>
                    	<td><?= $i; ?></td>
                      <td><?= $rowProduct['pbnf_invoice']; ?></td>
                      <td><?= $rowProduct['pbnf_date']; ?></td>
                      <td><?= $rowProduct['bnf_nama']; ?></td>
                      <td>
                        <?php  
                          $pbnf_provider = $rowProduct['pbnf_provider'];
                          $provider = mysqli_query($conn, "SELECT provider_nama FROM provider WHERE provider_id = $pbnf_provider");
                          $provider = mysqli_fetch_array($provider);
                          $provider_nama = $provider['provider_nama'];
                          echo $provider_nama;
                        ?>
                      </td>
                      <td><?= $rowProduct['pbnf_qty']; ?></td>
                  </tr>
                  <?php $i++; ?>
                  <?php } ?>
                  <tr>
                      <td colspan="6">
                        <b>Total <span style="color: red;">Terjual <?= mysqli_num_rows($queryPenjualan); ?>x</span> dengan Jumlah Keseluruhan <span style="color: red">QTY Terjual <?= $total; ?></span></b>
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
    <!-- /.content -->
    <?php  } ?>
  </div>
</div>



<?php include '_footer.php'; ?>
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