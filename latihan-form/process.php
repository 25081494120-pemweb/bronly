
<?php
date_default_timezone_set("Asia/Jakarta");

function aman($teks) {
    return htmlspecialchars(
        (string) $teks,
        ENT_QUOTES,
        "UTF-8"
    );
}

function rupiah($angka) {
    return "Rp" . number_format(
        $angka,
        0,
        ",",
        "."
    );
}

$menus = [
    "coffee" => [
        "nama" => "Signature Coffee",
        "harga" => 25000
    ],
    "burger" => [
        "nama" => "Bron Burger",
        "harga" => 45000
    ],
    "fries" => [
        "nama" => "French Fries",
        "harga" => 20000
    ]
];

$error = "";
$nama = "";
$namaMenu = "";
$harga = 0;
$jumlah = 0;
$subtotal = 0;
$diskon = 0;
$total = 0;

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    $error = "Silakan kirim pesanan melalui form terlebih dahulu.";
} else {
    $namaInput = $_POST["nama"] ?? "";
    $produkInput = $_POST["produk"] ?? "";
    $jumlahInput = $_POST["jumlah"] ?? "";

    if (!is_string($namaInput) ||
        !is_string($produkInput) ||
        !is_string($jumlahInput)) {
        $error = "Data pesanan tidak valid.";
    } else {
        $nama = trim($namaInput);
        $produk = trim($produkInput);
        $jumlahInput = trim($jumlahInput);

        if ($nama === "") {
            $error = "Nama pelanggan wajib diisi.";
        } elseif (strlen($nama) > 300) {
            $error = "Nama pelanggan terlalu panjang.";
        } elseif (!isset($menus[$produk])) {
            $error = "Menu yang dipilih tidak tersedia.";
        } elseif (!preg_match('/^[0-9]+$/', $jumlahInput)) {
            $error = "Jumlah pesanan harus berupa angka bulat.";
        } else {
            $jumlah = (int) $jumlahInput;

            if ($jumlah < 1 || $jumlah > 99) {
                $error = "Jumlah pesanan harus antara 1 sampai 99.";
            }
        }
    }

    if ($error === "") {
        $namaMenu = $menus[$produk]["nama"];
        $harga = $menus[$produk]["harga"];

        $subtotal = $harga * $jumlah;
        $diskon = ($subtotal >= 100000)
            ? $subtotal * 0.10
            : 0;

        $total = $subtotal - $diskon;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Pesanan - Bron Cafe Eatry</title>
    <link rel="stylesheet" href="./style.css">
</head>
<body>

<main class="receipt">
    <h1>Bron Cafe Eatry</h1>
    <p class="subtitle">Rincian Pesanan Kamu</p>

    <?php if ($error !== ""): ?>
        <div class="error">
            <strong>Pesanan belum dapat diproses.</strong>
            <p><?= aman($error) ?></p>
        </div>

        <a href="./index.html#order" class="back-button">
            Kembali ke Form Pesanan
        </a>

    <?php else: ?>
        <div class="success">
            Pesanan berhasil dihitung!
        </div>

        <div class="receipt-row">
            <span>Nama Pelanggan</span>
            <strong><?= aman($nama) ?></strong>
        </div>

        <div class="receipt-row">
            <span>Menu</span>
            <strong><?= aman($namaMenu) ?></strong>
        </div>

        <div class="receipt-row">
            <span>Harga Satuan</span>
            <span><?= rupiah($harga) ?></span>
        </div>

        <div class="receipt-row">
            <span>Jumlah Pesanan</span>
            <span><?= $jumlah ?></span>
        </div>

        <div class="receipt-row">
            <span>Subtotal</span>
            <span><?= rupiah($subtotal) ?></span>
        </div>

        <div class="receipt-row discount">
            <span>Diskon 10%</span>
            <span>- <?= rupiah($diskon) ?></span>
        </div>

        <div class="receipt-row total">
            <span>Total Pembayaran</span>
            <span><?= rupiah($total) ?></span>
        </div>

        <div class="receipt-row">
            <span>Tanggal Pesanan</span>
            <span><?= date("d-m-Y H:i:s") ?></span>
        </div>

        <div class="discount-info">
            <?php if ($diskon > 0): ?>
                <strong>Diskon berhasil diterapkan!</strong>
                <p>Subtotal mencapai Rp100.000, sehingga kamu mendapat diskon 10%.</p>
            <?php else: ?>
                <strong>Promo Bron Cafe</strong>
                <p>Diskon 10% berlaku untuk subtotal minimal Rp100.000.</p>
            <?php endif; ?>
        </div>

        <a href="./index.html#order" class="back-button">
            Pesan Lagi
        </a>
    <?php endif; ?>
</main>

</body>
</html>
