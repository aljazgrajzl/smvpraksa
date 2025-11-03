<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Oddaja Naloge - Matematika</title>
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
        
        .task-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 2.5rem 0;
            margin-bottom: 2rem;
            border-radius: 0 0 20px 20px;
        }
        
        .task-card {
            box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.1);
            border-radius: 1rem;
            border: none;
            background: white;
            margin-bottom: 2rem;
        }
        
        .upload-area {
            border: 2px dashed #dee2e6;
            border-radius: 1rem;
            padding: 3rem 2rem;
            text-align: center;
            background-color: #f8f9fa;
            transition: all 0.3s;
            cursor: pointer;
        }
        
        .upload-area:hover {
            border-color: var(--primary);
            background-color: #f0f2ff;
        }
        
        .upload-area.dragover {
            border-color: var(--primary);
            background-color: #e6f0ff;
        }
        
        .file-list {
            max-height: 300px;
            overflow-y: auto;
        }
        
        .file-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            border-bottom: 1px solid #dee2e6;
        }
        
        .file-item:last-child {
            border-bottom: none;
        }
        
        .file-info {
            flex-grow: 1;
        }
        
        .file-remove {
            color: #dc3545;
            cursor: pointer;
            padding: 0.5rem;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            border: none;
            padding: 0.75rem 2rem;
            font-weight: 600;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, var(--secondary) 0%, var(--primary) 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }
        
        .btn-outline-primary {
            border-color: var(--primary);
            color: var(--primary);
        }
        
        .btn-outline-primary:hover {
            background-color: var(--primary);
            border-color: var(--primary);
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
        
        .deadline-warning {
            background: linear-gradient(135deg, #ff7e5f 0%, #feb47b 100%);
            color: white;
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
        }
        
        .progress-info {
            background: #e9ecef;
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
        }
        
        /* Footer styling is provided by assets/theme.css */
        
        .upload-icon {
            font-size: 3rem;
            color: var(--primary);
            margin-bottom: 1rem;
        }
        
        .file-preview {
            max-width: 100px;
            max-height: 100px;
            object-fit: cover;
            border-radius: 0.5rem;
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

    <!-- Glava naloge -->
    <section class="task-header">
        <div class="container">
            <h1 class="display-5 fw-bold">Domača naloga 1: Linearne enačbe</h1>
            <p class="lead">Matematika - 4. letnik</p>
        </div>
    </section>

    <!-- Vsebina -->
    <main class="container mb-5">
        <div class="row">
            <!-- Stranski meni -->
            <div class="col-lg-3 mb-4">
                <div class="sidebar">
                    <h5 class="mb-3">Naloga</h5>
                    <a href="javascript:history.back()" class="sidebar-link">
                        <i class="bi bi-arrow-left"></i> Nazaj na predmet
                    </a>        
                    <div class="deadline-warning mt-4">
                        <h6><i class="bi bi-clock"></i> Rok oddaje</h6>
                        <p class="mb-0 fw-bold">5. december 2024, 23:59</p>
                        <small>Preostali čas: <span id="countdown">2 dni 5 ur</span></small>
                    </div>
                    
                    <div class="progress-info">
                        <h6>Status oddaje</h6>
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <div class="progress mb-2" style="height: 8px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: 0%;"></div>
                                </div>
                            </div>
                            <span class="badge bg-secondary ms-2">0%</span>
                        </div>
                        <small class="text-muted">Naloga še ni oddana</small>
                    </div>
                    
                    <h5 class="mt-4 mb-3">Ocenjevanje</h5>
                    <div class="small text-muted">
                        <p><strong>Točke:</strong> 20 točk</p>
                        <p><strong>Težavnost:</strong> Srednja</p>
                        <p><strong>Ocena:</strong> /</p>
                    </div>
                </div>
            </div>
            
            <!-- Glavna vsebina -->
            <div class="col-lg-9">
                <!-- Navodila za nalogo -->
                <div class="card task-card">
                    <div class="card-header bg-light">
                        <h4 class="mb-0"><i class="bi bi-info-circle me-2"></i>Navodila za nalogo</h4>
                    </div>
                    <div class="card-body">
                        <h5>Naloga:</h5>
                        <p>Rešite naslednji sistem linearnih enačb z uporabo substitucijske metode:</p>
                        <div class="bg-light p-3 rounded mb-3">
                            <p class="mb-1">1) 2x + 3y = 12</p>
                            <p class="mb-1">2) x - y = 1</p>
                        </div>
                        
                        <h5 class="mt-4">Navodila:</h5>
                        <ol>
                            <li>Rešite sistem enačb z substitucijsko metodo</li>
                            <li>Prikažite vse korake reševanja</li>
                            <li>Preverite rešitev z vstavljanjem v obe enačbi</li>
                            <li>Odgovor zapišite v urejenem paru (x, y)</li>
                        </ol>
                        
                        <h5 class="mt-4">Zahteve za oddajo:</h5>
                        <ul>
                            <li>Oddati morate PDF, DOC ali DOCX datoteko</li>
                            <li>Velikost datoteke ne sme presegati 10MB</li>
                            <li>Vse rešitve morajo biti napisane ročno ali v urejevalniku besedil</li>
                            <li>Vsak korak mora biti jasno prikazan</li>
                        </ul>
                        
                        <div class="alert alert-info mt-3">
                            <i class="bi bi-lightbulb"></i> <strong>Namig:</strong> Začnite z izražanjem ene spremenljivke iz ene enačbe in jo vstavite v drugo enačbo.
                        </div>
                    </div>
                </div>
                
                <!-- Območje za oddajo -->
                <div class="card task-card">
                    <div class="card-header bg-light">
                        <h4 class="mb-0"><i class="bi bi-cloud-upload me-2"></i>Oddaja nalogo</h4>
                    </div>
                    <div class="card-body">
                        <form id="uploadForm">
                            <!-- Območje za nalaganje datotek -->
                            <div class="upload-area mb-4" id="uploadArea">
                                <i class="bi bi-cloud-arrow-up upload-icon"></i>
                                <h5>Povlecite datoteke sem ali kliknite za izbiro</h5>
                                <p class="text-muted">Podprte oblike: PDF, DOC, DOCX (max. 10MB)</p>
                                <input type="file" id="fileInput" multiple accept=".pdf,.doc,.docx" style="display: none;">
                                <button type="button" class="btn btn-outline-primary mt-2" onclick="document.getElementById('fileInput').click()">
                                    Izberi datoteke
                                </button>
                            </div>
                            
                            <!-- Seznam naloženih datotek -->
                            <div id="fileList" class="file-list mb-4" style="display: none;">
                                <h6 class="mb-3">Izbrane datoteke:</h6>
                                <div id="fileItems"></div>
                            </div>
                            
                            <!-- Potrditveni gumbi -->
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="confirmSubmission">
                                    <label class="form-check-label" for="confirmSubmission">
                                        Potrjujem, da je to končna različica moje rešitve
                                    </label>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-outline-secondary me-2" onclick="saveDraft()">
                                        Shrani osnutek
                                    </button>
                                    <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                                        Oddaj nalogo
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Opozorilo -->
                            <div class="alert alert-warning mt-3">
                                <i class="bi bi-exclamation-triangle"></i> <strong>Pozor:</strong> Po oddaji ne boste mogli več spreminjati svoje rešitve.
                            </div>
                        </form>
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
    <?php include __DIR__ . '/includes/footer.php'; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Spremenljivke za upravljanje datotek
        let uploadedFiles = [];
        
        // Inicializacija
        document.addEventListener('DOMContentLoaded', function() {
            // Funkcije za nalaganje datotek
            document.getElementById('fileInput').addEventListener('change', handleFileSelect);
            document.getElementById('uploadArea').addEventListener('dragover', handleDragOver);
            document.getElementById('uploadArea').addEventListener('drop', handleFileDrop);
            document.getElementById('confirmSubmission').addEventListener('change', toggleSubmitButton);
            
            // Posodobi števec
            updateCountdown();
            setInterval(updateCountdown, 60000);
        });
        
        function handleFileSelect(event) {
            const files = event.target.files;
            processFiles(files);
        }
        
        function handleDragOver(event) {
            event.preventDefault();
            event.stopPropagation();
            document.getElementById('uploadArea').classList.add('dragover');
        }
        
        function handleFileDrop(event) {
            event.preventDefault();
            event.stopPropagation();
            document.getElementById('uploadArea').classList.remove('dragover');
            
            const files = event.dataTransfer.files;
            processFiles(files);
        }
        
        function processFiles(files) {
            let filesAdded = false;
            
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                
                // Preveri velikost datoteke (max 10MB)
                if (file.size > 10 * 1024 * 1024) {
                    alert(`Datoteka "${file.name}" je prevelika. Maksimalna dovoljena velikost je 10MB.`);
                    continue;
                }
                
                // Preveri vrsto datoteke
                const allowedTypes = [
                    'application/pdf', 
                    'application/msword', 
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
                ];
                
                const fileExtension = file.name.split('.').pop().toLowerCase();
                const isAllowedType = allowedTypes.includes(file.type) || 
                                    ['pdf', 'doc', 'docx'].includes(fileExtension);
                
                if (!isAllowedType) {
                    alert(`Datoteka "${file.name}" ni podprtega formata. Dovoljeni so le PDF, DOC in DOCX.`);
                    continue;
                }
                
                // Preveri, če datoteka že obstaja
                const fileExists = uploadedFiles.some(existingFile => 
                    existingFile.name === file.name && existingFile.size === file.size
                );
                
                if (!fileExists) {
                    uploadedFiles.push(file);
                    filesAdded = true;
                }
            }
            
            if (filesAdded) {
                updateFileList();
                toggleSubmitButton();
            }
        }
        
        function updateFileList() {
            const fileList = document.getElementById('fileList');
            const fileItems = document.getElementById('fileItems');
            
            if (uploadedFiles.length > 0) {
                fileList.style.display = 'block';
                fileItems.innerHTML = '';
                
                uploadedFiles.forEach((file, index) => {
                    const fileItem = document.createElement('div');
                    fileItem.className = 'file-item';
                    
                    const fileSize = (file.size / 1024 / 1024).toFixed(2);
                    
                    fileItem.innerHTML = `
                        <div class="file-info">
                            <strong>${file.name}</strong>
                            <div class="text-muted small">${fileSize} MB • ${file.type || 'Neznana vrsta'}</div>
                        </div>
                        <div class="file-remove" onclick="removeFile(${index})">
                            <i class="bi bi-x-circle"></i> Odstrani
                        </div>
                    `;
                    
                    fileItems.appendChild(fileItem);
                });
            } else {
                fileList.style.display = 'none';
            }
        }
        
        function removeFile(index) {
            if (index >= 0 && index < uploadedFiles.length) {
                uploadedFiles.splice(index, 1);
                updateFileList();
                toggleSubmitButton();
                
                // Če ni več datotek, skrij seznam
                if (uploadedFiles.length === 0) {
                    document.getElementById('fileList').style.display = 'none';
                }
            }
        }
        
        function toggleSubmitButton() {
            const submitBtn = document.getElementById('submitBtn');
            const confirmCheckbox = document.getElementById('confirmSubmission');
            const hasFiles = uploadedFiles.length > 0;
            const isConfirmed = confirmCheckbox ? confirmCheckbox.checked : true;
            
            submitBtn.disabled = !(isConfirmed && hasFiles);
        }
        
        function saveDraft() {
            if (uploadedFiles.length === 0) {
                alert('Ni datotek za shranjevanje. Prosimo, izberite datoteko.');
                return;
            }
            
            // Simulacija shranjevanja osnutka
            alert('Osnutek je bil shranjen! Lahko ga uredite do roka oddaje.');
        }
        
        // Obdelava oddaje obrazca
        document.getElementById('uploadForm').addEventListener('submit', function(event) {
            event.preventDefault();
            
            if (uploadedFiles.length === 0) {
                alert('Prosimo, izberite vsaj eno datoteko za oddajo.');
                return;
            }
            
            const confirmCheckbox = document.getElementById('confirmSubmission');
            if (confirmCheckbox && !confirmCheckbox.checked) {
                alert('Prosimo, potrdite, da je to končna različica vaše rešitve.');
                return;
            }
            
            // Simulacija pošiljanja na strežnik
            console.log('Oddane datoteke:', uploadedFiles);
            
            // Prikaži potrditveno sporočilo
            alert('Naloga je bila uspešno oddana!');
            
            // Preusmeritev nazaj na predmet po 2 sekundah
            setTimeout(() => {
                window.location.href = 'javascript:history.back()';
            }, 2000);
        });
        
        // Števec časa do roka
        function updateCountdown() {
            const deadline = new Date('2024-12-05T23:59:00').getTime();
            const now = new Date().getTime();
            const timeLeft = deadline - now;
            
            if (timeLeft > 0) {
                const days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
                const hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                document.getElementById('countdown').textContent = `${days} dni ${hours} ur`;
            } else {
                document.getElementById('countdown').textContent = 'Rok je potekel';
            }
        }
    </script>
</body>
</html>