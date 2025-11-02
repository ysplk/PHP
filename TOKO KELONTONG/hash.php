<?php
// =================================================================
// ALAT BANTU BUAT BIKIN HASH PASSWORD
// Ganti tulisan 'admin123' di bawah ini dengan password yang lu mau.
// =================================================================

$passwordAsli = '123'; // <-- GANTI INI DENGAN PASSWORD YANG LU MAU

// Proses bikin hash-nya
$hash = password_hash($passwordAsli, PASSWORD_DEFAULT);

// Tampilkan hasilnya biar gampang disalin
echo "<!DOCTYPE html><html><head><title>Pembuat Hash</title>";
echo "<style>body { font-family: monospace; padding: 20px; } textarea { width: 100%; padding: 10px; margin-top: 10px; font-size: 16px; } </style>";
echo "</head><body>";
echo "<h1>Alat Bikin Hash Password</h1>";
echo "<p>Password asli yang mau di-hash: <strong>" . htmlspecialchars($passwordAsli) . "</strong></p>";
echo "<p><strong>Salin semua tulisan acak di dalem kotak di bawah ini:</strong></p>";
echo "<textarea rows='4' readonly onclick='this.select();' title='Klik buat milih semua'>" . htmlspecialchars($hash) . "</textarea>";
echo "<p>Tempel (paste) kode di atas ke kolom 'password' di database lu.</p>";
echo "</body></html>";

?>
