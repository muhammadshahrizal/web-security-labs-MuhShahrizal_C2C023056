<?php
// web_keamanan_data/search_vul.php (MODIFIED to search articles AND comments)
// lab/demo: intentionally vulnerable — DO NOT USE IN PRODUCTION
include 'koneksi.php'; 
$q = $_GET['q'] ?? '';
$results = [];

if ($q !== '') {
    
    // VULNERABLE: concatenation => SQLi demonstration with UNION
    $sql = "
        (SELECT 
            a.id AS result_id, 
            a.title AS result_title, 
            a.body AS result_body, 
            a.created_at, 
            u.fullname AS author_name,
            'Artikel' AS result_type,
            a.id AS article_id
        FROM upload_articles a
        LEFT JOIN sqli_users_safe u ON a.author_id = u.id
        WHERE a.title LIKE '%$q%' 
           OR a.body LIKE '%$q%' 
           OR u.fullname LIKE '%$q%')
        
        UNION
        
        (SELECT 
            c.id AS result_id, 
            c.comment_text AS result_title,
            NULL AS result_body, 
            c.created_at, 
            u.fullname AS author_name,
            'Komentar' AS result_type,
            c.article_id
        FROM comments c
        LEFT JOIN sqli_users_safe u ON c.user_id = u.id
        WHERE c.comment_text LIKE '%$q%' 
           OR u.fullname LIKE '%$q%')
           
        ORDER BY created_at DESC
        LIMIT 50";

    $result = $conn->query($sql);
    if ($result) {
        $results = $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Search (VULN) - Artikel & Komentar</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background: linear-gradient(120deg,#f8fafc 0%, #eef6ff 100%); min-height:100vh; }
    .search-card { max-width:980px; margin:42px auto; border-radius:12px; box-shadow:0 10px 30px rgba(15,23,42,0.06); }
    .brand { width:56px; height:56px; border-radius:10px; display:inline-flex; align-items:center; justify-content:center; background:#fff; box-shadow:0 4px 12px rgba(16,24,40,0.06); font-weight:700; color:#0d6efd; }
    .result-item { padding:14px; border-radius:8px; background:#fff; box-shadow:0 6px 18px rgba(15,23,42,0.03); margin-bottom:12px; }
    .meta { color:#6c757d; font-size:.9rem; }
    .note { font-size:.85rem; color:#6c757d; }
    .vuln-badge { font-size:.75rem; background:#ffe9e9; color:#b02a37; padding:4px 8px; border-radius:999px; }
    .result-title { font-size: 1.1rem; font-weight: 600; color: #212529; text-decoration: none; }
    .result-title:hover { color: #dc3545; }
    .type-badge { font-size: .7rem; font-weight: 600; padding: .15rem .4rem; border-radius: 4px; vertical-align: middle; margin-right: 5px; }
    .type-artikel { background-color: #e0e7ff; color: #4338ca; }
    .type-komentar { background-color: #fef3c7; color: #92400e; }
  </style>
</head>
<body>
  <div class="card search-card">
    <div class="card-body p-4">
      <div class="d-flex align-items-center mb-3">
        <div class="brand me-3">LAB</div>
        <div>
          <h4 class="mb-0">Search (VULNERABLE)</h4>
          <div class="note">Mencari Artikel DAN Komentar.</div>
        </div>
        <div class="ms-auto">
          <span class="vuln-badge">VULNERABLE</span>
            <a class="btn btn-outline-warning btn-sm" href="index.php">Kembali</a>
        </div>
      </div>

      <form class="row g-2 align-items-center" method="get" action="">
        <div class="col-md-9">
          <input name="q" class="form-control" placeholder="Cari artikel atau komentar..." value="<?php echo $q; ?>">
        </div>
        <div class="col-md-3 d-grid">
          <button class="btn btn-danger" type="submit">Search</button>
        </div>
      </form>

      <?php if ($q !== ''): ?>
        <hr class="my-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div>
            <h5 class="mb-0">Hasil untuk: 
                <small class="text-muted"><?php echo $q; ?></small>
            </h5>
          </div>
          <div class="text-end">
            <small class="text-muted"><?php echo count($results); ?> hasil</small>
          </div>
        </div>
        
        <?php if ($conn->error): ?>
            <div class="alert alert-danger">
                <strong>SQL Error:</strong> <?php echo htmlspecialchars($conn->error); ?>
            </div>
        <?php endif; ?>


        <?php if (empty($results) && !$conn->error): ?>
          <div class="alert alert-info">Tidak ada hasil.</div>
        <?php else: ?>
          <div>
            <?php 
            foreach ($results as $r): 
                $is_artikel = ($r['result_type'] === 'Artikel');
                
                // MODIFIKASI: Hapus &mode=vuln dari link
                $link_url = BASE_URL . "Artikel/Artikel1.php?id=" . $r['article_id'];
                if (!$is_artikel) {
                    $link_url .= "#komentar-section";
                }
            ?>
              <div class="result-item">
                <div class="d-flex justify-content-between">
                  <div>
                    <span class="type-badge <?php echo $is_artikel ? 'type-artikel' : 'type-komentar'; ?>">
                        <?php echo $r['result_type']; ?>
                    </span>
                    <a href="<?php echo $link_url; ?>" class="result-title">
                        <?php 
                            // VULNERABLE: Output mentah (Reflected XSS)
                            echo $r['result_title'] ?? 'N/A'; 
                        ?>
                    </a>
                    <div class="meta">
                        Oleh: <strong><?php echo htmlspecialchars($r['author_name'] ?? 'Guest'); ?></strong> | 
                        <?php echo htmlspecialchars($r['created_at'] ?? ''); ?>
                    </div>
                  </div>
                </div>

                <?php if ($is_artikel && !empty($r['result_body'])): ?>
                <div class="mt-2 text-break" style="word-wrap: break-word;">
                  <?php 
                    // VULNERABLE: Output mentah (Reflected XSS)
                    echo $r['result_body']; 
                  ?>
                </div>
                <?php endif; ?>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      <?php endif; ?>

    </div>
  </div>
</body>
</html>