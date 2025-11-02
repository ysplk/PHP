<?php
// membuat fungsi 
function countdownLoopBiasa(int $angka) {
    
    for ($i = $angka; $i >= 0; $i--) {
        // Menampilkan data
        echo $i . "...<br>";
    }
    
    echo "<h2>Meluncuuur...................</h2>";
}

?>
<!doctype html>
<html lang="en">
<head>
    <title>Soal 5</title>
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
            <h2>Soal 5: Countdown Roket</h2>
            <?php
            // memaggil fungsi
                countdownLoopBiasa(10);
            ?>
        </section>

    </div>
</body>
</html>

