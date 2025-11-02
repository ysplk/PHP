<?php
session_start();
// Cek sesi login, kalo belom login, tendang ke halaman login
if (!isset($_SESSION['id_pengguna'])) {
    header("Location: login.php");
    exit;
}

require 'database.php'; // Panggil file koneksi terpusat

// Inisialisasi
$is_edit = false;
$edit_data = ['id_pemasok' => '', 'nama_pemasok' => '', 'no_telepon' => '', 'alamat' => ''];

// PROSES TAMBAH & UPDATE
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_pemasok = $_POST['id_pemasok'] ?? null;
    $nama_pemasok = mysqli_real_escape_string($conn, $_POST['nama_pemasok']);
    $no_telepon = mysqli_real_escape_string($conn, $_POST['no_telepon']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);

    if ($id_pemasok) {
        $sql = "UPDATE tbl_pemasok SET nama_pemasok = '$nama_pemasok', no_telepon = '$no_telepon', alamat = '$alamat' WHERE id_pemasok = $id_pemasok";
    } else {
        $sql = "INSERT INTO tbl_pemasok (nama_pemasok, no_telepon, alamat) VALUES ('$nama_pemasok', '$no_telepon', '$alamat')";
    }
    
    if (mysqli_query($conn, $sql)) {
        header("Location: pemasok.php");
        exit;
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// PROSES HAPUS
if (isset($_GET['hapus_id'])) {
    $id_hapus = (int)$_GET['hapus_id'];
    $sql_hapus = "DELETE FROM tbl_pemasok WHERE id_pemasok = $id_hapus";
    if (mysqli_query($conn, $sql_hapus)) {
        header("Location: pemasok.php");
        exit;
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// AMBIL DATA UNTUK EDIT
if (isset($_GET['edit_id'])) {
    $is_edit = true;
    $id_edit = (int)$_GET['edit_id'];
    $sql_edit = "SELECT * FROM tbl_pemasok WHERE id_pemasok = $id_edit";
    $result_edit = mysqli_query($conn, $sql_edit);
    $edit_data = mysqli_fetch_assoc($result_edit);
}

// AMBIL SEMUA DATA PEMASOK
$result = mysqli_query($conn, "SELECT * FROM tbl_pemasok ORDER BY id_pemasok DESC");
$data_pemasok = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Pemasok</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style> body { font-family: 'Inter', sans-serif; } .nav-active { background-color: #4f46e5; color: white; } </style>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-100">

    <?php include 'navigasi.php'; ?>

    <div class="container mx-auto p-4 sm:p-6 lg:p-8 pt-0">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Manajemen Pemasok</h1>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="md:col-span-1">
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-xl font-semibold mb-4"><?php echo $is_edit ? 'Edit Pemasok' : 'Tambah Pemasok'; ?></h2>
                    <form action="pemasok.php" method="POST">
                        <input type="hidden" name="id_pemasok" value="<?php echo htmlspecialchars($edit_data['id_pemasok']); ?>">
                        <div class="space-y-4">
                            <div>
                                <label for="nama_pemasok" class="block text-sm font-medium text-gray-700">Nama Pemasok</label>
                                <input type="text" id="nama_pemasok" name="nama_pemasok" value="<?php echo htmlspecialchars($edit_data['nama_pemasok']); ?>" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" required>
                            </div>
                            <div>
                                <label for="no_telepon" class="block text-sm font-medium text-gray-700">No. Telepon</label>
                                <input type="text" id="no_telepon" name="no_telepon" value="<?php echo htmlspecialchars($edit_data['no_telepon']); ?>" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm">
                            </div>
                            <div>
                                <label for="alamat" class="block text-sm font-medium text-gray-700">Alamat</label>
                                <textarea id="alamat" name="alamat" rows="3" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm"><?php echo htmlspecialchars($edit_data['alamat']); ?></textarea>
                            </div>
                        </div>
                        <div class="mt-6 flex items-center justify-end gap-x-4">
                             <?php if ($is_edit): ?>
                                <a href="pemasok.php" class="text-sm font-semibold text-gray-900">Batal</a>
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
                    <table class="min-w-full leading-normal">
                        <thead>
                            <tr class="border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase">
                                <th class="px-5 py-3">Nama Pemasok</th>
                                <th class="px-5 py-3">Telepon</th>
                                <th class="px-5 py-3">Alamat</th>
                                <th class="px-5 py-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($data_pemasok)): ?>
                                <?php foreach ($data_pemasok as $pemasok): ?>
                                <tr class="hover:bg-gray-50 border-b border-gray-200">
                                    <td class="px-5 py-4 text-sm bg-white font-medium text-gray-900"><?php echo htmlspecialchars($pemasok['nama_pemasok']); ?></td>
                                    <td class="px-5 py-4 text-sm bg-white"><?php echo htmlspecialchars($pemasok['no_telepon']); ?></td>
                                    <td class="px-5 py-4 text-sm bg-white"><?php echo htmlspecialchars($pemasok['alamat']); ?></td>
                                    <td class="px-5 py-4 text-sm bg-white whitespace-nowrap">
                                        <a href="?edit_id=<?php echo $pemasok['id_pemasok']; ?>" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                        <a href="?hapus_id=<?php echo $pemasok['id_pemasok']; ?>" class="text-red-600 hover:text-red-900" onclick="return confirm('Yakin mau hapus pemasok ini?')">Hapus</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center py-10 text-gray-500">Belum ada data pemasok.</td>
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
