<?php
session_start();
require_once 'connessione.php';

// Se l'utente è già loggato, vai all'index
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$errore_login = "";
$errore_registrazione = "";
$successo_registrazione = "";
$tab_attiva = "login"; // default

// ========================
// GESTIONE LOGIN
// ========================
if (isset($_POST['azione']) && $_POST['azione'] === 'login') {
    $tab_attiva = "login";
    $email = trim($_POST['email_login'] ?? '');
    $password = $_POST['password_login'] ?? '';

    if (empty($email) || empty($password)) {
        $errore_login = "Compila tutti i campi.";
    } else {
        $stmt = mysqli_prepare($connessione, "SELECT id, nome, cognome, email, password, ruolo FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $risultato = mysqli_stmt_get_result($stmt);

        if ($riga = mysqli_fetch_assoc($risultato)) {
            if (password_verify($password, $riga['password'])) {
                // Login riuscito
                $_SESSION['user_id'] = $riga['id'];
                $_SESSION['user_nome'] = $riga['nome'];
                $_SESSION['user_cognome'] = $riga['cognome'];
                $_SESSION['user_email'] = $riga['email'];
                $_SESSION['user_ruolo'] = $riga['ruolo'];
                header("Location: index.php");
                exit();
            } else {
                $errore_login = "Password errata.";
            }
        } else {
            $errore_login = "Nessun account trovato con questa email.";
        }
        mysqli_stmt_close($stmt);
    }
}

// ========================
// GESTIONE REGISTRAZIONE
// ========================
if (isset($_POST['azione']) && $_POST['azione'] === 'registrazione') {
    $tab_attiva = "registrazione";
    $nome = trim($_POST['nome'] ?? '');
    $cognome = trim($_POST['cognome'] ?? '');
    $email = trim($_POST['email_reg'] ?? '');
    $compleanno = $_POST['compleanno'] ?? '';
    $password = $_POST['password_reg'] ?? '';
    $conferma_password = $_POST['conferma_password'] ?? '';

    // Validazioni
    if (empty($nome) || empty($cognome) || empty($email) || empty($compleanno) || empty($password) || empty($conferma_password)) {
        $errore_registrazione = "Compila tutti i campi.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errore_registrazione = "Formato email non valido.";
    } elseif (strlen($password) < 6) {
        $errore_registrazione = "La password deve avere almeno 6 caratteri.";
    } elseif ($password !== $conferma_password) {
        $errore_registrazione = "Le password non coincidono.";
    } else {
        // Controlla se l'email esiste già
        $stmt_check = mysqli_prepare($connessione, "SELECT id FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt_check, "s", $email);
        mysqli_stmt_execute($stmt_check);
        $risultato_check = mysqli_stmt_get_result($stmt_check);

        if (mysqli_num_rows($risultato_check) > 0) {
            $errore_registrazione = "Esiste già un account con questa email.";
        } else {
            // Hash della password
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt_insert = mysqli_prepare($connessione, "INSERT INTO users (nome, cognome, email, compleanno, password, ruolo) VALUES (?, ?, ?, ?, ?, 'user')");
            mysqli_stmt_bind_param($stmt_insert, "sssss", $nome, $cognome, $email, $compleanno, $password_hash);

            if (mysqli_stmt_execute($stmt_insert)) {
                $successo_registrazione = "Registrazione completata! Ora puoi accedere.";
                $tab_attiva = "login";
            } else {
                $errore_registrazione = "Errore durante la registrazione. Riprova.";
            }
            mysqli_stmt_close($stmt_insert);
        }
        mysqli_stmt_close($stmt_check);
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accedi — Newborn Pilot</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="css/auth.css">
</head>
<body>

    <!-- Sfondo animato -->
    <div class="auth-bg">
        <div class="bg-gradient"></div>
        <div class="bg-grid"></div>
        <div class="floating-elements">
            <div class="float-el el-1"><i class="fas fa-car-side"></i></div>
            <div class="float-el el-2"><i class="fas fa-shield-alt"></i></div>
            <div class="float-el el-3"><i class="fas fa-road"></i></div>
            <div class="float-el el-4"><i class="fas fa-tachometer-alt"></i></div>
            <div class="float-el el-5"><i class="fas fa-gas-pump"></i></div>
        </div>
    </div>

    <div class="auth-wrapper">
        <!-- Logo / Brand -->
        <div class="auth-brand">
            <a href="auth.php" class="brand-logo">
                <i class="fas fa-car-side"></i>
                <span>Newborn<strong>Pilot</strong></span>
            </a>
            <p class="brand-tagline">La tua guida completa per il mondo dell'auto</p>
        </div>

        <!-- Card principale -->
        <div class="auth-card">
            <!-- Tab switcher -->
            <div class="auth-tabs">
                <button class="auth-tab <?php echo ($tab_attiva === 'login') ? 'active' : ''; ?>" data-tab="login">
                    <i class="fas fa-sign-in-alt"></i> Accedi
                </button>
                <button class="auth-tab <?php echo ($tab_attiva === 'registrazione') ? 'active' : ''; ?>" data-tab="registrazione">
                    <i class="fas fa-user-plus"></i> Registrati
                </button>
                <div class="tab-indicator"></div>
            </div>

            <!-- Messaggi -->
            <?php if (!empty($successo_registrazione)): ?>
                <div class="auth-message success">
                    <i class="fas fa-check-circle"></i>
                    <?php echo htmlspecialchars($successo_registrazione); ?>
                </div>
            <?php endif; ?>

            <!-- ========== FORM LOGIN ========== -->
            <div class="auth-form-container <?php echo ($tab_attiva === 'login') ? 'active' : ''; ?>" id="form-login">
                <form method="POST" class="auth-form" novalidate>
                    <input type="hidden" name="azione" value="login">

                    <?php if (!empty($errore_login)): ?>
                        <div class="auth-message error">
                            <i class="fas fa-exclamation-circle"></i>
                            <?php echo htmlspecialchars($errore_login); ?>
                        </div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="email_login">
                            <i class="fas fa-envelope"></i> Email
                        </label>
                        <input 
                            type="email" 
                            id="email_login" 
                            name="email_login" 
                            placeholder="esempio@email.it"
                            value="<?php echo isset($_POST['email_login']) ? htmlspecialchars($_POST['email_login']) : ''; ?>"
                            required
                            autocomplete="email"
                        >
                    </div>

                    <div class="form-group">
                        <label for="password_login">
                            <i class="fas fa-lock"></i> Password
                        </label>
                        <div class="password-wrapper">
                            <input 
                                type="password" 
                                id="password_login" 
                                name="password_login" 
                                placeholder="La tua password"
                                required
                                autocomplete="current-password"
                            >
                            <button type="button" class="toggle-password" data-target="password_login">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn-auth">
                        <span>Accedi</span>
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </form>

                <div class="auth-switch">
                    Non hai un account? <a href="#" class="switch-tab" data-target="registrazione">Registrati</a>
                </div>
            </div>

            <!-- ========== FORM REGISTRAZIONE ========== -->
            <div class="auth-form-container <?php echo ($tab_attiva === 'registrazione') ? 'active' : ''; ?>" id="form-registrazione">
                <form method="POST" class="auth-form" novalidate>
                    <input type="hidden" name="azione" value="registrazione">

                    <?php if (!empty($errore_registrazione)): ?>
                        <div class="auth-message error">
                            <i class="fas fa-exclamation-circle"></i>
                            <?php echo htmlspecialchars($errore_registrazione); ?>
                        </div>
                    <?php endif; ?>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="nome">
                                <i class="fas fa-user"></i> Nome
                            </label>
                            <input 
                                type="text" 
                                id="nome" 
                                name="nome" 
                                placeholder="Mario"
                                value="<?php echo isset($_POST['nome']) ? htmlspecialchars($_POST['nome']) : ''; ?>"
                                required
                                autocomplete="given-name"
                            >
                        </div>
                        <div class="form-group">
                            <label for="cognome">
                                <i class="fas fa-user"></i> Cognome
                            </label>
                            <input 
                                type="text" 
                                id="cognome" 
                                name="cognome" 
                                placeholder="Rossi"
                                value="<?php echo isset($_POST['cognome']) ? htmlspecialchars($_POST['cognome']) : ''; ?>"
                                required
                                autocomplete="family-name"
                            >
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email_reg">
                            <i class="fas fa-envelope"></i> Email
                        </label>
                        <input 
                            type="email" 
                            id="email_reg" 
                            name="email_reg" 
                            placeholder="esempio@email.it"
                            value="<?php echo isset($_POST['email_reg']) ? htmlspecialchars($_POST['email_reg']) : ''; ?>"
                            required
                            autocomplete="email"
                        >
                    </div>

                    <div class="form-group">
                        <label for="compleanno">
                            <i class="fas fa-calendar-alt"></i> Data di nascita
                        </label>
                        <input 
                            type="date" 
                            id="compleanno" 
                            name="compleanno" 
                            value="<?php echo isset($_POST['compleanno']) ? htmlspecialchars($_POST['compleanno']) : ''; ?>"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="password_reg">
                            <i class="fas fa-lock"></i> Password
                        </label>
                        <div class="password-wrapper">
                            <input 
                                type="password" 
                                id="password_reg" 
                                name="password_reg" 
                                placeholder="Minimo 6 caratteri"
                                required
                                autocomplete="new-password"
                                minlength="6"
                            >
                            <button type="button" class="toggle-password" data-target="password_reg">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="password-strength" id="password-strength">
                            <div class="strength-bar"><div class="strength-fill"></div></div>
                            <span class="strength-text"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="conferma_password">
                            <i class="fas fa-lock"></i> Conferma Password
                        </label>
                        <div class="password-wrapper">
                            <input 
                                type="password" 
                                id="conferma_password" 
                                name="conferma_password" 
                                placeholder="Ripeti la password"
                                required
                                autocomplete="new-password"
                            >
                            <button type="button" class="toggle-password" data-target="conferma_password">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn-auth">
                        <span>Crea Account</span>
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </form>

                <div class="auth-switch">
                    Hai già un account? <a href="#" class="switch-tab" data-target="login">Accedi</a>
                </div>
            </div>
        </div>

        <div class="auth-footer">
            <p>&copy; 2025 Newborn Pilot — Tutti i diritti riservati</p>
        </div>
    </div>

    <script>
        // ========================
        // TAB SWITCHING
        // ========================
        const tabs = document.querySelectorAll('.auth-tab');
        const forms = document.querySelectorAll('.auth-form-container');
        const switchLinks = document.querySelectorAll('.switch-tab');
        const indicator = document.querySelector('.tab-indicator');

        function switchTab(targetTab) {
            tabs.forEach(t => t.classList.remove('active'));
            forms.forEach(f => f.classList.remove('active'));

            document.querySelector(`.auth-tab[data-tab="${targetTab}"]`).classList.add('active');
            document.getElementById(`form-${targetTab}`).classList.add('active');

            updateIndicator();
        }

        function updateIndicator() {
            const activeTab = document.querySelector('.auth-tab.active');
            if (activeTab && indicator) {
                indicator.style.width = activeTab.offsetWidth + 'px';
                indicator.style.left = activeTab.offsetLeft + 'px';
            }
        }

        tabs.forEach(tab => {
            tab.addEventListener('click', () => switchTab(tab.dataset.tab));
        });

        switchLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                switchTab(link.dataset.target);
            });
        });

        // Init indicator
        window.addEventListener('load', updateIndicator);
        window.addEventListener('resize', updateIndicator);

        // ========================
        // TOGGLE PASSWORD VISIBILITY
        // ========================
        document.querySelectorAll('.toggle-password').forEach(btn => {
            btn.addEventListener('click', () => {
                const input = document.getElementById(btn.dataset.target);
                const icon = btn.querySelector('i');
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.replace('fa-eye', 'fa-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.replace('fa-eye-slash', 'fa-eye');
                }
            });
        });

        // ========================
        // PASSWORD STRENGTH METER
        // ========================
        const passInput = document.getElementById('password_reg');
        const strengthBar = document.querySelector('.strength-fill');
        const strengthText = document.querySelector('.strength-text');

        if (passInput) {
            passInput.addEventListener('input', () => {
                const val = passInput.value;
                let score = 0;
                if (val.length >= 6) score++;
                if (val.length >= 10) score++;
                if (/[A-Z]/.test(val)) score++;
                if (/[0-9]/.test(val)) score++;
                if (/[^A-Za-z0-9]/.test(val)) score++;

                const levels = [
                    { text: '', color: 'transparent', width: '0%' },
                    { text: 'Molto debole', color: '#e63946', width: '20%' },
                    { text: 'Debole', color: '#f4a261', width: '40%' },
                    { text: 'Discreta', color: '#e9c46a', width: '60%' },
                    { text: 'Buona', color: '#2a9d8f', width: '80%' },
                    { text: 'Ottima', color: '#06d6a0', width: '100%' }
                ];

                const level = levels[score] || levels[0];
                strengthBar.style.width = level.width;
                strengthBar.style.background = level.color;
                strengthText.textContent = val.length > 0 ? level.text : '';
                strengthText.style.color = level.color;
            });
        }

        // ========================
        // INPUT FOCUS ANIMATIONS
        // ========================
        document.querySelectorAll('.form-group input').forEach(input => {
            input.addEventListener('focus', () => {
                input.closest('.form-group').classList.add('focused');
            });
            input.addEventListener('blur', () => {
                input.closest('.form-group').classList.remove('focused');
            });
        });
    </script>
</body>
</html>