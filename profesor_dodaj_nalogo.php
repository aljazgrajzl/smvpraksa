<?php
session_start();
include 'povezava.php';

// Preveri, če je uporabnik prijavljen in je profesor
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'profesor') {
    header("Location: index.php");
    exit();
}

$profesor_id = $_SESSION['user_id'];
$message = '';

// Pridobi predmete, ki jih profesor uči
$predmeti = [];
$stmt = $conn->prepare("SELECT p.id, p.ime_predmeta FROM profesorji_predmeti pp JOIN predmeti p ON pp.predmet_id = p.id WHERE pp.profesor_id = ? ORDER BY p.ime_predmeta");
if ($stmt) {
    $stmt->bind_param("i", $profesor_id);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $predmeti[] = $row;
    }
    $stmt->close();
}

// Pridobi vse razrede
$razredi = [];
$razred_query = "SELECT DISTINCT razred FROM uporabniki WHERE tip_uporabnika = 'dijak' AND razred IS NOT NULL AND razred != '' ORDER BY razred";
$razred_result = $conn->query($razred_query);
if ($razred_result) {
    while ($row = $razred_result->fetch_assoc()) {
        $razredi[] = $row['razred'];
    }
}

// Obdelaj dodajanje naloge
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $predmet_id = (int)($_POST['predmet_id'] ?? 0);
    $razred = trim($_POST['razred'] ?? '');
    $naziv = trim($_POST['naziv'] ?? '');
    $opis = trim($_POST['opis'] ?? '');
    $rok_oddaje = trim($_POST['rok_oddaje'] ?? '');

    if ($predmet_id <= 0 || $razred === '' || $naziv === '') {
        $message = 'Prosim izpolnite vsa obvezna polja (predmet, razred, naziv).';
    } else {
        $ins = $conn->prepare("INSERT INTO naloge (predmet_id, razred, naziv, opis, rok_oddaje, created_by_profesor_id) VALUES (?, ?, ?, ?, ?, ?)");
        if ($ins) {
            $rok = $rok_oddaje !== '' ? $rok_oddaje : null;
            $ins->bind_param("issssi", $predmet_id, $razred, $naziv, $opis, $rok, $profesor_id);
            if ($ins->execute()) {
                $message = 'Naloga je bila uspešno dodana!';
                header("Location: spletna.ucilnica.domstran.php");
                exit();
            } else {
                $message = 'Napaka pri dodajanju naloge: ' . $conn->error;
            }
            $ins->close();
        }
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dodaj nalogo</title>
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
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="bi bi-plus-circle"></i> Dodaj novo nalogo</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($message): ?>
                            <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
                        <?php endif; ?>
                        <form method="post">
                            <div class="mb-3">
                                <label class="form-label">Predmet *</label>
                                <select name="predmet_id" class="form-select" required>
                                    <option value="">-- Izberi predmet --</option>
                                    <?php foreach ($predmeti as $p): ?>
                                        <option value="<?php echo (int)$p['id']; ?>">
                                            <?php echo htmlspecialchars($p['ime_predmeta']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Razred *</label>
                                <select name="razred" class="form-select" required>
                                    <option value="">-- Izberi razred --</option>
                                    <?php foreach ($razredi as $r): ?>
                                        <option value="<?php echo htmlspecialchars($r); ?>">
                                            <?php echo htmlspecialchars($r); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Naziv naloge *</label>
                                <input type="text" name="naziv" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Opis naloge</label>
                                <textarea name="opis" class="form-control" rows="5"></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Rok oddaje</label>
                                <input type="date" name="rok_oddaje" class="form-control">
                            </div>
                            <div class="d-flex justify-content-between">
                                <a href="spletna.ucilnica.domstran.php" class="btn btn-secondary">Nazaj</a>
                                <button type="submit" class="btn btn-primary">Dodaj nalogo</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
