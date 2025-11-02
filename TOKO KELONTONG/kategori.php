<?php
session_start();
// Cek sesi login, kalo belom login, tendang ke halaman login
if (!isset($_SESSION['id_pengguna'])) {
    header("Location: login.php");
    exit;
}

// Ambil pesan notifikasi dari session, terus langsung hapus
$message = $_SESSION['message'] ?? null;
unset($_SESSION['message']);

require 'database.php'; // Panggil file koneksi terpusat

// Inisialisasi
$is_edit = false;
$edit_data = ['id_kategori' => '', 'nama_kategori' => ''];

// PROSES TAMBAH & UPDATE
// PROSES TAMBAH & UPDATE
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_kategori = $_POST['id_kategori'] ?? null;
    $nama_kategori = mysqli_real_escape_string($conn, $_POST['nama_kategori']);

    // INI BAGIAN PENTINGNYA: CEK DULU SEBELUM EKSEKUSI
    $sql_cek = $id_kategori ?
        "SELECT id_kategori FROM tbl_kategori WHERE nama_kategori = '$nama_kategori' AND id_kategori != $id_kategori" :
        "SELECT id_kategori FROM tbl_kategori WHERE nama_kategori = '$nama_kategori'";

    $result_cek = mysqli_query($conn, $sql_cek);

    // KALO DITEMUKAN ADA YANG SAMA (DUPLIKAT)
    if (mysqli_num_rows($result_cek) > 0) {
        // JANGAN EKSEKUSI, TAPI KIRIM PESAN ERROR KE SESSION
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Gagal! Nama kategori "' . htmlspecialchars($nama_kategori) . '" udah ada.'];
    } else {
        // KALO AMAN, BARU PROSES INSERT ATAU UPDATE
        if ($id_kategori) {
            $sql = "UPDATE tbl_kategori SET nama_kategori = '$nama_kategori' WHERE id_kategori = $id_kategori";
            $pesan_sukses = 'Kategori berhasil diupdate.';
        } else {
            $sql = "INSERT INTO tbl_kategori (nama_kategori) VALUES ('$nama_kategori')";
            $pesan_sukses = 'Kategori baru berhasil ditambah.';
        }

        // Eksekusi query yang udah pasti aman
        if (mysqli_query($conn, $sql)) {
            $_SESSION['message'] = ['type' => 'success', 'text' => $pesan_sukses];
        } else {
            // Ini buat jaga-jaga kalo ada error laen
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Terjadi kesalahan pada database: ' . mysqli_error($conn)];
        }
    }

    // Apapun hasilnya (duplikat, sukses, atau gagal), balikin lagi ke halaman kategori
    header("Location: kategori.php");
    exit;
}

// PROSES HAPUS
if (isset($_GET['hapus_id'])) {
    $id_hapus = (int)$_GET['hapus_id'];
    $sql_hapus = "DELETE FROM tbl_kategori WHERE id_kategori = $id_hapus";
    if (mysqli_query($conn, $sql_hapus)) {
        header("Location: kategori.php");
        exit;
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// AMBIL DATA UNTUK EDIT
if (isset($_GET['edit_id'])) {
    $is_edit = true;
    $id_edit = (int)$_GET['edit_id'];
    $sql_edit = "SELECT * FROM tbl_kategori WHERE id_kategori = $id_edit";
    $result_edit = mysqli_query($conn, $sql_edit);
    $edit_data = mysqli_fetch_assoc($result_edit);
}

// AMBIL SEMUA DATA KATEGORI DENGAN FITUR SEARCH
$search_query = $_GET['search'] ?? '';
$sql_select = "SELECT * FROM tbl_kategori";
if (!empty($search_query)) {
    $search_safe = mysqli_real_escape_string($conn, $search_query);
    $sql_select .= " WHERE nama_kategori LIKE '%$search_safe%'";
}
$sql_select .= " ORDER BY id_kategori DESC";
$result = mysqli_query($conn, $sql_select);
$data_kategori = [];
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $data_kategori[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Kategori</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .nav-active {
            background-color: #4f46e5;
            color: white;
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>

<body class="bg-gray-100">

    <?php include 'navigasi.php'; ?>

    <!-- pesan erros duplikat -->
    <?php if ($message): ?>
        <div class="container mx-auto mt-4">
            <div class="p-4 rounded-md <?php echo $message['type'] == 'success' ? 'bg-green-100 border border-green-200 text-green-800' : 'bg-red-100 border border-red-200 text-red-800'; ?>" role="alert">
                <?php echo htmlspecialchars($message['text']); ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="container mx-auto p-4 sm:p-6 lg:p-8 pt-0">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Manajemen Kategori</h1>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="md:col-span-1">
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-xl font-semibold mb-4"><?php echo $is_edit ? 'Edit Kategori' : 'Tambah Kategori'; ?></h2>
                    <form action="kategori.php" method="POST">
                        <input type="hidden" name="id_kategori" value="<?php echo htmlspecialchars($edit_data['id_kategori']); ?>">
                        <div>
                            <label for="nama_kategori" class="block text-sm font-medium text-gray-700">Nama Kategori</label>
                            <input type="text" id="nama_kategori" name="nama_kategori" value="<?php echo htmlspecialchars($edit_data['nama_kategori']); ?>" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" required>
                        </div>
                        <div class="mt-6 flex items-center justify-end gap-x-4">
                            <?php if ($is_edit): ?>
                                <a href="kategori.php" class="text-sm font-semibold leading-6 text-gray-900">Batal</a>
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
                    <div class="p-4"> <!-- Gua tambahin p-4 biar ada napasnya, kaga nempel -->
                        <!-- Form Pencarian -->
                        <form action="kategori.php" method="GET" class="flex">
                            <input type="text" name="search" placeholder="Cari nama kategori..." class="w-full px-3 py-2 border border-gray-300 rounded-l-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" value="<?php echo htmlspecialchars($search_query ?? ''); ?>">
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white font-semibold rounded-r-md hover:bg-indigo-700">Cari</button>
                        </form>
                    </div>
                    <table class="min-w-full leading-normal">
                        <thead>
                            <tr class="border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase">
                                <th class="px-5 py-3">Nama Kategori</th>
                                <th class="px-5 py-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($data_kategori)): ?>
                                <?php foreach ($data_kategori as $kategori): ?>
                                    <tr class="hover:bg-gray-50 border-b border-gray-200">
                                        <td class="px-5 py-4 text-sm bg-white font-medium text-gray-900"><?php echo htmlspecialchars($kategori['nama_kategori']); ?></td>
                                        <td class="px-5 py-4 text-sm bg-white whitespace-nowrap">
                                            <a href="?edit_id=<?php echo $kategori['id_kategori']; ?>" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                            <a href="?hapus_id=<?php echo $kategori['id_kategori']; ?>" class="text-red-600 hover:text-red-900" onclick="return confirm('Yakin mau hapus kategori ini?')">Hapus</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="2" class="text-center py-10 text-gray-500">Belum ada kategori.</td>
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