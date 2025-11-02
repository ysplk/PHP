<?php
session_start();
// Cek sesi login, kalo belom login, tendang ke halaman login
if (!isset($_SESSION['id_pengguna'])) {
    header("Location: login.php");
    exit;
}

require 'database.php'; // Panggil file koneksi terpusat

// Ambil pesan notifikasi dari session, terus langsung hapus
$message = $_SESSION['message'] ?? null;
unset($_SESSION['message']);

// Inisialisasi
$is_edit = false;
$edit_data = ['id_pelanggan' => '', 'nama_pelanggan' => '', 'no_telepon' => '', 'alamat' => '', 'total_utang' => ''];

// PROSES TAMBAH & UPDATE
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_pelanggan = $_POST['id_pelanggan'] ?? null;
    $nama_pelanggan = mysqli_real_escape_string($conn, $_POST['nama_pelanggan']);
    $no_telepon = mysqli_real_escape_string($conn, $_POST['no_telepon']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $total_utang = (float)($_POST['total_utang'] ?? 0);

    // Cek duplikat nama pelanggan
    $sql_cek = $id_pelanggan ? 
        "SELECT id_pelanggan FROM tbl_pelanggan WHERE nama_pelanggan = '$nama_pelanggan' AND id_pelanggan != $id_pelanggan" : 
        "SELECT id_pelanggan FROM tbl_pelanggan WHERE nama_pelanggan = '$nama_pelanggan'";
    
    $result_cek = mysqli_query($conn, $sql_cek);

    if (mysqli_num_rows($result_cek) > 0) {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Gagal! Nama pelanggan "' . htmlspecialchars($nama_pelanggan) . '" udah ada.'];
    } else {
        if ($id_pelanggan) {
            $sql = "UPDATE tbl_pelanggan SET nama_pelanggan = '$nama_pelanggan', no_telepon = '$no_telepon', alamat = '$alamat', total_utang = $total_utang WHERE id_pelanggan = $id_pelanggan";
            $pesan_sukses = 'Data pelanggan berhasil diupdate.';
        } else {
            $sql = "INSERT INTO tbl_pelanggan (nama_pelanggan, no_telepon, alamat, total_utang) VALUES ('$nama_pelanggan', '$no_telepon', '$alamat', $total_utang)";
            $pesan_sukses = 'Pelanggan baru berhasil ditambah.';
        }

        if (mysqli_query($conn, $sql)) {
            $_SESSION['message'] = ['type' => 'success', 'text' => $pesan_sukses];
        } else {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Error: ' . mysqli_error($conn)];
        }
    }
    header("Location: pelanggan.php");
    exit;
}

// PROSES HAPUS
if (isset($_GET['hapus_id'])) {
    $id_hapus = (int)$_GET['hapus_id'];
    $sql_hapus = "DELETE FROM tbl_pelanggan WHERE id_pelanggan = $id_hapus";
    if (mysqli_query($conn, $sql_hapus)) {
        $_SESSION['message'] = ['type' => 'success', 'text' => 'Pelanggan berhasil dihapus.'];
    } else {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Gagal menghapus pelanggan: ' . mysqli_error($conn)];
    }
    header("Location: pelanggan.php");
    exit;
}

// AMBIL DATA UNTUK EDIT
if (isset($_GET['edit_id'])) {
    $is_edit = true;
    $id_edit = (int)$_GET['edit_id'];
    $edit_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tbl_pelanggan WHERE id_pelanggan = $id_edit"));
}

// AMBIL SEMUA DATA PELANGGAN DENGAN FITUR SEARCH
$search_query = $_GET['search'] ?? '';
$sql_select = "SELECT * FROM tbl_pelanggan";
if (!empty($search_query)) {
    $search_safe = mysqli_real_escape_string($conn, $search_query);
    $sql_select .= " WHERE nama_pelanggan LIKE '%$search_safe%' OR no_telepon LIKE '%$search_safe%'";
}
$sql_select .= " ORDER BY id_pelanggan DESC";
$result = mysqli_query($conn, $sql_select);
$data_pelanggan = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Pelanggan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style> body { font-family: 'Inter', sans-serif; } </style>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-100">

    <?php include 'navigasi.php'; ?>

    <div class="container mx-auto p-4 sm:p-6 lg:p-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Manajemen Pelanggan</h1>

        <?php if ($message): ?>
        <div class="mb-6 p-4 rounded-md <?php echo $message['type'] == 'success' ? 'bg-green-100 border border-green-200 text-green-800' : 'bg-red-100 border border-red-200 text-red-800'; ?>" role="alert">
            <?php echo htmlspecialchars($message['text']); ?>
        </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="md:col-span-1">
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-xl font-semibold mb-4"><?php echo $is_edit ? 'Edit Pelanggan' : 'Tambah Pelanggan'; ?></h2>
                    <form action="pelanggan.php" method="POST">
                        <input type="hidden" name="id_pelanggan" value="<?php echo htmlspecialchars($edit_data['id_pelanggan']); ?>">
                        <div class="space-y-4">
                            <div>
                                <label for="nama_pelanggan" class="block text-sm font-medium text-gray-700">Nama Pelanggan</label>
                                <input type="text" id="nama_pelanggan" name="nama_pelanggan" value="<?php echo htmlspecialchars($edit_data['nama_pelanggan']); ?>" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" required>
                            </div>
                            <div>
                                <label for="no_telepon" class="block text-sm font-medium text-gray-700">No. Telepon</label>
                                <input type="text" id="no_telepon" name="no_telepon" value="<?php echo htmlspecialchars($edit_data['no_telepon']); ?>" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm">
                            </div>
                            <div>
                                <label for="alamat" class="block text-sm font-medium text-gray-700">Alamat</label>
                                <textarea id="alamat" name="alamat" rows="3" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm"><?php echo htmlspecialchars($edit_data['alamat']); ?></textarea>
                            </div>
                            <div>
                                <label for="total_utang" class="block text-sm font-medium text-gray-700">Total Utang</label>
                                <input type="number" step="0.01" id="total_utang" name="total_utang" value="<?php echo htmlspecialchars($edit_data['total_utang'] ?? 0); ?>" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm">
                                <p class="mt-1 text-xs text-gray-500">Isi dengan 0 jika tidak ada utang.</p>
                            </div>
                        </div>
                        <div class="mt-6 flex items-center justify-end gap-x-4">
                             <?php if ($is_edit): ?>
                                <a href="pelanggan.php" class="text-sm font-semibold text-gray-900">Batal</a>
                            <?php endif; ?>
                            <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                                <?php echo $is_edit ? 'Update' : 'Simpan'; ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="md:col-span-2">
                <div class="bg-white rounded-lg shadow-md overflow-x-auto">
                    <div class="p-4">
                        <form action="pelanggan.php" method="GET" class="flex">
                            <input type="text" name="search" placeholder="Cari nama atau telepon..." class="w-full px-3 py-2 border border-gray-300 rounded-l-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" value="<?php echo htmlspecialchars($search_query); ?>">
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white font-semibold rounded-r-md hover:bg-indigo-700">Cari</button>
                        </form>
                    </div>
                    <table class="min-w-full leading-normal">
                        <thead>
                            <tr class="border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase">
                                <th class="px-5 py-3">Nama Pelanggan</th>
                                <th class="px-5 py-3">Telepon</th>
                                <th class="px-5 py-3">Alamat</th>
                                <th class="px-5 py-3">Total Utang</th>
                                <th class="px-5 py-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($data_pelanggan)): ?>
                                <?php foreach ($data_pelanggan as $pelanggan): ?>
                                <tr class="hover:bg-gray-50 border-b border-gray-200">
                                    <td class="px-5 py-4 text-sm bg-white font-medium text-gray-900"><?php echo htmlspecialchars($pelanggan['nama_pelanggan']); ?></td>
                                    <td class="px-5 py-4 text-sm bg-white"><?php echo htmlspecialchars($pelanggan['no_telepon']); ?></td>
                                    <td class="px-5 py-4 text-sm bg-white"><?php echo htmlspecialchars($pelanggan['alamat']); ?></td>
                                    <td class="px-5 py-4 text-sm bg-white font-semibold <?php echo ($pelanggan['total_utang'] > 0) ? 'text-red-600' : 'text-green-600'; ?>">
                                        Rp <?php echo number_format($pelanggan['total_utang'], 0, ',', '.'); ?>
                                    </td>
                                    <td class="px-5 py-4 text-sm bg-white whitespace-nowrap">
                                        <a href="?edit_id=<?php echo $pelanggan['id_pelanggan']; ?>&search=<?php echo urlencode($search_query); ?>" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                        <a href="?hapus_id=<?php echo $pelanggan['id_pelanggan']; ?>&search=<?php echo urlencode($search_query); ?>" class="text-red-600 hover:text-red-900" onclick="return confirm('Yakin mau hapus pelanggan ini?')">Hapus</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-10 text-gray-500">
                                        <?php if (!empty($search_query)): ?>
                                            Pelanggan "<?php echo htmlspecialchars($search_query); ?>" tidak ditemukan.
                                        <?php else: ?>
                                            Belum ada data pelanggan.
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
<?php mysqli_close($conn); ?>

