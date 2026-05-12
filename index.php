<?php
session_start();

// Se l'utente NON è loggato, reindirizza al login
if (!isset($_SESSION['user_id'])) {
    header("Location: auth.php");
    exit();
}

$nomeUtente = htmlspecialchars($_SESSION['user_nome']);
$cognomeUtente = htmlspecialchars($_SESSION['user_cognome']);
$ruoloUtente = $_SESSION['user_ruolo'];
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Newborn Pilot — La tua guida completa per il mondo dell'auto</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .user-menu {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-left: 16px;
        }
        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 6px 16px;
            background: rgba(255,255,255,0.05);
            border: 1px solid var(--border);
            border-radius: 99px;
            font-size: 13px;
            color: var(--text-light);
        }
        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--grad);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
            color: #fff;
        }
        .user-name {
            font-weight: 600;
        }
        .user-role {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--primary);
            background: rgba(230,57,70,0.1);
            padding: 2px 8px;
            border-radius: 99px;
        }
        .btn-logout {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            background: rgba(230,57,70,0.1);
            border: 1px solid rgba(230,57,70,0.3);
            border-radius: 99px;
            color: var(--primary-light);
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        .btn-logout:hover {
            background: rgba(230,57,70,0.2);
            border-color: rgba(230,57,70,0.5);
            transform: translateY(-1px);
        }  @media (max-width: 900px) {
            .user-menu {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                background: rgba(10,10,15,0.95);
                backdrop-filter: blur(20px);
                padding: 12px 20px;
                border-top: 1px solid var(--border);
                justify-content: space-between;
                z-index: 1001;
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <div id="preloader">
        <div class="loader">
            <div class="steering-wheel">
                <i class="fas fa-dharmachakra"></i>
            </div>
            <p>Newborn Pilot</p>
        </div>
    </div>

    <nav class="navbar" id="navbar">
        <div class="nav-container">
            <a href="index.php" class="nav-logo">
                <i class="fas fa-car-side"></i>
                <span>Newborn<strong>Pilot</strong></span>
            </a>
            <div class="nav-menu" id="navMenu">
                <a href="index.php" class="nav-link active">Home</a>
                <div class="nav-dropdown">
                    <a href="#" class="nav-link dropdown-toggle">Patente <i class="fas fa-chevron-down"></i></a>
                    <div class="dropdown-menu">
                        <a href="pages/patente-teoria.html"><i class="fas fa-book"></i> Teoria</a>
                        <a href="pages/patente-pratica.html"><i class="fas fa-steering-wheel"></i> Pratica</a>
                    </div>
                </div>
                <a href="pages/come-funziona-auto.html" class="nav-link">Come Funziona l'Auto</a>
                <a href="pages/manutenzione.html" class="nav-link">Manutenzione</a>
                <a href="pages/tutorial.html" class="nav-link">Tutorial</a>
                <a href="pages/acquisto.html" class="nav-link">Acquisto Auto</a>
                <a href="pages/assicurazione.html" class="nav-link">Assicurazione</a>
                <a href="pages/multe.html" class="nav-link">Multe</a>
                <a href="pages/curiosita.html" class="nav-link">Curiosità</a>
                <a href="pages/risorse.html" class="nav-link">Risorse</a>
            </div>

            <!-- User Menu -->
            <div class="user-menu">
                <div class="user-info">
                    <div class="user-avatar">
                        <?php echo strtoupper(substr($nomeUtente, 0, 1) . substr($cognomeUtente, 0, 1)); ?>
                    </div>
                    <span class="user-name"><?php echo $nomeUtente; ?></span>
                    <?php if ($ruoloUtente === 'admin'): ?>
                        <span class="user-role">Admin</span>
                    <?php endif; ?>
                </div>
                <a href="logout.php" class="btn-logout">
                    <i class="fas fa-sign-out-alt"></i> Esci
                </a>
            </div>

            <button class="nav-toggle" id="navToggle" aria-label="Toggle menu">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </nav>

    <header class="hero">
        <div class="hero-bg">
            <div class="hero-particles" id="particles"></div>
            <div class="hero-road">
                <div class="road-line"></div>
                <div class="road-line"></div>
                <div class="road-line"></div>
            </div>
        </div>
        <div class="hero-content">
            <div class="hero-badge">
                <i class="fas fa-shield-check"></i>
                La guida che nessuno ti ha mai dato
            </div>
            <h1>NEWBORN <span class="gradient-text">PILOT</span></h1>
            <p class="hero-subtitle">Benvenuto, <strong><?php echo $nomeUtente; ?></strong>! La tua guida completa per imparare tutto ciò che <strong>nessuno ti spiega</strong> sulle auto</p>
            <p class="hero-description">Dalla patente alla manutenzione, dall'acquisto alle multe. Tutto quello che dovresti sapere, spiegato come te lo direbbe un amico esperto.</p>
            <div class="hero-actions">
                <a href="#sezioni" class="btn btn-primary btn-lg">
                    <i class="fas fa-rocket"></i> Inizia il Percorso
                </a>
                <a href="#intro" class="btn btn-outline btn-lg">
                    <i class="fas fa-info-circle"></i> Scopri di più
                </a>
            </div>
        </div>
        <div class="scroll-indicator">
            <div class="mouse">
                <div class="wheel"></div>
            </div>
            <p>Scorri verso il basso</p>
        </div>
    </header>

    <!-- Introduction Section -->
    <section class="section intro-section" id="intro">
        <div class="container">
            <div class="intro-grid">
                <div class="intro-content">
                    <span class="section-tag"><i class="fas fa-heart"></i> La nostra missione</span>
                    <h2>Perché esiste <span class="gradient-text">Newborn Pilot</span>?</h2>
                    <p class="intro-lead">Che tu stia per mettere le mani sul volante per la prima volta o che tu abbia già macinato migliaia di chilometri, <strong>Newborn Pilot</strong> è pensato per te.</p>
                    <p>La scuola guida ti prepara all'esame teorico e pratico, ma poi? Chi ti spiega come scegliere la tua prima auto? Come funziona davvero un motore? Come comportarti quando si accende una spia sul cruscotto che non hai mai visto?</p>
                    <p>Newborn Pilot nasce per <strong>colmare questo vuoto enorme</strong>. Non è un manuale tecnico noioso e incomprensibile. È una guida pratica, chiara, moderna — scritta come te la spiegherebbe un amico esperto, seduto accanto a te in macchina.</p>
                    <div class="intro-features">
                        <div class="intro-feature">
                            <i class="fas fa-check-circle"></i>
                            <span>Semplice e chiaro</span>
                        </div>
                        <div class="intro-feature">
                            <i class="fas fa-check-circle"></i>
                            <span>Pratico e concreto</span>
                        </div>
                        <div class="intro-feature">
                            <i class="fas fa-check-circle"></i>
                            <span>Sempre aggiornato</span>
                        </div>
                        <div class="intro-feature">
                            <i class="fas fa-check-circle"></i>
                            <span>Per tutti i livelli</span>
                        </div>
                    </div>
                </div>
                <div class="intro-visual">
                    <div class="intro-card-stack">
                        <div class="floating-card card-1">
                            <i class="fas fa-id-card"></i>
                            <span>Patente</span>
                        </div>
                        <div class="floating-card card-2">
                            <i class="fas fa-wrench"></i>
                            <span>Manutenzione</span>
                        </div>
                        <div class="floating-card card-3">
                            <i class="fas fa-shield-alt"></i>
                            <span>Assicurazione</span>
                        </div>
                        <div class="floating-card card-4">
                            <i class="fas fa-car"></i>
                            <span>Acquisto</span>
                        </div>
                        <div class="floating-card card-5">
                            <i class="fas fa-tools"></i>
                            <span>Tutorial</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section sections-overview" id="sezioni">
        <div class="container">
            <div class="section-header">
                <span class="section-tag"><i class="fas fa-compass"></i> Il tuo percorso</span>
                <h2>Esplora tutte le <span class="gradient-text">Sezioni</span></h2>
                <p>Dieci sezioni complete che coprono ogni aspetto del mondo dell'auto. Scegli da dove iniziare.</p>
            </div>
            <div class="sections-grid">
                <a href="pages/patente-teoria.html" class="section-card" data-aos="fade-up">
                    <div class="section-card-icon">
                        <i class="fas fa-book-open"></i>
                        <span class="section-number">01</span>
                    </div>
                    <h3>Patente: Teoria</h3>
                    <p>Il percorso completo per ottenere la patente. Scuola guida vs privatista, documenti, visita medica e metodo di studio.</p>
                    <div class="section-card-footer">
                        <span class="tag">Fondamentale</span>
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </a>

                <a href="pages/patente-pratica.html" class="section-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="section-card-icon">
                        <i class="fas fa-car"></i>
                        <span class="section-number">02</span>
                    </div>
                    <h3>Patente: Pratica</h3>
                    <p>Tutto quello che nessuno ti dice prima di sederti al volante. Tips, errori comuni e come affrontare l'esame.</p>
                    <div class="section-card-footer">
                        <span class="tag">Fondamentale</span>
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </a>

                <a href="pages/come-funziona-auto.html" class="section-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="section-card-icon">
                        <i class="fas fa-cogs"></i>
                        <span class="section-number">03</span>
                    </div>
                    <h3>Come Funziona l'Auto</h3>
                    <p>Motore, trasmissione, freni, sospensioni, pneumatici e cruscotto spiegati senza bisogno di una laurea.</p>
                    <div class="section-card-footer">
                        <span class="tag">Formativo</span>
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </a>

                <a href="pages/manutenzione.html" class="section-card" data-aos="fade-up" data-aos-delay="300">
                    <div class="section-card-icon">
                        <i class="fas fa-wrench"></i>
                        <span class="section-number">04</span>
                    </div>
                    <h3>Manutenzione</h3>
                    <p>Manutenzione ordinaria, controlli fai-da-te e revisione obbligatoria. Fai durare la tua auto e risparmia.</p>
                    <div class="section-card-footer">
                        <span class="tag">Pratico</span>
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </a>

                <a href="pages/tutorial.html" class="section-card" data-aos="fade-up" data-aos-delay="400">
                    <div class="section-card-icon">
                        <i class="fas fa-play-circle"></i>
                        <span class="section-number">05</span>
                    </div>
                    <h3>Tutorial Pratici</h3>
                    <p>Guide passo passo: fare benzina, cambiare una gomma, batteria scarica, parcheggio e molto altro.</p>
                    <div class="section-card-footer">
                        <span class="tag">Essenziale</span>
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </a>

                <a href="pages/acquisto.html" class="section-card" data-aos="fade-up" data-aos-delay="500">
                    <div class="section-card-icon">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="section-number">06</span>
                    </div>
                    <h3>Acquisto Auto</h3>
                    <p>Configuratore intelligente, nuova vs usata, checklist ispezione e costi nascosti che nessuno ti dice.</p>
                    <div class="section-card-footer">
                        <span class="tag">Strategico</span>
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </a>

                <a href="pages/assicurazione.html" class="section-card" data-aos="fade-up" data-aos-delay="600">
                    <div class="section-card-icon">
                        <i class="fas fa-shield-alt"></i>
                        <span class="section-number">07</span>
                    </div>
                    <h3>Assicurazione</h3>
                    <p>RC Auto, classi di merito, garanzie accessorie e come risparmiare davvero sulla polizza.</p>
                    <div class="section-card-footer">
                        <span class="tag">Importante</span>
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </a>

                <a href="pages/multe.html" class="section-card" data-aos="fade-up" data-aos-delay="700">
                    <div class="section-card-icon">
                        <i class="fas fa-gavel"></i>
                        <span class="section-number">08</span>
                    </div>
                    <h3>Multe e Sanzioni</h3>
                    <p>Tipologie, importi, come ottenere sconti, come contestare e il sistema della patente a punti.</p>
                    <div class="section-card-footer">
                        <span class="tag">Utile</span>
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </a>

                <a href="pages/curiosita.html" class="section-card" data-aos="fade-up" data-aos-delay="800">
                    <div class="section-card-icon">
                        <i class="fas fa-lightbulb"></i>
                        <span class="section-number">09</span>
                    </div>
                    <h3>Curiosità</h3>
                    <p>Fatti sorprendenti, curiosità tecniche e il glossario completo dei termini automobilistici.</p>
                    <div class="section-card-footer">
                        <span class="tag">Divertente</span>
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </a>

                <a href="pages/risorse.html" class="section-card" data-aos="fade-up" data-aos-delay="900">
                    <div class="section-card-icon">
                        <i class="fas fa-toolbox"></i>
                        <span class="section-number">10</span>
                    </div>
                    <h3>Risorse e Strumenti</h3>
                    <p>Documenti scaricabili, configuratore auto, comparatore assicurazioni, calcolatore costi e altro.</p>
                    <div class="section-card-footer">
                        <span class="tag">Strumenti</span>
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </a>
            </div>
        </div>
    </section>

    <section class="section philosophy-section">
        <div class="container">
            <div class="philosophy-content">
                <span class="section-tag"><i class="fas fa-star"></i> La nostra filosofia</span>
                <h2>Non un manuale. Non un corso noioso.<br><span class="gradient-text">Una guida nella tua lingua.</span></h2>
                <div class="philosophy-grid">
                    <div class="philosophy-item">
                        <div class="philosophy-icon"><i class="fas fa-feather-alt"></i></div>
                        <h4>Semplice</h4>
                        <p>Niente gergo tecnico inutile. Se usiamo un termine specifico, lo spieghiamo.</p>
                    </div>
                    <div class="philosophy-item">
                        <div class="philosophy-icon"><i class="fas fa-hands-helping"></i></div>
                        <h4>Pratico</h4>
                        <p>Non teoria fine a sé stessa, ma informazioni che puoi usare subito.</p>
                    </div>
                    <div class="philosophy-item">
                        <div class="philosophy-icon"><i class="fas fa-sync-alt"></i></div>
                        <h4>Aggiornato</h4>
                        <p>Normative, costi e consigli vengono verificati e aggiornati regolarmente.</p>
                    </div>
                    <div class="philosophy-item">
                        <div class="philosophy-icon"><i class="fas fa-infinity"></i></div>
                        <h4>Completo</h4>
                        <p>Dalla prima lezione all'acquisto, dalla manutenzione alle multe — tutto in un unico posto.</p>
                    </div>
                    <div class="philosophy-item">
                        <div class="philosophy-icon"><i class="fas fa-users"></i></div>
                        <h4>Per tutti</h4>
                        <p>Che tu abbia 18 o 50 anni. C'è sempre qualcosa di nuovo da imparare.</p>
                    </div>
                </div>
                <div class="philosophy-quote">
                    <blockquote>
                        <i class="fas fa-quote-left"></i>
                        Newborn Pilot. Perché sulla strada, l'unica cosa che non puoi permetterti è <strong>non sapere</strong>.
                        <i class="fas fa-quote-right"></i>
                    </blockquote>
                </div>
            </div>
        </div>
    </section>

    <section class="section cta-section">
        <div class="container">
            <div class="cta-content">
                <h2>Pronto a partire?</h2>
                <p>Scegli la sezione che ti interessa e inizia il tuo percorso. È tutto gratuito, sempre.</p>
                <a href="#sezioni" class="btn btn-primary btn-lg">
                    <i class="fas fa-compass"></i> Esplora le Sezioni
                </a>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <a href="index.php" class="footer-logo">
                        <i class="fas fa-car-side"></i>
                        <span>Newborn<strong>Pilot</strong></span>
                    </a>
                    <p>La tua guida completa per il mondo dell'auto. Perché sulla strada, l'unica cosa che non puoi permetterti è non sapere.</p>
                </div>
                <div class="footer-links">
                    <h4>Sezioni Principali</h4>
                    <a href="pages/patente-teoria.html">Patente: Teoria</a>
                    <a href="pages/patente-pratica.html">Patente: Pratica</a>
                    <a href="pages/come-funziona-auto.html">Come Funziona l'Auto</a>
                    <a href="pages/manutenzione.html">Manutenzione</a>
                    <a href="pages/tutorial.html">Tutorial Pratici</a>
                </div>
                <div class="footer-links">
                    <h4>Altre Sezioni</h4>
                    <a href="pages/acquisto.html">Acquisto Auto</a>
                    <a href="pages/assicurazione.html">Assicurazione</a>
                    <a href="pages/multe.html">Multe e Sanzioni</a>
                    <a href="pages/curiosita.html">Curiosità</a>
                    <a href="pages/risorse.html">Risorse e Strumenti</a>
                </div>
                <div class="footer-links">
                    <h4>Strumenti</h4>
                    <a href="pages/risorse.html#configuratore">Configuratore Auto</a>
                    <a href="pages/risorse.html#comparatore">Comparatore Assicurazioni</a>
                    <a href="pages/risorse.html#quiz">Quiz Patente</a>
                    <a href="pages/risorse.html#calcolatore">Calcolatore Costi</a>
                </div>
            </div>
            <div class="footer-bottom">
            </div>
        </div>
    </footer>

    <button class="back-to-top" id="backToTop" aria-label="Torna in cima">
        <i class="fas fa-chevron-up"></i>
    </button>

    <script src="script.js"></script>
</body>
</html>