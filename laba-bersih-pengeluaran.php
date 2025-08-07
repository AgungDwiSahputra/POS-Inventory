<?php 
  error_reporting(0);
  include '_header.php';
  include '_nav.php';
  include '_sidebar.php'; 

   if (isset($_GET['date'])) {
      $bulan_Ini = $_GET['date'];
   } else {
      $bulan_Ini = date("Y-m");
   }

   $pecah_data       = explode("-",$bulan_Ini);
   $tahun_ini        = $pecah_data[0];
   $bln_ini          = $pecah_data[1];
?>
<?php  
  if ( $levelLogin === "driver") {
    echo "
      <script>
        document.location.href = 'bo';
      </script>
    ";
  }
    
?>
  <style>
    .title-bo span {
      display: inline-block;
    }
    .title-bo span:nth-child(1) {
      padding-right: 20px;
    }
    .title-bo .col-3 {
      margin-top: -2px;
      padding-left: 0px;
    }
  </style>

	<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-9">
            <div class="title-bo">
                <span>
                    <h1 class="m-0 text-dark">Data Pengeluaran</h1>
                </span>
                <span>
                  <form action="" method="GET" role="form">
                    <div class="row">
                        <div class="col-9">
                            <input type="month" class="form-control" name="date" id="bulan-tahun" value="<?= $bulan_Ini; ?>">
                        </div>
                        <div class="col-3">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-filter"></i></button>
                        </div>
                    </div>
                  </form>
                </span>
            </div>
          </div>
          <div class="col-sm-3">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="bo">Home</a></li>
              <li class="breadcrumb-item active">Pengeluaran</li>
            </ol>
          </div>
  
          <div class="tambah-data">
          	<a href="laba-bersih-pengeluaran-add" class="btn btn-primary">Tambah Data</a>
          </div>
        
        </div>
      </div><!-- /.container-fluid -->
    </section>


    <?php  
      $userIdLogin = $_SESSION['user_id'];
      if ( $levelLogin === "super admin" ) :
    	 $data = query("SELECT * FROM pengeluaran WHERE MONTH(pengeluaran_date)='".$bln_ini."' && YEAR(pengeluaran_date)='".$tahun_ini."' && pengeluaran_cabang = $sessionCabang ORDER BY pengeluaran_id DESC");
      else :
        $data = query("SELECT * FROM pengeluaran WHERE pengeluaran_create = $userIdLogin && MONTH(pengeluaran_date)='".$bln_ini."' && YEAR(pengeluaran_date)='".$tahun_ini."' && pengeluaran_cabang = $sessionCabang ORDER BY pengeluaran_id DESC");
      endif;
    ?>
    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-12">

          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Data Pengeluaran Keseluruhan</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <div class="table-auto">
                <table id="example1" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th style="width: 5%;">No.</th>
                    <th>Pengeluaran</th>
                    <th>Penerima</th>
                    <th>Tanggal</th>
                    <th>User Create</th>
                    <!-- <th>Metode</th> -->
                    <th>Total Dibayar</th>

                    <?php if ( $levelLogin === "super admin" ) { ?>
                    <th>Laba Bersih</th>
                    <?php } ?>
                    
                    <?php if ( $levelLogin !== "kasir" ) { ?>
                    <th style="width: 10%; text-align: center;">Aksi</th>
                    <?php } ?>
                  </tr>
                  </thead>
                  <tbody>

                  <?php $i = 1; ?>
                  <?php foreach ( $data as $row ) : ?>
                  <tr>
                    	<td><?= $i; ?></td>
                    	<td><?= $row['pengeluaran_name']; ?></td>
                      <td><?= $row['pengeluaran_penerima']; ?></td>
                      <td><?= tanggal_indo($row['pengeluaran_date']); ?></td>
                      <td>
                          <?php  
                            $pengeluaran_create = $row['pengeluaran_create'];
                            $userId = mysqli_query($conn, "SELECT user_nama FROM user WHERE user_id = $pengeluaran_create ");
                            $userId = mysqli_fetch_array($userId);
                            $userId = $userId['user_nama'];
                            echo $userId;
                          ?>      
                      </td>
                    <?php /*
                      <td>
                          <?php  
                            $pengeluaran_metode = $row['pengeluaran_metode'];
 
                            $cp_id  = $pengeluaran_metode;
                            if ( $cp_id == 1 ) {
                                $namaChannel = "Transfer";
                            } elseif ( $cp_id == 0 ) {
                                $namaChannel = "Cash";
                            } else {
                                $typePembayaran = query("SELECT * FROM payment WHERE payment_id = $cp_id && payment_cabang = $sessionCabang && payment_status > 0 ")[0];

                                $payment_type_id = $typePembayaran['payment_type_id'];
                                                  $payment_type_nama = mysqli_query($conn, "SELECT payment_type_nama FROM payment_type WHERE payment_type_id = $payment_type_id ");
                                                  $payment_type_nama = mysqli_fetch_array($payment_type_nama);
                                                  $payment_type_nama = $payment_type_nama['payment_type_nama'];

                                $namaChannel = $payment_type_nama." / ".$typePembayaran['payment_nama']." / ".$typePembayaran['payment_no'];
                            }
                            echo $namaChannel;
                        ?>  
                      </td>
                    */ ?>
                      <td>Rp. <?= number_format($row['pengeluaran_total_dibayar'], 0, ',', '.'); ?></td>
                      
                      <?php if ( $levelLogin === "super admin" ) { ?>
                      <td>
                        <?php  
                          $pengeluaran_lababersih = $row['pengeluaran_lababersih'];
                          if ( $pengeluaran_lababersih == 1 ) {
                            echo "<b>Tampil</b>";
                          } else {
                            echo "Tidak";
                          }
                        ?>
                      </td>
                      <?php } ?>

                      <?php if ( $levelLogin !== "kasir" ) { ?>
                      <td class="orderan-online-button">
                        <?php $id = base64_encode($row["pengeluaran_id"]); ?>
                          <a href="laba-bersih-pengeluaran-zoom?id=<?= $id; ?>" target="_blank" title="Lihat Data">
                              <button class="btn btn-success" type="submit">
                                 <i class="fa fa-search"></i>
                              </button>
                          </a>
                      	  <a href="laba-bersih-pengeluaran-edit?id=<?= $id; ?>" title="Edit Data">
                              <button class="btn btn-primary" type="submit">
                                 <i class="fa fa-edit"></i>
                              </button>
                          </a>

                         <?php if ( $levelLogin === "super admin" || $levelLogin === "admin" ) { ?> 
                          <a href="laba-bersih-pengeluaran-delete?id=<?= $id; ?>" onclick="return confirm('Yakin dihapus ?')" title="Delete Data">
                              <button class="btn btn-danger" type="submit" name="hapus">
                                  <i class="fa fa-trash-o"></i>
                              </button>
                          </a>
                        <?php } ?>
                      </td>
                      <?php } ?>
                  </tr>
                  <?php $i++; ?>
              	<?php endforeach; ?>
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
  </div>
</div>



<?php include '_footer.php'; ?>
<script>
  const input = document.getElementById("bulan-tahun");
  input.addEventListener("change", function() {
    const selectedValue = input.value;
    console.log("Bulan dan Tahun yang dipilih:", selectedValue);
    // Lakukan tindakan lain dengan nilai bulan dan tahun yang dipilih
  });
</script>