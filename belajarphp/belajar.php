<?php

echo "<h1>Bagian 1: Penulisan</h1>";
echo "<h1>Kode Pertamaku</h1>";
echo "<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>";
echo "<hr>";

echo "<h1>Bagian 2: Penulisan Tipe Data</h1>";

$nama_lengkap = "Budi";
$umur = 20;
$tinggi_badan = 175.5;
$udah_nikah = true;

echo "Nama Saya: " . $nama_lengkap . "<br>";
echo "Umur Saya: $umur tahun<br>";
echo "Tinggi Badan: $tinggi_badan cm<br>";
echo "Status Nikah (0=belum, 1=sudah): $udah_nikah <br>";
echo "<hr>";

echo "<h1>Bagian 3: Penulisan Operator</h1>";

$angka1 = 100;
$angka2 = 50;

$hasil_tambah = $angka1 + $angka2;
$hasil_bagi = $angka1 / $angka2;

echo "100 + 50 = " . $hasil_tambah . "<br>";
echo "100 / 50 = " . $hasil_bagi . "<br>";

$apakah_sama = ($angka1 == $angka2);
echo "Apakah $angka1 sama dengan $angka2? Jawabannya: ";
var_dump($apakah_sama);
echo "<br>";
echo "<hr>";

// --- 4. STRUKTUR KONTROL (IF-ELSE & LOOPING) ---
// Ini otaknya program, buat bikin keputusan dan perulangan.
echo "<h2>Bagian 4: Struktur Kontrol</h2>";

$nilai_ujian = 75;
echo "Nilai ujian lu: $nilai_ujian <br>";

if ($nilai_ujian > 85) {
    echo "Hasil: Mantep, Lulus dengan Pujian!";
} elseif ($nilai_ujian >= 70) {
    echo "Hasil: Oke lah, Lulus!";
} else {
    echo "Hasil: Yah, Ngulang lagi lu, goblok!";
}
echo "<br><br>";

// Looping pake 'for'
echo "Looping pake 'for' dari 1 sampe 5:<br>";
for ($i = 1; $i <= 5; $i++) {
    echo "Ini perulangan ke-" . $i . "<br>";
}
echo "<br>";

// Looping pake 'while'
echo "Looping pake 'while':<br>";
$j = 1;
while ($j <= 3) {
    echo "Angka J sekarang: " . $j . "<br>";
    $j++; // Ini sama aja kayak $j = $j + 1;
}
echo "<hr>";

// --- 5. SUPERGLOBALS (CONTOH PAKE $_GET) ---
// Ini variabel sakti dari PHP.
// Coba akses file ini di browser pake: localhost/belajar.php?nama=Udin
echo "<h2>Bagian 5: Superglobals ($_GET)</h2>";

if (isset($_GET['nama'])) { // 'isset' buat ngecek variabelnya ada apa kaga
    $nama_dari_url = $_GET['nama'];
    echo "Halo, " . $nama_dari_url . "! Nama lu nongol dari URL.";
} else {
    echo "Coba tambahin `?nama=NAMA_LU` di akhir URL browser, Mar.";
}
echo "<hr>";
?>