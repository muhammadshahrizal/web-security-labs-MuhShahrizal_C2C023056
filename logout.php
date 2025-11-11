<?php
include 'koneksi.php'; // Ini akan otomatis memanggil session_start()

// Hancurkan semua data sesi
session_unset();
session_destroy();

// Arahkan kembali ke halaman utama
header('Location: index.php');
exit();
?>