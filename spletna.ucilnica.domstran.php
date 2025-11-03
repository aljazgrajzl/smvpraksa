<?php
session_start();
include 'povezava.php';

// Preprečimo predpomnjenje strani
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Preusmeri če uporabnik ni prijavljen
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Admin uporabnike preusmeri na admin nadzorno ploščo
if ((isset($_SESSION['user_type']) && ($_SESSION['user_type'] === 'admin' || $_SESSION['user_type'] === 'administrator'))) {
    header('Location: admin.php');
    exit();
}

// Preveri če je dijak prvič prijavljen
if ($_SESSION['user_type'] == 'dijak') {
    $check_profil = $conn->prepare("SELECT prvic_prijavljen FROM dijak_profil WHERE dijak_id = ?");
    $check_profil->bind_param("i", $_SESSION['user_id']);
    $check_profil->execute();
    $profil_result = $check_profil->get_result();
    
    if ($profil_result->num_rows == 0) {
        // Dijak še nima profila - ustvari ga
        $insert_profil = $conn->prepare("INSERT INTO dijak_profil (dijak_id, prvic_prijavljen) VALUES (?, TRUE)");
        $insert_profil->bind_param("i", $_SESSION['user_id']);
        $insert_profil->execute();
        $prvic_prijavljen = true;
    } else {
        $profil = $profil_result->fetch_assoc();
        $prvic_prijavljen = $profil['prvic_prijavljen'];
    }
    $check_profil->close();
}

// Funkcija za pridobitev predmetov glede na razred
function getPredmetiByRazred($razred) {
    $letnik = substr($razred, 0, 1);
    $program = substr($razred, -1);
    
    $predmeti = [];
    
    // SKUPNI PREDMETI ZA VSE
    $skupniPredmeti = [
        ['id' => 1, 'naslov' => 'Slovenščina', 'opis' => 'Spoznavanje pisateljev in pesnikov.', 'link' => 'predmet.slo.php', 'slika' => 'https://placehold.co/600x400/4e54c8/white?text=Slovenščina'],
        ['id' => 2, 'naslov' => 'Matematika', 'opis' => 'Osnove algebre, geometrije in računanja.', 'link' => 'predmet.mat.php', 'slika' => 'https://placehold.co/600x400/ff7e5f/white?text=Matematika'],
        ['id' => 3, 'naslov' => 'Angleščina', 'opis' => 'Učenje časov, esejev in ponovitev.', 'link' => 'predmet.ang.php', 'slika' => 'https://placehold.co/600x400/11998e/white?text=Angleščina']
    ];
    
    $predmeti = array_merge($predmeti, $skupniPredmeti);
    
    // SPECIFIČNI PREDMETI
    if ($program == 'a' || $program == 'b') {
        $gimnazijaPredmeti = [
            ['id' => 4, 'naslov' => 'Zgodovina', 'opis' => 'Preučevanje zgodovinskih dogodkov.', 'link' => 'predmet.zgo.php', 'slika' => 'https://placehold.co/600x400/8f94fb/white?text=Zgodovina'],
            ['id' => 5, 'naslov' => 'Geografija', 'opis' => 'Raziskovanje Zemlje in njenih lastnosti.', 'link' => 'predmet.geo.php', 'slika' => 'https://placehold.co/600x400/ff7e5f/white?text=Geografija'],
            ['id' => 6, 'naslov' => 'Sociologija', 'opis' => 'Preučevanje družbe in družbenih pojavov.', 'link' => 'predmet.soc.php', 'slika' => 'https://placehold.co/600x400/11998e/white?text=Sociologija']
        ];
        $predmeti = array_merge($predmeti, $gimnazijaPredmeti);
    } elseif ($program == 'c') {
        $tehniskaPredmeti = [
            ['id' => 7, 'naslov' => 'Računalniški praktikum', 'opis' => 'Programiranje na višjem nivoju.', 'link' => 'predmet.rpr.php', 'slika' => 'https://placehold.co/600x400/8f94fb/white?text=RPR'],
            ['id' => 8, 'naslov' => 'Stroka moderne vsebine', 'opis' => 'Uvod v programiranje in spletni razvoj.', 'link' => 'predmet.smv.php', 'slika' => 'https://placehold.co/600x400/ff7e5f/white?text=SMV'],
            ['id' => 9, 'naslov' => 'Napredna uporaba podatkovnih baz', 'opis' => 'Delov s podatkovnimi bazami in SQL.', 'link' => 'predmet.nup.php', 'slika' => 'https://placehold.co/600x400/11998e/white?text=NUP']
        ];
        $predmeti = array_merge($predmeti, $tehniskaPredmeti);
    }
    
    // Dodaj letnik vsakemu predmetu
    foreach ($predmeti as &$predmet) {
        $predmet['letnik'] = $letnik . '. letnik';
    }
    
    return $predmeti;
}

// Pridobi izbrane predmete dijaka
$izbrani_predmeti = [];
if ($_SESSION['user_type'] == 'dijak') {
    $stmt = $conn->prepare("
        SELECT p.id, p.ime_predmeta as naslov, p.opis, p.link, p.slika 
        FROM dijak_predmeti dp 
        JOIN predmeti p ON dp.predmet_id = p.id 
        WHERE dp.dijak_id = ?
    ");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        // Dodaj letnik predmetu
        $row['letnik'] = substr($_SESSION['razred'], 0, 1) . '. letnik';
        $izbrani_predmeti[] = $row;
    }
    $stmt->close();
}

// Pridobi podatke za profesorja
$profesor_predmeti = [];
$profesor_naloge = [];
if ($_SESSION['user_type'] == 'profesor') {
    // Pridobi predmete, ki jih profesor uči
    $stmt = $conn->prepare("
        SELECT p.id, p.ime_predmeta 
        FROM profesorji_predmeti pp 
        JOIN predmeti p ON pp.predmet_id = p.id 
        WHERE pp.profesor_id = ?
        ORDER BY p.ime_predmeta
    ");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $profesor_predmeti[] = $row;
    }
    $stmt->close();

    // Pridobi naloge, ki jih je razpisal profesor, z število oddaj
    $stmt = $conn->prepare("
        SELECT n.id, n.naziv, n.razred, n.rok_oddaje, p.ime_predmeta,
               (SELECT COUNT(*) FROM oddaje_nalog o WHERE o.naloga_id = n.id) as st_oddaj,
               (SELECT COUNT(DISTINCT u.id) FROM uporabniki u WHERE u.razred = n.razred AND u.tip_uporabnika = 'dijak') as st_dijakov
        FROM naloge n
        JOIN predmeti p ON n.predmet_id = p.id
        WHERE n.created_by_profesor_id = ?
        ORDER BY n.rok_oddaje DESC, n.created_at DESC
    ");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $profesor_naloge[] = $row;
    }
    $stmt->close();
}

// Če dijak še nima izbranih predmetov, prikaži privzete glede na razred
$prikazaniPredmeti = [];
if ($_SESSION['user_type'] == 'dijak' && isset($_SESSION['razred'])) {
    if (empty($izbrani_predmeti) && !$prvic_prijavljen) {
        $prikazaniPredmeti = getPredmetiByRazred($_SESSION['razred']);
    } else {
        $prikazaniPredmeti = $izbrani_predmeti;
    }
}

// Obdelaj odjavo
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Šola - Spletna Učilnica</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
    :root {
        --primary: #3b41c8;
        --secondary: #4349eeff;
        --accent: #2d33b8;
        --light: #f0f2ff;
        --dark: #1e2a4a;
        --info: #e6ebff;
        --hover: #2565b3ff;
        } 
    
    .user-info-bar {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        color: white;
        padding: 0.5rem 0;
        font-size: 0.9rem;
    }
    
    body {
        background-color: #f5f7ff;
        color: #333;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    
    .navbar {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }
    
    .navbar-brand {
        font-weight: 700;
        font-size: 1.5rem;
    }
    
    .hero-section {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        color: white;
        padding: 4rem 0;
        margin-bottom: 3rem;
        border-radius: 0 0 20px 20px;
    }
    
    .course-card {
        border-radius: 1rem;
        overflow: hidden;
        transition: transform 0.3s, box-shadow 0.3s;
        height: 100%;
        border: none;
        box-shadow: 0 5px 15px rgba(78, 84, 200, 0.1);
        border: 1px solid #e9ecef;
    }
    
    .course-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(78, 84, 200, 0.15);
        border-color: var(--primary);
    }
    
    .course-badge {
        position: absolute;
        top: 1rem;
        right: 1rem;
        padding: 0.5rem 1rem;
        border-radius: 2rem;
        font-weight: 600;
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        color: white;
    }
    
    .badge-student {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
    }
    
    .program-info {
        background: white;
        border-radius: 10px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        border-left: 4px solid var(--primary);
    }
    
    .btn-primary {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        border: none;
        padding: 0.75rem 1.5rem;
        font-weight: 600;
    }
    
    .btn-primary:hover {
        background: linear-gradient(135deg, var(--secondary) 0%, var(--primary) 100%);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(78, 84, 200, 0.3);
    }
    
    .btn-success {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        border: none;
    }
    
    .alert-info {
        background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);
        border: 1px solid #c7d2fe;
        color: #374151;
    }
    
    .subject-card {
        border: 2px solid #e9ecef;
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 15px;
        transition: all 0.3s;
        background: white;
    }
    
    .subject-card:hover {
        border-color: var(--primary);
        background-color: #f8f9ff;
        transform: translateY(-2px);
    }
    
    .subject-card.selected {
        border-color: var(--primary);
        background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);
    }
    
    .breadcrumb {
        background-color: #f8f9ff;
        border-radius: 0.5rem;
        padding: 0.75rem 1rem;
    }
    
    .card {
        border: none;
        box-shadow: 0 5px 15px rgba(78, 84, 200, 0.1);
        border-radius: 1rem;
    }
    
    .card-header {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        color: white;
        border-radius: 1rem 1rem 0 0 !important;
        border: none;
    }
    
    .form-control:focus {
        border-color: var(--secondary);
        box-shadow: 0 0 0 0.25rem rgba(78, 84, 200, 0.25);
    }
    
    .badge.bg-secondary {
        background: linear-gradient(135deg, #6b7280 0%, #9ca3af 100%) !important;
    }
    
    .badge.bg-primary {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%) !important;
    }
    
    .list-group-item {
        border: 1px solid #e9ecef;
        border-radius: 0.5rem !important;
        margin-bottom: 0.5rem;
        transition: all 0.3s;
    }
    
    .list-group-item:hover {
        border-color: var(--primary);
        background-color: #f8f9ff;
    }
    
    footer {
        background: linear-gradient(135deg, var(--dark) 0%, #4a5568 100%);
        color: white;
        padding: 3rem 0 1.5rem;
        margin-top: 4rem;
    }
</style>
</head>   
<body>
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
                    <a class="nav-link" href="./logout.php"><i class="bi bi-box-arrow-right"></i> Odjava</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
   

    <!-- Hero Sekcija -->
    <section class="hero-section text-center">
        <div class="container">
            <h1 class="display-4 fw-bold">Dobrodošli v Spletni Učilnici</h1>
            <p class="lead">Moderna platforma za učenje, ki povezuje dijake in profesorje</p>
        </div>
    </section>

    <!-- Vsebina -->
    <main class="container my-5">
        <?php if ($_SESSION['user_type'] == 'profesor'): ?>
            <!-- Profesor Dashboard -->
            <h2 class="text-center my-5">Moje Naloge in Predmeti</h2>
            
            <div class="mb-4 text-end">
                <a href="profesor_dodaj_nalogo.php" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Dodaj novo nalogo
                </a>
            </div>

            <?php if (!empty($profesor_naloge)): ?>
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-list-task"></i> Razpisane naloge</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Naziv naloge</th>
                                        <th>Predmet</th>
                                        <th>Razred</th>
                                        <th>Rok oddaje</th>
                                        <th>Oddaje</th>
                                        <th>Akcije</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($profesor_naloge as $naloga): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($naloga['naziv']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($naloga['ime_predmeta']); ?></td>
                                        <td><span class="badge bg-secondary"><?php echo htmlspecialchars($naloga['razred']); ?></span></td>
                                        <td><?php echo htmlspecialchars($naloga['rok_oddaje'] ?? 'Ni roka'); ?></td>
                                        <td>
                                            <span class="badge bg-info text-dark">
                                                <?php echo (int)$naloga['st_oddaj']; ?> / <?php echo (int)$naloga['st_dijakov']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="profesor_naloga_detalji.php?id=<?php echo (int)$naloga['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye"></i> Poglej
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-info text-center">
                    <h5>Še nimaš razpisanih nalog</h5>
                    <p>Klikni gumb zgoraj, da dodaš prvo nalogo.</p>
                </div>
            <?php endif; ?>

            <?php if (!empty($profesor_predmeti)): ?>
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-book"></i> Moji predmeti</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php foreach ($profesor_predmeti as $predmet): ?>
                                <div class="col-md-4 mb-2">
                                    <span class="badge bg-light text-dark p-2 w-100">
                                        <i class="bi bi-journal-check"></i> <?php echo htmlspecialchars($predmet['ime_predmeta']); ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-warning text-center mt-4">
                    <h5>Nimaš dodeljenih predmetov</h5>
                    <p>Pojdi v <a href="profil.php">profil</a> in izberi predmete, ki jih učiš.</p>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <!-- Dijak Dashboard -->
            <h2 class="text-center my-5">Moji Predmeti</h2>
        
        <?php if ($_SESSION['user_type'] == 'dijak' && !empty($prikazaniPredmeti)): ?>
            <div class="row">
                <?php foreach ($prikazaniPredmeti as $predmet): ?>
                <div class="col-md-4 mb-4">
                    <a href="<?php echo $predmet['link']; ?>" style="text-decoration: none; color: inherit;">
                        <div class="card course-card">
                            <span class="course-badge badge-student text-white"><?php echo $predmet['letnik'] ?? '1. letnik'; ?></span>
                            <img src="<?php echo $predmet['slika']; ?>" class="card-img-top" alt="<?php echo $predmet['naslov']; ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $predmet['naslov']; ?></h5>
                                <p class="card-text"><?php echo $predmet['opis']; ?></p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge bg-primary"><?php echo $predmet['letnik'] ?? '1. letnik'; ?></span>
                                    <span class="text-muted"><i class="bi bi-people-fill"></i> 24 dijakov</span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
            
            <?php if (empty($izbrani_predmeti) && !$prvic_prijavljen): ?>
                <div class="text-center mt-4">
                    <a href="dijak_izbira_predmetov.php" class="btn btn-primary btn-lg">
                        <i class="bi bi-plus-circle"></i> Izberi svoje predmete
                    </a>
                </div>
            <?php endif; ?>
            
        <?php elseif ($_SESSION['user_type'] == 'dijak' && $prvic_prijavljen): ?>
            <div class="alert alert-info text-center">
                <h4>Dobrodošli!</h4>
                <p>Kot nov uporabnik morate najprej izbrati svoje predmete.</p>
                <a href="dijak_izbira_predmetov.php" class="btn btn-primary">Začni z izbiro predmetov</a>
            </div>
        <?php else: ?>
            <div class="alert alert-warning text-center">
                <h4>Ni predmetov</h4>
                <p>Za vaš račun ni na voljo predmetov.</p>
            </div>
        <?php endif; ?>
        <?php endif; ?>
    </main>

    <?php include __DIR__ . '/includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>