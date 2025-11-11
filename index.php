<?php
session_start();
include 'koneksi.php';

// Tentukan link "Buat Post" dan mode login
$post_link = BASE_URL . 'login.php'; 
$current_login_mode = $_SESSION['login_mode'] ?? 'aman'; // Default ke aman

if (isset($_SESSION['user_id'])) {
    $post_link = ($current_login_mode === 'rentan') 
        ? BASE_URL . 'post_rentan.php' 
        : BASE_URL . 'post_safe.php';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web Keamanan Data</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide-icons"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #F0F2F5; }
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
    </style>
</head>
<body class="text-gray-800">

    <header class="bg-white shadow-sm w-full sticky top-0 z-50">
        <div class="container mx-auto max-w-7xl px-4">
            <div class="flex justify-between items-center h-16">
                
                <div class="flex items-center gap-4">
                    <a href="<?php echo BASE_URL; ?>index.php" class="text-2xl font-bold text-blue-600">Web Keamanan Data</a>
                    <a href="<?php echo BASE_URL; ?>search_safe.php" class="text-sm font-medium text-green-600 hover:text-green-700">Search (Safe)</a>
                    <a href="<?php echo BASE_URL; ?>search_vul.php" class="text-sm font-medium text-red-600 hover:text-red-700">Search (Vulnerable)</a>
                </div>
                <div class="flex items-center space-x-4">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="<?php echo $post_link; ?>" class="hidden sm:flex items-center bg-transparent text-blue-600 px-4 py-2 rounded-lg text-sm font-semibold border border-blue-600 hover:bg-blue-50">
                            <i data-lucide="edit-3" class="w-4 h-4 mr-1"></i>
                            Buat Post
                        </a>
                        <div class="flex items-center space-x-3">
                            <?php if ($current_login_mode === 'aman'): ?>
                                <a href="javascript:void(0)" id="edit-profile-button" class="text-sm font-semibold text-gray-700 hover:text-blue-600">
                                    Hi, <?php echo htmlspecialchars($_SESSION['username']); ?>
                                </a>
                            <?php else: ?>
                                <a href="javascript:void(0)" id="edit-profile-button-rentan" class="text-sm font-semibold text-red-700 hover:text-red-600">
                                    Hi, <?php echo htmlspecialchars($_SESSION['username']); ?> (Rentan)
                                </a>
                            <?php endif; ?>
                            <a href="<?php echo BASE_URL; ?>logout.php" class="text-sm text-red-600 hover:bg-gray-100 px-3 py-1 rounded-md">
                                Log out
                            </a>
                        </div>
                        <?php else: ?>
                        <a href="<?php echo $post_link; ?>" class="hidden sm:flex items-center bg-transparent text-blue-600 px-4 py-2 rounded-lg text-sm font-semibold border border-blue-600 hover:bg-blue-50">
                            <i data-lucide="edit-3" class="w-4 h-4 mr-1"></i>
                            Buat Post
                        </a>
                        <a href="<?php echo BASE_URL; ?>login.php" class="bg-blue-600 text-white px-5 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700">
                            Masuk
                        </a>
                    <?php endif; ?>
                </div> 
            </div>
        </div>
    </header>

<div class="container mx-auto max-w-7xl p-4 mt-4">
    <div class="flex justify-center">
        <main class="w-full lg:max-w-3xl space-y-4">
            <?php
            if (isset($_GET['status']) && $_GET['status'] === 'sukses_rentan' && isset($_GET['file'])) {
                $file_url = BASE_URL . 'uploads/' . htmlspecialchars(urldecode($_GET['file']));
                echo "<div style='padding: 10px; margin-bottom: 15px; background: #ffebee; border: 1px solid #d32f2f; border-radius: 5px; font-weight: bold;'>
                      Upload rentan sukses! File Anda ada di: <a href='$file_url' target='_blank' style='color: #d32f2f; text-decoration: underline;'>$file_url</a>
                      </div>";
            }
            
            $sql = "SELECT 
                        a.id, a.title, a.file_path, a.created_at, u.fullname,
                        COUNT(c.id) AS comment_count 
                    FROM upload_articles AS a
                    JOIN sqli_users_safe AS u ON a.author_id = u.id
                    LEFT JOIN comments AS c ON a.id = c.article_id
                    GROUP BY a.id, a.title, a.file_path, a.created_at, u.fullname
                    ORDER BY a.created_at DESC";
            
            $result = $conn->query($sql);
            if ($result && $result->num_rows > 0):
                while($row = $result->fetch_assoc()):
                    $post_id = $row['id'];
                    $title = htmlspecialchars($row['title']);
                    $created_at = date('d-m-Y H:i', strtotime($row['created_at']));
                    $author_name = htmlspecialchars($row['fullname']);
                    $comment_count = $row['comment_count'];
                    
                    $file_path_raw = $row['file_path'];
                    if (empty($file_path_raw) || !file_exists(__DIR__ . '/' . $file_path_raw)) {
                        $display_image_path = 'https://placehold.co/600x330/333/999?text=Image+Error';
                    } else {
                        $display_image_path = BASE_URL . htmlspecialchars($file_path_raw);
                    }
            ?>
            <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">
                <div class="p-4">
                    <div class="flex items-center space-x-2 text-xs text-gray-500 mt-1">
                        <span class="font-semibold text-gray-800"><?php echo $author_name; ?></span> 
                        <span>•</span>
                        <span><?php echo $created_at; ?></span>
                    </div>
                </div>
                <div class="px-4 pb-4">
                    <a href="<?php echo BASE_URL; ?>Artikel/Artikel1.php?id=<?php echo $post_id; ?>" class="hover:underline">
                        <h2 class="text-xl font-bold text-gray-900 mb-2 leading-tight">
                            <?php echo $title; ?>
                        </h2>
                    </a>
                    <a href="<?php echo BASE_URL; ?>Artikel/Artikel1.php?id=<?php echo $post_id; ?>">
                        <img src="<?php echo $display_image_path; ?>" alt="<?php echo $title; ?>" class="w-full h-auto rounded-lg object-cover border" style="aspect-ratio: 600/330;" onerror="this.src='https://placehold.co/600x330/333/999?text=Image+Error'">
                    </a>
                </div>
                <div class="bg-gray-50 p-3 border-t flex justify-between text-gray-600 text-sm">
                    <div class="flex space-x-4">
                         <a href="<?php echo BASE_URL; ?>Artikel/Artikel1.php?id=<?php echo $post_id; ?>#komentar-section" class="flex items-center space-x-1 hover:text-blue-600 text-gray-600">
                            <i data-lucide="message-circle" class="w-5 h-5"></i>
                            <span><?php echo $comment_count; ?></span>
                        </a>
                    </div>
                </div>
            </div>
            <?php
                endwhile; 
            else:
                echo "<p class='text-center text-gray-500'>Belum ada post.</p>";
            endif;
            ?>
        </main>
    </div>
</div>

<?php
if (isset($_SESSION['user_id'])) {
    if ($current_login_mode === 'aman') {
        include 'modal_profile.php';
    } else {
        include 'modal_profile_rentan.php';
    }
}
?>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        try {
            lucide.createIcons();
        } catch (e) {
            console.error("Lucide icons gagal dimuat:", e);
        }
        
        // --- MODIFIKASI: Javascript untuk DUA Modal ---
        
        // Logika untuk Modal Aman
        const editProfileButton = document.getElementById('edit-profile-button');
        const profileModal = document.getElementById('profile-modal');
        const closeModalButton = document.getElementById('close-modal-button');

        if (editProfileButton && profileModal && closeModalButton) {
            editProfileButton.addEventListener('click', function() {
                profileModal.classList.remove('hidden');
            });
            closeModalButton.addEventListener('click', function() {
                profileModal.classList.add('hidden');
            });
            profileModal.addEventListener('click', function(event) {
                if (event.target === profileModal) {
                    profileModal.classList.add('hidden');
                }
            });
        }

        // Logika untuk Modal Rentan
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

        // Logika Otomatis buka modal jika ada error/sukses
        <?php 
        $modal_error_aman = $_SESSION['modal_error'] ?? null;
        $modal_success_aman = $_SESSION['modal_success'] ?? null;
        $modal_error_rentan = $_SESSION['modal_error_rentan'] ?? null;
        $modal_success_rentan = $_SESSION['modal_success_rentan'] ?? null;
        
        if (($modal_error_aman || $modal_success_aman) && isset($_GET['profile_updated'])): 
        ?>
            if (profileModal) profileModal.classList.remove('hidden');
        <?php 
        elseif (($modal_error_rentan || $modal_success_rentan) && isset($_GET['profile_updated'])): 
        ?>
            if (profileModalRentan) profileModalRentan.classList.remove('hidden');
        <?php endif; ?>
        // --- AKHIR MODIFIKASI JAVASCRIPT ---
    });
</script>

</body>
</html>