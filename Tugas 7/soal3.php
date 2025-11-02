<?php
// membuat fungsi
function konversiPecahan(int $jumlahUang): array {
    // Daftar pecahan uang yang berlaku
    $denominasi = [100000, 50000, 20000, 10000, 5000, 2000, 1000, 500];
    $hasilPecahan = [];

    echo "<h3>Rincian untuk Rp " . number_format($jumlahUang) . ":</h3>";
    echo "<ul>";

    foreach ($denominasi as $nominal) {
        $jumlahLembar = floor($jumlahUang / $nominal);

        if ($jumlahLembar > 0) {
            $hasilPecahan["Rp " . number_format($nominal)] = $jumlahLembar;
            echo "<li>Rp " . number_format($nominal) . " : " . $jumlahLembar . " lembar/koin</li>";
            
            $jumlahUang = $jumlahUang % $nominal;
        }
    }

    if ($jumlahUang > 0) {
        $hasilPecahan["Sisa"] = $jumlahUang;
        echo "<li>Sisa (tidak bisa dipecah): Rp " . number_format($jumlahUang) . "</li>";
    }
    
    echo "</ul>";
    return $hasilPecahan; 
}

?>
<!doctype html>
<html lang="en">
<head>
    <title>Soal 3</title>
    <style>
        body { 
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
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
            <h2>Soal 3: Konversi Pecahan Uang</h2>
            <?php
                $pecahan = konversiPecahan(1487500);
                
            ?>
        </section>

    </div>
</body>
</html>
