<?php
include 'koneksi.php';

// --- 1. VALIDASI AKSES ---
if (!isset($_SESSION['login_mode']) || $_SESSION['login_mode'] !== 'rentan') {
    if (isset($_SESSION['user_id'])) {
         die("Akses Ditolak. Halaman ini hanya untuk mode rentan. Silakan <a href='logout.php'>logout</a> dan login kembali dengan mode 'Rentan'.");
    }
    header('Location: login.php');
    exit();
}

$current_login_mode = 'rentan'; // Tentu saja rentan
$message = '';
$error = '';

// --- 2. PROSES FORM ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $title = $_POST['judul'];
    $body = $_POST['deskripsi'];
    $author_id = $_SESSION['user_id'];
    $file_path = null; 
    $upload_dir = 'uploads/'; 

    // --- 3. LOGIKA UPLOAD RENTAN ---
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $file_tmp_name = $_FILES['gambar']['tmp_name'];
        $file_name = basename($_FILES['gambar']['name']);
        
        $new_file_name = uniqid('vuln_') . '-' . $file_name;
        $target_file = $upload_dir . $new_file_name;
        
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

        if (move_uploaded_file($file_tmp_name, $target_file)) {
            $file_path = $target_file; 
            $message = "Sukses (Rentan): File Anda (termasuk .php) berhasil di-upload.";
        } else {
            $error = "Error (Rentan): Gagal mengupload file.";
        }
    
    } elseif (isset($_FILES['gambar']) && $_FILES['gambar']['error'] != 4) {
         $error = "Terjadi error upload: Kode " . $_FILES['gambar']['error'];
    }

    // --- 4. SIMPAN KE DATABASE (Jika tidak error) ---
    if (empty($error)) {
        $stmt = $conn->prepare("INSERT INTO upload_articles (title, body, file_path, author_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $title, $body, $file_path, $author_id);
        if ($stmt->execute()) {
            header('Location: index.php?status=sukses_rentan&file=' . urlencode(basename($file_path)));
            exit();
        } else {
            $error = "Gagal menyimpan post ke database: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Posting (RENTAN) - Web Keamanan Data</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide-icons"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; background-color: #F0F2F5; } </style>
</head>
<body class="bg-gray-100">

    <nav class="bg-white shadow-sm sticky top-0 z-50">
        <div class="container mx-auto max-w-7xl px-4">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-4">
                    <a href="index.php" class="text-2xl font-bold text-blue-600">Web Keamanan Data</a>
                </div>
                <div class="flex items-center gap-4">
                    <div class="flex items-center space-x-3">
                        <a href="javascript:void(0)" id="edit-profile-button-rentan" class="text-sm font-semibold text-red-700 hover:text-red-600">
                            Hi, <?php echo htmlspecialchars($_SESSION['username']); ?> (Rentan)
                        </a>
                        <a href="<?php echo BASE_URL; ?>logout.php" class="text-sm text-red-600 hover:bg-gray-100 px-3 py-1 rounded-md">
                            Log out
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    <main class="container mx-auto max-w-7xl p-4 mt-6">
        <div class="mb-4">
            <a href="index.php" class="text-blue-600 hover:underline">&larr; Kembali</a>
        </div>
        <div class="lg:col-span-3 bg-white p-6 rounded-lg shadow">
            <div class="flex justify-between items-center mb-4">
                <h1 class="text-2xl font-bold">Buat Post (Mode RENTAN)</h1>
                <span class="text-sm font-medium text-red-700 bg-red-100 px-3 py-1 rounded-full">Semua File Diizinkan</span>
            </div>
            
            <?php if ($message): ?>
                <p style="color:green; background:#e8f5e9; padding:10px; border-radius:4px; margin-bottom: 1rem; font-weight: bold;"><?php echo htmlspecialchars($message); ?></p>
            <?php endif; ?>
            <?php if ($error): ?>
                <p style="color:#d32f2f; background:#ffebee; padding:10px; border-radius:4px; margin-bottom: 1rem; font-weight: bold;"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>

            <form class="space-y-6" method="post" action="post_rentan.php" enctype="multipart/form-data">
                <div>
                    <label for="judul" class="block text-sm font-semibold text-gray-700 mb-2">Judul</label>
                    <input type="text" id="judul" name="judul" class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-600"
                            placeholder="Tulis judul yang menarik ya" required value="<?php echo isset($title) ? htmlspecialchars($title) : ''; ?>">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Gambar / File</label>
                    <label for="gambar-upload" id="upload-label" class="border-2 border-dashed border-gray-300 rounded-lg p-12 text-center cursor-pointer hover:border-blue-600 block">
                        <i data-lucide="upload-cloud" class="mx-auto h-12 w-12 text-gray-400"></i>
                        <p class="mt-2 text-gray-500">Drag file ke sini atau <span class="text-blue-600 font-semibold">Upload</span></p>
                        <p class="text-xs text-red-500 mt-1">Mode Rentan: Anda bisa upload .php atau file lainnya.</p>
                    </label>
                    <input type="file" id="gambar-upload" name="gambar" class="hidden">
                </div>
                <div>
                    <label for="deskripsi" class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi</label>
                    <textarea id="deskripsi" name="deskripsi" rows="8" class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-600"
                            placeholder="Karena semua butuh penjelasan (opsional)"><?php echo isset($body) ? htmlspecialchars($body) : ''; ?></textarea>
                </div>
                <div>
                    <button type="submit" class="w-full bg-blue-600 text-white p-3 rounded-lg text-center font-semibold hover:bg-blue-700 transition duration-200">
                        Post
                    </button>
                </div>
            </form>
        </div>
    </main>

    <?php if (isset($_SESSION['user_id'])) include 'modal_profile_rentan.php'; ?>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            try {
                lucide.createIcons();
            } catch (e) {
                console.error("Lucide icons gagal dimuat:", e);
            }

            // Logika Modal (Tetap sama)
            const editProfileButtonRentan = document.getElementById('edit-profile-button-rentan');
            const profileModalRentan = document.getElementById('profile-modal-rentan');
            const closeModalButtonRentan = document.getElementById('close-modal-button-rentan');

            if (editProfileButtonRentan && profileModalRentan && closeModalButtonRentan) {
                editProfileButtonRentan.addEventListener('click', function() {
                    profileModalRentan.classList.remove('hidden');
                });
                closeModalButtonRentan.addEventListener('click', function() {
                    profileModalRentan.classList.add('hidden');
                });
                profileModalRentan.addEventListener('click', function(event) {
                    if (event.target === profileModalRentan) {
                        profileModalRentan.classList.add('hidden');
                    }
                });
            }
            
            <?php 
            $modal_error_rentan = $_SESSION['modal_error_rentan'] ?? null;
            $modal_success_rentan = $_SESSION['modal_success_rentan'] ?? null;
            if (($modal_error_rentan || $modal_success_rentan) && isset($_GET['profile_updated'])): 
            ?>
                if (profileModalRentan) profileModalRentan.classList.remove('hidden');
            <?php endif; ?>

            // --- JAVASCRIPT BARU UNTUK UMPAN BALIK UPLOAD ---
            const fileInput = document.getElementById('gambar-upload');
            const uploadLabel = document.getElementById('upload-label');
            // Simpan teks asli label
            const originalLabelText = uploadLabel.innerHTML;

            fileInput.addEventListener('change', function() {
                if (fileInput.files.length > 0) {
                    // Jika file dipilih, tampilkan nama file
                    uploadLabel.innerHTML = `
                        <i data-lucide="check-circle" class="mx-auto h-12 w-12 text-green-500"></i>
                        <p class="mt-2 text-gray-700 font-semibold">File Terpilih:</p>
                        <p class="text-sm text-gray-600">${fileInput.files[0].name}</p>
                    `;
                } else {
                    // Jika dibatalkan, kembalikan ke teks asli
                    uploadLabel.innerHTML = originalLabelText;
                }
                // (Penting) Panggil createIcons() lagi jika Anda mengubah ikon
                try {
                    lucide.createIcons();
                } catch(e) {}
            });
            // --- AKHIR JAVASCRIPT BARU ---
        });
    </script>
    </body>
</html>