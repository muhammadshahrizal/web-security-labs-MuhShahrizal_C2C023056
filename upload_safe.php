<?php
include 'koneksi.php';
$message = '';
$error = '';

// Folder aman (berbeda/lebih spesifik)
$upload_dir = 'uploads/avatars/'; 
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['fileToUpload'])) {
    
    // Cek jika ada error upload dasar
    if ($_FILES['fileToUpload']['error'] != 0) {
        $error = "Error upload: Kode " . $_FILES['fileToUpload']['error'];
    
    } else {
        $file_tmp_name = $_FILES['fileToUpload']['tmp_name'];
        $file_name = $_FILES['fileToUpload']['name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        // --- 1. SAFE: Whitelist ekstensi yang diizinkan ---
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($file_ext, $allowed_ext)) {
            
            // --- 2. SANGAT SAFE: Verifikasi isi file (MIME Type/Image Header) ---
            // getimagesize() akan gagal (return false) jika file bukan gambar
            $image_info = @getimagesize($file_tmp_name);
            
            if ($image_info !== false) {
                // File adalah gambar asli, lanjutkan
                
                // --- 3. SAFE: Buat nama file unik ---
                $new_file_name = uniqid('safe_upload_', true) . '.' . $file_ext;
                $target_file = $upload_dir . $new_file_name;

                if (move_uploaded_file($file_tmp_name, $target_file)) {
                    $message = "Sukses (Aman): File " . htmlspecialchars($file_name) . " berhasil di-upload sebagai " . $new_file_name;
                } else {
                    $error = "Error saat memindahkan file.";
                }
                
            } else {
                // --- GAGAL VERIFIKASI ISI ---
                $error = "Error: File yang di-upload bukan gambar asli. (Misalnya: file .php yang di-rename .jpg).";
            }
        } else {
            // --- GAGAL VERIFIKASI EKSTENSI ---
            $error = "Invalid extension. Hanya " . implode(', ', $allowed_ext) . " yang diizinkan.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>File Upload - Safe</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container login-box" style="max-width: 600px;">
        <h1>File Upload (SAFE)</h1>
        <div class="alert alert-success">
            AMAN — Validasi ekstensi (whitelist), nama file di-randomize, DAN verifikasi isi file (getimagesize).
        </div>
        
        <?php if ($message) echo "<p class'safe-text' style='color:green; background:#e8f5e9; padding:10px; border-radius:4px;'>$message</p>"; ?>
        <?php if ($error) echo "<p class='vulnerable-text' style='color:#d32f2f; background:#ffebee; padding:10px; border-radius:4px;'>$error</p>"; ?>


        <form method="post" enctype="multipart/form-data">
            <label for="fileToUpload" class="form-label">Pilih file (hanya jpg, png, gif)</label>
            <input type="file" name="fileToUpload" id="fileToUpload" class="form-control">
            <button type="submit" class="btn-main">Upload</button>
        </form>
        <p style="text-align: center;"><a href="index.php">Kembali ke Halaman Utama</a></p>
    </div>
</body>
</html>