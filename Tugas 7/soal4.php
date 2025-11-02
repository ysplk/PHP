<?php
// fungsi Gaji
function hitungHonor(string $golongan, float $jamKerja): float {
    $upah_berdasarkan_golongan = [
        'A' => 4000,
        'B' => 5000,
        'C' => 6000,
        'D' => 7500,
    ];
    $upah_lembur_perjam = 3000;
    $batas_jam_normal = 48; // Batas jam kerja normal seminggu

    if (!array_key_exists($golongan, $upah_berdasarkan_golongan)) {
        echo "<p>Error: Golongan '$golongan' tidak valid.</p>";
        return 0.0; // Golongan tidak valid
    }

    $upah_perjam = $upah_berdasarkan_golongan[$golongan];

    // jam kerja dibagi normal dan lembur
    $jam_normal = min($jamKerja, $batas_jam_normal);
    $jam_lembur = max(0, $jamKerja - $batas_jam_normal);

    // Hitung gaji
    $gaji_normal = $jam_normal * $upah_perjam;
    $gaji_lembur = $jam_lembur * $upah_lembur_perjam;
    $total_gaji = $gaji_normal + $gaji_lembur;
    
    return $total_gaji;
}

?>
<!doctype html>
<html lang="en">
<head>
    <title>Soal 4</title>
    <style>
        body { 
            font-family: "seui-ui", 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 2rem; 
            background-color: #f9f9f9; 
            line-height: 1.6; 
            padding-bottom: 5rem;
        }
        .container {
            max-width: 800px;
            margin: auto;
            background: #fff;
            padding: 1rem 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        h1 { text-align: center; color: #333; }
        h2 { color: #17a2b8; border-bottom: 2px solid #17a2b8; padding-bottom: 5px; }
        pre { background: #eee; padding: 10px; border-radius: 5px; overflow-x: auto; }
        ul { padding-left: 20px; }
        li { margin-bottom: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <section>
            <h2>Soal 4: Hitung Honor (Gaji Golongan)</h2>
            <?php
              
                $golongan_tes = 'C';
                $jam_tes = 50;
                $honor = hitungHonor($golongan_tes, $jam_tes);
                
                echo "<p>Perhitungan Gaji untuk:<br>Golongan: <strong>$golongan_tes</strong><br>Total Jam Kerja: <strong>$jam_tes jam</strong></p>";
                echo "<p>Total honor (gaji) yang diterima: <strong>Rp " . number_format($honor) . "</strong></p>";
            ?>
        </section>

    </div>
</body>
</html>
