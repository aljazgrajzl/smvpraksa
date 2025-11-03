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
    <title>Stroka moderne vede - Spletna Učilnica</title>
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
            <h1 class="display-4 fw-bold">Stroka moderne vede</h1>
            <p class="lead">4. letnik - Srednja šola</p>
            <div class="d-flex flex-wrap align-items-center mt-3">
                <div class="me-4">
                    <span class="badge bg-light text-dark p-2"><i class="bi bi-person-check me-1"></i> Prof. Andraž Pušnik</span>
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
                    <a href="#Gradiva" class="sidebar-link">
                        <i class="bi bi-journal-text"></i> Gradiva
                    </a>
                    
                    <h5 class="mt-4 mb-3">Napredek</h5>
                    <div class="progress mb-2" style="height: 10px;">
                        <div class="progress-bar" role="progressbar" style="width: 65%;" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <p class="small text-muted">65% zaključeno</p>
                    
                    <h5 class="mt-4 mb-3">Roki</h5>
                    <div class="d-flex align-items-center mb-2">
                        <div class="flex-shrink-0">
                            <i class="bi bi-calendar-check text-primary"></i>
                        </div>
                        <div class="flex-grow-1 ms-2">
                            <p class="mb-0 small">Domača naloga 1</p>
                            <p class="mb-0 small text-muted">5. okt 2025</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                        <div class="flex-shrink-0">
                            <i class="bi bi-calendar-check text-primary"></i>
                        </div>
                        <div class="flex-grow-1 ms-2">
                            <p class="mb-0 small">Domača naloga 2</p>
                            <p class="mb-0 small text-muted">12. nov 2025</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-calendar-check text-primary"></i>
                        </div>
                        <div class="flex-grow-1 ms-2">
                            <p class="mb-0 small">Seminarska naloga</p>
                            <p class="mb-0 small text-muted">15. dec 2025</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Glavna vsebina -->
            <div class="col-lg-9">
                <div class="card mb-4">
                    <div class="card-body">
                        <h4 class="card-title">O predmetu</h4>
                        <p class="card-text"> Pri tem predmetu se bomo naučili kako se pripraviti na odlično organizirano semirarsko nalgo in kako si narediti popoln plan za izpeljevanje le te.</p>
                        <p class="card-text">Predmet poteka v četrtem letniku srednje šole in je obvezen za vse dijake.</p>
                    </div>
                </div>
                
                <h4 class="mb-4">Aktivne naloge</h4>
                <!-- Naloga 1 -->
                <div class="card task-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="card-title">Domača naloga 1: Organizacija načrta</h5>
                            <span class="task-status status-in-progress">V poteku</span>
                        </div>
                        <p class="card-text">Naredite osnovni načrt kako boste speljali vašo seminarsko nalogo</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-muted"><i class="bi bi-clock me-1"></i> Rok: 5. oktober 2025</span>
                                <span class="text-muted ms-3"><i class="bi bi-file-earmark-text me-1"></i> Zahtevnost: Srednja</span>
                            </div>
                            <div>
                                <a href="oddaja-naloge.html" class="btn btn-primary btn-sm">Oddaj rešitev</a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Naloga 2 -->
                <div class="card task-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="card-title">Domača naloga 2: Priprava na začetek seminarske naloge.</h5>
                            <span class="task-status status-not-started">Ni začeto</span>
                        </div>
                        <p class="card-text">Napišite točno kako bo sestavljena spletna stran in kako bo drugačna kot vse druge.</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-muted"><i class="bi bi-clock me-1"></i> Rok: 12. november 2025</span>
                                <span class="text-muted ms-3"><i class="bi bi-file-earmark-text me-1"></i> Zahtevnost: Srednja</span>
                            </div>
                            <div>
                                <button class="btn btn-primary btn-sm">Oddaj rešitev</button>
                                <button class="btn btn-outline-secondary btn-sm ms-2">Podrobnosti</button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Naloga 3 -->
                <div class="card task-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="card-title">Seminarska naloga: Končna seminarska naloga.</h5>
                            <span class="task-status status-not-started">Ni začeto</span>
                        </div>
                        <p class="card-text">Oddajte končano seminarsko nalogo in jo poimenujte kot dogovorjeno</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-muted"><i class="bi bi-clock me-1"></i> Rok: 15. december 2025</span>
                                <span class="text-muted ms-3"><i class="bi bi-file-earmark-text me-1"></i> Zahtevnost: Visoka</span>
                            </div>
                            <div>
                                <button class="btn btn-primary btn-sm">Oddaj rešitev</button>
                                <button class="btn btn-outline-secondary btn-sm ms-2">Podrobnosti</button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Gradiva -->
                <h4 class="my-4">Gradiva</h4>

                <section id="Gradiva">
                    
                   
                </section>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <i class="bi bi-file-earmark-pdf display-4 text-danger"></i>
                                <h5 class="card-title mt-2">Navodila</h5>
                                <p class="card-text">PDF • 2.4 MB</p>
                                <button class="btn btn-outline-primary btn-sm">Prenesi</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <i class="bi bi-file-earmark-text display-4 text-primary"></i>
                                <h5 class="card-title mt-2">Primer odlične seminarske naloge</h5>
                                <p class="card-text">DOCX • 1.1 MB</p>
                                <button class="btn btn-outline-primary btn-sm">Prenesi</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <i class="bi bi-play-btn display-4 text-success"></i>
                                <h5 class="card-title mt-2">Razzlaga kako začeti.</h5>
                                <p class="card-text">MP4 • 15.2 MB</p>
                                <button class="btn btn-outline-primary btn-sm">Prenesi</button>
                            </div>
                        </div>
                    </div>
                </div>
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
                        <li><a href="https://www.instagram.com" target="_blank">Instagram</a></li>
                        <li><a href="https://www.facebook.com" target="_blank">Facebook</a></li>
                        <li><a href="https://www.tiktok.com/@ker.sccelje" target="_blank">TikTok</a></li>
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
    <?php include __DIR__ . '/includes/footer.php'; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
