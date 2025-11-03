<?php
session_start();
include 'povezava.php';

// Preusmeri če uporabnik ni prijavljen ali ni profesor
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'profesor') {
    header("Location: index.php");
    exit();
}

// Pridobi podatke o profesorju
$profesor_id = $_SESSION['user_id'];
$profesor_ime = $_SESSION['user_name'];

// Pridobi razrede ki jih profesor uči (vsi razredi iz dijakov)
$razredi_query = "SELECT DISTINCT razred FROM uporabniki WHERE tip_uporabnika = 'dijak' ORDER BY razred";
$razredi_result = $conn->query($razredi_query);

$vsi_razredi = [];
if ($razredi_result && $razredi_result->num_rows > 0) {
    while($row = $razredi_result->fetch_assoc()) {
        $vsi_razredi[] = $row['razred'];
    }
}

// Pridobi predmete ki jih profesor uči (poenostavljena verzija)
$moji_predmeti = [];
try {
    $predmeti_query = "SELECT p.ime_predmeta 
                       FROM profesorji_predmeti pp 
                       JOIN predmeti p ON pp.predmet_id = p.id 
                       WHERE pp.profesor_id = ?";
    $stmt = $conn->prepare($predmeti_query);
    if ($stmt) {
        $stmt->bind_param("i", $profesor_id);
        $stmt->execute();
        $predmeti_result = $stmt->get_result();
        
        if ($predmeti_result->num_rows > 0) {
            while($row = $predmeti_result->fetch_assoc()) {
                $moji_predmeti[] = $row['ime_predmeta'];
            }
        }
        $stmt->close();
    }
} catch (Exception $e) {
    // Če tabela ne obstaja, uporabi privzete predmete
    $moji_predmeti = ['Matematika', 'Fizika'];
}

// Če še nimaš tabele profesorji_predmeti, uporabi privzete podatke
if (empty($moji_predmeti)) {
    $moji_predmeti = ['Matematika', 'Fizika'];
}

// Pridobi dijake za izbrani razred
$izbrani_razred = isset($_GET['razred']) ? $_GET['razred'] : (count($vsi_razredi) > 0 ? $vsi_razredi[0] : '');
$dijaki_v_razredu = [];

if ($izbrani_razred) {
    try {
        $dijaki_query = "SELECT id, ime, priimek, email, razred FROM uporabniki WHERE tip_uporabnika = 'dijak' AND razred = ? ORDER BY priimek, ime";
        $stmt = $conn->prepare($dijaki_query);
        if ($stmt) {
            $stmt->bind_param("s", $izbrani_razred);
            $stmt->execute();
            $dijaki_result = $stmt->get_result();
            
            if ($dijaki_result->num_rows > 0) {
                while($row = $dijaki_result->fetch_assoc()) {
                    $dijaki_v_razredu[] = $row;
                }
            }
            $stmt->close();
        }
    } catch (Exception $e) {
        // Napaka pri pridobivanju dijakov
        $dijaki_v_razredu = [];
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nadzorna Plošča - Profesor</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
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
        
        .teacher-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 2.5rem 0;
            margin-bottom: 2rem;
            border-radius: 0 0 20px 20px;
        }
        
        .class-card {
            box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.1);
            border-radius: 1rem;
            border: none;
            background: white;
            margin-bottom: 2rem;
            transition: transform 0.3s;
        }
        
        .class-card:hover {
            transform: translateY(-5px);
        }
        
        .student-card {
            border-left: 4px solid var(--primary);
            transition: all 0.3s;
        }
        
        .student-card:hover {
            background-color: #f8f9fa;
            transform: translateX(5px);
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
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }
        
        .class-tabs {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.1);
            padding: 1rem;
            margin-bottom: 2rem;
        }
        
        .class-tab {
            padding: 1rem 1.5rem;
            border: none;
            background: none;
            border-radius: 0.5rem;
            margin-right: 0.5rem;
            transition: all 0.3s;
            font-weight: 500;
            text-decoration: none;
            color: inherit;
            display: inline-block;
        }
        
        .class-tab.active {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
        }
        
        .class-tab:hover:not(.active) {
            background-color: #f8f9fa;
        }
        
        .stats-card {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
            text-align: center;
            margin-bottom: 1.5rem;
        }
        
        .stats-number {
            font-size: 2.5rem;
            font-weight: bold;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .sidebar {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
            height: fit-content;
        }
        
        .sidebar-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            color: var(--dark);
            text-decoration: none;
            margin-bottom: 0.5rem;
            transition: all 0.3s;
        }
        
        .sidebar-link:hover, .sidebar-link.active {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
        }
        
        .sidebar-link i {
            margin-right: 0.75rem;
            font-size: 1.2rem;
        }
        
        footer {
            background: linear-gradient(135deg, var(--dark) 0%, #4a5568 100%);
            color: white;
            padding: 3rem 0 1.5rem;
            margin-top: 4rem;
        }
        
        .student-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1.2rem;
        }
        
        .predmet-badge {
            background: linear-gradient(135deg, var(--accent) 0%, #feb47b 100%);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.8rem;
            margin-right: 0.5rem;
            margin-bottom: 0.5rem;
            display: inline-block;
        }
        
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #6c757d;
        }
        
        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }
    </style>
</head>
<body>
    <!-- Navigacija -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="spletna.ucilnica.domstran.php">
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
                    <li class="nav-item">
                        <a class="nav-link active" href="profesor.php">Nadzorna plošča</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="profesor_predmeti.php">Moji predmeti</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="profesor_naloge.php">Naloge</a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="profil.php"><i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($profesor_ime); ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="?logout=1"><i class="bi bi-box-arrow-right"></i></a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Glava profesorjeve strani -->
    <section class="teacher-header">
        <div class="container">
            <h1 class="display-5 fw-bold">Nadzorna plošča</h1>
            <p class="lead">Pregled razredov in dijakov</p>
            <?php if (!empty($moji_predmeti)): ?>
            <div class="mt-3">
                <h6 class="mb-2">Moji predmeti:</h6>
                <?php foreach ($moji_predmeti as $predmet): ?>
                    <span class="predmet-badge"><?php echo htmlspecialchars($predmet); ?></span>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Vsebina -->
    <main class="container mb-5">
        <div class="row">
            <!-- Stranski meni -->
            <div class="col-lg-3 mb-4">
                <div class="sidebar">
                    <h5 class="mb-3">Hitri dostop</h5>
                    <a href="profesor.php" class="sidebar-link active">
                        <i class="bi bi-speedometer2"></i> Nadzorna plošča
                    </a>
                    <a href="profesor_dodaj_nalogo.php" class="sidebar-link">
                        <i class="bi bi-plus-circle"></i> Nova naloga
                    </a>
                    <a href="profesor_ocene.php" class="sidebar-link">
                        <i class="bi bi-journal-check"></i> Vnos ocen
                    </a>
                    <a href="profesor_gradiva.php" class="sidebar-link">
                        <i class="bi bi-folder"></i> Gradiva
                    </a>
                    <a href="profesor_sporocila.php" class="sidebar-link">
                        <i class="bi bi-chat-dots"></i> Sporočila
                    </a>
                    
                    <h5 class="mt-4 mb-3">Statistika</h5>
                    <div class="stats-card">
                        <div class="stats-number"><?php echo count($vsi_razredi); ?></div>
                        <div class="text-muted">Razredi</div>
                    </div>
                    <div class="stats-card">
                        <div class="stats-number"><?php echo count($dijaki_v_razredu); ?></div>
                        <div class="text-muted">Dijaki v razredu</div>
                    </div>
                    <div class="stats-card">
                        <div class="stats-number"><?php echo count($moji_predmeti); ?></div>
                        <div class="text-muted">Predmeti</div>
                    </div>
                </div>
            </div>
            
            <!-- Glavna vsebina -->
            <div class="col-lg-9">
                <!-- Zavihki za razrede -->
                <div class="class-tabs">
                    <h5 class="mb-3">Moji razredi:</h5>
                    <div class="d-flex flex-wrap">
                        <?php if (!empty($vsi_razredi)): ?>
                            <?php foreach ($vsi_razredi as $razred): ?>
                                <a href="?razred=<?php echo urlencode($razred); ?>" 
                                   class="class-tab <?php echo $razred == $izbrani_razred ? 'active' : ''; ?>">
                                    <?php echo htmlspecialchars($razred); ?> razred
                                </a>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted">Ni razredov v bazi.</p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Vsebina izbranega razreda -->
                <?php if ($izbrani_razred): ?>
                <div class="class-content">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3><?php echo htmlspecialchars($izbrani_razred); ?> razred</h3>
                        <div>
                            <button class="btn btn-primary me-2">
                                <i class="bi bi-download"></i> Izvozi podatke
                            </button>
                            <button class="btn btn-outline-primary">
                                <i class="bi bi-printer"></i> Natisni
                            </button>
                        </div>
                    </div>
                    
                    <!-- Seznam dijakov -->
                    <div class="row">
                        <?php if (!empty($dijaki_v_razredu)): ?>
                            <?php foreach ($dijaki_v_razredu as $dijak): ?>
                                <div class="col-md-6 mb-3">
                                    <div class="card student-card">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="student-avatar me-3">
                                                    <?php 
                                                    // VARNI način za inicialke
                                                    $inicialke = '';
                                                    if (!empty($dijak['ime']) && !empty($dijak['priimek'])) {
                                                        $inicialke = strtoupper(
                                                            substr($dijak['ime'], 0, 1) . 
                                                            substr($dijak['priimek'], 0, 1)
                                                        );
                                                    } else {
                                                        $inicialke = '??';
                                                    }
                                                    echo htmlspecialchars($inicialke);
                                                    ?>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1"><?php echo htmlspecialchars($dijak['ime'] . ' ' . $dijak['priimek']); ?></h6>
                                                    <p class="text-muted small mb-1">Razred: <?php echo htmlspecialchars($dijak['razred']); ?></p>
                                                    <p class="text-muted small mb-1"><?php echo htmlspecialchars($dijak['email']); ?></p>
                                                    <div class="progress mb-1" style="height: 6px;">
                                                        <div class="progress-bar" style="width: 0%;"></div>
                                                    </div>
                                                    <small class="text-muted">Podatki bodo prikazani kasneje</small>
                                                </div>
                                                <div class="text-end">
                                                    <span class="badge bg-success">Aktiven</span>
                                                    <div class="mt-1">
                                                        <button class="btn btn-sm btn-outline-primary" onclick="showStudentDetails(<?php echo (int)$dijak['id']; ?>)">
                                                            <i class="bi bi-eye"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="col-12">
                                <div class="empty-state">
                                    <i class="bi bi-people"></i>
                                    <h4>Ni dijakov v tem razredu</h4>
                                    <p>V razredu <?php echo htmlspecialchars($izbrani_razred); ?> še ni registriranih dijakov.</p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="bi bi-people"></i>
                        <h4>Ni razredov na voljo</h4>
                        <p>V sistemu še ni registriranih razredov.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4 mb-md-0">
                    <h5 class="text-uppercase">E-Šola</h5>
                    <p>Vodilna platforma za spletno učenje v Sloveniji. Povezujemo dijake, profesorje in šole v enostavnem virtualnem okolju.</p>
                </div>
                <div class="col-md-2 mb-4 mb-md-0">
                    <h5 class="text-uppercase">Povezave</h5>
                    <ul class="list-unstyled">
                        <li><a href="https://www.instagram.com" target="_blank" class="text-white">Instagram</a></li>
                        <li><a href="https://www.facebook.com" target="_blank" class="text-white">Facebook</a></li>
                        <li><a href="https://www.tiktok.com/@ker.sccelje" target="_blank" class="text-white">TikTok</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5 class="text-uppercase">Kontakt</h5>
                    <address>
                        <i class="bi bi-geo-alt"></i> Pot na lavo 22<br>
                        3000 Celje<br>
                        <i class="bi bi-envelope"></i> <a href="mailto:info@e-sola.si" class="text-white">info@e-sola.si</a><br>
                        <i class="bi bi-telephone"></i> +386 1 234 56 78
                    </address>
                </div>
            </div>
            <div class="text-center border-top pt-3 mt-3">
                <p>&copy; 2025 E-Šola.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Funkcija za prikaz podrobnosti dijaka
        function showStudentDetails(studentId) {
            alert('Podrobnosti za dijaka z ID: ' + studentId + '\n\nTa funkcionalnost bo implementirana v naslednji različici.');
        }
    </script>
</body>
</html>