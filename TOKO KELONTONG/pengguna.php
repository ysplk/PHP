<?php
session_start();
// Cek sesi login & role admin. Kalo bukan admin atau belom login, tendang!
if (!isset($_SESSION['id_pengguna']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit;
}

require 'database.php'; // Panggil file koneksi terpusat

// Inisialisasi
$is_edit = false;
$edit_data = ['id_pengguna' => '', 'nama_lengkap' => '', 'username' => '', 'password' => '', 'role' => 'kasir'];

// PROSES TAMBAH & UPDATE
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_pengguna = $_POST['id_pengguna'] ?? null;
    $nama_lengkap = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    if ($id_pengguna) {
        // Update data
        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql = "UPDATE tbl_pengguna SET nama_lengkap = '$nama_lengkap', username = '$username', password = '$hashed_password', role = '$role' WHERE id_pengguna = $id_pengguna";
        } else {
            // Jangan update password kalo kolomnya kosong
            $sql = "UPDATE tbl_pengguna SET nama_lengkap = '$nama_lengkap', username = '$username', role = '$role' WHERE id_pengguna = $id_pengguna";
        }
    } else {
        // Tambah data baru, password wajib di-hash
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO tbl_pengguna (nama_lengkap, username, password, role) VALUES ('$nama_lengkap', '$username', '$hashed_password', '$role')";
    }
    
    if (mysqli_query($conn, $sql)) {
        header("Location: pengguna.php");
        exit;
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// PROSES HAPUS
if (isset($_GET['hapus_id'])) {
    $id_hapus = (int)$_GET['hapus_id'];
    // Jangan biarkan admin menghapus dirinya sendiri
    if ($id_hapus != $_SESSION['id_pengguna']) {
        $sql_hapus = "DELETE FROM tbl_pengguna WHERE id_pengguna = $id_hapus";
        if (mysqli_query($conn, $sql_hapus)) {
            header("Location: pengguna.php");
            exit;
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }
}

// AMBIL DATA UNTUK EDIT
if (isset($_GET['edit_id'])) {
    $is_edit = true;
    $id_edit = (int)$_GET['edit_id'];
    $sql_edit = "SELECT * FROM tbl_pengguna WHERE id_pengguna = $id_edit";
    $result_edit = mysqli_query($conn, $sql_edit);
    $edit_data = mysqli_fetch_assoc($result_edit);
}

// AMBIL SEMUA DATA PENGGUNA
$result = mysqli_query($conn, "SELECT * FROM tbl_pengguna ORDER BY id_pengguna DESC");
$data_pengguna = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Pengguna</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style> body { font-family: 'Inter', sans-serif; } .nav-active { background-color: #4f46e5; color: white; } </style>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-100">

    <?php include 'navigasi.php'; ?>

    <div class="container mx-auto p-4 sm:p-6 lg:p-8 pt-0">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Manajemen Pengguna</h1>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="md:col-span-1">
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-xl font-semibold mb-4"><?php echo $is_edit ? 'Edit Pengguna' : 'Tambah Pengguna Baru'; ?></h2>
                    <form action="pengguna.php" method="POST">
                        <input type="hidden" name="id_pengguna" value="<?php echo htmlspecialchars($edit_data['id_pengguna']); ?>">
                        <div class="space-y-4">
                            <div>
                                <label for="nama_lengkap" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                                <input type="text" id="nama_lengkap" name="nama_lengkap" value="<?php echo htmlspecialchars($edit_data['nama_lengkap']); ?>" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" required>
                            </div>
                            <div>
                                <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($edit_data['username']); ?>" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" required>
                            </div>
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                                <input type="password" id="password" name="password" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" <?php if (!$is_edit) echo 'required'; ?>>
                                <?php if ($is_edit): ?>
                                    <p class="mt-1 text-xs text-gray-500">Kosongkan jika tidak ingin mengubah password.</p>
                                <?php endif; ?>
                            </div>
                             <div>
                                <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                                <select id="role" name="role" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" required>
                                    <option value="admin" <?php if($edit_data['role'] == 'admin') echo 'selected'; ?>>Admin</option>
                                    <option value="kasir" <?php if($edit_data['role'] == 'kasir') echo 'selected'; ?>>Kasir</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-6 flex items-center justify-end gap-x-4">
                             <?php if ($is_edit): ?>
                                <a href="pengguna.php" class="text-sm font-semibold text-gray-900">Batal</a>
                            <?php endif; ?>
                            <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                                <?php echo $is_edit ? 'Update Pengguna' : 'Simpan Pengguna'; ?>
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
                                <th class="px-5 py-3">Nama Lengkap</th>
                                <th class="px-5 py-3">Username</th>
                                <th class="px-5 py-3">Role</th>
                                <th class="px-5 py-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($data_pengguna)): ?>
                                <?php foreach ($data_pengguna as $pengguna): ?>
                                <tr class="hover:bg-gray-50 border-b border-gray-200">
                                    <td class="px-5 py-4 text-sm bg-white font-medium text-gray-900"><?php echo htmlspecialchars($pengguna['nama_lengkap']); ?></td>
                                    <td class="px-5 py-4 text-sm bg-white"><?php echo htmlspecialchars($pengguna['username']); ?></td>
                                    <td class="px-5 py-4 text-sm bg-white">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $pengguna['role'] == 'admin' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'; ?>">
                                            <?php echo htmlspecialchars(ucfirst($pengguna['role'])); ?>
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 text-sm bg-white whitespace-nowrap">
                                        <a href="?edit_id=<?php echo $pengguna['id_pengguna']; ?>" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                        <?php if ($pengguna['id_pengguna'] != $_SESSION['id_pengguna']): ?>
                                        <a href="?hapus_id=<?php echo $pengguna['id_pengguna']; ?>" class="text-red-600 hover:text-red-900" onclick="return confirm('Yakin mau hapus pengguna ini?')">Hapus</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center py-10 text-gray-500">Belum ada data pengguna.</td>
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
