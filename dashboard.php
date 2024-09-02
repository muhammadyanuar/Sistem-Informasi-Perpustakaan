<?php
include 'koneksi.php';
include 'auth.php';
check_login(); // Memeriksa apakah pengguna sudah login
check_admin(); // Memeriksa apakah pengguna adalah admin

// Query untuk menghitung jumlah anggota
$sql_count_members = "SELECT COUNT(*) FROM member";
$stmt_count_members = $conn->query($sql_count_members);
$count_members = $stmt_count_members->fetchColumn();

// Query untuk menghitung jumlah buku
$sql_count_books = "SELECT COUNT(*) FROM book";
$stmt_count_books = $conn->query($sql_count_books);
$count_books = $stmt_count_books->fetchColumn();

// Query untuk menghitung jumlah pengguna
$sql_count_users = "SELECT COUNT(*) FROM user";
$stmt_count_users = $conn->query($sql_count_users);
$count_users = $stmt_count_users->fetchColumn();

// Query untuk menghitung jumlah transaksi
$sql_count_transactions = "SELECT COUNT(*) FROM transaction";
$stmt_count_transactions = $conn->query($sql_count_transactions);
$count_transactions = $stmt_count_transactions->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistem Informasi Perpustakaan</title>
    <link rel="stylesheet" href="css/styles.css"> <!-- Sesuaikan dengan path CSS Anda -->
    <style>
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

        .menu {
            display: flex;
            justify-content: space-around;
            margin-top: 20px;
        }

        .menu a {
            display: block;
            text-align: center;
            text-decoration: none;
            color: #333;
            padding: 10px 20px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .menu a:hover {
            background-color: #007bff;
            color: #fff;
        }

        .dashboard-content {
            margin-top: 20px;
        }

        .stats {
            margin-top: 20px;
        }

        .stats p {
            margin-bottom: 10px;
        }

        .logout {
            text-align: center;
            margin-top: 20px;
        }

        .logout a {
            color: #dc3545;
            text-decoration: none;
        }

        .logout a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Dashboard - Sistem Informasi Perpustakaan</h2>

        <!-- Menu -->
        <div class="menu">
            <a href="manage_books.php">Kelola Buku</a>
            <a href="manage_members.php">Kelola Anggota</a>
            <a href="manage_users.php">Kelola Pengguna</a>
            <a href="manage_transactions.php">Kelola Transaksi</a>
        </div>

        <!-- Konten Dashboard -->
        <div class="dashboard-content">
            <p>Selamat datang, <?php echo $_SESSION['username']; ?>!</p>

            <!-- Statistik -->
            <div class="stats">
                <p>Jumlah Anggota: <?php echo $count_members; ?></p>
                <p>Jumlah Buku: <?php echo $count_books; ?></p>
                <p>Jumlah Pengguna: <?php echo $count_users; ?></p>
                <p>Jumlah Transaksi: <?php echo $count_transactions; ?></p>
            </div>
        </div>

        <!-- Logout -->
        <div class="logout">
            <a href="logout.php">Logout</a>
        </div>
    </div>
</body>
</html>
