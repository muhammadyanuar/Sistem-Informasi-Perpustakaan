<?php
include 'koneksi.php'; // Sertakan file koneksi ke database

$message = ''; // Variabel untuk menyimpan pesan kesalahan atau informasi

if ($_SERVER["REQUEST_METHOD"] == "POST") { // Periksa apakah form dikirimkan dengan metode POST
    if (isset($_POST['username']) && isset($_POST['password'])) { // Periksa apakah username dan password diisi
        $username = $_POST['username']; // Ambil username dari form
        $password = $_POST['password']; // Ambil password dari form

        // Hash password menggunakan password_hash
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        try {
            // Query untuk memeriksa apakah username sudah terdaftar
            $check_query = "SELECT COUNT(*) FROM user WHERE username = :username";
            $check_stmt = $conn->prepare($check_query); // Persiapkan statement SQL
            $check_stmt->bindParam(':username', $username); // Binding parameter username ke statement
            $check_stmt->execute(); // Eksekusi query
            $count = $check_stmt->fetchColumn(); // Ambil hasil query (jumlah username yang ditemukan)

            if ($count > 0) { // Jika username sudah terdaftar
                $message = "Username sudah terdaftar, silakan gunakan username lain."; // Pesan kesalahan
            } else {
                // Query untuk memasukkan user baru ke dalam database
                $insert_query = "INSERT INTO user (username, password) VALUES (:username, :password)";
                $stmt = $conn->prepare($insert_query); // Persiapkan statement SQL
                $stmt->bindParam(':username', $username); // Binding parameter username ke statement
                $stmt->bindParam(':password', $hashed_password); // Binding parameter password yang sudah dihash ke statement

                if ($stmt->execute()) { // Jika eksekusi berhasil
                    // Redirect ke halaman login jika registrasi berhasil
                    header("Location: index.php");
                    exit(); // Keluar dari skrip PHP
                } else {
                    $message = "Registrasi gagal"; // Pesan kesalahan jika eksekusi gagal
                }
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage(); // Tangkap dan tampilkan pesan error jika terjadi kesalahan PDO
        }
    } else {
        $message = "Data registrasi tidak lengkap"; // Pesan kesalahan jika data registrasi tidak lengkap
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Informasi Perpustakaan - Registrasi</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>


    <div class="container">
        <h2>Sistem Informasi Perpustakaan</h2>

        <h3>Registrasi</h3>
        <?php if (!empty($message)): ?>
            <p><?php echo $message; ?></p>
        <?php endif; ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <label for="username">Username:</label><br>
            <input type="text" id="username" name="username" required><br>
            <label for="password">Password:</label><br>
            <input type="password" id="password" name="password" required><br><br>
            <input type="submit" value="Register">
        </form>

        <div class="register-link">
            Sudah punya akun? <a href="index.php">Login disini</a>
        </div>
    </div>
</body>
</html>
