<?php
// ===================================================================
// BAGIAN 1: LOGIKA PHP
// ===================================================================
$host = 'localhost';
$db_name = 'belajar_php';
$username = 'root';
$password = '123'; // JANGAN LUPA GANTI INI!

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("ERROR: Gagal konek, anjir. " . $e->getMessage());
}

$pesan_sukses = '';
$pesan_error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['nama']) && !empty($_POST['email'])) {
        try {
            $sql_cek_email = "SELECT COUNT(*) FROM users WHERE email = :email";
            $stmt_cek = $pdo->prepare($sql_cek_email);
            $stmt_cek->bindParam(':email', $_POST['email']);
            $stmt_cek->execute();
            $jumlah_email = $stmt_cek->fetchColumn();

            if ($jumlah_email > 0) {
                $pesan_error = "Anjir, email '" . htmlspecialchars($_POST['email']) . "' udah ada yang pake. Ganti yang laen!";
            } else {
                $sql_insert = "INSERT INTO users (nama, email) VALUES (:nama, :email)";
                $stmt_insert = $pdo->prepare($sql_insert);
                $stmt_insert->bindParam(':nama', $_POST['nama']);
                $stmt_insert->bindParam(':email', $_POST['email']);
                $stmt_insert->execute();
                $pesan_sukses = "User baru '" . htmlspecialchars($_POST['nama']) . "' berhasil ditambahin!";
            }
        } catch (PDOException $e) {
            die("ERROR: Ada masalah sama database. " . $e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplikasi CRUD Ganteng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">

                <h1 class="mb-4 text-center">Form Tambah User Baru</h1>

                <?php
                if (!empty($pesan_sukses)) {
                    echo "<div class='alert alert-success'>$pesan_sukses</div>";
                } 
                if (!empty($pesan_error)) {
                    echo "<div class='alert alert-danger'>$pesan_error</div>";
                }
                ?>

                <div class="card bg-dark text-light border-secondary">
                    <div class="card-body">
                        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama:</label>
                                <input type="text" class="form-control" id="nama" name="nama" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email:</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Tambah User</button>
                        </form>
                    </div>
                </div>

                <hr class="my-5">

                <h2 class="mb-4">Daftar User yang Udah Ada</h2>

                <ul class="list-group">
                    <?php
                    $sql_select = "SELECT * FROM users ORDER BY id DESC";
                    $stmt_select = $pdo->query($sql_select);

                    while ($row = $stmt_select->fetch(PDO::FETCH_ASSOC)) {
                        echo "<li class='list-group-item d-flex justify-content-between align-items-center bg-dark text-light border-secondary'>";
                        echo htmlspecialchars($row['nama']);
                        echo "<span class='badge bg-primary rounded-pill'>" . htmlspecialchars($row['email']) . "</span>";
                        echo "</li>";
                    }
                    ?>
                </ul>

            </div>
        </div>
    </div>

</body>
</html>