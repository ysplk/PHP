<?php
/*
 * =================================================================
 * FILE KONEKSI DATABASE TERPUSAT
 * =================================================================
 * Ganti pengaturan di bawah ini sesuai dengan server database lu.
 * Semua file lain akan manggil file ini.
 */
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '123'; // <-- KOSONGIN KALO GAK PAKE PASSWORD
$db_name = 'toko_kelontong'; // <-- GANTI NAMA DATABASE LU

// Buat koneksi ke database
$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

// Kalo koneksi gagal, langsung matiin program dan kasih tau errornya
if (!$conn) {
    die("Koneksi ke database GAGAL: " . mysqli_connect_error());
}
?>
