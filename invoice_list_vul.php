<?php
include 'koneksi.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// VULNERABLE: Memilih SEMUA invoice dari semua pengguna
// MODIFIKASI: Menggunakan UNION untuk menggabungkan data dari kedua tabel user
// agar nama Author (Pemilik) tampil dengan benar.
$sql = "(
    -- Ambil invoice untuk user AMAN (dan invoice lama yang tidak punya tag)
    SELECT 
        i.id, i.amount, i.description, 
        u_safe.username AS author_name
    FROM invoices i
    JOIN sqli_users_safe u_safe ON i.user_id = u_safe.id
    WHERE 
        i.description LIKE '%(Aman)%' 
        OR (i.description NOT LIKE '%(Aman)%' AND i.description NOT LIKE '%(Rentan)%')
)
UNION
(
    -- Ambil invoice untuk user RENTAN
    SELECT 
        i.id, i.amount, i.description, 
        u_vul.username AS author_name
    FROM invoices i
    JOIN sqli_users_vul u_vul ON i.user_id = u_vul.id
    WHERE 
        i.description LIKE '%(Rentan)%'
)
ORDER BY id ASC";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>All Invoices (VULNERABLE)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style> body { padding: 2rem; } </style>
</head>
<body>
    <div class="container" style="max-width: 900px;">
        <h1 class="mb-3">VULN — All Invoices</h1>
        <div class="alert alert-danger">
            <b>Rentan (Broken Access Control):</b> Halaman ini menampilkan invoice milik <em>semua</em> pengguna, tidak hanya milik Anda.
        </div>
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID Invoice</th>
                    <th>Author (Pemilik)</th>
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
                            <td><?php echo htmlspecialchars($row['author_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['description']); ?></td>
                            <td><?php echo htmlspecialchars($row['amount']); ?></td>
                            <td>
                                <a href="invoice_view_vul.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm">
                                    View (VULN)
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5">No invoices found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
        <a href="index.php" class="btn btn-secondary mt-3">&larr; Back to Dashboard</a>
    </div>
</body>
</html>