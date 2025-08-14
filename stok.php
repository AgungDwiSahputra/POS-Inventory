<?php
include '_header.php';
include '_nav.php';
include '_sidebar.php';
?>
<?php
if ($levelLogin === "kasir") {
  echo "
      <script>
        document.location.href = 'bo';
      </script>
    ";
}

if (isset($_POST['submit'])) {
  $kategoriId = htmlspecialchars($_POST['kategori_id']);

  echo "
      <script>
        document.location.href = 'stok?id=" . base64_encode($kategoriId) . " ';
      </script>
    ";
}

if (empty($_GET['id'])) {
  $kategoriId = 0;
} else {
  $kategoriId = base64_decode($_GET['id']);
}
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Data Stok</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="bo">Home</a></li>
            <li class="breadcrumb-item active">Stok</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

  <section class="content">
    <div class="container-fluid">
      <div class="card card-default" style="max-width: 500px;">
        <div class="card-header">
          <h3 class="card-title">Filter Data Berdasrkan Kategori</h3>

          <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
            <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-remove"></i></button>
          </div>
        </div>
        <!-- /.card-header -->
        <form role="form" action="" method="POST">
          <div class="card-body">
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label for="tanggal_akhir">Nama Kategori</label>
                  <?php $data = query("SELECT * FROM kategori WHERE kategori_cabang = $sessionCabang ORDER BY kategori_id DESC"); ?>
                  <select name="kategori_id" required="" class="form-control ">
                    <option value="">--Pilih Kategori--</option>
                    <option value="0">Semua Kategori</option>
                    <?php foreach ($data as $row) : ?>
                      <?php if ($row['kategori_status'] === '1') { ?>
                        <option value="<?= $row['kategori_id']; ?>">
                          <?= $row['kategori_nama']; ?>
                        </option>
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
            </div>
          </div>
        </form>
      </div>
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-12">

        <?php if ($kategoriId < 1) : ?>
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Data barang Kategori Keseluruhan</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <div class="table-auto">
                <table id="laporan-stok-terkecil" class="table table-bordered table-striped table-laporan">
                  <thead>
                    <tr>
                      <th style="width: 6%;">No.</th>
                      <th style="width: 13%;">Kode Barang</th>
                      <th>Nama</th>
                      <th>Kategori</th>
                      <th>Stock</th>
                      <th>Satuan</th>
                      <th>Total Asset</th>
                      <th>HPP</th>
                      <th>Total HPP</th>
                    </tr>
                  </thead>
                  <tbody>

                    <?php
                    $i = 1;
                    $totalBeli = 0;
                    $totalJual = 0;
                    $queryProduct = $conn->query("SELECT barang.barang_id, barang.barang_kode, barang.barang_nama, barang.barang_harga_beli, barang.barang_harga, barang.hpp, barang.barang_stock, barang.barang_cabang, kategori.kategori_id, kategori.kategori_nama, sk.id as sub_kategori_id, sk.sub_kategori_kode, satuan.satuan_id, satuan.satuan_nama
                               FROM barang 
                               JOIN kategori ON barang.kategori_id = kategori.kategori_id
                               LEFT JOIN sub_kategori as sk ON barang.barang_sub_kategori_id = sk.id
                               JOIN satuan ON barang.satuan_id = satuan.satuan_id
                               WHERE barang_cabang = '" . $sessionCabang . "' ORDER BY CAST(barang_stock AS UNSIGNED) ASC
                               ");
                    while ($rowProduct = mysqli_fetch_array($queryProduct)) {
                      $totalBeli += floatval($rowProduct['barang_harga_beli']) * intval($rowProduct['barang_stock']);
                      $totalJual += $rowProduct['barang_harga'] * $rowProduct['barang_stock'];
                    ?>
                      <tr>
                        <td><?= $i; ?></td>
                        <td><?= $rowProduct['barang_kode']; ?></td>
                        <!-- <td><?= (empty($rowProduct['sub_kategori_kode']) ? '' : ('[ ' . $rowProduct['sub_kategori_kode']) . ' ] ') . $rowProduct['barang_nama'] ?></td> -->
                        <td><?= (empty($rowProduct['sub_kategori_kode']) ? '' : ($rowProduct['sub_kategori_kode']) . '-') . $rowProduct['barang_nama'] ?></td>
                        <td><?= $rowProduct['kategori_nama']; ?></td>
                        <td>
                          <b><?= $rowProduct['barang_stock']; ?></b>
                        </td>
                        <td><?= $rowProduct['satuan_nama']; ?></td>
                        <td>
                          <?php if ($rowProduct['barang_harga_beli'] > 0) : ?>
                            Rp. <?= number_format($rowProduct['barang_harga_beli'], 0, ',', '.'); ?>
                          <?php else : ?>
                            Rp. 0
                          <?php endif; ?>
                        </td>
                        <td>
                          <?php if ($rowProduct['hpp'] > 0) : ?>
                            Rp. <?= number_format($rowProduct['hpp'], 0, ',', '.'); ?>
                          <?php else : ?>
                            Rp. 0
                          <?php endif; ?>
                        </td>
                        <td>
                          <?php if ($rowProduct['hpp'] > 0) : ?>
                            <?php $totalHargaBeli = $rowProduct['hpp'] * $rowProduct['barang_stock']; ?>
                            Rp. <?= number_format($totalHargaBeli, 0, ',', '.'); ?>
                          <?php else : ?>
                            Rp. 0
                          <?php endif; ?>
                        </td>
                      </tr>
                      <?php $i++; ?>
                    <?php } ?>
                    <!-- <tr>
                      <td colspan="6">
                        <b>Total</b>
                      </td>
                      <td>
                        <b>
                            <?php if ($totalBeli > 0) : ?>
                              Rp. <?= number_format($totalBeli, 0, ',', '.'); ?>
                            <?php else : ?>
                              Rp. 0
                            <?php endif; ?>      
                        </b>
                      </td>
                      <td>
                        <b>Rp. <?php echo number_format($totalJual, 0, ',', '.'); ?></b>
                      </td>
                  </tr> -->
                  </tbody>
                </table>
              </div>
              <br>
              <h4>Total Keseluruhan Harga Beli: Rp. <?php echo number_format($totalBeli, 0, ',', '.'); ?></h4>
            </div>
            <!-- /.card-body -->
          </div>
        <?php else : ?>
          <div class="card">
            <div class="card-header">
              <?php
              $namaKategori = mysqli_query($conn, "SELECT kategori_nama FROM kategori WHERE kategori_id = $kategoriId ");
              $namaKategori = mysqli_fetch_array($namaKategori);
              $namaKategori = $namaKategori['kategori_nama'];
              ?>
              <h3 class="card-title">Data barang Keseluruhan <b>Kategori <?= $namaKategori; ?></b></h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <div class="table-auto">
                <table id="laporan-stok-terkecil" class="table table-bordered table-striped table-laporan">
                  <thead>
                    <tr>
                      <th style="width: 6%;">No.</th>
                      <th style="width: 13%;">Kode Barang</th>
                      <th>Nama</th>
                      <th>Kategori</th>
                      <th>Stock</th>
                      <th>Satuan</th>
                      <th>Total Asset</th>
                      <th>HPP</th>
                      <th>Total HPP</th>
                    </tr>
                  </thead>
                  <tbody>

                    <?php
                    $i = 1;
                    $totalBeli = 0;
                    $totalJual = 0;
                    $queryProduct = $conn->query("SELECT barang.barang_id, barang.barang_kode, barang.barang_nama, barang.barang_harga_beli, barang.barang_harga, barang.hpp, barang.barang_stock, barang.barang_kategori_id, barang.barang_cabang, kategori.kategori_id, kategori.kategori_nama, satuan.satuan_id, satuan.satuan_nama
                               FROM barang 
                               JOIN kategori ON barang.kategori_id = kategori.kategori_id
                               JOIN satuan ON barang.satuan_id = satuan.satuan_id
                               WHERE barang_kategori_id = $kategoriId && barang_cabang = '" . $sessionCabang . "' ORDER BY CAST(barang_stock AS UNSIGNED) ASC
                               ");
                    while ($rowProduct = mysqli_fetch_array($queryProduct)) {
                      $totalBeli += $rowProduct['barang_harga_beli'] * $rowProduct['barang_stock'];
                      $totalJual += $rowProduct['barang_harga'] * $rowProduct['barang_stock'];
                    ?>
                      <tr>
                        <td><?= $i; ?></td>
                        <td><?= $rowProduct['barang_kode']; ?></td>
                        <td><?= $rowProduct['barang_nama']; ?></td>
                        <td><?= $rowProduct['kategori_nama']; ?></td>
                        <td><?= (empty($rowProduct['sub_kategori_kode']) ? '' : ('[ ' . $rowProduct['sub_kategori_kode']) . ' ] ') . $rowProduct['barang_nama'] ?></td>
                        <td>
                          <b><?= $rowProduct['barang_stock']; ?></b>
                        </td>
                        <td><?= $rowProduct['satuan_nama']; ?></td>
                        <td>
                          <?php if ($rowProduct['barang_harga_beli'] > 0) : ?>
                            Rp. <?= number_format($rowProduct['barang_harga_beli'], 0, ',', '.'); ?>
                          <?php else : ?>
                            Rp. 0
                          <?php endif; ?>
                        </td>
                        <td>
                          <?php if ($rowProduct['hpp'] > 0) : ?>
                            Rp. <?= number_format($rowProduct['hpp'], 0, ',', '.'); ?>
                          <?php else : ?>
                            Rp. 0
                          <?php endif; ?>
                        </td>
                        <td>
                          <?php if ($rowProduct['hpp'] > 0) : ?>
                            <?php $totalHargaBeli = $rowProduct['hpp'] * $rowProduct['barang_stock']; ?>
                            Rp. <?= number_format($totalHargaBeli, 0, ',', '.'); ?>
                          <?php else : ?>
                            Rp. 0
                          <?php endif; ?>
                        </td>
                      </tr>
                      <?php $i++; ?>
                    <?php } ?>
                    <!-- <tr>
                      <td colspan="6">
                        <b>Total</b>
                      </td>
                      <td>
                        <b>
                            <?php if ($totalBeli > 0) : ?>
                              Rp. <?= number_format($totalBeli, 0, ',', '.'); ?>
                            <?php else : ?>
                              Rp. 0
                            <?php endif; ?>      
                        </b>
                      </td>
                      <td>
                        <b>Rp. <?php echo number_format($totalJual, 0, ',', '.'); ?></b>
                      </td>
                  </tr> -->
                  </tbody>
                </table>
              </div>
              <br>
              <h4>Total Keseluruhan Harga Beli: Rp. <?php echo number_format($totalBeli, 0, ',', '.'); ?></h4>
            </div>
            <!-- /.card-body -->
          </div>
        <?php endif; ?>

      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
  </section>
  <!-- /.content -->
</div>
</div>



<?php include '_footer.php'; ?>

<!-- DataTables -->
<script src="plugins/datatables/jquery.dataTables.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.js"></script>
<script>
  $(function() {
    $("#laporan-stok-terkecil").DataTable();
  });
</script>
</body>

</html>