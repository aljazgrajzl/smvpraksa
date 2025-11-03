<?php
session_start();
include 'povezava.php';

// Preusmeri če uporabnik ni prijavljen
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = '';

// Pridobi obstojece podatke
$stmt = $conn->prepare("SELECT ime, priimek, email, razred, tip_uporabnika FROM uporabniki WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();
if ($res && $res->num_rows == 1) {
    $user = $res->fetch_assoc();
} else {
    // Ne najde uporabnika - odjava
    session_destroy();
    header("Location: index.php");
    exit();
}
$stmt->close();

// Pridobi vse razpolozljive predmete (za dijake)
$predmeti_all = [];
$predmeti_stmt = $conn->prepare("SELECT id, ime_predmeta FROM predmeti ORDER BY id");
if ($predmeti_stmt) {
    $predmeti_stmt->execute();
    $pres = $predmeti_stmt->get_result();
    while ($prow = $pres->fetch_assoc()) {
        $predmeti_all[] = $prow;
    }
    $predmeti_stmt->close();
}

// Pridobi trenutno izbrane predmete za tega dijaka
$selected_predmeti = [];
if ($user['tip_uporabnika'] == 'dijak') {
    $sel_stmt = $conn->prepare("SELECT predmet_id FROM dijak_predmeti WHERE dijak_id = ?");
    if ($sel_stmt) {
        $sel_stmt->bind_param("i", $user_id);
        $sel_stmt->execute();
        $sres = $sel_stmt->get_result();
        while ($r = $sres->fetch_assoc()) {
            $selected_predmeti[] = (int)$r['predmet_id'];
        }
        $sel_stmt->close();
    }
} elseif ($user['tip_uporabnika'] == 'profesor') {
    // Pridobi trenutno izbrane predmete za tega profesorja
    $sel_stmt = $conn->prepare("SELECT predmet_id FROM profesorji_predmeti WHERE profesor_id = ?");
    if ($sel_stmt) {
        $sel_stmt->bind_param("i", $user_id);
        $sel_stmt->execute();
        $sres = $sel_stmt->get_result();
        while ($r = $sres->fetch_assoc()) {
            $selected_predmeti[] = (int)$r['predmet_id'];
        }
        $sel_stmt->close();
    }
}

// Obdelaj posodobitev profila
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ime = trim($_POST['ime'] ?? '');
    $priimek = trim($_POST['priimek'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $razred = trim($_POST['razred'] ?? '');
    $new_password = $_POST['new_password'] ?? '';
    $new_password_confirm = $_POST['new_password_confirm'] ?? '';

    // Preveri obvezna polja
    if ($ime === '' || $priimek === '' || $email === '') {
        $message = 'Prosim izpolnite vsa obvezna polja (ime, priimek, email).';
    } elseif ($new_password !== '' && $new_password !== $new_password_confirm) {
        $message = 'Nova gesla se ne ujemata.';
    } else {
        // Preveri, ali je email v uporabi pri drugem uporabniku
        $check = $conn->prepare("SELECT id FROM uporabniki WHERE email = ? AND id != ?");
        $check->bind_param("si", $email, $user_id);
        $check->execute();
        $cres = $check->get_result();
        if ($cres && $cres->num_rows > 0) {
            $message = 'Ta email je že v uporabi pri drugem računu.';
            $check->close();
        } else {
            $check->close();
            // Pripravi UPDATE
            if ($new_password !== '') {
                $hashed = password_hash($new_password, PASSWORD_DEFAULT);
                $upd = $conn->prepare("UPDATE uporabniki SET ime = ?, priimek = ?, email = ?, razred = ?, geslo = ? WHERE id = ?");
                $upd->bind_param("sssssi", $ime, $priimek, $email, $razred, $hashed, $user_id);
            } else {
                $upd = $conn->prepare("UPDATE uporabniki SET ime = ?, priimek = ?, email = ?, razred = ? WHERE id = ?");
                $upd->bind_param("ssssi", $ime, $priimek, $email, $razred, $user_id);
            }

            if ($upd->execute()) {
                $message = 'Profil je bil uspešno posodobljen.';
                // Osvezi session ime/razred
                $_SESSION['user_name'] = $ime . ' ' . $priimek;
                $_SESSION['razred'] = $razred;
                // Ponovno pridobi uporabnika
                $user['ime'] = $ime;
                $user['priimek'] = $priimek;
                $user['email'] = $email;
                $user['razred'] = $razred;

                // Obdelaj izbiro predmetov (za dijake in profesorje)
                if ($user['tip_uporabnika'] == 'dijak') {
                    $posted = $_POST['selected_predmeti'] ?? [];
                    if (!is_array($posted)) { $posted = [$posted]; }
                    $posted = array_map('intval', $posted);

                    // Validiraj id-je glede na obstojece predmete
                    $valid_ids = array_map(function($p){ return (int)$p['id']; }, $predmeti_all);
                    $to_save = array_values(array_intersect($valid_ids, $posted));

                    // Izbrisi obstojece izbire in vstavi nove
                    $del = $conn->prepare("DELETE FROM dijak_predmeti WHERE dijak_id = ?");
                    if ($del) {
                        $del->bind_param("i", $user_id);
                        $del->execute();
                        $del->close();
                    }

                    if (!empty($to_save)) {
                        $ins = $conn->prepare("INSERT INTO dijak_predmeti (dijak_id, predmet_id) VALUES (?, ?)");
                        if ($ins) {
                            foreach ($to_save as $pid) {
                                $ins->bind_param("ii", $user_id, $pid);
                                $ins->execute();
                            }
                            $ins->close();
                        }
                    }

                    // Osvezi polje, da se v formi prikaze pravilno
                    $selected_predmeti = $to_save;
                    $message .= ' Izbira predmetov je bila posodobljena.';
                } elseif ($user['tip_uporabnika'] == 'profesor') {
                    $posted = $_POST['selected_predmeti'] ?? [];
                    if (!is_array($posted)) { $posted = [$posted]; }
                    $posted = array_map('intval', $posted);

                    // Validiraj id-je glede na obstojece predmete
                    $valid_ids = array_map(function($p){ return (int)$p['id']; }, $predmeti_all);
                    $to_save = array_values(array_intersect($valid_ids, $posted));

                    // Izbrisi obstojece izbire in vstavi nove
                    $del = $conn->prepare("DELETE FROM profesorji_predmeti WHERE profesor_id = ?");
                    if ($del) {
                        $del->bind_param("i", $user_id);
                        $del->execute();
                        $del->close();
                    }

                    if (!empty($to_save)) {
                        $ins = $conn->prepare("INSERT INTO profesorji_predmeti (profesor_id, predmet_id) VALUES (?, ?)");
                        if ($ins) {
                            foreach ($to_save as $pid) {
                                $ins->bind_param("ii", $user_id, $pid);
                                $ins->execute();
                            }
                            $ins->close();
                        }
                    }

                    // Osvezi polje, da se v formi prikaze pravilno
                    $selected_predmeti = $to_save;
                    $message .= ' Izbira predmetov je bila posodobljena.';
                }

            } else {
                $message = 'Napaka pri posodabljanju profila: ' . $conn->error;
            }
            $upd->close();
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Uredi profil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/theme.css">
</head>
<body class="bg-light">
    <!-- Navigacija -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="bi bi-book-half"></i> E-Šola
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="spletna.ucilnica.domstran.php">Domov</a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="profil.php"><i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($_SESSION['user_name']); ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-right"></i> Odjava</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">Uredi profil</div>
                    <div class="card-body">
                        <?php if ($message): ?>
                            <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
                        <?php endif; ?>
                        <form method="post">
                            <div class="mb-3">
                                <label class="form-label">Ime</label>
                                <input type="text" name="ime" class="form-control" value="<?php echo htmlspecialchars($user['ime'] ?? ''); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Priimek</label>
                                <input type="text" name="priimek" class="form-control" value="<?php echo htmlspecialchars($user['priimek'] ?? ''); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
                            </div>
                            <?php if ($user['tip_uporabnika'] == 'dijak'): ?>
                            <div class="mb-3">
                                <label class="form-label">Razred (npr. 1a, 2b)</label>
                                <input type="text" name="razred" class="form-control" value="<?php echo htmlspecialchars($user['razred'] ?? ''); ?>">
                            </div>
                            <?php endif; ?>

                            <?php if ($user['tip_uporabnika'] == 'dijak' || $user['tip_uporabnika'] == 'profesor'): ?>
                            <div class="mb-3">
                                <label class="form-label"><?php echo $user['tip_uporabnika'] == 'dijak' ? 'Izberi predmete' : 'Izberi predmete, ki jih učiš'; ?></label>
                                <div class="row">
                                    <?php if (!empty($predmeti_all)): ?>
                                        <?php foreach ($predmeti_all as $p): ?>
                                            <div class="col-md-6">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="selected_predmeti[]" value="<?php echo (int)$p['id']; ?>" id="predmet_<?php echo (int)$p['id']; ?>" <?php echo in_array((int)$p['id'], $selected_predmeti) ? 'checked' : ''; ?>>
                                                    <label class="form-check-label" for="predmet_<?php echo (int)$p['id']; ?>"><?php echo htmlspecialchars($p['ime_predmeta']); ?></label>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="col-12"><small class="text-muted">Ni razpoložljivih predmetov.</small></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                            <hr>
                            <div class="mb-3">
                                <label class="form-label">Novo geslo (pusti prazno če nočeš spreminjati)</label>
                                <input type="password" name="new_password" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Potrdi novo geslo</label>
                                <input type="password" name="new_password_confirm" class="form-control">
                            </div>
                            <div class="d-flex justify-content-between">
                                <a href="spletna.ucilnica.domstran.php" class="btn btn-secondary">Nazaj</a>
                                <button type="submit" class="btn btn-primary">Shrani spremembe</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        </div>
        <?php include __DIR__ . '/includes/footer.php'; ?>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
