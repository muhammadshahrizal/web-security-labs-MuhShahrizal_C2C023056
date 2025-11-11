<?php
include 'koneksi.php'; // Ini sudah otomatis session_start()

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $mode = $_POST['logic_mode']; // Mengambil pilihan Rentan/Aman

    if ($mode === 'aman') {
        $table_name = 'sqli_users_safe'; // Tabel untuk user yang di-hash
        
        // --- LOGIKA AMAN: MENGGUNAKAN PREPARED STATEMENTS ---
        $stmt = $conn->prepare("SELECT id, username, password FROM " . $table_name . " WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                // Simpan semua data penting ke session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                
                // --- MODIFIKASI DIMULAI ---
                $_SESSION['login_mode'] = 'aman'; // Simpan mode login
                // --- MODIFIKASI SELESAI ---
                
                header('Location: index.php'); 
                exit();
            } else {
                $error = "Aman: Username atau password salah!";
            }
        } else {
            $error = "Aman: Username atau password salah!";
        }
        $stmt->close();
        
    } else {
        $table_name = 'sqli_users_vul'; // Tabel untuk user yang passwordnya mentah
        
        // --- LOGIKA RENTAN: MENGGUNAKAN CONCATENATED QUERY ---
        $sql = "SELECT id, username, password FROM " . $table_name . " WHERE username = '$username' AND password = '$password'";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            
            // --- MODIFIKASI DIMULAI ---
            $_SESSION['login_mode'] = 'rentan'; // Simpan mode login
            // --- MODIFIKASI SELESAI ---
            
            header('Location: index.php'); 
            exit();
        } else {
            $error = "Rentan: Username atau password salah! Coba gunakan <b>' OR '1'='1' -- </b> di username atau password untuk bypass.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login Web Keamanan Data</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container login-box">
        <h1>Sistem Demo Keamanan Data</h1>
        <p>Silakan masuk dan pilih mode Anda</p>
        
        <?php if (!empty($error)) echo "<p class='vulnerable-text'>$error</p>"; ?>

        <form method="post">
            
            <label for="logic_mode">Pilih Mode Demo:</label>
            <select name="logic_mode" id="logic_mode" required class="input-full">
                <option value="aman" <?= (isset($_POST['logic_mode']) && $_POST['logic_mode'] === 'aman') ? 'selected' : ''; ?>>Aman</option>
                <option value="rentan" <?= (isset($_POST['logic_mode']) && $_POST['logic_mode'] === 'rentan') ? 'selected' : ''; ?>>Rentan</option>
            </select>
            <br><br>

            <label for="username">Masukkan Username</label>
            <input type="text" name="username" id="username" placeholder="Masukkan Username" required>

            <label for="password">Masukkan Password</label>
            <input type="password" name="password" id="password" placeholder="Masukkan Password" required>
            
            <button type="submit" class="btn-main">Masuk</button>
        </form>
        
        <p style="text-align: center;">Belum punya akun? <a href="register.php">Daftar Akun </a></p> 
        <p style="text-align: center;"><a href="index.php">Kembali ke Halaman Utama</a></p>
    </div>
</body>
</html>