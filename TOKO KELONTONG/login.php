<?php
session_start();
// Kalo udah login, tendang ke halaman utama
if (isset($_SESSION['id_pengguna'])) {
    header("Location: index.php");
    exit;
}

require 'database.php'; // Pastikan lu punya file koneksi.php

$error_msg = '';

// PROSES LOGIN
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password']; // Gak perlu di-escape karena gak langsung masuk query

    if (empty($username) || empty($password)) {
        $error_msg = 'Username dan Password tidak boleh kosong!';
    } else {
        $sql = "SELECT id_pengguna, username, password, role FROM tbl_pengguna WHERE username = '$username'";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) == 1) {
            $user = mysqli_fetch_assoc($result);
            
            // INI BAGIAN PENTINGNYA: Pake password_verify() buat ngecek
            if (password_verify($password, $user['password'])) {
                // Login berhasil, simpan data ke session
                $_SESSION['id_pengguna'] = $user['id_pengguna'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                header("Location: index.php"); // Arahkan ke halaman utama
                exit;
            } else {
                $error_msg = 'Password yang Anda masukkan salah!';
            }
        } else {
            $error_msg = 'Username tidak ditemukan!';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Aplikasi Kasir Toko Kelontong</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style> body { font-family: 'Inter', sans-serif; } </style>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="flex min-h-screen items-center justify-center">
        <div class="w-full max-w-md p-8 space-y-6 bg-white rounded-lg shadow-md">
            <div>
                <h2 class="text-center text-3xl font-bold tracking-tight text-gray-900">
                    Login ke Toko
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Masukkan username dan password Anda
                </p>
            </div>

            <?php if (!empty($error_msg)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline"><?php echo $error_msg; ?></span>
            </div>
            <?php endif; ?>

            <form class="space-y-6" action="login.php" method="POST">
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                    <div class="mt-1">
                        <input id="username" name="username" type="text" required class="block w-full appearance-none rounded-md border border-gray-300 px-3 py-2 placeholder-gray-400 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm">
                    </div>
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <div class="mt-1">
                        <input id="password" name="password" type="password" required class="block w-full appearance-none rounded-md border border-gray-300 px-3 py-2 placeholder-gray-400 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm">
                    </div>
                </div>
                <div>
                    <button type="submit" class="flex w-full justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        Masuk
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
<?php mysqli_close($conn); ?>

