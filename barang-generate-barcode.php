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


  $id = abs((int)base64_decode($_GET['id']));
  $barang = query("SELECT * FROM barang WHERE barang_id = $id")[0];

  // Proses Generate Barcode
  require "vendor/barcode/autoload.php";
  $generator = new Picqer\Barcode\BarcodeGeneratorPNG();
  file_put_contents('vendor/barcode/img/'.$barang["barang_kode"].'-produk-'.$barang["barang_nama"].'-cabang-'.$sessionCabang.'.png', $generator->getBarcode($barang["barang_kode"], $generator::TYPE_CODE_128, 3, 50));
    
?>


  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-8">
            <h1>Generate Barcode <b>Produk <?= $barang['barang_nama']; ?></b></h1>
          </div>
          <div class="col-sm-4">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="bo">Home</a></li>
              <li class="breadcrumb-item active">Barcode</li>
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
                <h3 class="card-title">Barcode</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-6 col-lg-6">
                        <div class="detail-barcode-box" id="detail-barcode-box">
                            <b class="title-barcode-box"><?= $barang["barang_nama"]; ?></b><br>
                            <img src="vendor/barcode/img/<?= $barang["barang_kode"]; ?>-produk-<?= $barang["barang_nama"]; ?>-cabang-<?= $sessionCabang; ?>.png" alt="Barcode Produk <?= $barang["barang_nama"]; ?>" class="img-fluid">
                            
                            <div class="row">
                              <div class="col-3">
                                <b>Rp</b>
                              </div>

                              <div class="col-9">
                                <b style="float: right;"><?= number_format($barang["barang_harga"], 0, ',', '.'); ?></b>
                              </div>
                            </div>
                         </div>

                          <div class="card-footer text-right">
                              <!-- <a href="vendor/barcode/img/<?= $barang["barang_kode"]; ?>-produk-<?= $barang["barang_nama"]; ?>-cabang-<?= $sessionCabang; ?>.png" class="btn btn-primary" download="<?= $barang["barang_nama"]; ?>-kode-barcode-<?= $barang["barang_kode"]; ?>-cabang-<?= $sessionCabang; ?>">Download &nbsp;<i class="fa fa-download"></i></a> -->
                             
                              <input id="btn_convert" class="btn btn-primary" type="button" value="Download" />
                          </div>
                          <br>
                    </div>

                    <div class="col-md-6 col-lg-6">
                      <form action="barang-generate-barcode-lots-new" method="post" target="_blank">
                          <div class="form-group">
                              <label for="barang_kode">Panjang Kertas (mm)</label>
                              <input type="number" name="panjang_kertas" class="form-control" id="barang_kode" placeholder="Contoh: 105" value="105" required>
                          </div>
                          <div class="row">
                              <div class="col-md-6 col-lg-6">
                                  <div class="form-group">
                                    <label for="barang_kode">Panjang Per Kotak (mm)</label>
                                    <input type="number" name="panjang_per_kotak" class="form-control" id="barang_kode" placeholder="Contoh: 33" value="33" required>
                                  </div>
                              </div>
                              <div class="col-md-6 col-lg-6">
                                <!-- value="15" -->
                                  <div class="form-group">
                                    <label for="barang_kode">Tinggi Per Kotak (mm)</label>
                                    <input type="number" name="tinggi_per_kotak" class="form-control" id="barang_kode" placeholder="Contoh: 10" value="10" required>
                                  </div>
                              </div>

                              <div class="col-md-6 col-lg-6">
                                <!-- value="3" -->
                                  <div class="form-group">
                                    <label for="barang_kode">Margin Atas (mm)</label>
                                    <input type="number" name="magin_atas" class="form-control" id="barang_kode" placeholder="Contoh: 2" value="2" required>
                                  </div>
                              </div>

                              <div class="col-md-6 col-lg-6">
                                <!-- value="3" -->
                                  <div class="form-group">
                                    <label for="barang_kode">Margin Bawah (mm)</label>
                                    <input type="number" name="magin_bawah" class="form-control" id="barang_kode" placeholder="Contoh: 2" value="2" required>
                                  </div>
                              </div>

                              <div class="col-md-6 col-lg-6">
                                  <div class="form-group">
                                    <label for="barang_kode">Margin Kanan Kiri (mm)</label>
                                    <input type="number" name="margin_kanan_kiri" class="form-control" id="barang_kode" placeholder="Contoh: 3" value="3" required>
                                  </div>
                              </div>
                          </div>
                          <div class="form-group">
                            <label for="barang_kode">Cetak Barcode Sesuai Jumlah Keinginan</label>
                            <input type="number" name="input_barcode" class="form-control" id="barang_kode" placeholder="Contoh: 26" required>
                            <input type="hidden" name="input_kode" value="<?= $barang['barang_kode']; ?>">
                          </div>
                          <div class="card-footer text-right">
                            <button type="submit" name="submit" class="btn btn-primary">Generate Barcode</button>
                          </div>
                          <br>
                      </form>
                    </div>
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
<script>
    document.getElementById("btn_convert").addEventListener("click", function() {
      html2canvas(document.getElementById("detail-barcode-box"), {
        allowTaint: true,
        useCORS: true
      }).then(function (canvas) {
        var anchorTag = document.createElement("a");
        document.body.appendChild(anchorTag);
        anchorTag.download = "<?= $barang["barang_nama"]; ?>-kode-barcode-<?= $barang["barang_kode"]; ?>-cabang-<?= $sessionCabang; ?>.jpg";
        anchorTag.href = canvas.toDataURL();
        anchorTag.target = '_blank';
        anchorTag.click();
      });
 });
</script>
