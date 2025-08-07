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
            <h1>Laporan Tarik Tunai</h1>
          </div>
          <div class="col-sm-4">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="bo">Home</a></li>
              <li class="breadcrumb-item active">Tarik Tunai</li>
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
                        <label for="tanggal_akhir">Provider</label>
                        <select class="form-control select2bs4" required="" name="barang_id">
                            <option selected="selected" value="">-- Pilih --</option>
                            <option value="0">Semua</option>
                            <?php  
                              $produk = query("SELECT * FROM provider WHERE provider_status > 0 && provider_cabang = $sessionCabang ORDER BY provider_id  DESC ");
                            ?>
                            <?php foreach ( $produk as $row ) : ?>
                              <option value="<?= $row['provider_id'] ?>"><?= $row['provider_nama'] ?></option>
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

      <?php if ( $barang_id < 1 ) : ?>
        <section class="content">
          <div class="row">
            <div class="col-12">

              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">Laporan Tarik Tunai Keseluruhan dari Tanggal <b><?= tanggal_indo($tanggal_awal); ?></b> Sampai <b><?= tanggal_indo($tanggal_akhir); ?></b></h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                  <div class="table-auto">
                    <table id="laporan-tarik-tunai" class="table table-bordered table-striped table-laporan">
                      <thead>
                      <tr>
                        <th style="width: 6%;">No.</th>
                        <th style="width: 13%;">Invoice</th>
                        <th>Tanggal</th>
                        <th>Produk</th>
                        <th>Provider</th>
                        <th>Harga Modal</th>
                        <th>Harga Jual</th>
                        <th>Laba</th>
                      </tr>
                      </thead>
                      <tbody>

                      <?php 
                        $i            = 1; 
                        $totalModal   = 0;
                        $totalJual    = 0;
                        $totalProfit  = 0;
                        $queryPenjualan = $conn->query("SELECT penjualan_barang_non_fisik.pbnf_id, 
                          penjualan_barang_non_fisik.pbnf_barang_id, 
                          penjualan_barang_non_fisik.pbnf_barang_nama,
                          penjualan_barang_non_fisik.pbnf_invoice, 
                          penjualan_barang_non_fisik.pbnf_date, 
                          penjualan_barang_non_fisik.pbnf_provider, 
                          penjualan_barang_non_fisik.pbnf_harga_beli, 
                          penjualan_barang_non_fisik.pbnf_harga_jual, 
                          penjualan_barang_non_fisik.pbnf_cabang, 
                          barang_non_fisik.bnf_id, 
                          barang_non_fisik.bnf_nama
                                   FROM penjualan_barang_non_fisik 
                                   JOIN barang_non_fisik ON penjualan_barang_non_fisik.pbnf_barang_id = barang_non_fisik.bnf_id
                                   WHERE pbnf_barang_nama LIKE '%tarik tunai%' && pbnf_cabang = '".$sessionCabang."' && pbnf_date BETWEEN '".$tanggal_awal."' AND '".$tanggal_akhir."' 
                                   ORDER BY pbnf_id DESC
                                   ");
                        while ($rowProduct = mysqli_fetch_array($queryPenjualan)) {
                        $totalModal += $rowProduct['pbnf_harga_beli'];
                        $totalJual += $rowProduct['pbnf_harga_jual'];
                        $totalProfit = $totalJual - $totalModal;
                      ?>
                      <tr>
                        	<td><?= $i; ?></td>
                          <td><?= $rowProduct['pbnf_invoice']; ?></td>
                          <td><?= tanggal_indo($rowProduct['pbnf_date']); ?></td>
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
                          <td>Rp. <?= number_format($rowProduct['pbnf_harga_beli'], 0, ',', '.'); ?></td>
                          <td>Rp. <?= number_format($rowProduct['pbnf_harga_jual'], 0, ',', '.'); ?></td>
                          <td>
                            <?php  
                              $profit = $rowProduct['pbnf_harga_jual'] - $rowProduct['pbnf_harga_beli'];
                            ?>
                            Rp. <?= number_format($profit, 0, ',', '.'); ?>
                          </td>
                      </tr>
                      <?php $i++; ?>
                      <?php } ?>
                      <tr>
                          <td colspan="5">
                            <b>Total</b>
                          </td>
                          <td>
                            <b>Rp. <?= number_format($totalModal, 0, ',', '.'); ?></b>
                          </td>
                          <td>
                            <b>Rp. <?= number_format($totalJual, 0, ',', '.'); ?></b>
                          </td>
                          <td>
                            <b>Rp. <?= number_format($totalProfit, 0, ',', '.'); ?></b>
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
      <?php else : ?>

        <section class="content">
          <div class="row">
            <div class="col-12">
              <?php 
                // Mencari Nama Provider 
                $provider = mysqli_query($conn, "SELECT provider_nama FROM provider WHERE provider_id = $barang_id");
                $provider = mysqli_fetch_array($provider);
                $provider_nama = $provider['provider_nama'];
              ?>
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">Laporan Tarik Tunai <b>Provider <?= $provider_nama; ?></b> dari Tanggal <b><?= tanggal_indo($tanggal_awal); ?></b> Sampai <b><?= tanggal_indo($tanggal_akhir); ?></b></h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                  <div class="table-auto">
                    <table id="laporan-tarik-tunai" class="table table-bordered table-striped table-laporan">
                      <thead>
                      <tr>
                        <th style="width: 6%;">No.</th>
                        <th style="width: 13%;">Invoice</th>
                        <th>Tanggal</th>
                        <th>Produk</th>
                        <th>Provider</th>
                        <th>Harga Modal</th>
                        <th>Harga Jual</th>
                        <th>Laba</th>
                      </tr>
                      </thead>
                      <tbody>

                      <?php 
                        $i            = 1; 
                        $totalModal   = 0;
                        $totalJual    = 0;
                        $totalProfit  = 0;
                        $queryPenjualan = $conn->query("SELECT penjualan_barang_non_fisik.pbnf_id, 
                          penjualan_barang_non_fisik.pbnf_barang_id, 
                          penjualan_barang_non_fisik.pbnf_barang_nama,
                          penjualan_barang_non_fisik.pbnf_invoice, 
                          penjualan_barang_non_fisik.pbnf_date, 
                          penjualan_barang_non_fisik.pbnf_provider, 
                          penjualan_barang_non_fisik.pbnf_harga_beli, 
                          penjualan_barang_non_fisik.pbnf_harga_jual, 
                          penjualan_barang_non_fisik.pbnf_cabang, 
                          barang_non_fisik.bnf_id, 
                          barang_non_fisik.bnf_nama
                                   FROM penjualan_barang_non_fisik 
                                   JOIN barang_non_fisik ON penjualan_barang_non_fisik.pbnf_barang_id = barang_non_fisik.bnf_id
                                   WHERE pbnf_barang_nama LIKE '%tarik tunai%' && pbnf_provider = $barang_id && pbnf_cabang = '".$sessionCabang."' && pbnf_date BETWEEN '".$tanggal_awal."' AND '".$tanggal_akhir."' 
                                   ORDER BY pbnf_id DESC
                                   ");
                        while ($rowProduct = mysqli_fetch_array($queryPenjualan)) {
                        $totalModal += $rowProduct['pbnf_harga_beli'];
                        $totalJual += $rowProduct['pbnf_harga_jual'];
                        $totalProfit = $totalJual - $totalModal;
                      ?>
                      <tr>
                          <td><?= $i; ?></td>
                          <td><?= $rowProduct['pbnf_invoice']; ?></td>
                          <td><?= tanggal_indo($rowProduct['pbnf_date']); ?></td>
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
                          <td>Rp. <?= number_format($rowProduct['pbnf_harga_beli'], 0, ',', '.'); ?></td>
                          <td>Rp. <?= number_format($rowProduct['pbnf_harga_jual'], 0, ',', '.'); ?></td>
                          <td>
                            <?php  
                              $profit = $rowProduct['pbnf_harga_jual'] - $rowProduct['pbnf_harga_beli'];
                            ?>
                            Rp. <?= number_format($profit, 0, ',', '.'); ?>
                          </td>
                      </tr>
                      <?php $i++; ?>
                      <?php } ?>
                      <tr>
                          <td colspan="5">
                            <b>Total</b>
                          </td>
                          <td>
                            <b>Rp. <?= number_format($totalModal, 0, ',', '.'); ?></b>
                          </td>
                          <td>
                            <b>Rp. <?= number_format($totalJual, 0, ',', '.'); ?></b>
                          </td>
                          <td>
                            <b>Rp. <?= number_format($totalProfit, 0, ',', '.'); ?></b>
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

      <?php endif; ?>

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