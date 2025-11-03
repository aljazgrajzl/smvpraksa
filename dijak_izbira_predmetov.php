<?php
session_start();
include 'povezava.php';

// Preusmeri če uporabnik ni prijavljen ali ni dijak
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'dijak') {
    header("Location: index.php");
    exit();
}

// Določi program glede na razred
$program = 'vsi';
if (isset($_SESSION['razred'])) {
    $zadnji_znak = substr($_SESSION['razred'], -1);
    if ($zadnji_znak == 'c') {
        $program = 'tehniska';
    } elseif ($zadnji_znak == 'a' || $zadnji_znak == 'b') {
        $program = 'gimnazija';
    }
}

// Določi razpoložljive predmete iz datotek
// Map subject short codes to DB IDs used across the site (these IDs match those used in spletna.ucilnica.domstran.php)
$predmeti_info = [
    // db_id => numeric id in `predmeti` table
    'ang' => ['db_id' => 3, 'ime' => 'Angleščina', 'tip' => 'vsi', 'opis' => 'Angleški jezik', 'file' => 'predmet.ang.php'],
    'mat' => ['db_id' => 2, 'ime' => 'Matematika', 'tip' => 'vsi', 'opis' => 'Matematika', 'file' => 'predmet.mat.php'],
    'slo' => ['db_id' => 1, 'ime' => 'Slovenščina', 'tip' => 'vsi', 'opis' => 'Slovenski jezik in književnost', 'file' => 'predmet.slo.php'],
    'geo' => ['db_id' => 5, 'ime' => 'Geografija', 'tip' => 'vsi', 'opis' => 'Geografija', 'file' => 'predmet.geo.php'],
    'zgo' => ['db_id' => 4, 'ime' => 'Zgodovina', 'tip' => 'vsi', 'opis' => 'Zgodovina', 'file' => 'predmet.zgo.php'],
    'soc' => ['db_id' => 6, 'ime' => 'Sociologija', 'tip' => 'gimnazija', 'opis' => 'Sociologija', 'file' => 'predmet.soc.php'],
    'nup' => ['db_id' => 9, 'ime' => 'NUP', 'tip' => 'tehniska', 'opis' => 'Načrtovanje in uporaba podatkovnih baz', 'file' => 'predmet.nup.php'],
    'rpr' => ['db_id' => 7, 'ime' => 'RPR', 'tip' => 'tehniska', 'opis' => 'Računalniški praktikum', 'file' => 'predmet.rpr.php'],
    'smv' => ['db_id' => 8, 'ime' => 'SMV', 'tip' => 'tehniska', 'opis' => 'Stroka moderne vede', 'file' => 'predmet.smv.php'],
];

// Pridobi vse predmete za program (vsi_predmeti will contain db_id and code)
$vsi_predmeti = [];
try {
    // Uporabi predmete iz seznama namesto iz baze
    foreach ($predmeti_info as $koda => $info) {
        if ($info['tip'] == 'vsi' || $info['tip'] == $program) {
            $predmet = [
                'code' => $koda,
                'db_id' => $info['db_id'],
                'ime_predmeta' => $info['ime'],
                'opis' => $info['opis'],
                'tip_programa' => $info['tip'],
                'file' => $info['file'] ?? ''
            ];
            $vsi_predmeti[] = $predmet;
        }
    }
} catch (Exception $e) {
    echo "<script>alert('Napaka pri pripravi seznama predmetov.');</script>";
}

// Fetch user's existing selections so we can pre-check them later
$user_selected = [];
if ($_SESSION['user_type'] == 'dijak') {
    $sel_stmt = $conn->prepare("SELECT predmet_id FROM dijak_predmeti WHERE dijak_id = ?");
    if ($sel_stmt) {
        $sel_stmt->bind_param("i", $_SESSION['user_id']);
        $sel_stmt->execute();
        $res = $sel_stmt->get_result();
        while ($r = $res->fetch_assoc()) {
            $user_selected[] = (int)$r['predmet_id'];
        }
        $sel_stmt->close();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['izbrani_predmeti'])) {
    $izbrani = $_POST['izbrani_predmeti'];
    // Ensure numeric values
    $izbrani = array_map('intval', $izbrani);

    // Preveri, če so izbrani predmeti veljavni (veljavni db_id-ji)
    $veljavni_db_ids = array_map(function($i){ return $i['db_id']; }, $predmeti_info);
    $izbrani = array_values(array_intersect($izbrani, $veljavni_db_ids));

    if (!empty($izbrani)) {
        try {
            // Izbriši prejšnje izbire
            $delete_stmt = $conn->prepare("DELETE FROM dijak_predmeti WHERE dijak_id = ?");
            $delete_stmt->bind_param("i", $_SESSION['user_id']);
            $delete_stmt->execute();

            // Dodaj nove izbire (shranimo db_id kot int)
            foreach ($izbrani as $predmet_id) {
                $insert_stmt = $conn->prepare("INSERT INTO dijak_predmeti (dijak_id, predmet_id) VALUES (?, ?)");
                $insert_stmt->bind_param("ii", $_SESSION['user_id'], $predmet_id);
                $insert_stmt->execute();
            }

            // Po uspehu osvežimo seznam izbranih predmetov (da so checkboxi vidni po redirectu če se vrne)
            header("Location: spletna.ucilnica.domstran.php");
            exit();
        } catch (Exception $e) {
            echo "<script>alert('Napaka pri shranjevanju izbir: " . $e->getMessage() . "');</script>";
        }
    } else {
        echo "<script>alert('Prosim izberite vsaj en predmet.');</script>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Izberi Predmete - E-Šola</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .subject-card {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            transition: all 0.3s;
        }
        .subject-card:hover {
            border-color: #4e54c8;
            background-color: #f8f9ff;
        }
        .subject-card.selected {
            border-color: #4e54c8;
            background-color: #eef2ff;
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
                            <a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-right"></i> Odjava</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>



    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="bi bi-bookmark-check"></i> Izberite svoje predmete</h4>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <h6>Pozdravljeni, <?php echo $_SESSION['user_name']; ?>!</h6>
                            <p class="mb-0">Izberite predmete, ki jih boste obiskovali. Izbire lahko kasneje spremenite v meniju "Uredi predmete".</p>
                        </div>
                        
                        <?php if (empty($vsi_predmeti)): ?>
                            <div class="alert alert-warning">
                                <h5>Ni najdenih predmetov za vaš program!</h5>
                                <a href="spletna.ucilnica.domstran.php" class="btn btn-secondary">Nazaj na domačo stran</a>
                            </div>
                        <?php else: ?>
                        <form method="POST" id="predmetiForm">
                            <div class="row">
                                <div class="col-12 mb-4">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Izbira</th>
                                                    <th>Predmet</th>
                                                    <th>Opis</th>
                                                    <th>Program</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($vsi_predmeti as $predmet): ?>
                                                <tr>
                                                    <td class="text-center" style="width: 80px;">
                                                        <div class="form-check">
                                    <input class="form-check-input" type="checkbox" 
                                        name="izbrani_predmeti[]" 
                                        value="<?php echo $predmet['db_id']; ?>" 
                                        id="predmet<?php echo $predmet['db_id']; ?>" 
                                        <?php if (!empty($user_selected) && in_array($predmet['db_id'], $user_selected)) echo 'checked'; ?>>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <label class="form-check-label" for="predmet<?php echo $predmet['db_id']; ?>">
                                                            <strong><?php echo $predmet['ime_predmeta']; ?></strong>
                                                        </label>
                                                    </td>
                                                    <td><?php echo $predmet['opis']; ?></td>
                                                    <td>
                                                        <span class="badge <?php 
                                                            echo $predmet['tip_programa'] == 'vsi' ? 'bg-success' : 
                                                                ($predmet['tip_programa'] == 'gimnazija' ? 'bg-primary' : 'bg-info'); 
                                                            ?>">
                                                            <?php 
                                                            echo $predmet['tip_programa'] == 'vsi' ? 'Vsi programi' : 
                                                                ($predmet['tip_programa'] == 'gimnazija' ? 'Gimnazija' : 'Tehniška'); 
                                                            ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                            </div>
                            
                            <div class="mt-4 border-top pt-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <small class="text-muted" id="selectedCount">Izbrali ste 0 predmetov</small>
                                    </div>
                                    <div>
                                        <button type="submit" class="btn btn-primary btn-lg">
                                            <i class="bi bi-check-circle"></i> Shrani izbire
                                        </button>
                                        <a href="spletna.ucilnica.domstran.php" class="btn btn-outline-secondary">Preskoči za zdaj</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Šteje izbrane predmete
        function updateSelectedCount() {
            const selected = document.querySelectorAll('input[name="izbrani_predmeti[]"]:checked').length;
            document.getElementById('selectedCount').textContent = `Izbrali ste ${selected} predmetov`;
        }

        // Dodaj event listenerje
        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('input[name="izbrani_predmeti[]"]');
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateSelectedCount);
                
                // Dodaj CSS class za izbrane (če obstaja element s to classy)
                checkbox.addEventListener('change', function() {
                    const card = this.closest('.subject-card');
                    if (!card) return;
                    if (this.checked) {
                        card.classList.add('selected');
                    } else {
                        card.classList.remove('selected');
                    }
                });
            });
            
            updateSelectedCount(); // Inicializacija
        });
    </script>
</body>
</html>