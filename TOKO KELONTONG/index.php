<?php
session_start();
// Cek sesi login, kalo belom login, tendang ke halaman login
if (!isset($_SESSION['id_pengguna'])) {
    header("Location: login.php");
    exit;
}

require 'database.php'; // Panggil file koneksi terpusat

// Inisialisasi variabel buat form edit
$is_edit = false;
$edit_data = [
    'id_barang' => '', 'kode_barang' => '', 'nama_barang' => '', 'id_kategori' => '',
    'satuan' => '', 'harga_beli' => '', 'harga_jual' => '', 'stok' => '', 'id_pemasok' => ''
];

// PROSES TAMBAH & UPDATE BARANG
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_barang = $_POST['id_barang'] ?? null;
    $kode_barang = mysqli_real_escape_string($conn, $_POST['kode_barang']);
    $nama_barang = mysqli_real_escape_string($conn, $_POST['nama_barang']);
    $id_kategori = !empty($_POST['id_kategori']) ? (int)$_POST['id_kategori'] : 'NULL';
    $satuan = mysqli_real_escape_string($conn, $_POST['satuan']);
    $harga_beli = (float)$_POST['harga_beli'];
    $harga_jual = (float)$_POST['harga_jual'];
    $stok = (int)$_POST['stok'];
    $id_pemasok = !empty($_POST['id_pemasok']) ? (int)$_POST['id_pemasok'] : 'NULL';

    if ($id_barang) {
        $sql = "UPDATE tbl_barang SET kode_barang = '$kode_barang', nama_barang = '$nama_barang', id_kategori = $id_kategori, satuan = '$satuan', harga_beli = $harga_beli, harga_jual = $harga_jual, stok = $stok, id_pemasok = $id_pemasok WHERE id_barang = $id_barang";
    } else {
        $sql = "INSERT INTO tbl_barang (kode_barang, nama_barang, id_kategori, satuan, harga_beli, harga_jual, stok, id_pemasok) VALUES ('$kode_barang', '$nama_barang', $id_kategori, '$satuan', $harga_beli, $harga_jual, $stok, $id_pemasok)";
    }

    if (mysqli_query($conn, $sql)) {
        header("Location: index.php");
        exit;
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// PROSES HAPUS BARANG
if (isset($_GET['hapus_id'])) {
    $id_hapus = (int)$_GET['hapus_id'];
    $sql_hapus = "DELETE FROM tbl_barang WHERE id_barang = $id_hapus";
    if (mysqli_query($conn, $sql_hapus)) {
        header("Location: index.php");
        exit;
    } else {
        echo "Error deleting record: " . mysqli_error($conn);
    }
}

// AMBIL DATA UNTUK FORM EDIT
if (isset($_GET['edit_id'])) {
    $is_edit = true;
    $id_edit = (int)$_GET['edit_id'];
    $sql_edit = "SELECT * FROM tbl_barang WHERE id_barang = $id_edit";
    $result_edit = mysqli_query($conn, $sql_edit);
    $edit_data = mysqli_fetch_assoc($result_edit);
}

// AMBIL SEMUA DATA BARANG
$sql_select = "SELECT b.*, k.nama_kategori, p.nama_pemasok FROM tbl_barang b LEFT JOIN tbl_kategori k ON b.id_kategori = k.id_kategori LEFT JOIN tbl_pemasok p ON b.id_pemasok = p.id_pemasok ORDER BY b.id_barang DESC";
$result = mysqli_query($conn, $sql_select);
$data_barang = [];
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $data_barang[] = $row;
    }
}

// AMBIL DATA KATEGORI & PEMASOK UNTUK DROPDOWN
$data_kategori = mysqli_fetch_all(mysqli_query($conn, "SELECT * FROM tbl_kategori ORDER BY nama_kategori ASC"), MYSQLI_ASSOC);
$data_pemasok = mysqli_fetch_all(mysqli_query($conn, "SELECT * FROM tbl_pemasok ORDER BY nama_pemasok ASC"), MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Barang</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style> body { font-family: 'Inter', sans-serif; } .nav-active { background-color: #4f46e5; color: white; } </style>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-100">

    <?php include 'navigasi.php'; // Panggil navigasi terpusat ?>

    <div class="container mx-auto p-4 sm:p-6 lg:p-8 pt-0">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Manajemen Barang Toko</h1>

        <div class="bg-white p-6 rounded-lg shadow-md mb-8">
            <h2 class="text-xl font-semibold mb-4"><?php echo $is_edit ? 'Edit Barang' : 'Tambah Barang Baru'; ?></h2>
            <form action="index.php" method="POST">
                <input type="hidden" name="id_barang" value="<?php echo htmlspecialchars($edit_data['id_barang']); ?>">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="kode_barang" class="block text-sm font-medium text-gray-700">Kode Barang (Barcode)</label>
                        <input type="text" id="kode_barang" name="kode_barang" value="<?php echo htmlspecialchars($edit_data['kode_barang']); ?>" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" required>
                    </div>
                    <div>
                        <label for="nama_barang" class="block text-sm font-medium text-gray-700">Nama Barang</label>
                        <input type="text" id="nama_barang" name="nama_barang" value="<?php echo htmlspecialchars($edit_data['nama_barang']); ?>" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" required>
                    </div>
                    <div>
                        <label for="satuan" class="block text-sm font-medium text-gray-700">Satuan (pcs/kg/botol)</label>
                        <input type="text" id="satuan" name="satuan" value="<?php echo htmlspecialchars($edit_data['satuan']); ?>" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" required>
                    </div>
                    <div>
                        <label for="stok" class="block text-sm font-medium text-gray-700">Stok</label>
                        <input type="number" id="stok" name="stok" value="<?php echo htmlspecialchars($edit_data['stok']); ?>" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" required>
                    </div>
                    <div>
                        <label for="harga_beli" class="block text-sm font-medium text-gray-700">Harga Beli (Modal)</label>
                        <input type="number" step="1" id="harga_beli" name="harga_beli" value="<?php echo htmlspecialchars($edit_data['harga_beli']); ?>" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" required>
                    </div>
                    <div>
                        <label for="harga_jual" class="block text-sm font-medium text-gray-700">Harga Jual</label>
                        <input type="number" step="1" id="harga_jual" name="harga_jual" value="<?php echo htmlspecialchars($edit_data['harga_jual']); ?>" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" required>
                    </div>
                    <div>
                        <label for="id_kategori" class="block text-sm font-medium text-gray-700">Kategori Barang</label>
                        <select id="id_kategori" name="id_kategori" class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm">
                            <option value="">-- Pilih Kategori --</option>
                            <?php foreach ($data_kategori as $kategori): ?>
                                <option value="<?php echo $kategori['id_kategori']; ?>" <?php echo ($edit_data['id_kategori'] == $kategori['id_kategori']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($kategori['nama_kategori']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="id_pemasok" class="block text-sm font-medium text-gray-700">Pemasok</label>
                        <select id="id_pemasok" name="id_pemasok" class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm">
                            <option value="">-- Pilih Pemasok --</option>
                            <?php foreach ($data_pemasok as $pemasok): ?>
                                <option value="<?php echo $pemasok['id_pemasok']; ?>" <?php echo ($edit_data['id_pemasok'] == $pemasok['id_pemasok']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($pemasok['nama_pemasok']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end gap-x-4">
                    <?php if ($is_edit): ?>
                        <a href="index.php" class="text-sm font-semibold leading-6 text-gray-900">Batal</a>
                    <?php endif; ?>
                    <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                        <?php echo $is_edit ? 'Update Barang' : 'Simpan Barang'; ?>
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-lg shadow-md overflow-x-auto">
            <table class="min-w-full leading-normal">
                <thead>
                    <tr class="border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase">
                        <th class="px-5 py-3">Kode</th>
                        <th class="px-5 py-3">Nama Barang</th>
                        <th class="px-5 py-3">Kategori</th>
                        <th class="px-5 py-3">Stok</th>
                        <th class="px-5 py-3">Harga Jual</th>
                        <th class="px-5 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data_barang)): ?>
                        <?php foreach ($data_barang as $barang): ?>
                            <tr class="hover:bg-gray-50 border-b border-gray-200">
                                <td class="px-5 py-4 text-sm bg-white"><?php echo htmlspecialchars($barang['kode_barang']); ?></td>
                                <td class="px-5 py-4 text-sm bg-white font-medium text-gray-900"><?php echo htmlspecialchars($barang['nama_barang']); ?></td>
                                <td class="px-5 py-4 text-sm bg-white"><?php echo htmlspecialchars($barang['nama_kategori'] ?? 'N/A'); ?></td>
                                <td class="px-5 py-4 text-sm bg-white"><?php echo htmlspecialchars($barang['stok'] . ' ' . $barang['satuan']); ?></td>
                                <td class="px-5 py-4 text-sm bg-white">Rp <?php echo number_format($barang['harga_jual'], 0, ',', '.'); ?></td>
                                <td class="px-5 py-4 text-sm bg-white whitespace-nowrap">
                                    <a href="?edit_id=<?php echo $barang['id_barang']; ?>" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                    <a href="?hapus_id=<?php echo $barang['id_barang']; ?>" class="text-red-600 hover:text-red-900" onclick="return confirm('Yakin nih mau hapus barang ini?')">Hapus</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-10 text-gray-500">Belum ada barang, Mar. Tambahin dulu gih.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>
<?php mysqli_close($conn); ?>
