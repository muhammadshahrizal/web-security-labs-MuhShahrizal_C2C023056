<?php
include 'koneksi.php'; 

$error = '';
$success = '';

if (!isset($_SESSION['user_id']) || $_SESSION['login_mode'] !== 'rentan') {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 1. Verifikasi Password Saat Ini (RENTAN: Teks Biasa)
    // MODIFIKASI: Mengambil dari 'sqli_users_vul'
    $stmt = $conn->prepare("SELECT password FROM sqli_users_vul WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    $current_password = $_POST['current_password'];
    
    // MODIFIKASI: Pengecekan password diubah dari password_verify() ke perbandingan string
    if (!$user || $current_password !== $user['password']) {
        $error = "Password Anda saat ini salah.";
    } else {
        // Password benar, lanjutkan
        
        // 2. Update Username dan Fullname
        $fullname = $_POST['fullname'];
        $username = $_POST['username'];
        
        // MODIFIKASI: Update ke 'sqli_users_vul'
        $stmt_update = $conn->prepare("UPDATE sqli_users_vul SET fullname = ?, username = ? WHERE id = ?");
        $stmt_update->bind_param("ssi", $fullname, $username, $user_id);
        
        if ($stmt_update->execute()) {
            $success .= "Profil (username/nama) berhasil diperbarui.";
            $_SESSION['username'] = $username; 
        } else {
            $error .= "Gagal memperbarui profil: " . $stmt_update->error;
        }
        $stmt_update->close();

        // 3. Update Password (RENTAN: Teks Biasa)
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        if (!empty($new_password)) {
            if ($new_password !== $confirm_password) {
                $error .= " Konfirmasi password baru tidak cocok.";
            } else {
                // MODIFIKASI: Simpan password baru sebagai teks biasa ke 'sqli_users_vul'
                $stmt_pass = $conn->prepare("UPDATE sqli_users_vul SET password = ? WHERE id = ?");
                $stmt_pass->bind_param("si", $new_password, $user_id);
                
                if ($stmt_pass->execute()) {
                    $success = "Profil DAN password berhasil diperbarui (Disimpan Teks Biasa).";
                } else {
                    $error .= " Gagal memperbarui password: " . $stmt_pass->error;
                }
                $stmt_pass->close();
            }
        }
    }

    // Simpan pesan ke session
    // MODIFIKASI: Menggunakan session key yang berbeda
    if ($error) $_SESSION['modal_error_rentan'] = $error;
    if ($success) $_SESSION['modal_success_rentan'] = $success;
}

// Kembali ke halaman utama
header('Location: index.php?profile_updated=1');
exit();
?>