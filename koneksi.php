<?php
// Mulai session di file terpusat ini
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// --- DEFINISIKAN BASE URL ANDA ---
// (Sesuaikan jika nama folder Anda berbeda)
define('BASE_URL', '/web_keamanan_data/');
// ---------------------------------


// --- KONFIGURASI DATABASE ANDA ---
$db_host = 'localhost';
$db_user = 'root';      
$db_pass = '';          
$db_name = 'web_keamanan_data';
// ---------------------------------

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi Gagal: " . $conn->connect_error);
}

// Set charset untuk keamanan
$conn->set_charset("utf8mb4");

?>