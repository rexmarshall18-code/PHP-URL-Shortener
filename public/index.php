<?php require_once __DIR__ . '/../config.php'; ?>

<?php
// --- Handle POST first (PRG) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $url = trim($_POST['url'] ?? '');
    if ($url === '') {
        header('Location: index.php?err=empty'); exit;
    }

    $insert = $conn->prepare("INSERT INTO urls (url) VALUES (:url)");
    $insert->execute([':url' => $url]);

    header('Location: index.php?ok=1');
    exit;
}

// --- Pagination setup ---
$limit  = 20;
$page   = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $limit;

// total rows for pagination
$total = (int)$conn->query("SELECT COUNT(*) FROM urls")->fetchColumn();
$total_pages = max(1, (int)ceil($total / $limit));

// fetch current page data
$stmt = $conn->prepare('SELECT * FROM urls ORDER BY id DESC LIMIT :lim OFFSET :off');
$stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
$stmt->bindValue(':off', $offset, PDO::PARAM_INT);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_OBJ);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" crossorigin="anonymous">
    <style>
    
       .container { 
        max-width: 80% !important; 
     }

    
       .form-wrap { 
        max-width: 700px; 
        margin: 0 auto; 
     }

       .table {
        table-layout: fixed;
        width: 100%;
     }

       .table td, .table th {
        vertical-align: middle;
        white-space: normal;
        word-break: break-word;
        overflow-wrap: anywhere;
     }

       .qr-cell img {
        width: 48px;
        height: 48px;
        display: inline-block;
     }

       .table-responsive {
        max-height: 70vh;
        overflow-y: auto;
     }
</style>

    </style>
</head>
<body>

<div class="container">
    <div class="row gx-4 gx-lg-5 justify-content-center">
        <div class="col-12">
            <form class="card p-2 margin form-wrap" method="POST" action="index.php">
                <div class="input-group">
                    <input type="text" name="url" class="form-control" placeholder="your url">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-success">Shorten</button>
                    </div>
                </div>
            </form>
        </div>
   </div>
</div>

<div class="container">
    <div class="row gx-4 gx-lg-5 justify-content-center">
        <div class="col-12">

            <div class="table-responsive">
  <table class="table mt-4">
    <colgroup>
      <col style="width:45%">
      <col style="width:35%">
      <col style="width:10%">
      <col style="width:10%">
    </colgroup>
    <thead>
      <tr>
        <th scope="col">Long url</th>
        <th scope="col">Short url</th>
        <th scope="col">QR</th>
        <th scope="col">Clicks</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($rows as $row): ?>
        <?php
          $short = "http://localhost/short-urls/u/index.php?id=" . (int)$row->id;
          $qrSrc = "https://api.qrserver.com/v1/create-qr-code/?size=80x80&data=" . urlencode($short);
        ?>
        <tr>
          <th scope="row" class="text-break"><?= htmlspecialchars($row->url, ENT_QUOTES, 'UTF-8') ?></th>
          <td class="align-middle text-break">
            <div class="d-flex align-items-center">
              <a href="<?= $short ?>" target="_blank"><?= $short ?></a>
              <button
                type="button"
                class="btn btn-sm btn-outline-secondary ml-2 px-2"
                onclick="copyText('<?= htmlspecialchars($short, ENT_QUOTES, 'UTF-8') ?>', this)">
                Copy
              </button>
            </div>
          </td>
          <td class="align-middle qr-cell"><img alt="QR" src="<?= $qrSrc ?>"></td>
          <td class="align-middle"><?= (int)$row->clicks ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>


            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center mt-3">
                    <li class="page-item <?= $page<=1?'disabled':'' ?>">
                        <a class="page-link" href="?page=<?= max(1, $page-1) ?>">Prev</a>
                    </li>
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?= $page>=$total_pages?'disabled':'' ?>">
                        <a class="page-link" href="?page=<?= min($total_pages, $page+1) ?>">Next</a>
                    </li>
                </ul>
            </nav>
            <?php endif; ?>

        </div>
     </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"></script>
<script>
function copyText(text, btn) {
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(function () {
            if (btn) { const old = btn.textContent; btn.textContent = 'Copied'; setTimeout(()=>btn.textContent=old, 1200); }
        });
    } else {
        const ta = document.createElement('textarea'); ta.value = text; document.body.appendChild(ta);
        ta.select(); try { document.execCommand('copy'); } catch(e) {}
        document.body.removeChild(ta);
        if (btn) { const old = btn.textContent; btn.textContent = 'Copied'; setTimeout(()=>btn.textContent=old, 1200); }
    }
}
</script>
</body>
</html>
