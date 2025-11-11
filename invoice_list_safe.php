<?php
include 'koneksi.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id_session = $_SESSION['user_id'];

// --- MODIFIKASI DIMULAI ---
// SAFE: Memilih invoice milik pengguna yang sedang login DAN
// memastikan itu adalah invoice (Aman) atau invoice lama (tanpa tag).
$sql = "SELECT id, amount, description 
        FROM invoices 
        WHERE user_id = ?
        AND (
            description LIKE '%(Aman)%' 
            OR (description NOT LIKE '%(Aman)%' AND description NOT LIKE '%(Rentan)%')
        )
        ORDER BY id ASC";
        
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id_session);
// --- MODIFIKASI SELESAI ---

$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Invoices (SAFE)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style> body { padding: 2rem; } </style>
</head>
<body>
    <div class="container" style="max-width: 900px;">
        <h1 class="mb-3">SAFE — My Invoices (Your Items)</h1>
        <div class="alert alert-success">
            <b>Aman:</b> Halaman ini hanya menampilkan invoice milik Anda (User ID: <?php echo $user_id_session; ?>).
        </div>
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID Invoice</th>
                    <th>Description</th>
                    <th>Amount</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['description']); ?></td>
                            <td><?php echo htmlspecialchars($row['amount']); ?></td>
                            <td>
                                <a href="invoice_view_safe.php?id=<?php echo $row['id']; ?>" class="btn btn-success btn-sm">
                                    View (SAFE)
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="4">You have no invoices.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
        <a href="index.php" class="btn btn-secondary mt-3">&larr; Back to Dashboard</a>
    </div>
</body>
</html>