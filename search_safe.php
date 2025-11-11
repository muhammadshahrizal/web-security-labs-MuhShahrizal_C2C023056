<?php
// web_keamanan_data/search_safe.php (MODIFIED to search articles AND comments)
include 'koneksi.php'; 

$q = trim((string)($_GET['q'] ?? ''));
$results = [];
$error = null;

if ($q !== '') {
    try {
        // SAFE: prepared statement with UNION
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
            WHERE LOWER(a.title) LIKE ? 
               OR LOWER(a.body) LIKE ? 
               OR LOWER(u.fullname) LIKE ?)
            
            UNION
            
            (SELECT 
                c.id AS result_id, 
                c.comment_text AS result_title, -- Tampilkan teks komentar sebagai 'judul'
                NULL AS result_body, -- Komentar tidak punya 'body'
                c.created_at, 
                u.fullname AS author_name,
                'Komentar' AS result_type,
                c.article_id -- ID artikel tempat komentar itu berada
            FROM comments c
            LEFT JOIN sqli_users_safe u ON c.user_id = u.id
            WHERE LOWER(c.comment_text) LIKE ? 
               OR LOWER(u.fullname) LIKE ?)
               
            ORDER BY created_at DESC
            LIMIT 50";
        
        $stmt = $conn->prepare($sql);
        
        $like = '%' . mb_strtolower($q, 'UTF-8') . '%';
        
        $stmt->bind_param("sssss", $like, $like, $like, $like, $like);
        
        $stmt->execute();
        $result = $stmt->get_result();
        $results = $result->fetch_all(MYSQLI_ASSOC);

    } catch (Exception $e) {
        $error = 'Terjadi kesalahan saat mencari: ' . $e->getMessage();
    }
}

// helper
function safe_highlight(string $text, string $query, int $truncate = 200): string {
    $escaped = htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    
    if (mb_strlen($escaped) > $truncate) {
        $escaped = mb_substr($escaped, 0, $truncate) . '...';
    }

    if ($query === '') return nl2br($escaped);
    
    $safe_q = htmlspecialchars($query, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    $pattern = '/' . preg_quote($safe_q, '/') . '/iu';
    $highlighted = preg_replace($pattern, '<mark>$0</mark>', $escaped);
    return nl2br($highlighted);
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Search (SAFE) - Artikel & Komentar</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background: linear-gradient(120deg,#f8fafc 0%, #eef6ff 100%); min-height:100vh; }
    .search-card { max-width:980px; margin:42px auto; border-radius:12px; box-shadow:0 10px 30px rgba(15,23,42,0.06); }
    .brand { width:56px; height:56px; border-radius:10px; display:inline-flex; align-items:center; justify-content:center; background:#fff; box-shadow:0 4px 12px rgba(16,24,40,0.06); font-weight:700; color:#0d6efd; }
    .result-item { padding:14px; border-radius:8px; background:#fff; box-shadow:0 6px 18px rgba(15,23,42,0.03); margin-bottom:12px; }
    .meta { color:#6c757d; font-size:.9rem; }
    .note { font-size:.85rem; color:#6c757d; }
    .safe-badge { font-size:.75rem; background:#e6f7ff; color:#055160; padding:4px 8px; border-radius:999px; }
    mark { background:#ffe58f; padding:0 .15rem; border-radius:.15rem; }
    .count-badge { font-weight:600; color:#495057; }
    .result-title { font-size: 1.1rem; font-weight: 600; color: #212529; text-decoration: none; }
    .result-title:hover { color: #0d6efd; }
    .type-badge { font-size: .7rem; font-weight: 600; padding: .15rem .4rem; border-radius: 4px; vertical-align: middle; margin-right: 5px; }
    .type-artikel { background-color: #e0e7ff; color: #4338ca; }
    .type-komentar { background-color: #fef3c7; color: #92400e; }
  </style>
</head>
<body>
  <div class="card search-card">
    <div class="card-body p-4">
      <div class="d-flex align-items-center mb-3">
        <div class="brand me-3">SAFE</div>
        <div>
          <h4 class="mb-0">Search (SAFE)</h4>
          <div class="note">Mencari Artikel DAN Komentar.</div>
        </div>
        <div class="ms-auto">
          <span class="safe-badge">SAFE</span>
            <a class="btn btn-outline-warning btn-sm" href="index.php">Kembali</a>
        </div>
      </div>

      <form class="row g-2 align-items-center" method="get" action="">
        <div class="col-md-9">
           <input name="q" class="form-control" placeholder="Cari artikel atau komentar..." value="<?php echo htmlspecialchars($q, ENT_QUOTES, 'UTF-8'); ?>" autofocus>
        </div>
        <div class="col-md-3 d-grid">
          <button class="btn btn-success" type="submit">Search</button>
        </div>
      </form>

      <?php if ($q !== ''): ?>
        <hr class="my-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div>
            <h5 class="mb-0">Hasil untuk: 
                <small class="text-muted"><?php echo htmlspecialchars($q, ENT_QUOTES, 'UTF-8'); ?></small>
            </h5>
          </div>
          <div class="text-end">
            <span class="count-badge"><?php echo count($results); ?> hasil</span>
          </div>
        </div>

        <?php if ($error): ?>
          <div class="alert alert-danger"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <?php if (empty($results)): ?>
          <div class="alert alert-info">Tidak ada hasil untuk pencarian ini.</div>
        <?php else: ?>
          <div>
            <?php 
            foreach ($results as $r): 
                $is_artikel = ($r['result_type'] === 'Artikel');
                
                // MODIFIKASI: Hapus &mode=safe dari link
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
                            echo safe_highlight((string)($r['result_title'] ?? 'N/A'), $q, 150); 
                        ?>
                    </a>
                    <div class="meta">
                        Oleh: <strong><?php echo htmlspecialchars($r['author_name'] ?? 'Guest'); ?></strong> | 
                        <?php echo htmlspecialchars($r['created_at'] ?? ''); ?>
                    </div>
                  </div>
                </div>

                <?php if ($is_artikel && !empty($r['result_body'])): ?>
                <div class="mt-2 text-break">
                  <?php echo safe_highlight((string)$r['result_body'], $q, 250); ?>
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