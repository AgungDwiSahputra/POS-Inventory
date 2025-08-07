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
            <h1>Data Laporan Rincian Penjualan</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="bo">Home</a></li>
              <li class="breadcrumb-item active">Laporan Rincian Penjualan</li>
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
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="tanggal_awal">Tanggal Awal</label>
                        <input type="date" name="tanggal_awal" class="form-control" id="tanggal_awal" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="tanggal_akhir">Tanggal Akhir</label>
                        <input type="date" name="tanggal_akhir" class="form-control" id="tanggal_akhir" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="tanggal_akhir">Tipe</label>
                        <select class="form-control select2bs4" required="" name="tipe">
                            <option selected="selected" value="">-- Pilih --</option>
                            <option value="1">Invoice</option>
                            <option value="0">Per Produk</option>
                          </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="tanggal_akhir">Jenis Transaksi</label>
                        <select class="form-control select2bs4" required="" name="jenis">
                            <option selected="selected" value="">-- Pilih --</option>
                            <option value="0">Fisik</option>
                            <option value="1">Non Fisik</option>
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
          $tipe          = $_POST['tipe'];
          $jenis         = $_POST['jenis'];

          if ( $tipe == 0 ) {
            $textTitle = "Penjualan";
          } else {
            $textTitle = "Invoice";
          }
        ?>
    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-12">

          <div class="card">
            <div class="card-header">
              <div class="row">
                  <div class="col-md-9 col-lg-9">
                      <h3 class="card-title">Laporan Rincian <?= $textTitle; ?> ALL Sales Dari <b><?= tanggal_indo($tanggal_awal); ?></b> ke <b><?= tanggal_indo($tanggal_akhir); ?></b></h3>
                  </div>
                  <div class="col-md-3 col-lg-3"></div>
              </div>
            </div>

          <?php if ( $jenis < 1 ) : ?>
            <?php if ( $tipe == 0 ) : ?>
              <!-- Penjualan Fisik-->
              <div class="card-body">
                <div class="table-auto">
                  <table id="laporan-transaksi-kasir" class="table table-bordered table-striped table-laporan">
                    <thead>
                    <tr>
                      <th style="width: 6%;">No</th>
                      <th>TGL</th>
                      <th>Invoice</th>
                      <th>Customer</th>
                      <th class="text-center">QTY</th>
                      <th>Kode Barang</th>
                      <th>Nama Barang</th>
                      <th>Harga</th>
                      <th>Total</th>
                      <th>Modal</th>
                      <th>Total Modal</th>
                      <th>Laba</th>
                    </tr>
                    </thead>
                    <tbody>

                    <?php 
                      $i = 1; 
                      $totalKeseluruhanHarga = 0;
                      $totalKeseluruhanHBeli = 0;
                      $totalKeseluruhanLaba  = 0;
                      $queryInvoice = $conn->query("SELECT penjualan.penjualan_id,
                        penjualan.penjualan_barang_id, 
                        penjualan.barang_qty, 
                        penjualan.keranjang_harga_beli, 
                        penjualan.keranjang_harga,
                        penjualan.penjualan_invoice, 
                        penjualan.penjualan_date, 
                        penjualan.penjualan_cabang,
                        barang.barang_id,
                        barang.barang_nama,
                        barang.barang_kode
                                 FROM penjualan 
                                 JOIN barang ON penjualan.penjualan_barang_id = barang.barang_id
                                 WHERE penjualan_date BETWEEN '".$tanggal_awal."' AND '".$tanggal_akhir."' && penjualan_cabang = $sessionCabang 
                                 ORDER BY penjualan_id DESC
                                 ");
                      while ($rowProduct = mysqli_fetch_array($queryInvoice)) {
                        $totalHarga = $rowProduct['keranjang_harga'] * $rowProduct['barang_qty'];
                        $totalBeli = $rowProduct['keranjang_harga_beli'] * $rowProduct['barang_qty'];
                        $totalLaba = $totalHarga - $totalBeli;

                        $totalKeseluruhanHarga += $totalHarga;
                        $totalKeseluruhanHBeli += $totalBeli;
                        $totalKeseluruhanLaba  += $totalLaba;
                    ?>
                    <tr>
                      	<td><?= $i; ?></td>
                        <td><?= tanggal_indo($rowProduct['penjualan_date']); ?></td>
                        <td><?= $rowProduct['penjualan_invoice']; ?></td>
                        <td>
                          <?php  
                            $penjualan_invoice = $rowProduct['penjualan_invoice'];
                            $noInvoice = mysqli_query($conn, "SELECT invoice_customer FROM invoice WHERE penjualan_invoice = $penjualan_invoice && invoice_cabang = $sessionCabang");
                            $noInvoice = mysqli_fetch_array($noInvoice);
                            $noInvoice = $noInvoice['invoice_customer'];

                            $namaCustomer = mysqli_query($conn, "SELECT customer_nama FROM customer WHERE customer_id = $noInvoice && customer_cabang = $sessionCabang");
                            $namaCustomer = mysqli_fetch_array($namaCustomer);
                            $namaCustomer = $namaCustomer['customer_nama'];

                            echo $namaCustomer;
                          ?>
                        </td>
                        <td class="text-center"><?= $rowProduct['barang_qty']; ?></td>
                        <td><?= $rowProduct['barang_kode']; ?></td>
                        <td><?= $rowProduct['barang_nama']; ?></td>
                        <td><?= number_format($rowProduct['keranjang_harga'], 0, ',', '.'); ?></td>
                        <td><?= number_format($totalHarga, 0, ',', '.'); ?> </td>
                        <td>
                            <?php if ( $rowProduct['keranjang_harga_beli'] > 0 ) : ?>
                              <?= number_format($rowProduct['keranjang_harga_beli'], 0, ',', '.'); ?>
                            <?php else : ?>
                              0
                            <?php endif; ?>
                        </td>
                        <td><?= number_format($totalBeli, 0, ',', '.'); ?></td>
                        <td><?= number_format($totalLaba, 0, ',', '.'); ?></td>
                    </tr>
                    <?php $i++; ?>
                    <?php } ?>
                    <tr>
                        <td colspan="8"><!-- <b>Total Invoice: 123.000</b> --></td>
                        <td style="color: red;"><?php echo number_format($totalKeseluruhanHarga, 0, ',', '.'); ?></td>
                        <td></td>
                        <td style="color: red;"><?php echo number_format($totalKeseluruhanHBeli, 0, ',', '.'); ?></td>
                        <td style="color: red;"><?php echo number_format($totalKeseluruhanLaba, 0, ',', '.'); ?></td>
                    </tr>
                   </tbody>
                  </table>
                </div>
              </div>

            <?php else : ?>
              <!-- Invoice -->
              <div class="card-body">
                <div class="table-auto">
                  <table id="laporan-transaksi-kasir" class="table table-bordered table-striped table-laporan">
                    <thead>
                    <tr>
                      <th style="width: 6%;">No</th>
                      <th>TGL</th>
                      <th>Invoice</th>
                      <th>Nama Toko</th>
                      <th>Total</th>
                      <th>Modal</th>
                      <th>Laba</th>
                    </tr>
                    </thead>
                    <tbody>

                    <?php 
                    // invoice_piutang_lunas = 0 &&
                      $i = 1; 
                      $totalKeseluruhanHarga = 0;
                      $totalKeseluruhanHBeli = 0;
                      $totalKeseluruhanLaba  = 0;
                      $queryInvoice = $conn->query("SELECT invoice.invoice_id,
                        invoice.invoice_customer,
                        invoice.invoice_total_beli, 
                        invoice.invoice_sub_total,
                        invoice.penjualan_invoice, 
                        invoice.invoice_date, 
                        invoice.invoice_cabang,
                        customer.customer_id,
                        customer.customer_nama
                                 FROM invoice 
                                 JOIN customer ON invoice.invoice_customer = customer.customer_id
                                 WHERE invoice_date BETWEEN '".$tanggal_awal."' AND '".$tanggal_akhir."' && invoice_cabang = $sessionCabang && invoice_tipe_non_fisik < 1 && invoice_piutang = 0 && invoice_draft = 0 && invoice_tipe_tarik_tunai = 0
                                 ORDER BY invoice_id DESC
                                 ");
                      while ($rowProduct = mysqli_fetch_array($queryInvoice)) {
                        $totalLaba = $rowProduct['invoice_sub_total'] - $rowProduct['invoice_total_beli'];

                        $totalKeseluruhanHarga += $rowProduct['invoice_sub_total'];
                        $totalKeseluruhanHBeli += $rowProduct['invoice_total_beli'];
                        $totalKeseluruhanLaba  += $totalLaba;
                    ?>
                    <tr>
                        <td><?= $i; ?></td>
                        <td><?= tanggal_indo($rowProduct['invoice_date']); ?></td>
                        <td><?= $rowProduct['penjualan_invoice']; ?></td>
                        <td><?= $rowProduct['customer_nama']; ?></td>
                        <td><?= number_format($rowProduct['invoice_sub_total'], 0, ',', '.'); ?></td>
                        <td><?= number_format($rowProduct['invoice_total_beli'], 0, ',', '.'); ?></td>
                        <td><?= number_format($totalLaba, 0, ',', '.'); ?></td>
                    </tr>
                    <?php $i++; ?>
                    <?php } ?>
                    <tr>
                        <td colspan="4">
                        <td style="color: red;"><?php echo number_format($totalKeseluruhanHarga, 0, ',', '.'); ?></td>
                        <td style="color: red;"><?php echo number_format($totalKeseluruhanHBeli, 0, ',', '.'); ?></td>
                        <td style="color: red;"><?php echo number_format($totalKeseluruhanLaba, 0, ',', '.'); ?></td>
                    </tr>
                   </tbody>
                  </table>
                </div>
              </div>
            <?php endif; ?>

          <?php else : ?>
            <?php if ( $tipe == 0 ) : ?>
              <!-- Penjaualan -->
              <div class="card-body">
                <div class="table-auto">
                  <table id="laporan-transaksi-kasir" class="table table-bordered table-striped table-laporan">
                    <thead>
                    <tr>
                      <th style="width: 6%;">No</th>
                      <th>TGL</th>
                      <th>Invoice</th>
                      <th>Customer</th>
                      <th class="text-center">QTY</th>
                      <th>Nama Barang</th>
                      <th>Provider</th>
                      <th>Harga</th>
                      <th>Total</th>
                      <th>Modal</th>
                      <th>Total Modal</th>
                      <th>Laba</th>
                    </tr>
                    </thead>
                    <tbody>

                    <?php 
                      $i = 1; 
                      $totalKeseluruhanHarga = 0;
                      $totalKeseluruhanHBeli = 0;
                      $totalKeseluruhanLaba  = 0;
                      $queryInvoice = $conn->query("SELECT penjualan_barang_non_fisik.pbnf_id,
                        penjualan_barang_non_fisik.pbnf_barang_id, 
                        penjualan_barang_non_fisik.pbnf_provider, 
                        penjualan_barang_non_fisik.pbnf_qty, 
                        penjualan_barang_non_fisik.pbnf_harga_beli, 
                        penjualan_barang_non_fisik.pbnf_harga_jual,
                        penjualan_barang_non_fisik.pbnf_invoice, 
                        penjualan_barang_non_fisik.pbnf_date, 
                        penjualan_barang_non_fisik.pbnf_cabang,
                        barang_non_fisik.bnf_id,
                        barang_non_fisik.bnf_nama,
                        barang_non_fisik.bnf_kode
                                 FROM penjualan_barang_non_fisik 
                                 JOIN barang_non_fisik ON penjualan_barang_non_fisik.pbnf_barang_id = barang_non_fisik.bnf_id
                                 WHERE pbnf_date BETWEEN '".$tanggal_awal."' AND '".$tanggal_akhir."' && pbnf_cabang = $sessionCabang 
                                 ORDER BY pbnf_id DESC
                                 ");
                      while ($rowProduct = mysqli_fetch_array($queryInvoice)) {
                        $totalHarga = $rowProduct['pbnf_harga_jual'] * $rowProduct['pbnf_qty'];
                        $totalBeli = $rowProduct['pbnf_harga_beli'] * $rowProduct['pbnf_qty'];
                        $totalLaba = $totalHarga - $totalBeli;

                        $totalKeseluruhanHarga += $totalHarga;
                        $totalKeseluruhanHBeli += $totalBeli;
                        $totalKeseluruhanLaba  += $totalLaba;
                    ?>
                    <tr>
                        <td><?= $i; ?></td>
                        <td><?= tanggal_indo($rowProduct['pbnf_date']); ?></td>
                        <td><?= $rowProduct['pbnf_invoice']; ?></td>
                        <td>
                          <?php  
                            $pbnf_invoice = $rowProduct['pbnf_invoice'];
                            $noInvoice = mysqli_query($conn, "SELECT invoice_customer FROM invoice WHERE penjualan_invoice = $pbnf_invoice && invoice_cabang = $sessionCabang");
                            $noInvoice = mysqli_fetch_array($noInvoice);
                            $noInvoice = $noInvoice['invoice_customer'];

                            $namaCustomer = mysqli_query($conn, "SELECT customer_nama FROM customer WHERE customer_id = $noInvoice && customer_cabang = $sessionCabang");
                            $namaCustomer = mysqli_fetch_array($namaCustomer);
                            $namaCustomer = $namaCustomer['customer_nama'];

                            echo $namaCustomer;
                          ?>
                        </td>
                        <td class="text-center"><?= $rowProduct['pbnf_qty']; ?></td>
                        <td><?= $rowProduct['bnf_nama']; ?></td>
                        <td>
                            <?php 
                              // Mencari Nama Provider 
                              $pbnf_provider = $rowProduct['pbnf_provider'];
                              $provider = mysqli_query($conn, "SELECT provider_nama FROM provider WHERE provider_id = $pbnf_provider");
                              $provider = mysqli_fetch_array($provider);
                              $provider_nama = $provider['provider_nama'];
                              echo $provider_nama;
                            ?>      
                        </td>
                        <td><?= number_format($rowProduct['pbnf_harga_jual'], 0, ',', '.'); ?></td>
                        <td><?= number_format($totalHarga, 0, ',', '.'); ?> </td>
                        <td>
                            <?php if ( $rowProduct['pbnf_harga_beli'] > 0 ) : ?>
                              <?= number_format($rowProduct['pbnf_harga_beli'], 0, ',', '.'); ?>
                            <?php else : ?>
                              0
                            <?php endif; ?>
                        </td>
                        <td><?= number_format($totalBeli, 0, ',', '.'); ?></td>
                        <td><?= number_format($totalLaba, 0, ',', '.'); ?></td>
                    </tr>
                    <?php $i++; ?>
                    <?php } ?>
                    <tr>
                        <td colspan="8"><!-- <b>Total Invoice: 123.000</b> --></td>
                        <td style="color: red;"><?php echo number_format($totalKeseluruhanHarga, 0, ',', '.'); ?></td>
                        <td></td>
                        <td style="color: red;"><?php echo number_format($totalKeseluruhanHBeli, 0, ',', '.'); ?></td>
                        <td style="color: red;"><?php echo number_format($totalKeseluruhanLaba, 0, ',', '.'); ?></td>
                    </tr>
                   </tbody>
                  </table>
                </div>
              </div>

            <?php else : ?>
              <!-- Invoice -->
              <div class="card-body">
                <div class="table-auto">
                  <table id="laporan-transaksi-kasir" class="table table-bordered table-striped table-laporan">
                    <thead>
                    <tr>
                      <th style="width: 6%;">No</th>
                      <th>TGL</th>
                      <th>Invoice</th>
                      <th>Nama Toko</th>
                      <th>Total</th>
                      <th>Modal</th>
                      <th>Laba</th>
                    </tr>
                    </thead>
                    <tbody>

                    <?php 
                      $i = 1; 
                      $totalKeseluruhanHarga = 0;
                      $totalKeseluruhanHBeli = 0;
                      $totalKeseluruhanLaba  = 0;
                      $queryInvoice = $conn->query("SELECT invoice.invoice_id,
                        invoice.invoice_customer,
                        invoice.invoice_total_beli_non_fisik, 
                        invoice.invoice_sub_total,
                        invoice.penjualan_invoice, 
                        invoice.invoice_date, 
                        invoice.invoice_cabang,
                        customer.customer_id,
                        customer.customer_nama
                                 FROM invoice 
                                 JOIN customer ON invoice.invoice_customer = customer.customer_id
                                 WHERE invoice_date BETWEEN '".$tanggal_awal."' AND '".$tanggal_akhir."' && invoice_cabang = $sessionCabang && invoice_tipe_non_fisik > 0
                                 ORDER BY invoice_id DESC
                                 ");
                      while ($rowProduct = mysqli_fetch_array($queryInvoice)) {
                        $totalLaba = $rowProduct['invoice_sub_total'] - $rowProduct['invoice_total_beli_non_fisik'];

                        $totalKeseluruhanHarga += $rowProduct['invoice_sub_total'];
                        $totalKeseluruhanHBeli += $rowProduct['invoice_total_beli_non_fisik'];
                        $totalKeseluruhanLaba  += $totalLaba;
                    ?>
                    <tr>
                        <td><?= $i; ?></td>
                        <td><?= tanggal_indo($rowProduct['invoice_date']); ?></td>
                        <td><?= $rowProduct['penjualan_invoice']; ?></td>
                        <td><?= $rowProduct['customer_nama']; ?></td>
                        <td><?= number_format($rowProduct['invoice_sub_total'], 0, ',', '.'); ?></td>
                        <td><?= number_format($rowProduct['invoice_total_beli_non_fisik'], 0, ',', '.'); ?></td>
                        <td><?= number_format($totalLaba, 0, ',', '.'); ?></td>
                    </tr>
                    <?php $i++; ?>
                    <?php } ?>
                    <tr>
                        <td colspan="4">
                        <td style="color: red;"><?php echo number_format($totalKeseluruhanHarga, 0, ',', '.'); ?></td>
                        <td style="color: red;"><?php echo number_format($totalKeseluruhanHBeli, 0, ',', '.'); ?></td>
                        <td style="color: red;"><?php echo number_format($totalKeseluruhanLaba, 0, ',', '.'); ?></td>
                    </tr>
                   </tbody>
                  </table>
                </div>
              </div>
            <?php endif; ?>
          <?php endif; ?>
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