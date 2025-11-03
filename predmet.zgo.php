<?php
session_start();
include 'povezava.php';

// Preveri, če je uporabnik prijavljen
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zgodovina - Spletna Učilnica</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/theme.css">
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
        
        .subject-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 3rem 0;
            margin-bottom: 2rem;
            border-radius: 0 0 20px 20px;
        }
        
        .task-card {
            box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.1);
            border-radius: 1rem;
            border: none;
            background: white;
            transition: transform 0.3s;
            margin-bottom: 1.5rem;
        }
        
        .task-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 1rem 2rem rgba(0, 0, 0, 0.15);
        }
        
        .task-status {
            padding: 0.5rem 1rem;
            border-radius: 2rem;
            font-weight: 600;
            font-size: 0.85rem;
        }
        
        .status-not-started {
            background-color: #f8f9fa;
            color: #6c757d;
        }
        
        .status-in-progress {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .status-completed {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-late {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            border: none;
            padding: 0.75rem;
            font-weight: 600;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, var(--secondary) 0%, var(--primary) 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
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
        
        .progress-bar {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
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

    <!-- Glava predmeta -->
    <section class="subject-header">
        <div class="container">
            <h1 class="display-4 fw-bold">Zgodovina</h1>
            <p class="lead">4. letnik - Srednja šola</p>
            <div class="d-flex flex-wrap align-items-center mt-3">
                <div class="me-4">
                    <span class="badge bg-light text-dark p-2"><i class="bi bi-person-check me-1"></i> Prof. Marko Kovač</span>
                </div>
                <div class="me-4">
                    <span class="badge bg-light text-dark p-2"><i class="bi bi-people me-1"></i> 24 dijakov</span>
                </div>
                <div>
                    <span class="badge bg-light text-dark p-2"><i class="bi bi-calendar-event me-1"></i> Rok: 15. december 2025</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Vsebina -->
    <main class="container mb-5">
        <div class="row">
            <!-- Stranski meni -->
            <div class="col-lg-3 mb-4">
                <div class="sidebar">
                    <h5 class="mb-3">Navigacija</h5>
                    <a href="#" class="sidebar-link active">
                        <i class="bi bi-house-door"></i> Pregled
                    </a>
                    <a href="#" class="sidebar-link">
                        <i class="bi bi-journal-text"></i> Gradiva
                    </a>
                    <a href="#" class="sidebar-link">
                        <i class="bi bi-check2-square"></i> Naloge
                    </a>
                    <a href="#" class="sidebar-link">
                        <i class="bi bi-people"></i> Sodelujoči
                    </a>
                    <a href="#" class="sidebar-link">
                        <i class="bi bi-graph-up"></i> Ocene
                    </a>
                </div>
            </div>

            <!-- Glavna vsebina -->
            <div class="col-lg-9">
                <div class="row">
                    <!-- Statistika -->
                    <div class="col-md-4 mb-4">
                        <div class="card task-card">
                            <div class="card-body">
                                <h6 class="card-subtitle mb-2 text-muted">Uspešnost</h6>
                                <h3 class="card-title mb-3">90%</h3>
                                <div class="progress">
                                    <div class="progress-bar" role="progressbar" style="width: 90%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card task-card">
                            <div class="card-body">
                                <h6 class="card-subtitle mb-2 text-muted">Opravljene naloge</h6>
                                <h3 class="card-title">14/15</h3>
                                <small class="text-success">Odlično napreduješ!</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card task-card">
                            <div class="card-body">
                                <h6 class="card-subtitle mb-2 text-muted">Naslednji rok</h6>
                                <h3 class="card-title">3 dni</h3>
                                <small class="text-warning">Predstavitev zgodovinskega obdobja</small>
                            </div>
                        </div>
                    </div>

                    <!-- Seznam nalog -->
                    <div class="col-12">
                        <h4 class="mb-4">Aktualne naloge</h4>
                        
                        <!-- Naloga 1 -->
                        <div class="card task-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="card-title mb-0">Raziskava srednjega veka</h5>
                                    <span class="task-status status-in-progress">V delu</span>
                                </div>
                                <p class="card-text">Pripravite predstavitev o življenju v srednjem veku s poudarkom na gospodarstvu.</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">Rok: 15. december 2025</small>
                                    <a href="#" class="btn btn-primary">Odpri nalogo</a>
                                </div>
                            </div>
                        </div>

                        <!-- Naloga 2 -->
                        <div class="card task-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="card-title mb-0">Analiza zgodovinskih virov</h5>
                                    <span class="task-status status-not-started">Ni začeto</span>
                                </div>
                                <p class="card-text">Preučite priložene zgodovinske vire in pripravite kritično analizo.</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">Rok: 20. december 2025</small>
                                    <a href="#" class="btn btn-primary">Odpri nalogo</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Noga -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>O predmetu</h5>
                    <p>Zgodovina je predmet, ki raziskuje preteklost človeštva in pomembne dogodke, ki so oblikovali naš svet.</p>
                </div>
                <div class="col-md-4">
                    <h5>Uporabne povezave</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-light">Učni načrt</a></li>
                        <li><a href="#" class="text-light">Literatura</a></li>
                        <li><a href="#" class="text-light">Spletni viri</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Kontakt</h5>
                    <p>Prof. Marko Kovač<br>marko.kovac@sola.si</p>
                </div>
            </div>
            <hr class="mt-4">
            <div class="text-center">
                <small>&copy; 2025 Spletna Učilnica. Vse pravice pridržane.</small>
            </div>
        </div>
    <?php include __DIR__ . '/includes/footer.php'; ?>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>