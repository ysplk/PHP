<?php

// membuat fungsi berat badan
function hitungBeratIdeal(float $tinggi): float {
    $berat_ideal = ($tinggi - 100) * 0.9;
    return $berat_ideal;
}

// hasil awal
$hasil = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tinggi_input = $_POST['tinggi'] ?? '';
    $berat_input = $_POST['berat'] ?? '';

    if (is_numeric($tinggi_input) && is_numeric($berat_input) && $tinggi_input > 0 && $berat_input > 0) {
        $tinggi = (float)$tinggi_input;
        $berat_asli = (float)$berat_input;

        $berat_ideal = hitungBeratIdeal($tinggi);

        if ($berat_asli == $berat_ideal) {
            $hasil = [
                'pesan' => 'Berat badan Anda ideal',
                'kelas' => 'ideal',
                'ideal_tampil' => $berat_ideal
            ];
        } else {
            $hasil = [
                'pesan' => 'Berat badan Anda tidak ideal',
                'kelas' => 'not-ideal',
                'ideal_tampil' => $berat_ideal
            ];
        }
    } 
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Soal2</title>
    <style>
        body { font-family: sans-serif; margin: 2rem; background-color: #f4f4f9; }
        .container { max-width: 400px; margin: auto; padding: 2rem; background: #fff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { text-align: center; }
        label, input, button { display: block; width: 100%; margin-bottom: 1rem; box-sizing: border-box; }
        input, button { padding: 0.75rem; border-radius: 4px; border: 1px solid #ddd; }
        button { background-color: #17a2b8; color: white; border: none; cursor: pointer; font-weight: bold; }
        .result { margin-top: 1.5rem; padding: 1.25rem; text-align: center; border-radius: 4px; color: white; }
        .ideal { background-color: #28a745; }
        .not-ideal { background-color: #dc3545; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Cek Berat Badan Ideal</h2>


        <form method="post" action="">
            <label for="tinggi">Tinggi Badan (cm):</label>
            <input type="number" id="tinggi" name="tinggi" value="<?= $_POST['tinggi'] ?? ''; ?>" required>
            
            <label for="berat">Berat Badan (kg):</label>
            <input type="number" id="berat" name="berat" value="<?= $_POST['berat'] ?? ''; ?>" required>
            
            <button type="submit">Cek Berat Badan</button>
        </form>

        <?php if ($hasil): ?>
            <div class="result <?= $hasil['kelas']; ?>">
                <h3><?= $hasil['pesan']; ?></h3>
                <p>(Berat ideal untuk tinggi Anda adalah <?= $hasil['ideal_tampil']; ?> kg)</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
