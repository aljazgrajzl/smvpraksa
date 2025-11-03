<?php
session_start();
include 'povezava.php';

// Preveri, če je uporabnik prijavljen in je profesor
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'profesor') {
    header("Location: index.php");
    exit();
}

$naloga_id = (int)($_GET['id'] ?? 0);
if ($naloga_id <= 0) {
    header("Location: spletna.ucilnica.domstran.php");
    exit();
}

$profesor_id = $_SESSION['user_id'];

// Pridobi podatke o nalogi
$naloga = null;
$stmt = $conn->prepare("
    SELECT n.*, p.ime_predmeta 
    FROM naloge n 
    JOIN predmeti p ON n.predmet_id = p.id 
    WHERE n.id = ? AND n.created_by_profesor_id = ?
");
if ($stmt) {
    $stmt->bind_param("ii", $naloga_id, $profesor_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows == 1) {
        $naloga = $res->fetch_assoc();
    }
    $stmt->close();
}

if (!$naloga) {
    header("Location: spletna.ucilnica.domstran.php");
    exit();
}

// Pridobi dijake iz tega razreda in njihove oddaje
$dijaki = [];
$stmt = $conn->prepare("
    SELECT u.id, u.ime, u.priimek, u.email, o.oddano_datum, o.datoteka_pot, o.ocena
    FROM uporabniki u
    LEFT JOIN oddaje_nalog o ON u.id = o.dijak_id AND o.naloga_id = ?
    WHERE u.razred = ? AND u.tip_uporabnika = 'dijak'
    ORDER BY u.priimek, u.ime
");
if ($stmt) {
    $stmt->bind_param("is", $naloga_id, $naloga['razred']);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $dijaki[] = $row;
    }
    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Naloga: <?php echo htmlspecialchars($naloga['naziv']); ?></title>
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
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="bi bi-file-earmark-text"></i> <?php echo htmlspecialchars($naloga['naziv']); ?></h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Predmet:</strong> <?php echo htmlspecialchars($naloga['ime_predmeta']); ?></p>
                        <p><strong>Razred:</strong> <span class="badge bg-secondary"><?php echo htmlspecialchars($naloga['razred']); ?></span></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Rok oddaje:</strong> <?php echo htmlspecialchars($naloga['rok_oddaje'] ?? 'Ni roka'); ?></p>
                        <p><strong>Ustvarjeno:</strong> <?php echo htmlspecialchars($naloga['created_at']); ?></p>
                    </div>
                </div>
                <?php if ($naloga['opis']): ?>
                    <hr>
                    <p><strong>Opis:</strong></p>
                    <p><?php echo nl2br(htmlspecialchars($naloga['opis'])); ?></p>
                <?php endif; ?>
                <hr>
                <a href="spletna.ucilnica.domstran.php" class="btn btn-secondary">Nazaj na domačo</a>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="bi bi-people"></i> Oddaje dijakov (<?php echo count($dijaki); ?>)</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($dijaki)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Dijak</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Datum oddaje</th>
                                    <th>Datoteka</th>
                                    <th>Ocena</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($dijaki as $dijak): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($dijak['ime'] . ' ' . $dijak['priimek']); ?></td>
                                    <td><?php echo htmlspecialchars($dijak['email']); ?></td>
                                    <td>
                                        <?php if ($dijak['oddano_datum']): ?>
                                            <span class="badge bg-success">Oddano</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning text-dark">Ni oddano</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($dijak['oddano_datum'] ?? '-'); ?></td>
                                    <td>
                                        <?php if ($dijak['datoteka_pot']): ?>
                                            <a href="<?php echo htmlspecialchars($dijak['datoteka_pot']); ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-download"></i> Prenesi
                                            </a>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($dijak['ocena'] ?? '-'); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">Ni dijakov v razredu <?php echo htmlspecialchars($naloga['razred']); ?>.</div>
                <?php endif; ?>
            </div>
        </div>
        </div>
        <?php include __DIR__ . '/includes/footer.php'; ?>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
