<?php
session_start();
include 'povezava.php';
$__role = $_SESSION['user_type'] ?? '';
if (!isset($_SESSION['user_id']) || !in_array($__role, ['admin','administrator'])) { header('Location:index.php'); exit(); }

$message = '';

// Handle delete
if (isset($_GET['delete'])) {
  $id = (int)$_GET['delete'];     
  $stmt = $conn->prepare("DELETE FROM predmeti WHERE id = ?");
  if ($stmt) { $stmt->bind_param('i', $id); $stmt->execute(); $stmt->close(); $message = 'Predmet izbrisan.'; }
}

// Handle create/update
if ($_SERVER['REQUEST_METHOD']==='POST') {
  $id = (int)($_POST['id'] ?? 0);
  $ime = trim($_POST['ime_predmeta'] ?? '');
  $opis = trim($_POST['opis'] ?? '');
  $link = trim($_POST['link'] ?? '');
  $slika = trim($_POST['slika'] ?? '');
  if ($ime === '' || $link === '') { $message = 'Ime predmeta in link sta obvezna.'; }
  else {
    if ($id > 0) {
      $stmt = $conn->prepare("UPDATE predmeti SET ime_predmeta=?, opis=?, link=?, slika=? WHERE id=?");
      $stmt->bind_param('ssssi', $ime, $opis, $link, $slika, $id);
      $stmt->execute(); $stmt->close(); $message = 'Predmet posodobljen.';
    } else {
      $stmt = $conn->prepare("INSERT INTO predmeti (ime_predmeta, opis, link, slika) VALUES (?,?,?,?)");
      $stmt->bind_param('ssss', $ime, $opis, $link, $slika);
      $stmt->execute(); $stmt->close(); $message = 'Predmet dodan.';
    }
  }
}

// Edit load
$edit = null;
if (isset($_GET['edit'])) {
  $id = (int)$_GET['edit'];
  $res = $conn->prepare("SELECT * FROM predmeti WHERE id = ?");
  $res->bind_param('i', $id); $res->execute(); $r = $res->get_result();
  if ($r && $r->num_rows===1) { $edit = $r->fetch_assoc(); }
  $res->close();
}

// List subjects
$predmeti = [];
$res = $conn->query("SELECT * FROM predmeti ORDER BY id");
if ($res) { while($row = $res->fetch_assoc()) { $predmeti[]=$row; } }
$conn->close();
?>
<!DOCTYPE html>
<html lang="sl">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin - Predmeti</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
  <link rel="stylesheet" href="assets/theme.css">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark">
  <div class="container">
    <a class="navbar-brand" href="admin.php"><i class="bi bi-shield-lock"></i> Admin</a>
    <div class="collapse navbar-collapse show">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link active" href="admin_predmeti.php">Predmeti</a></li>
        <li class="nav-item"><a class="nav-link" href="admin_ucitelji.php">Učitelji</a></li>
        <li class="nav-item"><a class="nav-link" href="admin_dijaki.php">Učenci</a></li>
      </ul>
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="profil.php"><i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($_SESSION['user_name'] ?? ''); ?></a></li>
        <li class="nav-item"><a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-right"></i> Odjava</a></li>
      </ul>
    </div>
  </div>
</nav>
<div class="container py-4">
  <h2 class="mb-3">Predmeti</h2>
  <?php if ($message): ?><div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>
  <div class="card mb-4"><div class="card-header bg-primary text-white"><?php echo $edit? 'Uredi predmet' : 'Dodaj predmet'; ?></div>
    <div class="card-body">
      <form method="post">
        <input type="hidden" name="id" value="<?php echo (int)($edit['id'] ?? 0); ?>">
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label">Ime predmeta *</label>
            <input class="form-control" name="ime_predmeta" required value="<?php echo htmlspecialchars($edit['ime_predmeta'] ?? ''); ?>">
          </div>
          <div class="col-md-4">
            <label class="form-label">Link *</label>
            <input class="form-control" name="link" required value="<?php echo htmlspecialchars($edit['link'] ?? ''); ?>" placeholder="predmet.xxx.php">
          </div>
          <div class="col-md-4">
            <label class="form-label">Slika (URL)</label>
            <input class="form-control" name="slika" value="<?php echo htmlspecialchars($edit['slika'] ?? ''); ?>">
          </div>
          <div class="col-12">
            <label class="form-label">Opis</label>
            <textarea class="form-control" name="opis" rows="3"><?php echo htmlspecialchars($edit['opis'] ?? ''); ?></textarea>
          </div>
        </div>
        <div class="mt-3 d-flex justify-content-end gap-2">
          <a href="admin_predmeti.php" class="btn btn-secondary">Prekliči</a>
          <button class="btn btn-primary" type="submit"><?php echo $edit? 'Shrani' : 'Dodaj'; ?></button>
        </div>
      </form>
    </div>
  </div>

  <div class="card">
    <div class="card-header bg-primary text-white">Seznam predmetov</div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover">
          <thead><tr><th>ID</th><th>Ime</th><th>Link</th><th>Akcije</th></tr></thead>
          <tbody>
          <?php foreach ($predmeti as $p): ?>
            <tr>
              <td><?php echo (int)$p['id']; ?></td>
              <td><?php echo htmlspecialchars($p['ime_predmeta']); ?></td>
              <td><code><?php echo htmlspecialchars($p['link']); ?></code></td>
              <td>
                <a class="btn btn-sm btn-outline-primary" href="admin_predmeti.php?edit=<?php echo (int)$p['id']; ?>"><i class="bi bi-pencil"></i></a>
                <a class="btn btn-sm btn-outline-danger" href="admin_predmeti.php?delete=<?php echo (int)$p['id']; ?>" onclick="return confirm('Izbrišem predmet?');"><i class="bi bi-trash"></i></a>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
</body></html>
