<?php
session_start();
include 'povezava.php';

// Allow only admin users (support legacy 'administrator')
$__role = $_SESSION['user_type'] ?? '';
if (!isset($_SESSION['user_id']) || !in_array($__role, ['admin','administrator'])) {
    header('Location: index.php');
    exit();
}

// Stats
$counts = [
    'predmeti' => 0,
    'profesorji' => 0,
    'dijaki' => 0
];

// Count subjects
$res = $conn->query("SELECT COUNT(*) AS c FROM predmeti");
if ($res) { $counts['predmeti'] = (int)$res->fetch_assoc()['c']; }
// Count teachers
$res = $conn->query("SELECT COUNT(*) AS c FROM uporabniki WHERE tip_uporabnika='profesor'");
if ($res) { $counts['profesorji'] = (int)$res->fetch_assoc()['c']; }
// Count students
$res = $conn->query("SELECT COUNT(*) AS c FROM uporabniki WHERE tip_uporabnika='dijak'");
if ($res) { $counts['dijaki'] = (int)$res->fetch_assoc()['c']; }

$conn->close();
?>
<!DOCTYPE html>
<html lang="sl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin nadzorna plošča</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
  <link rel="stylesheet" href="assets/theme.css">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark">
  <div class="container">
    <a class="navbar-brand" href="admin.php"><i class="bi bi-shield-lock"></i> Admin</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav"><span class="navbar-toggler-icon"></span></button>
    <div class="collapse navbar-collapse" id="nav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link" href="admin_predmeti.php">Predmeti</a></li>
        <li class="nav-item"><a class="nav-link" href="admin_ucitelji.php">Učitelji</a></li>
        <li class="nav-item"><a class="nav-link" href="admin_dijaki.php">Učenci</a></li>
      </ul>
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="profil.php"><i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($_SESSION['user_name']); ?></a></li>
        <li class="nav-item"><a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-right"></i> Odjava</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container py-5">
  <h1 class="mb-4">Admin nadzorna plošča</h1>
  <div class="row g-3 mb-4">
    <div class="col-md-4">
      <div class="card text-center">
        <div class="card-body">
          <h5 class="card-title"><i class="bi bi-journal-text"></i> Predmeti</h5>
          <p class="display-6"><?php echo $counts['predmeti']; ?></p>
          <a href="admin_predmeti.php" class="btn btn-primary btn-sm">Uredi predmete</a>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card text-center">
        <div class="card-body">
          <h5 class="card-title"><i class="bi bi-person-workspace"></i> Učitelji</h5>
          <p class="display-6"><?php echo $counts['profesorji']; ?></p>
          <a href="admin_ucitelji.php" class="btn btn-primary btn-sm">Uredi učitelje</a>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card text-center">
        <div class="card-body">
          <h5 class="card-title"><i class="bi bi-people"></i> Učenci</h5>
          <p class="display-6"><?php echo $counts['dijaki']; ?></p>
          <a href="admin_dijaki.php" class="btn btn-primary btn-sm">Uredi učence</a>
        </div>
      </div>
    </div>
  </div>

</div>
    </div>
    <?php include __DIR__ . '/includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
