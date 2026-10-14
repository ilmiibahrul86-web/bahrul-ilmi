
<?php
session_start();
require "koneksi.php";

if (!isset($_SESSION["keranjang"])) {
    $_SESSION["keranjang"] = [];
}

function rupiah($angka) {
    return "Rp " . number_format($angka, 0, ",", ".");
}

$pesan = "";

// Tambah barang ke database
if (isset($_POST["tambah_barang"])) {
    $nama = trim($_POST["nama_barang"]);
    $harga = (int) $_POST["harga"];
    $stok = (int) $_POST["stok"];

    if ($nama !== "" && $harga > 0 && $stok >= 0) {
        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO barang (nama_barang, harga, stok)
             VALUES (?, ?, ?)"
        );
        mysqli_stmt_bind_param($stmt, "sii", $nama, $harga, $stok);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        header("Location: index.php?pesan=barang");
        exit;
    }
}

// Masukkan barang ke keranjang
if (isset($_POST["masuk_keranjang"])) {
    $id = (int) $_POST["id_barang"];
    $jumlah = max(1, (int) $_POST["jumlah"]);

    $stmt = mysqli_prepare(
        $conn,
        "SELECT id, nama_barang, harga, stok
         FROM barang WHERE id = ?"
    );
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $hasil = mysqli_stmt_get_result($stmt);
    $barang = mysqli_fetch_assoc($hasil);
    mysqli_stmt_close($stmt);

    if ($barang && $barang["stok"] >= $jumlah) {
        $sudah = $_SESSION["keranjang"][$id]["jumlah"] ?? 0;

        if ($sudah + $jumlah <= $barang["stok"]) {
            $_SESSION["keranjang"][$id] = [
                "nama" => $barang["nama_barang"],
                "harga" => (int) $barang["harga"],
                "jumlah" => $sudah + $jumlah
            ];
        }
    }

    header("Location: index.php");
    exit;
}

// Hapus barang dari keranjang
if (isset($_POST["hapus_keranjang"])) {
    $id = (int) $_POST["hapus_keranjang"];
    unset($_SESSION["keranjang"][$id]);

    header("Location: index.php");
    exit;
}

// Kosongkan keranjang
if (isset($_POST["kosongkan"])) {
    $_SESSION["keranjang"] = [];
    header("Location: index.php");
    exit;
}

// Proses pembayaran
if (isset($_POST["bayar"])) {
    $uang = (int) $_POST["uang_bayar"];
    $keranjang = $_SESSION["keranjang"];
    $total = 0;

    foreach ($keranjang as $item) {
        $total += $item["harga"] * $item["jumlah"];
    }

    if (!$keranjang) {
        $pesan = "Keranjang masih kosong.";
    } elseif ($uang < $total) {
        $pesan = "Uang pembayaran kurang!";
    } else {
        mysqli_begin_transaction($conn);

        try {
            foreach ($keranjang as $id => $item) {
                $stmt = mysqli_prepare(
                    $conn,
                    "UPDATE barang
                     SET stok = stok - ?
                     WHERE id = ? AND stok >= ?"
                );
                mysqli_stmt_bind_param(
                    $stmt, "iii",
                    $item["jumlah"], $id, $item["jumlah"]
                );
                mysqli_stmt_execute($stmt);
                $berhasil = mysqli_stmt_affected_rows($stmt);
                mysqli_stmt_close($stmt);

                if ($berhasil !== 1) {
                    throw new Exception("Stok barang tidak mencukupi.");
                }
            }

            $kembalian = $uang - $total;

            $stmt = mysqli_prepare(
                $conn,
                "INSERT INTO transaksi (total, bayar, kembalian)
                 VALUES (?, ?, ?)"
            );
            mysqli_stmt_bind_param(
                $stmt, "iii", $total, $uang, $kembalian
            );
            mysqli_stmt_execute($stmt);
            $id_transaksi = mysqli_insert_id($conn);
            mysqli_stmt_close($stmt);

            mysqli_commit($conn);

            $_SESSION["struk"] = [
                "id" => $id_transaksi,
                "items" => $keranjang,
                "total" => $total,
                "bayar" => $uang,
                "kembalian" => $kembalian,
                "tanggal" => date("d-m-Y H:i:s")
            ];

            $_SESSION["keranjang"] = [];
            header("Location: index.php?selesai=1");
            exit;

        } catch (Throwable $e) {
            mysqli_rollback($conn);
            $pesan = "Transaksi gagal: " . $e->getMessage();
        }
    }
}

$daftar_barang = mysqli_query(
    $conn,
    "SELECT * FROM barang ORDER BY nama_barang ASC"
);

$total = 0;
foreach ($_SESSION["keranjang"] as $item) {
    $total += $item["harga"] * $item["jumlah"];
}

$jumlah_barang = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS jumlah FROM barang")
)["jumlah"];

$jumlah_transaksi = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS jumlah FROM transaksi")
)["jumlah"];

$pesan_sukses = "";
if (isset($_GET["pesan"]) && $_GET["pesan"] === "barang") {
    $pesan_sukses = "Barang berhasil ditambahkan!";
}
if (isset($_GET["selesai"]) && isset($_SESSION["struk"])) {
    $pesan_sukses = "Pembayaran berhasil disimpan!";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>KasirKu - Aplikasi Kasir</title>
<style>
*{box-sizing:border-box}
body{margin:0;font-family:Arial,sans-serif;background:#f3f6fb;color:#172033}
header{background:#123b2a;color:white;padding:22px 5%;display:flex;justify-content:space-between;align-items:center;gap:15px;flex-wrap:wrap}
header h1{margin:0;font-size:25px}
header p{margin:6px 0 0;color:#d0e8d8}
main{max-width:1200px;margin:25px auto;padding:0 18px}
.stats{display:grid;grid-template-columns:repeat(3,1fr);gap:15px;margin-bottom:22px}
.stat,.panel{background:white;border-radius:14px;padding:20px;box-shadow:0 4px 18px #1020300b}
.stat span{color:#667085;font-size:14px}
.stat h2{margin:10px 0 0;color:#176b45}
.layout{display:grid;grid-template-columns:1.2fr 1fr;gap:20px;align-items:start}
.panel h2{font-size:19px;margin:0 0 18px}
label{display:block;font-size:13px;font-weight:bold;margin:12px 0 6px}
input,select{width:100%;padding:11px;border:1px solid #d6dce5;border-radius:8px;font-size:14px}
button{border:0;border-radius:8px;padding:11px 14px;background:#176b45;color:white;font-weight:bold;cursor:pointer}
button:hover{opacity:.88}
.full{width:100%;margin-top:14px}
table{width:100%;border-collapse:collapse;font-size:13px}
th,td{text-align:left;padding:11px 7px;border-bottom:1px solid #edf0f4}
th{color:#667085}
.muted{color:#667085;font-size:13px}
.cart-item{padding:13px 0;border-bottom:1px solid #edf0f4;display:flex;justify-content:space-between;gap:10px}
.cart-item small{display:block;color:#667085;margin-top:5px}
.danger{background:#fee2e2;color:#991b1b;padding:7px 9px}
.total{display:flex;justify-content:space-between;font-size:20px;font-weight:bold;margin:20px 0}
.alert{padding:12px 15px;border-radius:8px;margin-bottom:18px;background:#dcfce7;color:#166534}
.error{background:#fee2e2;color:#991b1b}
.receipt{border:1px dashed #9ca3af;padding:16px;margin-top:18px;border-radius:8px}
@media(max-width:800px){.layout{grid-template-columns:1fr}.stats{grid-template-columns:1fr}header h1{font-size:21px}}
@media print{header,.stats,.layout,.no-print,.alert{display:none!important}body{background:white}.receipt{display:block!important;border:0}}
</style>
</head>
<body>
<header>
 <div>
  <h1>🛒 KasirKu</h1>
  <p>Aplikasi kasir dan pengelolaan barang</p>
 </div>
 <div><?= date("d M Y") ?></div>
</header>

<main>
<?php if ($pesan_sukses): ?>
 <div class="alert"><?= htmlspecialchars($pesan_sukses) ?></div>
<?php endif; ?>
<?php if ($pesan): ?>
 <div class="alert error"><?= htmlspecialchars($pesan) ?></div>
<?php endif; ?>

<section class="stats">
 <div class="stat"><span>Jenis Barang</span><h2><?= $jumlah_barang ?></h2></div>
 <div class="stat"><span>Total Transaksi</span><h2><?= $jumlah_transaksi ?></h2></div>
 <div class="stat"><span>Total Keranjang</span><h2><?= rupiah($total) ?></h2></div>
</section>

<div class="layout">
 <div>
  <section class="panel">
   <h2>➕ Tambah Barang Baru</h2>
   <form method="post">
    <label>Nama barang</label>
    <input name="nama_barang" required maxlength="100" placeholder="Contoh: Air Mineral">
    <label>Harga satuan (Rp)</label>
    <input type="number" name="harga" min="1" required placeholder="5000">
    <label>Stok awal</label>
    <input type="number" name="stok" min="0" required placeholder="20">
    <button class="full" name="tambah_barang">Simpan Barang</button>
   </form>
  </section>

  <section class="panel" style="margin-top:20px">
   <h2>📦 Daftar Barang</h2>
   <?php if (mysqli_num_rows($daftar_barang) === 0): ?>
    <p class="muted">Belum ada barang. Tambahkan barang terlebih dahulu.</p>
   <?php else: ?>
   <div style="overflow-x:auto">
   <table>
    <thead><tr><th>Barang</th><th>Harga</th><th>Stok</th><th>Keranjang</th></tr></thead>
    <tbody>
    <?php while ($b = mysqli_fetch_assoc($daftar_barang)): ?>
     <tr>
      <td><?= htmlspecialchars($b["nama_barang"]) ?></td>
      <td><?= rupiah($b["harga"]) ?></td>
      <td><?= $b["stok"] ?></td>
      <td>
       <?php if ($b["stok"] > 0): ?>
       <form method="post">
        <input type="hidden" name="id_barang" value="<?= $b["id"] ?>">
        <input type="number" name="jumlah" min="1" max="<?= $b["stok"] ?>" value="1" required style="width:65px;padding:7px">
        <button name="masuk_keranjang" style="padding:8px">+</button>
       </form>
       <?php else: ?><span class="muted">Habis</span><?php endif; ?>
      </td>
     </tr>
    <?php endwhile; ?>
    </tbody>
   </table>
   </div>
   <?php endif; ?>
  </section>
 </div>

 <section class="panel">
  <h2>🧺 Keranjang Belanja</h2>
  <?php if (empty($_SESSION["keranjang"])): ?>
   <p class="muted">Belum ada barang di keranjang.</p>
  <?php else: ?>
   <?php foreach ($_SESSION["keranjang"] as $id => $item): ?>
    <div class="cart-item">
     <div>
      <strong><?= htmlspecialchars($item["nama"]) ?></strong>
      <small><?= $item["jumlah"] ?> × <?= rupiah($item["harga"]) ?></small>
     </div>
     <div style="text-align:right">
      <strong><?= rupiah($item["jumlah"] * $item["harga"]) ?></strong>
      <form method="post" style="margin-top:7px">
       <button class="danger" name="hapus_keranjang" value="<?= $id ?>">Hapus</button>
      </form>
     </div>
    </div>
   <?php endforeach; ?>
  <?php endif; ?>

  <div class="total"><span>Total</span><span><?= rupiah($total) ?></span></div>
  <form method="post" id="formBayar">
   <label>Uang pembayaran (Rp)</label>
   <input type="number" id="uang_bayar" name="uang_bayar" min="<?= $total ?>" required placeholder="Masukkan uang pelanggan">
   <p class="muted">Perkiraan kembalian: <strong id="kembalian">Rp 0</strong></p>
   <button class="full" name="bayar" value="1" <?= $total <= 0 ? "disabled" : "" ?>>💳 Proses Pembayaran</button>
  </form>
  <form method="post" class="no-print">
   <button class="full" name="kosongkan" style="background:#e5e7eb;color:#374151" <?= empty($_SESSION["keranjang"]) ? "disabled" : "" ?>>Kosongkan Keranjang</button>
  </form>

  <?php if (isset($_GET["selesai"]) && isset($_SESSION["struk"])):
    $s = $_SESSION["struk"]; ?>
   <div class="receipt" id="struk">
    <h2 style="text-align:center">STRUK PEMBELIAN</h2>
    <p>No. transaksi: <?= $s["id"] ?><br><?= $s["tanggal"] ?></p>
    <?php foreach ($s["items"] as $it): ?>
     <p><?= htmlspecialchars($it["nama"]) ?><br>
     <?= $it["jumlah"] ?> × <?= rupiah($it["harga"]) ?>
     <span style="float:right"><?= rupiah($it["jumlah"]*$it["harga"]) ?></span></p>
    <?php endforeach; ?>
    <hr>
    <p>Total: <strong><?= rupiah($s["total"]) ?></strong></p>
    <p>Bayar: <?= rupiah($s["bayar"]) ?></p>
    <p>Kembalian: <strong><?= rupiah($s["kembalian"]) ?></strong></p>
    <button class="full no-print" onclick="window.print()">🖨️ Cetak Struk</button>
   </div>
  <?php endif; ?>
 </section>
</div>
</main>

<script>
const uang = document.getElementById("uang_bayar");
const keluaran = document.getElementById("kembalian");
const total = <?= (int)$total ?>;

function formatRupiah(n) {
 return "Rp " + n.toLocaleString("id-ID");
}
function hitungKembalian() {
 const bayar = Number(uang.value || 0);
 keluaran.textContent = formatRupiah(Math.max(0, bayar - total));
}
uang.addEventListener("input", hitungKembalian);
hitungKembalian();
</script>
</body>
</html>
