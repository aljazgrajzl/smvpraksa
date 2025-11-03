<?php
session_start();
include 'povezava.php';
$__role = $_SESSION['user_type'] ?? '';
if (!isset($_SESSION['user_id']) || !in_array($__role, ['admin','administrator'])) { header('Location:index.php'); exit(); }

$message = '';

// Fetch all subjects for assignment
$all_predmeti = [];
$r = $conn->query("SELECT id, ime_predmeta FROM predmeti ORDER BY ime_predmeta");
if ($r) { while($row=$r->fetch_assoc()) { $all_predmeti[]=$row; } }

// Handle delete teacher
if (isset($_GET['delete'])) {
  $id = (int)$_GET['delete'];
  // remove subject links
  $stmt = $conn->prepare("DELETE FROM profesorji_predmeti WHERE profesor_id = ?"); if ($stmt){$stmt->bind_param('i',$id);$stmt->execute();$stmt->close();}
  $stmt = $conn->prepare("DELETE FROM uporabniki WHERE id = ? AND tip_uporabnika='profesor'"); if ($stmt){$stmt->bind_param('i',$id);$stmt->execute();$stmt->close();}
  $message='Učitelj izbrisan.';
}

// Handle create/update teacher + assignments
if ($_SERVER['REQUEST_METHOD']==='POST') {
  $id = (int)($_POST['id'] ?? 0);
  $ime = trim($_POST['ime'] ?? '');
  $priimek = trim($_POST['priimek'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $geslo = $_POST['geslo'] ?? '';
  $predmeti = $_POST['predmeti'] ?? [];
  if (!is_array($predmeti)) $predmeti = [$predmeti];
  $predmeti = array_map('intval', $predmeti);
  if ($ime===''||$priimek===''||$email===''){ $message='Izpolni ime, priimek, email.'; }
  else {
    if ($id>0){
      $stmt = $conn->prepare("UPDATE uporabniki SET ime=?, priimek=?, email=? WHERE id=? AND tip_uporabnika='profesor'");
      $stmt->bind_param('sssi',$ime,$priimek,$email,$id); $stmt->execute(); $stmt->close();
    } else {
      $hash = password_hash($geslo!==''?$geslo:'geslo123', PASSWORD_DEFAULT);
      $stmt = $conn->prepare("INSERT INTO uporabniki (ime, priimek, email, geslo, tip_uporabnika) VALUES (?,?,?,?, 'profesor')");
      $stmt->bind_param('ssss',$ime,$priimek,$email,$hash); $stmt->execute(); $id = $stmt->insert_id; $stmt->close();
    }
    // replace assignments
    $del = $conn->prepare("DELETE FROM profesorji_predmeti WHERE profesor_id=?"); if ($del){$del->bind_param('i',$id);$del->execute();$del->close();}
    if (!empty($predmeti)){
      $ins = $conn->prepare("INSERT INTO profesorji_predmeti (profesor_id, predmet_id) VALUES (?,?)");
      if ($ins){ foreach($predmeti as $pid){ $ins->bind_param('ii',$id,$pid); $ins->execute(); } $ins->close(); }
    }
    $message = 'Učitelj in dodeljeni predmeti shranjeni.';
  }
}

// Edit load
$edit = null; $edit_predmeti = [];
if (isset($_GET['edit'])){
  $eid = (int)$_GET['edit'];
  $s = $conn->prepare("SELECT id, ime, priimek, email FROM uporabniki WHERE id=? AND tip_uporabnika='profesor'");
  $s->bind_param('i',$eid); $s->execute(); $res=$s->get_result(); if ($res && $res->num_rows===1){ $edit=$res->fetch_assoc(); }
  $s->close();
  $q = $conn->prepare("SELECT predmet_id FROM profesorji_predmeti WHERE profesor_id=?");
  $q->bind_param('i',$eid); $q->execute(); $r=$q->get_result(); while($row=$r->fetch_assoc()){ $edit_predmeti[]=(int)$row['predmet_id']; } $q->close();
}

// List teachers
$ucitelji=[];
$u = $conn->query("SELECT id, ime, priimek, email FROM uporabniki WHERE tip_uporabnika='profesor' ORDER BY priimek, ime");
if ($u){ while($row=$u->fetch_assoc()){ $ucitelji[]=$row; } }
$conn->close();
?>
<!DOCTYPE html><html lang="sl"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin - Učitelji</title>
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
        <li class="nav-item"><a class="nav-link" href="admin_predmeti.php">Predmeti</a></li>
        <li class="nav-item"><a class="nav-link active" href="admin_ucitelji.php">Učitelji</a></li>
        <li class="nav-item"><a class="nav-link" href="admin_dijaki.php">Učenci</a></li>
      </ul>
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="profil.php"><i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($_SESSION['user_name']); ?></a></li>
        <li class="nav-item"><a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-right"></i> Odjava</a></li>
      </ul>
    </div>
  </div>
</nav>
<div class="container py-4">
  <h2 class="mb-3">Učitelji</h2>
  <?php if ($message): ?><div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>
  <div class="card mb-4"><div class="card-header bg-primary text-white"><?php echo $edit? 'Uredi učitelja' : 'Dodaj učitelja'; ?></div>
    <div class="card-body">
      <form method="post">
        <input type="hidden" name="id" value="<?php echo (int)($edit['id'] ?? 0); ?>">
        <div class="row g-3">
          <div class="col-md-3"><label class="form-label">Ime *</label><input class="form-control" name="ime" required value="<?php echo htmlspecialchars($edit['ime'] ?? ''); ?>"></div>
          <div class="col-md-3"><label class="form-label">Priimek *</label><input class="form-control" name="priimek" required value="<?php echo htmlspecialchars($edit['priimek'] ?? ''); ?>"></div>
          <div class="col-md-4"><label class="form-label">Email *</label><input class="form-control" type="email" name="email" required value="<?php echo htmlspecialchars($edit['email'] ?? ''); ?>"></div>
          <?php if (!$edit): ?>
            <div class="col-md-2"><label class="form-label">Geslo</label><input class="form-control" type="password" name="geslo" placeholder="(privzeto: geslo123)"></div>
          <?php endif; ?>
          <div class="col-12"><label class="form-label">Predmeti, ki jih uči</label>
            <div class="row">
              <?php foreach ($all_predmeti as $p): ?>
                <div class="col-md-4">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="predmeti[]" id="p<?php echo (int)$p['id']; ?>" value="<?php echo (int)$p['id']; ?>" <?php echo in_array((int)$p['id'], $edit_predmeti)?'checked':''; ?>>
                    <label class="form-check-label" for="p<?php echo (int)$p['id']; ?>"><?php echo htmlspecialchars($p['ime_predmeta']); ?></label>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
        <div class="mt-3 d-flex justify-content-end gap-2">
          <a class="btn btn-secondary" href="admin_ucitelji.php">Prekliči</a>
          <button class="btn btn-primary" type="submit"><?php echo $edit? 'Shrani' : 'Dodaj'; ?></button>
        </div>
      </form>
    </div>
  </div>

  <div class="card">
    <div class="card-header bg-primary text-white">Seznam učiteljev</div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover"><thead><tr><th>ID</th><th>Ime</th><th>Email</th><th>Akcije</th></tr></thead><tbody>
          <?php foreach ($ucitelji as $u): ?>
            <tr>
              <td><?php echo (int)$u['id']; ?></td>
              <td><?php echo htmlspecialchars($u['priimek'] . ', ' . $u['ime']); ?></td>
              <td><?php echo htmlspecialchars($u['email']); ?></td>
              <td>
                <a class="btn btn-sm btn-outline-primary" href="admin_ucitelji.php?edit=<?php echo (int)$u['id']; ?>"><i class="bi bi-pencil"></i></a>
                <a class="btn btn-sm btn-outline-danger" href="admin_ucitelji.php?delete=<?php echo (int)$u['id']; ?>" onclick="return confirm('Izbrišem učitelja?')"><i class="bi bi-trash"></i></a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody></table>
      </div>
    </div>
  </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
</body></html>
