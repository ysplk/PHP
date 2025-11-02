<?php
// Dapetin nama file yang lagi dibuka
$current_page = basename($_SERVER['PHP_SELF']);
?>
<nav class="bg-white shadow-md sticky top-0 z-50">
    <div class="container mx-auto px-8">
        <div class="flex items-center justify-between h-16">
            <!-- Menu Kiri -->
            <div class="flex items-center space-x-4">
                <a href="index.php" class="px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-indigo-500 hover:text-white <?php if ($current_page == 'index.php') echo 'nav-active'; ?>">Manajemen Barang</a>
                <a href="kategori.php" class="px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-indigo-500 hover:text-white <?php if ($current_page == 'kategori.php') echo 'nav-active'; ?>">Manajemen Kategori</a>
                <a href="pemasok.php" class="px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-indigo-500 hover:text-white <?php if ($current_page == 'pemasok.php') echo 'nav-active'; ?>">Manajemen Pemasok</a>
                <a href="pelanggan.php" class="px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-indigo-500 hover:text-white <?php if ($current_page == 'pelanggan.php') echo 'nav-active'; ?>">Manajemen Pelanggan</a>
                <?php if ($_SESSION['role'] == 'admin'): ?>
                <a href="pengguna.php" class="px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-indigo-500 hover:text-white <?php if ($current_page == 'pengguna.php') echo 'nav-active'; ?>">Manajemen Pengguna</a>
                <?php endif; ?>
            </div>
            <!-- Menu Kanan -->
            <div class="flex items-center">
                 <span class="text-gray-700 text-sm mr-4">Halo, <b><?php echo htmlspecialchars($_SESSION['username']); ?></b> (<?php echo htmlspecialchars($_SESSION['role']); ?>)</span>
                <a href="logout.php" class="px-3 py-2 rounded-md text-sm font-medium text-white bg-red-600 hover:bg-red-700">Logout</a>
            </div>
        </div>
    </div>
</nav>
