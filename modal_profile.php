<?php
include_once 'koneksi.php';

$current_user_data = null;
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    // MODIFIKASI: Tidak lagi mengambil profile_picture_path
    $stmt = $conn->prepare("SELECT username, fullname FROM sqli_users_safe WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $current_user_data = $result->fetch_assoc();
    $stmt->close();
}

$modal_error = $_SESSION['modal_error'] ?? null;
$modal_success = $_SESSION['modal_success'] ?? null;
unset($_SESSION['modal_error']);
unset($_SESSION['modal_success']);
?>

<div id="profile-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-75 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-lg p-6 relative max-h-[90vh] overflow-y-auto">
        <button id="close-modal-button" class="absolute top-3 right-3 text-gray-500 hover:text-gray-800">
            <i data-lucide="x" class="w-6 h-6"></i>
        </button>
        <h2 class="text-2xl font-bold mb-4">Edit Profil</h2>

        <?php if ($modal_error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span><?php echo $modal_error; ?></span>
            </div>
        <?php endif; ?>
        <?php if ($modal_success): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span><?php echo $modal_success; ?></span>
            </div>
        <?php endif; ?>

        <?php if ($current_user_data): ?>
        <form action="<?php echo BASE_URL; ?>update_profile.php" method="POST" class="space-y-4" enctype="multipart/form-data">
            
            <div>
                <label for="fullname" class="block text-sm font-semibold text-gray-700">Nama Lengkap</label>
                <input type="text" name="fullname" id="fullname" value="<?php echo htmlspecialchars($current_user_data['fullname']); ?>" 
                       class="w-full mt-1 border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-blue-600">
            </div>
            
            <div>
                <label for="username" class="block text-sm font-semibold text-gray-700">Username</label>
                <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($current_user_data['username']); ?>" 
                       class="w-full mt-1 border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-blue-600" required>
            </div>
            
            <hr class="my-4">
            
            <h3 class="text-lg font-semibold">Ubah Password</h3>
            <p class="text-sm text-gray-500">Isi password saat ini untuk menyimpan perubahan. Biarkan kosong jika tidak ingin mengubah password.</p>
            
            <div>
                <label for="current_password" class="block text-sm font-semibold text-gray-700">Password Saat Ini (Wajib)</label>
                <input type="password" name="current_password" id="current_password" 
                       class="w-full mt-1 border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-blue-600" required>
            </div>
            
            <div>
                <label for="new_password" class="block text-sm font-semibold text-gray-700">Password Baru</label>
                <input type="password" name="new_password" id="new_password" 
                       class="w-full mt-1 border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-blue-600">
            </div>

            <div>
                <label for="confirm_password" class="block text-sm font-semibold text-gray-700">Konfirmasi Password Baru</label>
                <input type="password" name="confirm_password" id="confirm_password" 
                       class="w-full mt-1 border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-blue-600">
            </div>

            <button type="submit" 
                    class="w-full bg-blue-600 text-white p-3 rounded-lg text-center font-semibold hover:bg-blue-700 transition duration-200">
                Simpan Perubahan
            </button>
        </form>
        <?php endif; ?>
    </div>
</div>