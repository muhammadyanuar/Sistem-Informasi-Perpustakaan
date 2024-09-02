<?php
session_start(); // Memulai session

// Menghapus semua data session
$_SESSION = array();

// Menghapus session cookie jika ada
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Mengakhiri session
session_destroy();

// Redirect ke halaman login setelah logout
header("Location: index.php");
exit();
?>
