<?php
include '_header.php';

// Ambil parameter dari URL
$tanggal_awal  = isset($_GET['tanggal_awal']) ? $_GET['tanggal_awal'] : date('Y-m-d');
$tanggal_akhir = isset($_GET['tanggal_akhir']) ? $_GET['tanggal_akhir'] : date('Y-m-d');
$user_id       = isset($_GET['user_id']) ? $_GET['user_id'] : '';

// Total Transaksi Kasir (invoice_tipe_tarik_tunai = 0)
$totalTransaksi = 0;
$qTransaksi = $conn->query("SELECT SUM(invoice_sub_total) as total FROM invoice WHERE invoice_cabang = '".$sessionCabang."' AND invoice_date BETWEEN '".$tanggal_awal."' AND '".$tanggal_akhir."' AND invoice_piutang = 0 AND invoice_piutang_lunas = 0 AND invoice_draft = 0 AND invoice_tipe_tarik_tunai = 0 AND invoice_kasir = '".$user_id."'");
if ($row = $qTransaksi->fetch_assoc()) {
  $totalTransaksi = (int)$row['total'];
}

// Total Invoice Transaksi (invoice_tipe_tarik_tunai = 1)
$totalInvoice = 0;
$qInvoice = $conn->query("SELECT SUM(invoice_sub_total) as total FROM invoice WHERE invoice_cabang = '".$sessionCabang."' AND invoice_date BETWEEN '".$tanggal_awal."' AND '".$tanggal_akhir."' AND invoice_piutang = 0 AND invoice_piutang_lunas = 0 AND invoice_draft = 0 AND invoice_tipe_tarik_tunai = 1 AND invoice_kasir = '".$user_id."'");
if ($row = $qInvoice->fetch_assoc()) {
  $totalInvoice = (int)$row['total'];
}

// Total DP Penjualan
$totalDP = 0;
$qDP = $conn->query("SELECT SUM(invoice_piutang_dp) as total FROM invoice WHERE invoice_cabang = '".$sessionCabang."' AND invoice_date BETWEEN '".$tanggal_awal."' AND '".$tanggal_akhir."' AND invoice_piutang_dp > 0 AND invoice_kasir = '".$user_id."'");
if ($row = $qDP->fetch_assoc()) {
  $totalDP = (int)$row['total'];
}

// Total Cicilan Piutang
$totalCicilan = 0;
$qCicilan = $conn->query("SELECT SUM(piutang_nominal) as total FROM piutang WHERE piutang_cabang = '".$sessionCabang."' AND piutang_date BETWEEN '".$tanggal_awal."' AND '".$tanggal_akhir."' AND piutang_kasir = '".$user_id."'");
if ($row = $qCicilan->fetch_assoc()) {
  $totalCicilan = (int)$row['total'];
}

// Total Tarik Tunai (selisih jual - modal)
$totalTarikTunai = 0;
$qTarikTunai = $conn->query("SELECT SUM(invoice_sub_total - invoice_total_beli_non_fisik) as total FROM invoice WHERE invoice_cabang = '".$sessionCabang."' AND invoice_date BETWEEN '".$tanggal_awal."' AND '".$tanggal_akhir."' AND invoice_piutang = 0 AND invoice_piutang_lunas = 0 AND invoice_draft = 0 AND invoice_tipe_tarik_tunai = 1 AND invoice_kasir = '".$user_id."'");
if ($row = $qTarikTunai->fetch_assoc()) {
  $totalTarikTunai = (int)$row['total'];
}

// Total Setoran Kasir (contoh: total bersih)
$totalModal = 0;
$qModal = $conn->query("SELECT SUM(invoice_total_beli_non_fisik) as total FROM invoice WHERE invoice_cabang = '".$sessionCabang."' AND invoice_date BETWEEN '".$tanggal_awal."' AND '".$tanggal_akhir."' AND invoice_piutang = 0 AND invoice_piutang_lunas = 0 AND invoice_draft = 0 AND invoice_tipe_tarik_tunai = 1 AND invoice_kasir = '".$user_id."'");
if ($row = $qModal->fetch_assoc()) {
  $totalModal = (int)$row['total'];
}
$totalSetoran = ($totalTransaksi + $totalInvoice + $totalDP + $totalCicilan) - $totalModal;
?>

<!DOCTYPE html>
<html>
<head>
  <title>Laporan</title>
  <style>
    body { font-family: Arial, sans-serif; background: #fafafa; }
    h1 { text-align: center; margin-top: 30px; font-size: 2.5em; }
    #content { width: 100%; margin: 0 auto; padding: 20px; background: #fff; }
    table { width: 90%; margin: 40px auto; border-collapse: collapse; }
    th, td { border: 1px solid #222; padding: 14px; font-size: 1.2em; }
    th { background: #f5f5f5; text-align: left; }
    tr:nth-child(even) { background: #f5f5f5; }
    .btn-print {
      display: block;
      margin: 30px auto;
      padding: 12px 36px;
      background: #43b649;
      color: #fff;
      border: none;
      border-radius: 8px;
      font-size: 1.3em;
      cursor: pointer;
    }
    .btn-back {
      display: block;
      margin: 30px auto;
      padding: 12px 36px;
      background: #ff6b6b;
      color: #fff;
      border: none;
      border-radius: 8px;
      font-size: 1.3em;
      cursor: pointer;
    }

    .btn-back:hover {
      background: #e55e5e;
    }

    .btn-print:hover { background: #369c3a; }
  </style>
</head>
<body>
  <style>
    @media print {
      body * {
        visibility: hidden;
      }
      #content, #content * {
        visibility: visible;
      }
      #content {
        position: absolute;
        left: 0;
        top: 0;
      }
      #footer a {
        content: "POS Inventory | Copyright 2025";
      }
    }
  </style>
  <div id="content">
    <h1>Laporan</h1>
    <table>
      <tr>
        <th>Transaksi Kasir</th>
        <td>Rp. <?= number_format($totalTransaksi, 0, ',', '.') ?></td>
      </tr>
      <tr>
        <th>Invoice Transaksi</th>
        <td>Rp. <?= number_format($totalInvoice, 0, ',', '.') ?></td>
      </tr>
      <tr>
        <th>DP Penjualan</th>
        <td>Rp. <?= number_format($totalDP, 0, ',', '.') ?></td>
      </tr>
      <tr>
        <th>Cicilan Piutang</th>
        <td>Rp. <?= number_format($totalCicilan, 0, ',', '.') ?></td>
      </tr>
      <tr>
        <th>Tarik Tunai</th>
        <td>Rp. <?= number_format($totalTarikTunai, 0, ',', '.') ?></td>
      </tr>
      <tr>
        <th>Setoran Kasir</th>
        <td>Rp. <?= number_format($totalSetoran, 0, ',', '.') ?></td>
      </tr>
    </table>
  </div>
  <button class="btn-print" onclick="window.print()">Print</button>
  <a href="laporan-kasir" class="btn-back" style="margin-top: 20px;width:fit-content">Back to Laporan Kasir</a>
</body>
</html>

