<?php
session_start();

function check_login() {
    if (!isset($_SESSION['username'])) {
        // Jika pengguna belum login, redirect ke halaman login
        header("Location: index.php");
        exit();
    }
}

function is_admin() {
    return isset($_SESSION['username']) && $_SESSION['username'] === 'admin';
}

function check_admin() {
    if (!is_admin()) {
        // Jika pengguna bukan admin, redirect ke halaman user dashboard atau halaman yang sesuai
        header("Location: dashboard.php");
        exit();
    }
}

?>
