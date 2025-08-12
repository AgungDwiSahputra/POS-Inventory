<?php 
  include '_header.php';
?>
<?php

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['kategori_id'])) {
    $kategori_id = $_POST['kategori_id'];
    $sub_kategoris = query("SELECT * FROM sub_kategori WHERE kategori_id = $kategori_id AND sub_kategori_status > 0 ORDER BY id DESC");

    echo '<option value="">--Pilih Sub Kategori--</option>';
    foreach ($sub_kategoris as $sub_kategori) {
        echo '<option value="' . $sub_kategori['id'] . '">' . $sub_kategori['sub_kategori_nama'] . '</option>';
    }
    exit();
}
?>