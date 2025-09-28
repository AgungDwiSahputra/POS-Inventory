<?php 
include '_header-artibut.php';

echo "<h2>Debug Keranjang</h2>";
echo "<pre>";

// Debug variabel
$userId = $_SESSION['user_id'];
$tipeHarga = base64_decode($_GET['customer']);

echo "User ID: " . $userId . "\n";
echo "Tipe Harga: " . $tipeHarga . "\n";
echo "Session Cabang: " . $sessionCabang . "\n";

// Query debug
$query = "SELECT * FROM keranjang WHERE keranjang_id_kasir = $userId && keranjang_tipe_customer = $tipeHarga && keranjang_cabang = $sessionCabang ORDER BY keranjang_id ASC";
echo "Query: " . $query . "\n\n";

// Test query tanpa kondisi
$testQuery1 = "SELECT COUNT(*) as total FROM keranjang WHERE keranjang_id_kasir = $userId";
$result1 = mysqli_query($conn, $testQuery1);
$row1 = mysqli_fetch_array($result1);
echo "Total keranjang untuk user $userId: " . $row1['total'] . "\n";

$testQuery2 = "SELECT COUNT(*) as total FROM keranjang WHERE keranjang_tipe_customer = $tipeHarga";
$result2 = mysqli_query($conn, $testQuery2);
$row2 = mysqli_fetch_array($result2);
echo "Total keranjang untuk tipe harga $tipeHarga: " . $row2['total'] . "\n";

$testQuery3 = "SELECT COUNT(*) as total FROM keranjang WHERE keranjang_cabang = $sessionCabang";
$result3 = mysqli_query($conn, $testQuery3);
$row3 = mysqli_fetch_array($result3);
echo "Total keranjang untuk cabang $sessionCabang: " . $row3['total'] . "\n";

// Query lengkap
$keranjang = query($query);
echo "Hasil query lengkap: " . count($keranjang) . " data\n\n";

// Tampilkan data keranjang
if (count($keranjang) > 0) {
    echo "Data keranjang:\n";
    foreach ($keranjang as $row) {
        echo "ID: " . $row['keranjang_id'] . ", Nama: " . $row['keranjang_nama'] . ", Qty: " . $row['keranjang_qty_view'] . "\n";
    }
} else {
    echo "Tidak ada data keranjang\n";
    
    // Cek data keranjang tanpa filter
    $allKeranjang = query("SELECT * FROM keranjang ORDER BY keranjang_id DESC LIMIT 5");
    echo "\nData keranjang terbaru (5 data):\n";
    foreach ($allKeranjang as $row) {
        echo "ID: " . $row['keranjang_id'] . ", User: " . $row['keranjang_id_kasir'] . ", Tipe: " . $row['keranjang_tipe_customer'] . ", Cabang: " . $row['keranjang_cabang'] . ", Nama: " . $row['keranjang_nama'] . "\n";
    }
}

echo "</pre>";
?>