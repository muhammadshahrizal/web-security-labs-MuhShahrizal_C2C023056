<?php
include 'koneksi.php'; // Ini sudah otomatis menjalankan session_start()

$error = '';
$success = '';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 1. Verifikasi Password Saat Ini (WAJIB untuk semua perubahan)
    // MODIFIKASI: Menghapus pengambilan profile_picture_path
    $stmt = $conn->prepare("SELECT password FROM sqli_users_safe WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    $current_password = $_POST['current_password'];
    if (!$user || !password_verify($current_password, $user['password'])) {
        $error = "Password Anda saat ini salah.";
    } else {
        // Password benar, lanjutkan
        
        // MODIFIKASI: Seluruh blok (2. Proses Upload Foto Profil) DIHAPUS

        // 3. Update Username dan Fullname
        $fullname = $_POST['fullname'];
        $username = $_POST['username'];
        
        // MODIFIKASI: Query UPDATE tidak lagi menyertakan profile_picture_path
        $stmt_update = $conn->prepare("UPDATE sqli_users_safe SET fullname = ?, username = ? WHERE id = ?");
        $stmt_update->bind_param("ssi", $fullname, $username, $user_id);
        
        if ($stmt_update->execute()) {
            $success .= "Profil (username/nama) berhasil diperbarui.";
            // MODIFIKASI: Update session username jika berubah
            $_SESSION['username'] = $username; 
        } else {
            $error .= "Gagal memperbarui profil: " . $stmt_update->error;
        }
        $stmt_update->close();

        // 4. Update Password (Hanya jika diisi)
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        if (!empty($new_password)) {
            if ($new_password !== $confirm_password) {
                $error .= " Konfirmasi password baru tidak cocok.";
            } else {
                $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt_pass = $conn->prepare("UPDATE sqli_users_safe SET password = ? WHERE id = ?");
                $stmt_pass->bind_param("si", $new_hashed_password, $user_id);
                
                if ($stmt_pass->execute()) {
                    $success = "Profil DAN password berhasil diperbarui.";
                } else {
                    $error .= " Gagal memperbarui password: " . $stmt_pass->error;
                }
                $stmt_pass->close();
            }
        }
    }

    // Simpan pesan ke session
    if ($error) $_SESSION['modal_error'] = $error;
    if ($success) $_SESSION['modal_success'] = $success;
}

header('Location: index.php?profile_updated=1');
exit();
?>