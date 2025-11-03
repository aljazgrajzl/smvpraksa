<?php
session_start();
include 'povezava.php';

// Preusmeri če je uporabnik že prijavljen
if (isset($_SESSION['user_id'])) {
    $role = $_SESSION['user_type'] ?? '';
    if ($role === 'admin' || $role === 'administrator') {
        header("Location: admin.php");
    } else {
        header("Location: spletna.ucilnica.domstran.php");
    }
    exit();
}

// Obdelaj registracijo
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'register') {
    $tip = $_POST['user_type'];
    $ime = $_POST['ime'];
    $priimek = $_POST['priimek'];
    $razred = ($tip == 'dijak') ? $_POST['razred'] : '';
    $email = $_POST['email'];
    $geslo = password_hash($_POST['geslo'], PASSWORD_DEFAULT);
    
    // Debug info
    echo "<script>console.log('Registracija:', '" . $email . "', '" . $tip . "', '" . $razred . "');</script>";
    
    $stmt = $conn->prepare("INSERT INTO uporabniki (tip_uporabnika, ime, priimek, razred, email, geslo) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $tip, $ime, $priimek, $razred, $email, $geslo);
    
    if ($stmt->execute()) {
        echo "<script>alert('Registracija uspešna! Lahko se prijavite.');</script>";
        echo "<script>console.log('Registracija uspešna');</script>";
    } else {
        echo "<script>alert('Napaka pri registraciji: " . $conn->error . "');</script>";
        echo "<script>console.log('Napaka pri registraciji:', '" . $conn->error . "');</script>";
    }
    $stmt->close();
}

// Obdelaj prijavo
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'login') {
    $email = $_POST['email'];
    $geslo = $_POST['geslo'];
    
    echo "<script>console.log('Prijava:', '" . $email . "');</script>";
    
    $stmt = $conn->prepare("SELECT id, ime, priimek, geslo, tip_uporabnika, razred FROM uporabniki WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    echo "<script>console.log('Najdenih uporabnikov:', " . $result->num_rows . "');</script>";
    
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        echo "<script>console.log('Najden uporabnik:', '" . $user['email'] . "');</script>";
        
        // PREVERI GESLO
        if (password_verify($geslo, $user['geslo'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['ime'] . ' ' . $user['priimek'];
            // Normaliziraj vloge: 'administrator' -> 'admin'
            $role = $user['tip_uporabnika'];
            if ($role === 'administrator') { $role = 'admin'; }
            $_SESSION['user_type'] = $role;
            $_SESSION['razred'] = $user['razred'] ?? '';

            // Preusmeri na ustrezno nadzorno ploščo
            if ($role === 'admin') {
                header("Location: admin.php");
            } else {
                header("Location: spletna.ucilnica.domstran.php");
            }
            exit();
        } else {
            echo "<script>alert('Napačno geslo!');</script>";
        }
    } else {
        echo "<script>alert('Uporabnik ne obstaja! Ustvari nov račun.');</script>";
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
    <title>E-Šola - Prijava</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .login-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            max-width: 400px;
            width: 100%;
        }
        
        .login-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        
        .login-body {
            padding: 2rem;
        }
        
        .auth-tabs .nav-link {
            border: none;
            color: #6c757d;
            font-weight: 500;
            padding: 1rem;
        }
        
        .auth-tabs .nav-link.active {
            color: var(--primary);
            background: none;
            border-bottom: 3px solid var(--primary);
        }
        
        .user-type-btn {
            border: 2px solid #dee2e6;
            background: white;
            color: var(--dark);
            padding: 0.5rem 1rem;
            border-radius: 2rem;
            margin: 0.25rem;
            font-size: 0.9rem;
            transition: all 0.3s;
        }
        
        .user-type-btn.active, .user-type-btn:hover {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            border-color: var(--primary);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            border: none;
            padding: 0.75rem;
            font-weight: 600;
        }
        
        .form-control:focus {
            border-color: var(--secondary);
            box-shadow: 0 0 0 0.25rem rgba(78, 84, 200, 0.25);
        }
        
        #razredField {
            transition: all 0.3s;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="login-container">
                    <div class="login-header">
                        <h1><i class="bi bi-book-half"></i> E-Šola</h1>
                        <p class="mb-0">Prijava in registracija</p>
                    </div>
                    
                    <div class="login-body">
                        <ul class="nav nav-tabs auth-tabs w-100 mb-4" id="authTabs">
                            <li class="nav-item w-50 text-center">
                                <a class="nav-link active" id="login-tab" data-bs-toggle="tab" href="#login">Prijava</a>
                            </li>
                            <li class="nav-item w-50 text-center">
                                <a class="nav-link" id="register-tab" data-bs-toggle="tab" href="#register">Registracija</a>
                            </li>
                        </ul>
                        
                        <div class="tab-content" id="authTabsContent">
                            <!-- PRIJAVA -->
                            <div class="tab-pane fade show active" id="login">
                                <form method="POST">
                                    <input type="hidden" name="action" value="login">
                                    
                                    <div class="mb-3">
                                        <label for="loginEmail" class="form-label">E-poštni naslov</label>
                                        <input type="email" class="form-control" id="loginEmail" name="email" placeholder="vnesite@eposto.sl" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="loginPassword" class="form-label">Geslo</label>
                                        <input type="password" class="form-control" id="loginPassword" name="geslo" placeholder="Vnesite geslo" required>
                                    </div>
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary">Prijava</button>
                                    </div>
                                </form>
                            </div>
                            
                            <!-- REGISTRACIJA -->
                            <div class="tab-pane fade" id="register">
                                <div class="user-type-select mb-3">
                                    <p class="mb-2 fw-bold">Registriram se kot:</p>
                                    <div class="d-flex flex-wrap justify-content-center">
                                        <button type="button" class="btn user-type-btn active" data-type="dijak">Dijak</button>
                                        <button type="button" class="btn user-type-btn" data-type="profesor">Profesor</button>
                                        <button type="button" class="btn user-type-btn" data-type="admin">Administrator</button>
                                    </div>
                                </div>
                                
                                <form method="POST">
                                    <input type="hidden" name="action" value="register">
                                    <input type="hidden" name="user_type" id="registerUserType" value="dijak">
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="firstName" class="form-label">Ime</label>
                                            <input type="text" class="form-control" id="firstName" name="ime" placeholder="Vnesite ime" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="lastName" class="form-label">Priimek</label>
                                            <input type="text" class="form-control" id="lastName" name="priimek" placeholder="Vnesite priimek" required>
                                        </div>
                                    </div>
                                    
                                    <!-- Razred samo za dijake -->
                                    <div class="mb-3" id="razredField">
                                        <label for="razred" class="form-label">Razred</label>
                                        <select class="form-control" id="razred" name="razred" required>
                                            <option value="">Izberite razred</option>
                                            <option value="1.a">1.a</option>
                                            <option value="1.b">1.b</option>
                                            <option value="1.c">1.c</option>
                                            <option value="2.a">2.a</option>
                                            <option value="2.b">2.b</option>
                                            <option value="2.c">2.c</option>
                                            <option value="3.a">3.a</option>
                                            <option value="3.b">3.b</option>
                                            <option value="3.c">3.c</option>
                                            <option value="4.a">4.a</option>
                                            <option value="4.b">4.b</option>
                                            <option value="4.c">4.c</option>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="registerEmail" class="form-label">E-poštni naslov</label>
                                        <input type="email" class="form-control" id="registerEmail" name="email" placeholder="vnesite@eposto.sl" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="registerPassword" class="form-label">Geslo</label>
                                        <input type="password" class="form-control" id="registerPassword" name="geslo" placeholder="Ustvarite geslo" required>
                                    </div>
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary">Ustvari račun</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Funkcija za prikaz razreda samo za dijake
        function toggleRazredField(userType) {
            const razredField = document.getElementById('razredField');
            if (userType === 'dijak') {
                razredField.style.display = 'block';
                document.getElementById('razred').required = true;
            } else {
                razredField.style.display = 'none';
                document.getElementById('razred').required = false;
            }
        }

        // Funkcija za user type gumb
        function setupUserTypeButtons() {
            const container = document.querySelector('#register .user-type-select');
            const hiddenInput = document.getElementById('registerUserType');
            
            container.querySelectorAll('.user-type-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    container.querySelectorAll('.user-type-btn').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    const userType = this.getAttribute('data-type');
                    hiddenInput.value = userType;
                    
                    // Prikaži/skrij razred
                    toggleRazredField(userType);
                });
            });
        }

        // Funkcija za preklapanje zavihkov
        function setupTabs() {
            const triggerTabList = [].slice.call(document.querySelectorAll('#authTabs a'));
            triggerTabList.forEach(function (triggerEl) {
                const tabTrigger = new bootstrap.Tab(triggerEl);
                
                triggerEl.addEventListener('click', function (event) {
                    event.preventDefault();
                    tabTrigger.show();
                });
            });
        }

        // Inicializacija
        document.addEventListener('DOMContentLoaded', function() {
            setupUserTypeButtons();
            setupTabs();
            toggleRazredField('dijak'); // Začni z dijaki
            
            console.log('Stran naložena - registracija je na voljo');
        });
    </script>
</body>
</html>