<?php
include 'koneksi.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$invoice_id = $_GET['id'] ?? 0;
$user_id_session = $_SESSION['user_id']; // Ambil ID user yang sedang login
$data = null;

// AMAN: Query berdasarkan 'id' DARI URL DAN 'user_id' DARI SESSION
// Ini memastikan user hanya bisa melihat data miliknya sendiri.
$stmt = $conn->prepare("SELECT * FROM invoices WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $invoice_id, $user_id_session);
$stmt->execute();
$result = $stmt->get_result();
if ($result) {
    $data = $result->fetch_assoc();
}
?>
<!DOCTYPE html>
<html>
<head><title>Invoice (Safe)</title><link rel="stylesheet" href="style.css"></head>
<body>
    <div class="container login-box" style="max-width: 600px;">
        <h1>Invoice View (SAFE)</h1>
        <div class="alert alert-success" style="background: #e8f5e9; color: #388e3c; padding: 10px; border-radius: 5px;">
            AMAN: Endpoint ini mengecek ID session.
        </div>
        
        <?php if ($data): ?>
            <h3>Invoice #<?php echo htmlspecialchars($data['id']); ?></h3>
            <p><strong>Owner (User ID):</strong> <?php echo htmlspecialchars($data['user_id']); ?></p>
            <p><strong>Amount:</strong> <?php echo htmlspecialchars($data['amount']); ?></p>
            <p><strong>Description:</strong> <?php echo htmlspecialchars($data['description']); ?></p>
        <?php else: ?>
            <p class='vulnerable-text'>Akses Ditolak. Anda tidak memiliki izin untuk melihat invoice ini atau invoice ini tidak ada.</p>
        <?php endif; ?>
        <p style="text-align: center;"><a href="index.php">Kembali</a></p>
    </div>
</body>
</html>