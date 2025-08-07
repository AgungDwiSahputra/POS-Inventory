<?php 
  include '_header.php';
  include '_nav.php';
  include '_sidebar.php'; 
?>

<?php  
  $userId = $_SESSION['user_id'];
  $tipeHarga = 0;
  $invoiceDraft = base64_decode($_GET['invoice']);

  $invoiceDataDraft = query("SELECT * FROM invoice WHERE penjualan_invoice = $invoiceDraft && invoice_cabang = $sessionCabang")[0];
  if ( $invoiceDataDraft == null ) {
    echo "
      <script>
        document.location.href = 'bo';
      </script>
    ";
  }

  if ( $levelLogin === "kurir") {
    echo "
      <script>
        document.location.href = 'bo';
      </script>
    ";
  }  


if ( $dataTokoLogin['toko_status'] < 1 ) {
  echo "
      <script>
        alert('Status Toko Tidak Aktif Jadi Anda Tidak Bisa melakukan Transaksi !!');
        document.location.href = 'bo';
      </script>
    ";
}



// Insert Ke keranjang Scan Barcode
if( isset($_POST["inputbarcodeDraft"]) ){
  // var_dump($_POST);

  // cek apakah data berhasil di tambahkan atau tidak
  if( tambahKeranjangBarcodeDraft($_POST) > 0 ) {
    echo "
      <script>
        document.location.href = '';
      </script>
    ";
  }  
}

if( isset($_POST["inputbarcodeNonFisik"]) ){
  // var_dump($_POST);

  // cek apakah data berhasil di tambahkan atau tidak
  if( tambahKeranjangBarcodeNonFisikDraft($_POST) > 0 ) {
    echo "
      <script>
        document.location.href = '';
      </script>
    ";
  }  
}
?>



<?php 
// Insert Ke keranjang
if( isset($_POST["updateStock"]) ){
  $inv = $_POST["penjualan_invoice2"];

  if( updateStockSaveNonFisikDraft($_POST) > 0 ) {
    echo "
      <script>
        document.location.href = 'invoice?no=".$inv."&page=nonfisik';
      </script>
    ";
  } else {
    echo "
      <script>
        document.location.href = 'invoice?no=".$inv."&page=nonfisik';
      </script>
    ";
  } 
}
?>




<?php
  // Update Data Produk SN dan Non SN 
  if ( isset($_POST["updateSn"]) ) {
    if( updateSnDrfat($_POST) > 0 ) {
      echo "
        <script>
          document.location.href = '';
        </script>
      ";
    } else {
      echo "
        <script>
          alert('Data Gagal edit');
        </script>
      ";
    }
  }

  // Update Data Harga Produk di Keranjang
  if ( isset($_POST["updateHarga"]) ) {
    if( updateQTYHargaDraft($_POST) > 0 ) {
      echo "
        <script>
          document.location.href = '';
        </script>
      ";
    } else {
      echo "
        <script>
          alert('Data Gagal edit');
        </script>
      ";
    }
  }

  if ( isset($_POST["updateHargaJasa"]) ) {
    if( updateQTYHargaNonFisikDraft($_POST) > 0 ) {
      echo "
        <script>
          document.location.href = '';
        </script>
      ";
    } else {
      echo "
        <script>
          alert('Data Gagal edit');
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
          <div class="col-sm-8">
            <h1>Transaksi Kasir Produk Non Fisik</h1>
            <div class="btn-cash-piutang">
              <?php  
                // Ambil data dari URL Untuk memberikan kondisi transaksi Cash atau Piutang
                if (empty(abs((int)base64_decode($_GET['r'])))) {
                    $r = 0;
                } else {
                    $r = abs((int)base64_decode($_GET['r']));
                }
              ?>

              <?php if ( $r == 1 ) : ?>
              <a href="beli-langsung?customer=<?= $_GET['customer']; ?>" class="btn btn-default">Cash</a>
              <a href="beli-langsung?customer=<?= $_GET['customer']; ?>&r=MQ==" class="btn btn-primary">Piutang</a>
              <?php else : ?>
              <a href="beli-langsung?customer=<?= $_GET['customer']; ?>" class="btn btn-primary">Cash</a>
              <a href="beli-langsung?customer=<?= $_GET['customer']; ?>&r=MQ==" class="btn btn-default">Piutang</a>
              <?php endif; ?>
              <a class="btn btn-danger" data-toggle="modal" href='#modal-id-draft' data-backdrop="static">Pending</a>
              <div class="modal fade" id="modal-id-draft">
                <div class="modal-dialog modal-lg">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h4 class="modal-title">Data Transaksi Pending</h4>
                      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    </div>
                    <div class="modal-body">
                      <?php  
                          $draft = query("SELECT * FROM invoice WHERE invoice_draft = 1 && invoice_tipe_non_fisik = 1 && invoice_kasir = $userId && invoice_cabang = $sessionCabang ORDER BY invoice_id DESC");
                      ?>
                      <div class="table-auto">
                        <table id="example7" class="table table-bordered table-striped">
                          <thead>
                          <tr>
                            <th style="width: 5px;">No.</th>
                            <th>Invoice</th>
                            <th style="width: 40% !important;">Tanggal</th>
                            <th>Customer</th>
                            <th class="text-center" style="width: 5%;">Aksi</th>
                          </tr>
                          </thead>
                          <tbody>

                          <?php $i = 1; ?>
                          <?php foreach ( $draft as $row ) : ?>
                          <tr>
                              <td><?= $i; ?></td>
                              <td><?= $row['penjualan_invoice']; ?></td>
                              <td><?= $row['invoice_tgl']; ?></td>
                              <td>
                                  <?php 
                                    $customer_id_draft = $row['invoice_customer']; 
                                    $namaCustomerDraft = mysqli_query($conn, "SELECT customer_nama FROM customer WHERE customer_id = $customer_id_draft");
                                    $namaCustomerDraft = mysqli_fetch_array($namaCustomerDraft);
                                    $customer_nama_draft = $namaCustomerDraft['customer_nama'];

                                    if ( $customer_id_draft < 1 ) {
                                      echo "Customer Umum";
                                    } else {
                                      echo $customer_nama_draft;
                                    }
                                  ?> 
                              </td>
                              <td class="orderan-online-button">
                                <a href="beli-langsung-non-fisik-draft?customer=<?= base64_encode($row['invoice_customer_category']); ?>&r=<?= base64_encode($row['invoice_piutang']); ?>&invoice=<?= base64_encode($row['penjualan_invoice']); ?>" title="Edit Data">
                                      <button class="btn btn-primary" type="submit">
                                         <i class="fa fa-edit"></i>
                                      </button>
                                  </a>
                                  <a href="beli-langsung-draft-delete?invoice=<?= $row['penjualan_invoice']; ?>&customer=<?= $_GET['customer']; ?>&cabang=<?= $sessionCabang; ?>&page=<?= base64_encode("nonfisik"); ?>" onclick="return confirm('Yakin dihapus ?')" title="Delete Data">
                                      <button class="btn btn-danger" type="submit">
                                         <i class="fa fa-trash"></i>
                                      </button>
                                  </a>
                              </td>
                          </tr>
                          <?php $i++; ?>
                        <?php endforeach; ?>
                        </tbody>
                        </table>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-sm-4">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="bo">Home</a></li>
              <li class="breadcrumb-item active">Barang</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>


    <section class="content">
    <?php  
      $userId = $_SESSION['user_id'];

      $keranjangNonFisik = query("SELECT * FROM keranjang_non_fisik_draft WHERE knfd_invoice = $invoiceDraft && knfd_cabang = $sessionCabang ORDER BY knfd_id ASC");
    ?>
        <div class="col-lg-12">
          <div class="card">
            <div class="card-header">
              <div class="row">
                <div class="col-md-8 col-lg-8">
                    <div class="card-invoice">
                      <span>No. Invoice: </span>
                      <?php  
                        $di = $invoiceDraft;
                      ?>
                      <input type="" name="" value="<?= $di; ?>">
                    </div>
                </div>
                <div class="col-md-4 col-lg-4">
                  <?php  
                    // Count Nama Tarik Tunai
                    $countTarikTunai = mysqli_query($conn, "SELECT * FROM keranjang_non_fisik_draft WHERE knfd_id_kasir = $userId && knfd_cabang = $sessionCabang && knfd_barang_nama LIKE '%tarik tunai%' ");
                    $countTarikTunai = mysqli_num_rows($countTarikTunai);
                  ?>
                  <?php if ( $countTarikTunai < 1 ) { ?>
                    <div class="cari-barang-parent">
                        <div class="row">
                          <div class="col-10">
                              <form action="" method="post">
                                  <input type="hidden" name="knf_id_kasir" value="<?= $userId; ?>">
                                  <input type="hidden" name="knf_cabang" value="<?= $sessionCabang; ?>">
                                  <input type="hidden" name="knfd_invoice" value="<?= $di; ?>">
                                  <input type="text" class="form-control" autofocus="" name="inputbarcodeNonFisik" placeholder="Barcode / Kode Produk Non Fisik" required="">
                              </form>
                          </div>
                          <div class="col-2">
                              <a class="btn btn-primary" title="Cari Produk" data-toggle="modal" id="cari-barang" href='#modal-id-jasa'>
                                 <i class="fa fa-search"></i>
                              </a>
                          </div>
                        </div>
                    </div>
                  <?php } ?>
                </div>
                </div>
              </div>

            <!-- /.card-header -->
            <div class="card-body">
              <div class="table-auto">
                <!-- Tabel Keranjang Jasa -->
                <table id="" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th style="width: 6%;">No.</th>
                    <th>Nama</th>
                    <th>Provider</th>
                    <th>Harga Beli</th>
                    <th>Harga Jual</th>
                    <th style="text-align: center;">QTY</th>
                    <th style="width: 20%;">Sub Total</th>
                    <th style="text-align: center; width: 10%;">Aksi</th>
                  </tr>
                  </thead>
                  <tbody>
                  <?php 
                    $i          = 1; 
                    $totalJasa  = 0;
                    $totalBeliNonFisik = 0;
                  ?>
                  <?php 
                    foreach($keranjangNonFisik as $row) : 
                    $sub_totalJasa              = $row['knfd_harga_jual'] * $row['knfd_qty'];
                    $sub_totalBeliNonFisik      = $row['knfd_harga_beli'] * $row['knfd_qty'];
          
                    if ( $row['knfd_id_kasir'] === $_SESSION['user_id'] ) {
                    $totalJasa += $sub_totalJasa;
                    $totalBeliNonFisik += $sub_totalBeliNonFisik;
                  ?>
                  <tr>
                      <td><?= $i; ?></td>
                      <td><?= $row['knfd_nama'] ?></td>
                      <td>
                        <?php  
                          $knf_provider = $row['knf_provider'];
                          if ( $knf_provider < 1 ) {
                            echo "<span style='color: red;'>Belum Memilih Provider</span>";
                          } else {
                            $namaProvider = mysqli_query($conn, "SELECT provider_nama FROM provider WHERE provider_id = $knf_provider");
                            $namaProvider = mysqli_fetch_array($namaProvider);
                            $provider_nama = $namaProvider['provider_nama'];
                            echo $provider_nama;
                          }
                        ?>
                      </td>
                      <td>Rp. <?= number_format($row['knfd_harga_beli'], 0, ',', '.'); ?></td>
                      <td>Rp. <?= number_format($row['knfd_harga_jual'], 0, ',', '.'); ?></td>
                      <td style="text-align: center;"><?= $row['knfd_qty']; ?></td>
                      <td>Rp. <?= number_format($sub_totalJasa, 0, ',', '.'); ?></td>
                      <td class="orderan-online-button">
                          <a href="#!" title="Edit Data">
                            <button class="btn btn-primary" name="" class="keranjang-pembelian" id="keranjang-harga-non-fisik" data-id="<?= $row['knfd_id']; ?>">
                                <i class="fa fa-pencil"></i>
                            </button> 
                          </a>
                          <a href="beli-langsung-non-fisik-delete-draft.php?id=<?= $row['knfd_id']; ?>&customer=<?= $_GET['customer']; ?>&r=<?= $r; ?>&invoice=<?= $_GET['invoice']; ?>" title="Delete Data" onclick="return confirm('Yakin dihapus ?')">
                              <button class="btn btn-danger" type="submit" name="hapus">
                                  <i class="fa fa-trash-o"></i>
                              </button>
                          </a>
                      </td>
                  </tr>
                  <?php $i++; ?>
                  <?php } ?>
                  <?php endforeach; ?>
                </table>
                <!-- End Tabel Keranjang Jasa -->
              </div>
       
              <div class="btn-transaksi">
                <form role="form" action="" method="POST">
                  <div class="row">
                    <div class="col-md-6 col-lg-7">
                        <div class="filter-customer">
                          <div class="form-group">
                            <label>Customer</label>
                            <?php  
                              $idCustomerDraft = $invoiceDataDraft['invoice_customer'];
                              $namaCustomer = mysqli_query($conn, "SELECT customer_nama FROM customer WHERE customer_id = $idCustomerDraft ");
                              $namaCustomer = mysqli_fetch_array($namaCustomer);
                              $invoice_customer = $namaCustomer['customer_nama'];
                            ?>
                            <select class="form-control select2bs4 pilihan-marketplace" required="" name="invoice_customer">
                              <option selected="selected" value="<?= $idCustomerDraft; ?>"><?= $invoice_customer; ?></option>

                            <?php if (  $idCustomerDraft > 0 ) { ?> 
                              <?php if ( $r != 1 && $tipeHarga < 1 ) { ?>
                              <option value="0">Umum</option>
                              <?php } ?>
                            <?php } ?>

                              <?php  
                                $customer = query("SELECT * FROM customer WHERE customer_cabang = $sessionCabang && customer_status = 1 && customer_category = $tipeHarga ORDER BY customer_id DESC ");
                              ?>
                              <?php foreach ( $customer as $ctr ) : ?>
                                <?php if ( $ctr['customer_id'] > 1 && $ctr['customer_nama'] !== "Customer Umum" ) { ?>
                                <?php if ( $ctr['customer_id'] != $idCustomerDraft ) { ?>
                                <option value="<?= $ctr['customer_id'] ?>"><?= $ctr['customer_nama'] ?></option>
                                <?php } ?>
                                <?php } ?>
                              <?php endforeach; ?>
                            </select>
                            <small>
                              <a href="customer-add">Tambah Customer <i class="fa fa-plus"></i></a>
                            </small>
                        </div>

                        <!-- View Jika Select Dari Marketplace -->
                        <span id="beli-langsung-marketplace"></span>

                        <div class="form-group">
                            <label>Tipe Pembayaran</label>
                            <select class="form-control" required="" name="invoice_tipe_transaksi">
                              <option selected="selected" value="0">Cash</option>
                              <option value="1">Transfer</option>
                            </select>
                        </div>

                        <!-- kondisi jika memilih piutang -->
                        <?php if ( $r == 1 ) : ?>
                        <div class="form-group">
                            <label style="color: red;">Jatuh Tempo</label>
                            <input type="date" name="invoice_piutang_jatuh_tempo" class="form-control" required="" value="<?= date("Y-m-d"); ?>">
                        </div>
                       <?php else : ?>
                          <input type="hidden" name="invoice_piutang_jatuh_tempo" value="0">
                       <?php endif; ?>

                      </div>
                    </div>
                    <div class="col-md-6 col-lg-5">
                      <div class="invoice-table">
                        <table class="table">
                          <tr>
                              <td style="width: 110px;"><b>Total</b></td>
                              <td class="table-nominal">
                                 <?php $total = $totalJasa; ?>
                                 <span>Rp. </span>
                                 <span>
                                    <input type="text" name="invoice_total" id="angka2" class="a2"  value="<?= $total; ?>" onkeyup="return isNumberKey(event)" size="10" readonly>
                                 </span>
                                 
                              </td>
                          </tr>

                        <!-- Ongkir Statis untuk Inputan -->
                          <tr class="ongkir-statis">
                              <td>Diskon</td>
                              <td class="table-nominal tn">
                                 <span>Rp.</span> 
                                 <span>
                                   <input type="number" name="invoice_diskon" id="" class="f21 ongkir-statis-diskon" value="0" required="" autocomplete="off" onkeyup="hitung5();" onkeyup="return isNumberKey(event)" onkeypress="return hanyaAngka1(event)" size="10">
                                 </span>
                              </td>
                          </tr>
                          <tr class="ongkir-statis">
                              <td><b>Sub Total</b></td>
                              <td class="table-nominal">
                                 <span>Rp. </span>
                                 <span>
                                    <?php  
                                      $subTotal = $total;
                                    ?>
                                    <input type="hidden" name=""  class="g21"  value="<?= $subTotal; ?>" readonly>
                                    <input type="text" name="invoice_sub_total"  class="c21"  value="<?= $subTotal; ?>" readonly>
                                 </span>
                                 
                              </td>
                          </tr>
                          <tr class="ongkir-statis">
                              <td>
                                  <b style="color: red;">
                                      <?php  
                                        // kondisi jika memilih piutang
                                        if ( $r == 1 ) {
                                          echo "DP";
                                        } else {
                                          echo "Bayar";
                                        }
                                      ?>      
                                  </b>
                              </td>
                              <td class="table-nominal tn">
                                 <span>Rp.</span> 
                                 <span>
                                   <input type="number" name="angka1" id="angka1" class="d21 ongkir-statis-bayar" autocomplete="off" onkeyup="hitung4();"  onkeyup="return isNumberKey(event)" onkeypress="return hanyaAngka1(event)" size="10">
                                 </span>
                              </td>
                          </tr>
                          <tr class="ongkir-statis">
                              <td>
                                  <?php  
                                    // kondisi jika memilih piutang
                                    if ( $r == 1 ) {
                                        echo "Sisa Piutang";
                                    } else {
                                        echo "Kembali";
                                    }
                                  ?>  
                              </td>
                              <td class="table-nominal">
                                <span>Rp.</span>
                                 <span>
                                  <input type="text" name="hasil" id="hasil" class="e21" readonly size="10" disabled>
                                </span>
                              </td>
                          </tr>
                        <!-- End Ongkir Statis untuk Inputan -->

                          
                          <tr>
                              <td></td>
                              <td>
                                <?php  foreach ($keranjangNonFisik as $stkj) : ?>
                                <?php if ( $stkj['knfd_id_kasir'] === $userId ) { ?>
                                  <input type="hidden" name="knfd_nama[]" value="<?= $stkj['knfd_nama']; ?>">
                                  <input type="hidden" name="knfd_barang_kode[]" value="<?= $stkj['knfd_barang_kode']; ?>">
                                  <input type="hidden" name="knfd_barang_id[]" value="<?= $stkj['knfd_barang_id']; ?>">
                                  <?php  
                                    $bnf_id = $stkj['knfd_barang_id'];
                                    $namaProdukNonFisik = mysqli_query($conn, "SELECT bnf_nama FROM barang_non_fisik WHERE bnf_id = $bnf_id ");
                                    $namaProdukNonFisik = mysqli_fetch_array($namaProdukNonFisik);
                                    $bnf_nama           = $namaProdukNonFisik['bnf_nama'];
                                  ?>
                                  <input type="hidden" name="pbnf_barang_nama[]" value="<?= strtolower($bnf_nama); ?>">
                                  
                                  <input type="hidden" name="knf_provider[]" value="<?= $stkj['knf_provider']; ?>">
                                  <?php  
                                    $knf_provider  = $stkj['knf_provider'];
                                    $providerSaldo = mysqli_query($conn, "SELECT provider_saldo FROM provider WHERE provider_id = $knf_provider");
                                    $providerSaldo = mysqli_fetch_array($providerSaldo);
                                    $provider_saldo = $providerSaldo['provider_saldo'];


                                    $queryTotal = "SELECT SUM(knfd_harga_beli * knfd_qty) AS total_harga_beli FROM keranjang_non_fisik_draft WHERE knf_provider = $knf_provider";

                                    $resultTotal = mysqli_query($conn, $queryTotal);
                                    $totalHargaBeli = mysqli_fetch_array($resultTotal)['total_harga_beli'];

                                    $countBnfNama = strtolower($bnf_nama);
                                    // Kondisi jika ada produk tarik tunai
                                    if (strpos(strtolower($countBnfNama), 'tarik tunai') !== false) {
                                      $provider_saldo += $totalHargaBeli;
                                    } else {
                                      $provider_saldo -= $totalHargaBeli;
                                    }
                                  ?>
                                  <input type="hidden" name="pbnf_provider_sisa_saldo[]" value="<?= $provider_saldo; ?>">

                                  <input type="hidden" name="knfd_harga_beli[]" value="<?= $stkj['knfd_harga_beli']; ?>">
                                  <input type="hidden" name="knfd_harga_jual[]" value="<?= $stkj['knfd_harga_jual']; ?>">
                                  <input type="hidden" min="1" name="knfd_qty[]" value="<?= $stkj['knfd_qty']; ?>"> 
                                 <input type="hidden" name="knfd_catatan[]" value="<?= $stkj['knfd_catatan']; ?>"> 
                                  <input type="hidden" name="knfd_id_kasir[]" value="<?= $stkj['knfd_id_kasir']; ?>">
                                  <input type="hidden" name="knfd_id_cek[]" value="<?= $stkj['knfd_id_cek']; ?>">
                                <?php } ?>
                                <?php endforeach; ?> 

                                <input type="hidden" name="penjualan_invoice2" value="<?= $di; ?>">
                                <input type="hidden" name="invoice_customer_category" value="<?= $tipeHarga; ?>">
                                <input type="hidden" name="kik" value="<?= $userId; ?>">
                                <input type="hidden" name="penjualan_invoice_count" value="<?= $jmlPenjualan1; ?>">
                                <input type="hidden" name="invoice_piutang" value="<?= $r; ?>">
                                <input type="hidden" name="invoice_piutang_lunas" value="0">
                                <input type="hidden" name="invoice_cabang" value="<?= $sessionCabang; ?>">
                                <input type="hidden" name="invoice_id" value="<?= $invoiceDataDraft['invoice_id']; ?>">

                                <input type="hidden" name="invoice_total_beli_non_fisik" value="<?= $totalBeliNonFisik; ?>">
                                <input type="hidden" name="invoice_tipe_tarik_tunai" value="<?= $countTarikTunai; ?>">
                                <input type="hidden" name="invoice_kurir" value="0">

                                <!-- Menghitung transaksi non fisi -->
                                <?php  
                                  $countNonFisik = mysqli_query($conn, "SELECT * FROM keranjang_non_fisik_draft WHERE knfd_invoice = $di && knfd_cabang = $sessionCabang");
                                  $countNonFisik = mysqli_num_rows($countNonFisik);
                                ?>
                                <input type="hidden" name="invoice_count_non_fisik" value="<?= $countNonFisik; ?>">
                              </td>
                          </tr>
                        </table>
                      </div>
                      <div class="payment">
                          <?php  
                              $idKasirKeranjang = $_SESSION['user_id'];
                              $countProvider = "SELECT SUM(knf_provider) AS total_provider FROM keranjang_non_fisik_draft WHERE knfd_id_kasir = $idKasirKeranjang && knfd_cabang = $sessionCabang";

                              $result = mysqli_query($conn, $countProvider);
                              if( $result ) {
                                $row = mysqli_fetch_assoc($result);
                                $jmlDataSn  = $row['total_provider'];
                              }
                          ?>
                         <?php if ( $jmlDataSn > 0 ) : ?>
                              <a href="beli-langsung-non-fisik?customer=<?= $_GET['customer']; ?>&r=<?= base64_encode($r); ?>" class="btn btn-danger">Transaksi Pending <i class="fa fa-file-o"></i>
                              </a>

                              <button class="btn btn-primary" type="submit" name="updateStock">Simpan Payment <i class="fa fa-shopping-cart"></i></button>

                          <?php else : ?>

                              <a href="beli-langsung-non-fisik?customer=<?= $_GET['customer']; ?>&r=<?= base64_encode($r); ?>" class="btn btn-danger">Transaksi Pending <i class="fa fa-file-o"></i>
                              </a>

                              <a href="#!" class="btn btn-default jmlDataSn" type="" name="">Simpan Payment <i class="fa fa-shopping-cart"></i></a>
                          <?php endif; ?>
                        </div>
                    </div>
                  </div>
               </form>
              </div>
            </div>
            <!-- /.card-body -->
          </div>
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->
  </div>
</div>


    <div class="modal fade" id="modal-id-jasa" data-backdrop="static">
        <div class="modal-dialog modal-lg-pop-up">
          <div class="modal-content">
            <div class="modal-body">
                  <div class="card">
                <div class="card-header">
                  <h3 class="card-title">Data Keseluruhan</h3>
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                  <div class="table-auto">
                    <table id="example2" class="table table-bordered table-striped" style="width: 100%;">
                      <thead>
                      <tr>
                        <th style="width: 5%;">No.</th>
                        <th>Kode Barang</th>
                        <th>Nama</th>
                        <th style="text-align: center;">Aksi</th>
                      </tr>
                      </thead>
                      <tbody>

                      </tbody>
                  </table>
                </div>
              </div>
                <!-- /.card-body -->
              </div>    
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
    </div>
  
  <!-- Modal Update SN -->
  <div class="modal fade" id="modal-id-1">
    <div class="modal-dialog">
      <div class="modal-content">

        <form role="form" id="form-edit-no-sn" method="POST" action="">
          <div class="modal-header">
            <h4 class="modal-title">No. SN Produk</h4>
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          </div>
          <div class="modal-body" id="data-keranjang-no-sn">
            
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-dismiss="modal">Tutup</button>
            <button type="submit" class="btn btn-primary" name="updateSn" >Edit Data</button>
          </div>
        </form>

      </div>
    </div>
  </div>

  <!-- Modal Update QTY Penjualan -->
  <div class="modal fade" id="modal-id-2">
    <div class="modal-dialog">
      <div class="modal-content">

        <form role="form" id="form-edit-harga" method="POST" action="">
          <div class="modal-header">
            <h4 class="modal-title">Edit Produk</h4>
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          </div>
          <div class="modal-body" id="data-keranjang-harga">
            
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-dismiss="modal">Tutup</button>
            <button type="submit" class="btn btn-primary" name="updateHarga" >Edit Data</button>
          </div>
        </form>

      </div>
    </div>
  </div>

  <!-- Modal Update Harga Keranjang-jasa -->
  <div class="modal fade" id="modal-id-2-jasa">
    <div class="modal-dialog">
      <div class="modal-content">

        <form role="form" id="form-edit-harga" method="POST" action="">
          <div class="modal-header">
            <h4 class="modal-title">Edit Produk Non Fisik</h4>
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          </div>
          <div class="modal-body" id="data-keranjang-harga-non-fisik">
            
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-dismiss="modal">Tutup</button>
            <button type="submit" class="btn btn-primary" name="updateHargaJasa" >Edit Data</button>
          </div>
        </form>

      </div>
    </div>
  </div>


  <script>
    $(document).ready(function(){
        var table = $('#example2').DataTable( { 
             "processing": true,
             "serverSide": true,
             "ajax": "beli-langsung-search-data-non-fisik.php?cabang=<?= $sessionCabang; ?>",
             "columnDefs": 
             [
              {
                "targets": -1,
                  "data": null,
                  "defaultContent": 
                  `<center>

                      <button class='btn btn-primary tblInsert' title="Tambah Keranjang">
                         <i class="fa fa-shopping-cart"></i> Pilih
                      </button>

                  </center>` 
              }
            ]
        });

        table.on('draw.dt', function () {
            var info = table.page.info();
            table.column(0, { search: 'applied', order: 'applied', page: 'applied' }).nodes().each(function (cell, i) {
                cell.innerHTML = i + 1 + info.start;
            });
        });

        $('#example2 tbody').on( 'click', '.tblInsert', function () {
            var data = table.row( $(this).parents('tr')).data();
            var data0 = data[0];
            var data0 = btoa(data0);
            window.location.href = "beli-langsung-add-non-fisik-draft.php?id="+ data0 + "&customer=<?= $_GET['customer']; ?>&r=<?= $r; ?>&invoice=<?= $_GET['invoice']; ?>";
        });

    });
  </script>
<?php include '_footer.php'; ?>

<!-- DataTables -->
<script src="plugins/datatables/jquery.dataTables.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.js"></script>
<script>
  $(function () {
    $("#example1").DataTable();
  });
  $(function () {
    $("#example7").DataTable();
  });
</script>
<script>
    function hanyaAngka(evt) {
      var charCode = (evt.which) ? evt.which : event.keyCode
       if (charCode > 31 && (charCode < 48 || charCode > 57))
 
        return false;
      return true;
    }
    function hanyaAngka1(evt) {
      var charCode = (evt.which) ? evt.which : event.keyCode
       if (charCode > 31 && (charCode < 48 || charCode > 57))
 
        return false;
      return true;
    }
</script>
 <script>
      // function hitung2() {
      // var a = $(".a2").val();
      // var b = $(".b2").val();
      // c = a - b;
      // $(".c2").val(c);
      // }

      function hitung2() {
          var txtFirstNumberValue = document.querySelector('.a2').value;
          var txtSecondNumberValue = document.querySelector('.b2').value;
          var result = parseInt(txtFirstNumberValue) + parseInt(txtSecondNumberValue);
          if (!isNaN(result)) {
             document.querySelector('.c2').value = result;
          }
      }
      function hitung3() {
        var a = $(".d2").val();
        var b = $(".c2").val();
        c = a - b;
        $(".e2").val(c);
      }
      function hitung7() {
        var a = $(".h22").val();
        var b = $(".g2").val();
        c = a - b;
        $(".e2").val(c);
      }

      // Diskon
      function hitung6() {
        document.querySelector(".g2parent").style.display = "block";
        document.querySelector(".c2parent").style.display = "none";
        document.querySelector(".h2parent").style.display = "block";
        document.querySelector(".d2parent").style.display = "none";
        var a = $(".c2").val();
        var b = $(".f2").val();
        c = a - b;
        $(".g2").val(c);
      }

      // =================================== Statis ================================== //
      // Sub Total - Bayar = kembalian
      function hitung4() {
        var a = $(".d21").val();
        var b = $(".c21").val();
        c = a - b;
        $(".e21").val(c);
      }

      // Diskon
      function hitung5() {
        var a = $(".g21").val();
        var b = $(".f21").val();
        c = a - b;
        $(".c21").val(c);
      }
      // =================================== End Statis ================================== //

      function isNumberKey(evt){
       var charCode = (evt.which) ? evt.which : event.keyCode;
       if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
       return false;
       return true;
      }
</script>
<script>
  $(function () {

    //Initialize Select2 Elements
    $('.select2bs4').select2({
      theme: 'bootstrap4'
    })
  });
</script>

<script>
  $(document).ready(function(){
      $(".pilihan-marketplace").change(function(){
          $(this).find("option:selected").each(function(){
              var optionValue = $(this).attr("value");
              if(optionValue){
                  $(".box1").not("." + optionValue).hide();
                  $("." + optionValue).show();
              } else{
                  $(".box1").hide();
              }
          });
      }).change();

      // Memanggil Pop Up Data Produk SN dan Non SN
      $(document).on('click','#keranjang_sn',function(e){
          e.preventDefault();
          $("#modal-id-1").modal('show');
          $.post('beli-langsung-sn-draft.php',
            {id:$(this).attr('data-id')},
            function(html){
              $("#data-keranjang-no-sn").html(html);
            }   
          );
        });


      // Memanggil Pop Up Data Edit Harga
      $(document).on('click','#keranjang-harga',function(e){
          e.preventDefault();
          $("#modal-id-2").modal('show');
          $.post('beli-langsung-edit-qty-draft.php?customer=<?= $tipeHarga; ?>&invoice=<?= $invoiceDraft; ?>',
            {id:$(this).attr('data-id')},
            function(html){
              $("#data-keranjang-harga").html(html);
            }   
          );
        });

      // Memanggil Pop Up Data Edit Harga
      $(document).on('click','#keranjang-harga-non-fisik',function(e){
          e.preventDefault();
          $("#modal-id-2-jasa").modal('show');
          $.post('beli-langsung-edit-harga-non-fisik-draft.php?customer=<?= $tipeHarga; ?>&invoice=<?= $invoiceDraft; ?>',
            {id:$(this).attr('data-id')},
            function(html){
              $("#data-keranjang-harga-non-fisik").html(html);
            }   
          );
        });

      $(".jmlDataSn").click(function(){
        alert("Anda Tidak Bisa Melanjutkan Transaksi Karena data Provider masih ada yang belum dipilih !!");
      });

      // View Hidden Ongkir
      $(".fa-ongkir-statis").click(function(){
          $(".ongkir-statis").addClass("none");
          $(".ongkir-statis-input").attr("name", "");
          $(".ongkir-dinamis-input").attr("name", "invoice_ongkir");

          $(".ongkir-statis-diskon").attr("name", "");
          $(".ongkir-dinamis-diskon").attr("name", "invoice_diskon");

          $(".ongkir-statis-bayar").attr("name", "");
          $(".ongkir-dinamis-bayar").attr("name", "angka1");

          // $(".ongkir-dinamis-bayar").attr("required", true);
          $(".ongkir-statis-bayar").removeAttr("required");
          $(".ongkir-statis-diskon").removeAttr("required");
          $(".ongkir-dinamis-diskon").attr("required", true);
          $(".ongkir-dinamis").removeClass("none");
      });

      $(".fa-ongkir-dinamis").click(function(){
          $(".ongkir-dinamis").addClass("none");
          $(".ongkir-dinamis-input").attr("name", "");
          $(".ongkir-statis-input").attr("name", "invoice_ongkir");

          $(".ongkir-dinamis-diskon").attr("name", "");
          $(".ongkir-statis-diskon").attr("name", "invoice_diskon");

          $(".ongkir-dinamis-bayar").attr("name", "");
          $(".ongkir-statis-bayar").attr("name", "angka1");

          // $(".ongkir-dinamis-bayar").removeAttr("required");
          $(".ongkir-dinamis-diskon").removeAttr("required");
          $(".ongkir-statis-diskon").attr("required", true);
          $(".ongkir-statis-bayar").attr("required", true);
          $(".ongkir-statis").removeClass("none");
      });
  });

  // load halaman di pilihan select jenis usaha
  $('#beli-langsung-marketplace').load('beli-langsung-marketplace.php');

</script>

</body>
</html>

<script>
  // Aksi Select Status
  function myFunction() {
    var x = document.getElementById("mySelect").value;
    if ( x === "1" ) {
      document.location.href = "beli-langsung?customer=<?= base64_encode(1); ?>";

    } else if ( x === "2" ) {
      document.location.href = "beli-langsung?customer=<?= base64_encode(2); ?>";

    } else {
      document.location.href = "beli-langsung?customer=<?= base64_encode(0); ?>";
    }
  }
</script>