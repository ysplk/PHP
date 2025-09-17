<?php
// =================================================================
// SASANA TINJU SQL INJECTION - DIBUAT UNTUK LATIHAN
// OLEH GEMINI
// =================================================================
// --- Konfigurasi Database ---
// Ganti ini dengan detail database lu sendiri
define('DB_HOST', 'localhost'); // Pake '127.0.0.1' lebih aman daripada 'localhost'
define('DB_USER', 'root'); // Ganti dengan username database lu di WSL
define('DB_PASS', '123');     // Ganti dengan password database lu di WSL
define('DB_NAME', 'sasana_sql'); // Nama database yang lu buat pake skrip SQL

// --- Koneksi ke Database ---
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Koneksi Gagal: " . $conn->connect_error . ". Pastikan server database di WSL sudah jalan dan konfigurasinya benar.");
}

// --- Logika Pencarian ---
$products = [];
$search_term = '';
$category_id = null;
$search_method = '';

// BAGIAN AMAN (MENGGUNAKAN PREPARED STATEMENTS)
if (isset($_POST['search'])) {
    $search_term = $_POST['search_term'];
    $search_method = 'Pencarian Aman';
    $stmt = $conn->prepare("SELECT p.name, p.description, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.name LIKE ?");
    $like_term = "%" . $search_term . "%";
    $stmt->bind_param("s", $like_term);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    $stmt->close();
}
// BAGIAN RENTAN (VULNERABLE TO SQL INJECTION)
else if (isset($_GET['category_id'])) {
    $category_id = $_GET['category_id'];
    $search_method = 'Filter Kategori (Tidak Aman)';
    $sql = "SELECT p.name, p.description, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.category_id = " . $category_id;
    $result = $conn->query($sql);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
    }
}
// Tampilan default (menampilkan semua produk)
else {
    $search_method = 'Semua Produk';
    $sql = "SELECT p.name, p.description, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sasana Tinju SQL Injection</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-900 text-gray-200">

    <div class="container mx-auto p-4 md:p-8 max-w-4xl">
        <header class="text-center mb-8">
            <h1 class="text-4xl md:text-5xl font-bold text-cyan-400">🥊 Sasana Tinju SQL 🥊</h1>
            <p class="text-gray-400 mt-2">Tempat Latihan SQL Injection yang Aman</p>
        </header>

        <div class="bg-gray-800 p-6 rounded-xl shadow-lg mb-8">
            <h2 class="text-2xl font-bold mb-4 border-b-2 border-cyan-500 pb-2">Cari Produk</h2>

            <!-- FORM PENCARIAN AMAN -->
            <form method="POST" action="index.php" class="mb-6">
                <label for="search_term" class="block mb-2 text-sm font-medium text-gray-300">Cari berdasarkan nama (Metode Aman)</label>
                <div class="flex">
                    <input type="text" id="search_term" name="search_term" class="bg-gray-700 border border-gray-600 text-white text-sm rounded-l-lg focus:ring-cyan-500 focus:border-cyan-500 block w-full p-2.5" placeholder="Contoh: Laptop" value="<?= htmlspecialchars($search_term) ?>">
                    <button type="submit" name="search" class="bg-cyan-600 hover:bg-cyan-700 text-white font-bold py-2 px-4 rounded-r-lg">Cari</button>
                </div>
                <p class="text-xs text-gray-500 mt-1">Form ini menggunakan Prepared Statements. SQLMap akan mental.</p>
            </form>

            <!-- FILTER KATEGORI TIDAK AMAN -->
            <div>
                <h3 class="block mb-2 text-sm font-medium text-gray-300">Filter berdasarkan kategori (Metode Tidak Aman)</h3>
                <div class="flex space-x-2">
                    <a href="index.php?category_id=1" class="bg-gray-700 hover:bg-gray-600 text-white py-2 px-4 rounded-lg text-sm">Elektronik</a>
                    <a href="index.php?category_id=2" class="bg-gray-700 hover:bg-gray-600 text-white py-2 px-4 rounded-lg text-sm">Buku</a>
                    <a href="index.php?category_id=3" class="bg-gray-700 hover:bg-gray-600 text-white py-2 px-4 rounded-lg text-sm">Pakaian</a>
                </div>
                <p class="text-xs text-gray-500 mt-1">Link ini rentan terhadap SQL Injection. <span class="font-bold text-yellow-400">Ini target lu!</span></p>
            </div>
        </div>

        <div class="bg-gray-800 p-6 rounded-xl shadow-lg">
            <h2 class="text-2xl font-bold mb-4">Hasil: <span class="text-cyan-400"><?= htmlspecialchars($search_method) ?></span></h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <?php if (!empty($products)): ?>
                    <?php foreach ($products as $product): ?>
                        <div class="bg-gray-700 p-4 rounded-lg border border-gray-600">
                            <h3 class="font-bold text-lg text-cyan-300"><?= htmlspecialchars($product['name']) ?></h3>
                            <p class="text-sm text-gray-400 mb-2">[<?= htmlspecialchars($product['category_name']) ?>]</p>
                            <p class="text-gray-300"><?= htmlspecialchars($product['description']) ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-gray-400 md:col-span-2">Tidak ada produk yang ditemukan.</p>
                <?php endif; ?>
            </div>
        </div>

        <footer class="text-center mt-8 text-gray-500 text-sm">
            <p>Gunakan dengan bijak. Jangan pernah menyerang sistem tanpa izin.</p>
        </footer>
    </div>

</body>

</html>