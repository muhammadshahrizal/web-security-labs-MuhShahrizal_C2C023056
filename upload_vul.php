<?php
include 'koneksi.php';
$message = '';
$error = '';

$upload_dir = 'uploads/'; // Folder target

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['fileToUpload'])) {
    if ($_FILES['fileToUpload']['error'] == 0) {
        
        $target_file = $upload_dir . basename($_FILES['fileToUpload']['name']);
        
        // VULNERABLE: Tidak ada validasi ekstensi, file langsung dipindah
        if (move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $target_file)) {
            $message = "Sukses (Rentan): File ". htmlspecialchars(basename($_FILES['fileToUpload']['name'])) . " berhasil di-upload.";
            $message .= "<br>Anda bisa coba akses di: <a href='" . BASE_URL . $target_file . "' target='_blank'>" . $target_file . "</a>";
        } else {
            $error = "Error saat memindahkan file.";
        }
    } else {
        $error = "Error upload: " . $_FILES['fileToUpload']['error'];
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>File Upload - Vulnerable</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container login-box" style="max-width: 600px;">
        <h1>File Upload (VULNERABLE)</h1>
        <div class="alert alert-danger">
            INTENTIONALLY VULNERABLE — tidak ada validasi ekstensi.
        </div>
        
        <?php if ($message) echo "<p class='safe-text'>$message</p>"; ?>
        <?php if ($error) echo "<p class='vulnerable-text'>$error</p>"; ?>

        <form method="post" enctype="multipart/form-data">
            <label for="fileToUpload" class="form-label">Pilih file (Contoh: test.php)</label>
            <input type="file" name="fileToUpload" id="fileToUpload" class="form-control">
            <button type="submit" class="btn-main" style="background-color: #d32f2f;">Upload</button>
        </form>
        <p style="text-align: center;"><a href="index.php">Kembali ke Halaman Utama</a></p>
    </div>
</body>
</html>