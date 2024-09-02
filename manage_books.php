<?php
include 'koneksi.php';
$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $isbn = $_POST['isbn'];
    $category = $_POST['category'];
    $total_stock = $_POST['total_stock'];
    $available_stock = $total_stock;
    $image_path = '';

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image_name = basename($_FILES['image']['name']);
        $target_dir = "uploads/";
        $target_file = $target_dir . $image_name;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image_path = $target_file;
        } else {
            $message = "Terjadi kesalahan saat mengunggah gambar.";
        }
    }

    $sql = "INSERT INTO book (title, author, isbn, category, total_stock, available_stock, image_path)
            VALUES (:title, :author, :isbn, :category, :total_stock, :available_stock, :image_path)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':author', $author);
    $stmt->bindParam(':isbn', $isbn);
    $stmt->bindParam(':category', $category);
    $stmt->bindParam(':total_stock', $total_stock);
    $stmt->bindParam(':available_stock', $available_stock);
    $stmt->bindParam(':image_path', $image_path);

    if ($stmt->execute()) {
        $message = "Buku baru berhasil ditambahkan.";
    } else {
        $message = "Terjadi kesalahan saat menambahkan buku.";
    }
}

$sql = "SELECT * FROM book";
$stmt = $conn->prepare($sql);
$stmt->execute();
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Buku - Sistem Informasi Perpustakaan</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        /* Styling CSS */
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
        <h2>Data Buku - Sistem Informasi Perpustakaan</h2>

        <!-- Form untuk tambah data buku -->
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" enctype="multipart/form-data">
            <label for="title">Judul:</label><br>
            <input type="text" id="title" name="title" required><br><br>

            <label for="author">Pengarang:</label><br>
            <input type="text" id="author" name="author"><br><br>

            <label for="isbn">ISBN:</label><br>
            <input type="text" id="isbn" name="isbn"><br><br>

            <label for="category">Kategori:</label><br>
            <select id="category" name="category">
                <option value="Anak-Anak">Anak-Anak</option>
                <option value="Novel">Novel</option>
                <option value="Komik">Komik</option>
                <option value="Lainnya">Lainnya</option>
            </select><br><br>

            <label for="total_stock">Stok Total:</label><br>
            <input type="number" id="total_stock" name="total_stock" required><br><br>

            <label for="image">Gambar Buku:</label><br>
            <input type="file" id="image" name="image"><br><br>

            <input type="submit" value="Tambah Buku">
        </form>

        <!-- Menampilkan pesan notifikasi -->
        <?php if (!empty($message)): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>

        <!-- Tabel untuk menampilkan data buku -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Judul</th>
                    <th>Pengarang</th>
                    <th>ISBN</th>
                    <th>Kategori</th>
                    <th>Stok Total</th>
                    <th>Stok Tersedia</th>
                    <th>Gambar</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($books as $book): ?>
                    <tr>
                        <td><?php echo $book['id']; ?></td>
                        <td><?php echo htmlspecialchars($book['title']); ?></td>
                        <td><?php echo htmlspecialchars($book['author']); ?></td>
                        <td><?php echo htmlspecialchars($book['isbn']); ?></td>
                        <td><?php echo htmlspecialchars($book['category']); ?></td>
                        <td><?php echo $book['total_stock']; ?></td>
                        <td><?php echo $book['available_stock']; ?></td>
                        <td>
                            <?php if (!empty($book['image_path'])): ?>
                                <img src="<?php echo $book['image_path']; ?>" alt="Gambar Buku" width="100">
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
