<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Soal 1</title>
</head>
<body>
<?php

function mahasiswa(int $nim, string $nama, string $prodi) {
    return [
        'nim' => $nim,
        'nama' => $nama,
        'prodi' => $prodi
    ];
}

$mahasiswa1 = mahasiswa(202404001, 'Umar', 'Teknologi Rakasa Perangkat Lunak');
$mahasiswa2 = mahasiswa(202304002, 'Ahmad', 'Teknologi Listrik');
$mahasiswa3 = mahasiswa(202404003, 'Citra', 'Teknologi Rekayasa Mekatronika');
$mahasiswa4 = mahasiswa(202404004, 'Dodi', 'Teknologi Manufaktur');
$daftar_mahasiswa = [$mahasiswa1, $mahasiswa2, $mahasiswa3, $mahasiswa4];

echo "<h2>Daftar Mahasiswa</h2>";
echo "<ul>";
foreach ($daftar_mahasiswa as $mahasiswa) {
    echo "<li>NIM: {$mahasiswa['nim']}, Nama: {$mahasiswa['nama']}, Program Studi: {$mahasiswa['prodi']}</li>";
}
echo "</ul>";
?>

</body>
</html>
