<?php
include 'koneksi.php';
$message = '';

// Proses tambah transaksi baru
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_transaction'])) {
    $member_id = $_POST['member_id'];
    $book_id = $_POST['book_id'];
    $borrow_date = $_POST['borrow_date'];
    $return_date = $_POST['return_date'];

    try {
        // Insert transaksi baru
        $sql_insert_transaction = "INSERT INTO transaction (member_id, book_id, borrow_date, return_date)
                                   VALUES (:member_id, :book_id, :borrow_date, :return_date)";
        $stmt_insert_transaction = $conn->prepare($sql_insert_transaction);
        $stmt_insert_transaction->bindParam(':member_id', $member_id);
        $stmt_insert_transaction->bindParam(':book_id', $book_id);
        $stmt_insert_transaction->bindParam(':borrow_date', $borrow_date);
        $stmt_insert_transaction->bindParam(':return_date', $return_date);

        // Update stok buku yang tersedia
        $sql_update_book_stock = "UPDATE book SET available_stock = available_stock - 1 WHERE id = :book_id AND available_stock > 0";
        $stmt_update_book_stock = $conn->prepare($sql_update_book_stock);
        $stmt_update_book_stock->bindParam(':book_id', $book_id);

        // Transaksi PDO
        $conn->beginTransaction();
        
        if ($stmt_insert_transaction->execute() && $stmt_update_book_stock->execute()) {
            $conn->commit();
            $message = "Transaksi baru berhasil ditambahkan.";
        } else {
            $conn->rollback();
            $message = "Gagal menambahkan transaksi baru.";
        }
    } catch (PDOException $e) {
        $conn->rollback();
        $message = "Error: " . $e->getMessage();
    }
}

// Proses mengembalikan buku
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['return_book'])) {
    $transaction_id = $_POST['transaction_id'];
    $book_id = $_POST['book_id'];

    try {
        // Update stok buku yang tersedia
        $sql_update_book_stock = "UPDATE book SET available_stock = available_stock + 1 WHERE id = :book_id";
        $stmt_update_book_stock = $conn->prepare($sql_update_book_stock);
        $stmt_update_book_stock->bindParam(':book_id', $book_id);

        // Hapus entri transaksi
        $sql_delete_transaction = "DELETE FROM transaction WHERE id = :transaction_id";
        $stmt_delete_transaction = $conn->prepare($sql_delete_transaction);
        $stmt_delete_transaction->bindParam(':transaction_id', $transaction_id);

        // Transaksi PDO
        $conn->beginTransaction();
        
        if ($stmt_update_book_stock->execute() && $stmt_delete_transaction->execute()) {
            $conn->commit();
            $message = "Buku berhasil dikembalikan.";
        } else {
            $conn->rollback();
            $message = "Gagal mengembalikan buku.";
        }
    } catch (PDOException $e) {
        $conn->rollback();
        $message = "Error: " . $e->getMessage();
    }
}

// Query untuk mengambil data anggota
$query_members = "SELECT id, name FROM member";
$stmt_members = $conn->prepare($query_members);
$stmt_members->execute();
$members = $stmt_members->fetchAll(PDO::FETCH_ASSOC);

// Query untuk mengambil data buku
$query_books = "SELECT id, title FROM book WHERE available_stock > 0"; // Hanya tampilkan buku dengan stok tersedia
$stmt_books = $conn->prepare($query_books);
$stmt_books->execute();
$books = $stmt_books->fetchAll(PDO::FETCH_ASSOC);

// Query untuk mengambil data buku yang sedang dipinjam
$query_borrowed_books = "SELECT transaction.id, member.name AS member_name, book.title AS book_title, 
                         transaction.borrow_date, transaction.return_date 
                         FROM transaction 
                         INNER JOIN member ON transaction.member_id = member.id 
                         INNER JOIN book ON transaction.book_id = book.id";
$stmt_borrowed_books = $conn->prepare($query_borrowed_books);
$stmt_borrowed_books->execute();
$borrowed_books = $stmt_borrowed_books->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaksi - Sistem Informasi Perpustakaan</title>
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
        <h2>Data Transaksi - Sistem Informasi Perpustakaan</h2>
        <!-- Form untuk tambah data transaksi -->
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <label for="member_id">ID Anggota:</label><br>
            <select id="member_id" name="member_id" required>
                <option value="">Pilih Anggota</option>
                <?php foreach ($members as $member): ?>
                    <option value="<?php echo $member['id']; ?>"><?php echo htmlspecialchars($member['name']); ?></option>
                <?php endforeach; ?>
            </select><br><br>

            <label for="book_id">ID Buku:</label><br>
            <select id="book_id" name="book_id" required>
                <option value="">Pilih Buku</option>
                <?php foreach ($books as $book): ?>
                    <option value="<?php echo $book['id']; ?>"><?php echo htmlspecialchars($book['title']); ?></option>
                <?php endforeach; ?>
            </select><br><br>

            <label for="borrow_date">Tanggal Pinjam:</label><br>
            <input type="date" id="borrow_date" name="borrow_date" required><br><br>

            <label for="return_date">Tanggal Kembali:</label><br>
            <input type="date" id="return_date" name="return_date" required><br><br>

            <input type="submit" name="add_transaction" value="Tambah Transaksi">
        </form>

        <!-- Menampilkan pesan notifikasi -->
        <?php if (!empty($message)): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>

        <!-- Tabel untuk menampilkan data transaksi -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Anggota</th>
                    <th>Judul Buku</th>
                    <th>Tanggal Pinjam</th>
                    <th>Tanggal Kembali</th>
                    <th>Aksi</th> <!-- Tambah kolom untuk aksi -->
                </tr>
            </thead>
            <tbody>
                <?php foreach ($borrowed_books as $transaction): ?>
                    <tr>
                        <td><?php echo $transaction['id']; ?></td>
                        <td><?php echo htmlspecialchars($transaction['member_name']); ?></td>
                        <td><?php echo htmlspecialchars($transaction['book_title']); ?></td>
                        <td><?php echo $transaction['borrow_date']; ?></td>
                        <td><?php echo $transaction['return_date']; ?></td>
                        <td>
                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                                <input type="hidden" name="transaction_id" value="<?php echo $transaction['id']; ?>">
                                <input type="hidden" name="book_id" value="<?php echo $transaction['book_id']; ?>">
                                <input type="submit" name="return_book" value="Kembalikan">
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
