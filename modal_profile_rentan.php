<?php
include_once 'koneksi.php';

$current_user_data = null;
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    
    // MODIFIKASI: Mengambil dari tabel 'sqli_users_vul'
    $stmt = $conn->prepare("SELECT username, fullname FROM sqli_users_vul WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $current_user_data = $result->fetch_assoc();
    $stmt->close();
}

// MODIFIKASI: Menggunakan session key yang berbeda
$modal_error_rentan = $_SESSION['modal_error_rentan'] ?? null;
$modal_success_rentan = $_SESSION['modal_success_rentan'] ?? null;
unset($_SESSION['modal_error_rentan']);
unset($_SESSION['modal_success_rentan']);
?>

<div id="profile-modal-rentan" class="hidden fixed inset-0 bg-gray-600 bg-opacity-75 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-lg p-6 relative max-h-[90vh] overflow-y-auto">
        <button id="close-modal-button-rentan" class="absolute top-3 right-3 text-gray-500 hover:text-gray-800">
            <i data-lucide="x" class="w-6 h-6"></i>
        </button>
        <h2 class="text-2xl font-bold mb-4">Edit Profil (RENTAN)</h2>
        <div class="text-sm text-red-700 bg-red-100 px-3 py-1 rounded-full mb-4 inline-block">
            Mode Rentan: Password disimpan sebagai teks biasa.
        </div>

        <?php if ($modal_error_rentan): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span><?php echo $modal_error_rentan; ?></span>
            </div>
        <?php endif; ?>
        <?php if ($modal_success_rentan): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span><?php echo $modal_success_rentan; ?></span>
            </div>
        <?php endif; ?>

        <?php if ($current_user_data): ?>
        <form action="<?php echo BASE_URL; ?>update_profile_rentan.php" method="POST" class="space-y-4">
            
            <div>
                <label for="fullname_rentan" class="block text-sm font-semibold text-gray-700">Nama Lengkap</label>
                <input type="text" name="fullname" id="fullname_rentan" value="<?php echo htmlspecialchars($current_user_data['fullname']); ?>" 
                       class="w-full mt-1 border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-blue-600">
            </div>
            
            <div>
                <label for="username_rentan" class="block text-sm font-semibold text-gray-700">Username</label>
                <input type="text" name="username" id="username_rentan" value="<?php echo htmlspecialchars($current_user_data['username']); ?>" 
                       class="w-full mt-1 border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-blue-600" required>
            </div>
            
            <hr class="my-4">
            
            <h3 class="text-lg font-semibold">Ubah Password</h3>
            
            <div>
                <label for="current_password_rentan" class="block text-sm font-semibold text-gray-700">Password Saat Ini (Wajib)</label>
                <input type="password" name="current_password" id="current_password_rentan" 
                       class="w-full mt-1 border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-blue-600" required>
            </div>
            
            <div>
                <label for="new_password_rentan" class="block text-sm font-semibold text-gray-700">Password Baru</label>
                <input type="password" name="new_password" id="new_password_rentan" 
                       class="w-full mt-1 border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-blue-600">
            </div>

            <div>
                <label for="confirm_password_rentan" class="block text-sm font-semibold text-gray-700">Konfirmasi Password Baru</label>
                <input type="password" name="confirm_password" id="confirm_password_rentan" 
                       class="w-full mt-1 border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-blue-600">
            </div>

            <button type="submit" 
                    class="w-full bg-red-600 text-white p-3 rounded-lg text-center font-semibold hover:bg-red-700 transition duration-200">
                Simpan Perubahan (Rentan)
            </button>
        </form>
        <?php endif; ?>
    </div>
</div>