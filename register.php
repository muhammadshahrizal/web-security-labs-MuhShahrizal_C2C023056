<?php
// Wajib: Sertakan koneksi database tunggal
include 'koneksi.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $fullname = $_POST['fullname'] ?? ''; 
    $mode = $_POST['logic_mode']; // Mengambil pilihan Rentan/Aman

    if ($mode === 'aman') {
        $table_name = 'sqli_users_safe';
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare("INSERT INTO " . $table_name . " (username, password, fullname) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $hashed_password, $fullname); 

        if ($stmt->execute()) {
            
            // --- MODIFIKASI DIMULAI ---
            $new_user_id = $conn->insert_id; // Ambil ID dari user yang BARU saja dibuat
            $default_amount = 0.00;
            $default_desc = "Invoice default untuk (Aman) " . htmlspecialchars($username);

            $stmt_invoice = $conn->prepare("INSERT INTO invoices (user_id, amount, description) VALUES (?, ?, ?)");
            $stmt_invoice->bind_param("ids", $new_user_id, $default_amount, $default_desc);
            
            if ($stmt_invoice->execute()) {
                $message = "✅ Akun AMAN berhasil dibuat! Invoice default telah otomatis dibuat untuk Anda.";
            } else {
                $message = "✅ Akun AMAN berhasil dibuat! TAPI gagal membuat invoice default: " . $stmt_invoice->error;
            }
            $stmt_invoice->close();
            // --- MODIFIKASI SELESAI ---

        } else {
            $error = "Error Aman: " . $stmt->error;
        }
        $stmt->close();
        
    } else {
        $table_name = 'sqli_users_vul';
        
        $clean_username = $conn->real_escape_string($username);
        $clean_password = $conn->real_escape_string($password);
        $clean_fullname = $conn->real_escape_string($fullname);

        $sql = "INSERT INTO " . $table_name . " (username, password, fullname) VALUES ('$clean_username', '$clean_password', '$clean_fullname')";

        if ($conn->query($sql) === TRUE) {
            
            // --- MODIFIKASI DIMULAI ---
            // (Dengan asumsi Anda sudah menjalankan 'ALTER TABLE' dari Langkah 1)
            $new_user_id = $conn->insert_id; // Ambil ID dari user rentan yang BARU saja dibuat
            $default_amount = 0.00;
            $default_desc = "Invoice default untuk (Rentan) " . htmlspecialchars($username);

            $stmt_invoice = $conn->prepare("INSERT INTO invoices (user_id, amount, description) VALUES (?, ?, ?)");
            $stmt_invoice->bind_param("ids", $new_user_id, $default_amount, $default_desc);
            
            if ($stmt_invoice->execute()) {
                $message = "⚠️ Akun RENTAN berhasil dibuat! Invoice default juga telah otomatis dibuat.";
            } else {
                $message = "⚠️ Akun RENTAN berhasil dibuat! TAPI gagal membuat invoice default: " . $stmt_invoice->error . " (Apakah Anda sudah menjalankan 'ALTER TABLE'?)";
            }
            $stmt_invoice->close();
            // --- MODIFIKASI SELESAI ---
            
        } else {
            $error = "Error Rentan: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register Demo Keamanan Data</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container login-box">
        <h1>CREATE USER</h1>
        <p>Buat akun untuk menguji perbedaan logika keamanan.</p>

        <?php if ($message) echo "<p class".( ($mode=='aman') ? " 'safe-text'" : " 'vulnerable-text' style='background-color:#fffbee; color:#d32f2f;' " ) .">$message</p>"; ?>
        <?php if ($error) echo "<p class='vulnerable-text'>$error</p>"; ?>
        
        <form method="post">
            
            <label for="logic_mode">Pilih Mode Akun:</label>
            <select name="logic_mode" id="logic_mode" required class="input-full">
                <option value="aman">Aman (Password di-Hash, ke sqli_users_safe)</option>
                <option value="rentan">Rentan (Password Mentah, ke sqli_users_vul)</option>
            </select>
            <br><br>

            <label for="username">Username:</label>
            <input type="text" name="username" id="username" required>

            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>
            
            <label for="fullname">Full name (opsional):</label>
            <input type="text" name="fullname" id="fullname">
            
            <button type="submit" class="btn-main">Buat Akun</button>
        </form>
        
        <p style="text-align: center;">Sudah punya akun? <a href="login.php">Masuk</a></p>
        <p style="text-align: center;"><a href="index.php">Kembali ke Halaman Utama</a></p>
    </div>
</body>
</html>