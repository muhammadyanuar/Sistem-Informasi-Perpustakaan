<?php
include 'auth.php'; // Sertakan file auth.php untuk memeriksa login

check_admin(); // Pastikan hanya admin yang dapat mengakses halaman ini

include 'koneksi.php'; // Sertakan file koneksi ke database

$message = '';

// Fungsi untuk menambah pengguna baru
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['nama']) && isset($_POST['prodi']) && isset($_POST['nim']) && isset($_POST['telepon'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $nama = $_POST['nama'];
        $prodi = $_POST['prodi'];
        $nim = $_POST['nim'];
        $telepon = $_POST['telepon'];

        $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Hash password untuk keamanan

        try {
            // Query untuk memeriksa apakah username sudah ada
            $check_query = "SELECT COUNT(*) FROM user WHERE username = :username";
            $check_stmt = $conn->prepare($check_query); // Persiapkan statement SQL
            $check_stmt->bindParam(':username', $username); // Binding parameter username ke statement
            $check_stmt->execute(); // Eksekusi query
            $count = $check_stmt->fetchColumn(); // Ambil hasil query (jumlah username yang ditemukan)

            if ($count > 0) { // Jika username sudah ada
                $message = "Username sudah terdaftar, silakan gunakan username lain."; // Pesan kesalahan
            } else {
                // Query untuk memasukkan pengguna baru ke dalam database
                $insert_query = "INSERT INTO user (username, password, nama, prodi, nim, telepon) VALUES (:username, :password, :nama, :prodi, :nim, :telepon)";
                $stmt = $conn->prepare($insert_query); // Persiapkan statement SQL
                $stmt->bindParam(':username', $username); // Binding parameter username ke statement
                $stmt->bindParam(':password', $hashed_password); // Binding parameter password yang sudah dihash ke statement
                $stmt->bindParam(':nama', $nama); // Binding parameter nama ke statement
                $stmt->bindParam(':prodi', $prodi); // Binding parameter prodi ke statement
                $stmt->bindParam(':nim', $nim); // Binding parameter nim ke statement
                $stmt->bindParam(':telepon', $telepon); // Binding parameter telepon ke statement

                if ($stmt->execute()) { // Jika eksekusi berhasil
                    $message = "Pengguna baru berhasil ditambahkan."; // Pesan sukses
                } else {
                    $message = "Terjadi kesalahan saat menambahkan pengguna."; // Pesan kesalahan
                }
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage(); // Tangkap dan tampilkan pesan error jika terjadi kesalahan PDO
        }
    } else {
        $message = "Data pengguna tidak lengkap"; // Pesan kesalahan jika data pengguna tidak lengkap
    }
}

// Query untuk mendapatkan daftar pengguna
$sql = "SELECT id, username FROM user";
$stmt = $conn->prepare($sql);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Sistem Informasi Perpustakaan</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        /* CSS Styling */
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
        }
        form {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #007bff;
            color: #fff;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .message {
            margin-bottom: 10px;
            padding: 10px;
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            border-radius: 3px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Manage Users - Sistem Informasi Perpustakaan</h2>

        <!-- Tabel untuk menampilkan data pengguna -->
        <h3>Daftar Pengguna</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
