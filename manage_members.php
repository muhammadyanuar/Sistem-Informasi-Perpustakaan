<?php
include 'auth.php'; // Sertakan file auth.php untuk memeriksa login

check_admin(); // Pastikan hanya admin yang dapat mengakses halaman ini

include 'koneksi.php'; // Sertakan file koneksi ke database

$message = '';

// Fungsi untuk menambah anggota baru
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['name']) && isset($_POST['email']) && isset($_POST['phone']) && isset($_POST['address'])) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $address = $_POST['address'];

        try {
            // Query untuk memasukkan anggota baru ke dalam database
            $insert_query = "INSERT INTO member (name, email, phone, address) VALUES (:name, :email, :phone, :address)";
            $stmt = $conn->prepare($insert_query); // Persiapkan statement SQL
            $stmt->bindParam(':name', $name); // Binding parameter name ke statement
            $stmt->bindParam(':email', $email); // Binding parameter email ke statement
            $stmt->bindParam(':phone', $phone); // Binding parameter phone ke statement
            $stmt->bindParam(':address', $address); // Binding parameter address ke statement

            if ($stmt->execute()) { // Jika eksekusi berhasil
                $message = "Anggota baru berhasil ditambahkan."; // Pesan sukses
            } else {
                $message = "Terjadi kesalahan saat menambahkan anggota."; // Pesan kesalahan
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage(); // Tangkap dan tampilkan pesan error jika terjadi kesalahan PDO
        }
    } else {
        $message = "Data anggota tidak lengkap"; // Pesan kesalahan jika data anggota tidak lengkap
    }
}

// Query untuk mendapatkan daftar anggota
$sql = "SELECT * FROM member";
$stmt = $conn->prepare($sql);
$stmt->execute();
$members = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Members - Sistem Informasi Perpustakaan</title>
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
        <h2>Manage Members - Sistem Informasi Perpustakaan</h2>

        <!-- Form untuk tambah data anggota -->
        <h3>Tambah Anggota Baru</h3>
        <?php if (!empty($message)): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <label for="name">Nama:</label><br>
            <input type="text" id="name" name="name" required><br><br>

            <label for="email">Email:</label><br>
            <input type="email" id="email" name="email" required><br><br>

            <label for="phone">Telepon:</label><br>
            <input type="text" id="phone" name="phone" required><br><br>

            <label for="address">Alamat:</label><br>
            <textarea id="address" name="address" rows="4" required></textarea><br><br>

            <input type="submit" value="Tambah Anggota">
        </form>

        <!-- Tabel untuk menampilkan data anggota -->
        <h3>Daftar Anggota</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Telepon</th>
                    <th>Alamat</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($members as $member): ?>
                    <tr>
                        <td><?php echo $member['id']; ?></td>
                        <td><?php echo htmlspecialchars($member['name']); ?></td>
                        <td><?php echo htmlspecialchars($member['email']); ?></td>
                        <td><?php echo htmlspecialchars($member['phone']); ?></td>
                        <td><?php echo htmlspecialchars($member['address']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
