<?php
include 'koneksi.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$invoice_id = $_GET['id'] ?? 0;
$data = null;

// RENTAN: Query HANYA berdasarkan 'id' dari URL,
// tidak mengecek 'id' user yang sedang login
$stmt = $conn->prepare("SELECT * FROM invoices WHERE id = ?");
$stmt->bind_param("i", $invoice_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result) {
    $data = $result->fetch_assoc();
}
?>
<!DOCTYPE html>
<html>
<head><title>Invoice (Vulnerable)</title><link rel="stylesheet" href="style.css"></head>
<body>
    <div class="container login-box" style="max-width: 600px;">
        <h1>Invoice View (VULNERABLE)</h1>
        <div class="alert alert-danger" style="background: #ffebee; color: #d32f2f; padding: 10px; border-radius: 5px;">
            RENTAN: Endpoint ini tidak mengecek kepemilikan.
        </div>
        
        <?php if ($data): ?>
            <h3>Invoice #<?php echo htmlspecialchars($data['id']); ?></h3>
            <p><strong>Owner (User ID):</strong> <?php echo htmlspecialchars($data['user_id']); ?></p>
            <p><strong>Amount:</strong> <?php echo htmlspecialchars($data['amount']); ?></p>
            <p><strong>Description:</strong> <?php echo htmlspecialchars($data['description']); ?></p>
        <?php else: ?>
            <p>Invoice tidak ditemukan.</p>
        <?php endif; ?>
        <p style="text-align: center;"><a href="index.php">Kembali</a></p>
    </div>
</body>
</html>