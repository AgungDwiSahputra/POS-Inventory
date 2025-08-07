<?php 
  include '_header-artibut.php';
  $id = $_POST['id'];
  $customer = $_GET['customer'];

  $keranjang = query("SELECT * FROM keranjang_non_fisik_draft WHERE knfd_id = $id")[0];
?>


	<input type="hidden" name="knfd_id" value="<?= $id; ?>">
  <div class="form-group ">
     <label for="knf_provider">Provider</label>
     <div class="">
      <?php  
        $knf_provider = $keranjang['knf_provider'];
      ?>  
      <?php if ( $knf_provider < 1 ) : ?>
         <?php 
            $data2 = query("SELECT * FROM provider WHERE provider_saldo > 0 && provider_status > 0 && provider_cabang = $sessionCabang ORDER BY provider_id DESC"); 
         ?>
           <select name="knf_provider" required="" class="form-control ">
            <?php  
              $countProvider = mysqli_query($conn, "SELECT * FROM provider WHERE provider_status > 0 && provider_cabang = $sessionCabang");
              $countProvider = mysqli_num_rows($countProvider);
            ?>
            <?php if ( $countProvider < 1 ) : ?>
              <option value="">Belum Ada Provider</option>
            <?php else : ?>
              <option value="">-- Pilih Provider --</option>
              <?php foreach ( $data2 as $row ) : ?>
                  <option value="<?= $row['provider_id']; ?>">
                     <?= $row['provider_nama']; ?>
                  </option>
              <?php endforeach; ?>
            <?php endif; ?>

        <?php else : ?>
          <?php 
            $data2 = query("SELECT * FROM provider WHERE provider_saldo > 0 && provider_status > 0 && provider_cabang = $sessionCabang && provider_id != $knf_provider ORDER BY provider_id DESC");

            $namaProvider = mysqli_query($conn, "SELECT provider_nama FROM provider WHERE provider_id = $knf_provider");
            $namaProvider = mysqli_fetch_array($namaProvider);
            $provider_nama = $namaProvider['provider_nama']; 
          ?>
           <select name="knf_provider" required="" class="form-control ">
              <option value="<?= $knf_provider; ?>"><?= $provider_nama; ?></option>
              <?php foreach ( $data2 as $row ) : ?>
                  <option value="<?= $row['provider_id']; ?>">
                     <?= $row['provider_nama']; ?>
                  </option>
              <?php endforeach; ?>
        <?php endif; ?>
        </select>
      </div>
  </div>

	<div class="form-group">
        <label for="knf_harga_beli">Harga Beli</label>
        <input type="number" name="knfd_harga_beli" class="form-control" id="knfd_harga_beli" value="<?= $keranjang['knfd_harga_beli']; ?>" required>
    </div>

    <div class="form-group">
        <label for="knfd_harga_jual">Harga Jual</label>
        <input type="number" name="knfd_harga_jual" class="form-control" id="knfd_harga_jual" value="<?= $keranjang['knfd_harga_jual']; ?>" required>
    </div>


    <div class="form-group">
        <label for="knfd_qty">Edit QTY</label>
        <input type="number" min="1" name="knfd_qty" class="form-control" value="<?= $keranjang['knfd_qty'] ?>" required> 
    </div>

    <div class="form-group">
        <label for="knfd_qty">Catatan</label>
        <textarea name="knfd_catatan" id="knfd_catatan" class="form-control" rows="5" placeholder="Input Catatan Transkasi Produk"><?= $keranjang['knfd_catatan']; ?></textarea>
    </div>
