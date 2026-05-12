<?php
session_start();
require_once '../connessione.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth.php");
    exit();
}

if (isset($_POST['ajax_search']) && $_POST['ajax_search'] === '1') {
    header('Content-Type: application/json; charset=utf-8');

    $budget        = $_POST['budget'] ?? '';
    $utilizzo      = $_POST['utilizzo'] ?? '';
    $alimentazione = $_POST['alimentazione'] ?? '';
    $dimensioni    = $_POST['dimensioni'] ?? '';
    $neopatentato  = $_POST['neopatentato'] ?? '';

    $where = [];
    $params = [];
    $types = "";

    // Filtro BUDGET
    switch ($budget) {
        case '5000':
            $where[] = "prezzo < 5000";
            break;
        case '10000':
            $where[] = "prezzo >= 5000 AND prezzo < 10000";
            break;
        case '20000':
            $where[] = "prezzo >= 10000 AND prezzo < 20000";
            break;
        case '35000':
            $where[] = "prezzo >= 20000 AND prezzo < 35000";
            break;
        case '50000':
            $where[] = "prezzo >= 35000";
            break;
    }

    if (!empty($utilizzo)) {
        $where[] = "FIND_IN_SET(?, utilizzo) > 0";
        $params[] = $utilizzo;
        $types .= "s";
    }

    if (!empty($alimentazione) && $alimentazione !== 'boh') {
        $where[] = "alimentazione = ?";
        $params[] = $alimentazione;
        $types .= "s";
    }

    if (!empty($dimensioni)) {
        $where[] = "dimensioni = ?";
        $params[] = $dimensioni;
        $types .= "s";
    }

    if ($neopatentato === 'si') {
        $where[] = "neopatentato = 1";
    }

    $sql = "SELECT id, marca, modello, versione, anno, prezzo, alimentazione, dimensioni, utilizzo, neopatentato, potenza_kw, peso_kg, autonomia_elettrica FROM auto";

    if (!empty($where)) {
        $sql .= " WHERE " . implode(" AND ", $where);
    }

    $sql .= " ORDER BY prezzo ASC";

    $stmt = mysqli_prepare($connessione, $sql);

    if (!empty($params)) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }

    mysqli_stmt_execute($stmt);
    $risultato = mysqli_stmt_get_result($stmt);

    $auto = [];
    while ($riga = mysqli_fetch_assoc($risultato)) {
        $auto[] = $riga;
    }

    mysqli_stmt_close($stmt);

    if (empty($auto) && !empty($budget)) {
        $sql_fallback = "SELECT id, marca, modello, versione, anno, prezzo, alimentazione, dimensioni, utilizzo, neopatentato, potenza_kw, peso_kg, autonomia_elettrica FROM auto WHERE ";

        switch ($budget) {
            case '5000':
                $sql_fallback .= "prezzo < 5000";
                break;
            case '10000':
                $sql_fallback .= "prezzo >= 5000 AND prezzo < 10000";
                break;
            case '20000':
                $sql_fallback .= "prezzo >= 10000 AND prezzo < 20000";
                break;
            case '35000':
                $sql_fallback .= "prezzo >= 20000 AND prezzo < 35000";
                break;
            case '50000':
                $sql_fallback .= "prezzo >= 35000";
                break;
            default:
                $sql_fallback .= "1=1";
                break;
        }

        if ($neopatentato === 'si') {
            $sql_fallback .= " AND neopatentato = 1";
        }

        $sql_fallback .= " ORDER BY prezzo ASC";
        $result_fb = mysqli_query($connessione, $sql_fallback);

        while ($riga = mysqli_fetch_assoc($result_fb)) {
            $auto[] = $riga;
        }

        echo json_encode([
            'success'  => true,
            'auto'     => $auto,
            'count'    => count($auto),
            'fallback' => true,
            'message'  => 'Nessuna auto trovata con tutti i filtri. Ecco i risultati nel tuo budget:'
        ]);
        exit();
    }

    echo json_encode([
        'success'  => true,
        'auto'     => $auto,
        'count'    => count($auto),
        'fallback' => false,
        'message'  => ''
    ]);
    exit();
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acquisto Auto — Newborn Pilot</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/acquisto.css">
</head>
<body>
    <nav class="navbar" id="navbar">
        <div class="nav-container">
            <a href="../index.php" class="nav-logo"><i class="fas fa-car-side"></i><span>Newborn<strong>Pilot</strong></span></a>
            <div class="nav-menu" id="navMenu">
                <a href="../index.php" class="nav-link">Home</a>
                <div class="nav-dropdown">
                    <a href="#" class="nav-link dropdown-toggle">Patente <i class="fas fa-chevron-down"></i></a>
                    <div class="dropdown-menu">
                        <a href="patente-teoria.html"><i class="fas fa-book"></i> Teoria</a>
                        <a href="patente-pratica.html"><i class="fas fa-car"></i> Pratica</a>
                    </div>
                </div>
                <a href="come-funziona-auto.html" class="nav-link">Come Funziona l'Auto</a>
                <a href="manutenzione.html" class="nav-link">Manutenzione</a>
                <a href="tutorial.html" class="nav-link">Tutorial</a>
                <a href="acquisto.php" class="nav-link active">Acquisto Auto</a>
                <a href="assicurazione.html" class="nav-link">Assicurazione</a>
                <a href="multe.html" class="nav-link">Multe</a>
                <a href="curiosita.html" class="nav-link">Curiosità</a>
                <a href="risorse.html" class="nav-link">Risorse</a>
            </div>
            <button class="nav-toggle" id="navToggle"><span></span><span></span><span></span></button>
        </div>
    </nav>

    <header class="page-hero">
        <div class="page-hero-content">
            <div class="breadcrumb"><a href="../index.php">Home</a><i class="fas fa-chevron-right"></i><span>Acquisto Auto</span></div>
            <div class="page-hero-badge"><span class="section-number-badge">Sezione 06</span></div>
            <h1><i class="fas fa-shopping-cart"></i> Guida all'<span class="gradient-text">Acquisto</span></h1>
            <p>La guida definitiva per non farti fregare e scegliere l'auto giusta</p>
        </div>
    </header>

    <main class="page-content">
        <div class="container">
            <div class="content-layout">
                <aside class="content-sidebar">
                    <div class="sidebar-nav sticky-sidebar">
                        <h4><i class="fas fa-bookmark"></i> Indice</h4>
                        <ul>
                            <li><a href="#configuratore" class="sidebar-link active">6.1 — Configuratore intelligente</a></li>
                            <li><a href="#nuova-usata" class="sidebar-link">6.2 — Nuova vs Usata vs Km0</a></li>
                            <li><a href="#ispezione" class="sidebar-link">6.3 — Ispezionare un'auto usata</a></li>
                            <li><a href="#costi-nascosti" class="sidebar-link">6.4 — Costi nascosti</a></li>
                        </ul>
                    </div>
                </aside>

                <div class="content-main">

                  
                    <section id="configuratore" class="content-section">
                        <h2><span class="section-num">6.1</span> Il configuratore intelligente</h2>
                        <p>Rispondi a semplici domande e il nostro sistema interroga il database per trovare le auto più adatte alle tue esigenze:</p>

                        <div class="configurator-preview">
                            <div class="config-form" id="configForm">
                                <div class="config-group">
                                    <label><i class="fas fa-euro-sign"></i> Budget</label>
                                    <select id="budget" name="budget">
                                        <option value="">Seleziona...</option>
                                        <option value="5000">Sotto 5.000€</option>
                                        <option value="10000">5.000 — 10.000€</option>
                                        <option value="20000">10.000 — 20.000€</option>
                                        <option value="35000">20.000 — 35.000€</option>
                                        <option value="50000">Oltre 35.000€</option>
                                    </select>
                                </div>
                                <div class="config-group">
                                    <label><i class="fas fa-road"></i> Utilizzo principale</label>
                                    <select id="utilizzo" name="utilizzo">
                                        <option value="">Seleziona...</option>
                                        <option value="citta">Città</option>
                                        <option value="extra">Extraurbano</option>
                                        <option value="auto">Autostrada</option>
                                        <option value="misto">Misto</option>
                                    </select>
                                </div>
                                <div class="config-group">
                                    <label><i class="fas fa-gas-pump"></i> Alimentazione</label>
                                    <select id="alimentazione" name="alimentazione">
                                        <option value="">Seleziona...</option>
                                        <option value="benzina">Benzina</option>
                                        <option value="diesel">Diesel</option>
                                        <option value="gpl">GPL</option>
                                        <option value="ibrido">Ibrido</option>
                                        <option value="elettrico">Elettrico</option>
                                        <option value="boh">Non so, consigliami</option>
                                    </select>
                                </div>
                                <div class="config-group">
                                    <label><i class="fas fa-car"></i> Dimensioni</label>
                                    <select id="dimensioni" name="dimensioni">
                                        <option value="">Seleziona...</option>
                                        <option value="city">Citycar</option>
                                        <option value="util">Utilitaria</option>
                                        <option value="berl">Berlina</option>
                                        <option value="suv">SUV / Crossover</option>
                                        <option value="sw">Station Wagon</option>
                                    </select>
                                </div>
                                <div class="config-group">
                                    <label><i class="fas fa-id-card"></i> Neopatentato?</label>
                                    <select id="neopatentato" name="neopatentato">
                                        <option value="">Seleziona...</option>
                                        <option value="si">Sì (vincolo 55 kW/t)</option>
                                        <option value="no">No</option>
                                    </select>
                                </div>
                                <button class="btn btn-primary" id="btnCerca" type="button">
                                    <i class="fas fa-search"></i> Trova la tua auto
                                </button>
                            </div>

                            <!-- Loading -->
                            <div id="configLoading" class="config-loading" style="display:none;">
                                <div class="loading-spinner">
                                    <i class="fas fa-dharmachakra fa-spin"></i>
                                </div>
                                <p>Stiamo cercando l'auto perfetta per te...</p>
                            </div>

                            <!-- Risultati -->
                            <div id="configResult" class="config-result-area" style="display:none;"></div>
                        </div>
                    </section>

                   
                    <section id="nuova-usata" class="content-section">
                        <h2><span class="section-num">6.2</span> Nuova vs. Usata vs. Km0</h2>
                        <div class="three-comparison">
                            <div class="comp-card">
                                <h3><i class="fas fa-star"></i> Auto Nuova</h3>
                                <div class="pros">
                                    <h5>Pro</h5>
                                    <ul>
                                        <li>✅ Garanzia piena (2-7 anni)</li>
                                        <li>✅ Personalizzazione completa</li>
                                        <li>✅ Ultimi standard sicurezza</li>
                                    </ul>
                                </div>
                                <div class="cons">
                                    <h5>Contro</h5>
                                    <ul>
                                        <li>❌ Svalutazione 15-25% immediata</li>
                                        <li>❌ Costo più alto</li>
                                        <li>❌ Tempi di consegna</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="comp-card">
                                <h3><i class="fas fa-history"></i> Auto Usata</h3>
                                <div class="pros">
                                    <h5>Pro</h5>
                                    <ul>
                                        <li>✅ Prezzo più basso</li>
                                        <li>✅ Svalutazione già avvenuta</li>
                                        <li>✅ Disponibilità immediata</li>
                                    </ul>
                                </div>
                                <div class="cons">
                                    <h5>Contro</h5>
                                    <ul>
                                        <li>❌ Rischio problemi nascosti</li>
                                        <li>❌ Garanzia limitata</li>
                                        <li>❌ Tecnologia meno aggiornata</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="comp-card">
                                <h3><i class="fas fa-tags"></i> Km0 / Semestrali</h3>
                                <div class="pros">
                                    <h5>Pro</h5>
                                    <ul>
                                        <li>✅ Praticamente nuove</li>
                                        <li>✅ Sconti 10-25%</li>
                                        <li>✅ Garanzia ancora valida</li>
                                    </ul>
                                </div>
                                <div class="cons">
                                    <h5>Contro</h5>
                                    <ul>
                                        <li>❌ Non scegli colore/allestimento</li>
                                        <li>❌ Disponibilità variabile</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </section>

            
                    <section id="ispezione" class="content-section">
                        <h2><span class="section-num">6.3</span> Come ispezionare un'auto usata</h2>
                        <div class="checklist-box">
                            <h4><i class="fas fa-clipboard-check"></i> Checklist completa</h4>
                            <ul class="checklist">
                                <li><i class="fas fa-check-square"></i> <strong>Carrozzeria:</strong> ruggine, differenze colore tra pannelli, allineamento</li>
                                <li><i class="fas fa-check-square"></i> <strong>Interni:</strong> usura sedili, volante, pedali (km scalati?)</li>
                                <li><i class="fas fa-check-square"></i> <strong>Motore:</strong> rumore regolare, olio pulito, nessuna perdita</li>
                                <li><i class="fas fa-check-square"></i> <strong>Pneumatici:</strong> usura uniforme (altrimenti problemi assetto)</li>
                                <li><i class="fas fa-check-square"></i> <strong>Prova su strada:</strong> freni, sterzo, cambio, rumori, vibrazioni</li>
                                <li><i class="fas fa-check-square"></i> <strong>Documenti:</strong> km con tagliandi, revisioni, fermi amministrativi</li>
                            </ul>
                        </div>
                    </section>

       
                    <section id="costi-nascosti" class="content-section">
                        <h2><span class="section-num">6.4</span> Costi nascosti dell'auto</h2>
                        <div class="cost-grid">
                            <div class="cost-item"><i class="fas fa-shield-alt"></i><h4>Assicurazione</h4><p>Neopatentato? Paghi molto di più. Classe, zona, tipo auto e età influenzano il premio.</p></div>
                            <div class="cost-item"><i class="fas fa-file-invoice-dollar"></i><h4>Bollo auto</h4><p>Tassa regionale annuale, calcolata in base ai kW e alla classe ambientale.</p></div>
                            <div class="cost-item"><i class="fas fa-gas-pump"></i><h4>Carburante</h4><p>15 km/l vs 10 km/l = centinaia di euro di differenza all'anno.</p></div>
                            <div class="cost-item"><i class="fas fa-wrench"></i><h4>Manutenzione</h4><p>Tagliandi, olio, filtri, pastiglie. Auto complesse costano di più.</p></div>
                            <div class="cost-item"><i class="fas fa-tire"></i><h4>Pneumatici</h4><p>Un treno di 4 gomme: dai 200€ ai 600€+ secondo misura e brand.</p></div>
                            <div class="cost-item"><i class="fas fa-chart-line"></i><h4>Svalutazione</h4><p>Il costo nascosto più grande. Auto da 20.000€ → dopo 5 anni vale 8.000-10.000€.</p></div>
                        </div>
                    </section>

                    <!-- Navigazione pagine -->
                    <div class="page-navigation">
                        <a href="tutorial.html" class="page-nav-btn prev">
                            <i class="fas fa-arrow-left"></i>
                            <div><span>Precedente</span><strong>Tutorial Pratici</strong></div>
                        </a>
                        <a href="assicurazione.html" class="page-nav-btn next">
                            <div><span>Prossimo</span><strong>Assicurazione</strong></div>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="footer"><div class="container"><div class="footer-bottom"><p>&copy; 2024 Newborn Pilot.</p></div></div></footer>
    <button class="back-to-top" id="backToTop"><i class="fas fa-chevron-up"></i></button>

    <script>

    document.addEventListener('DOMContentLoaded', function() {

        const btnCerca     = document.getElementById('btnCerca');
        const configResult = document.getElementById('configResult');
        const configLoading = document.getElementById('configLoading');

        btnCerca.addEventListener('click', cercaAuto);

        function cercaAuto() {
            const budget        = document.getElementById('budget').value;
            const utilizzo      = document.getElementById('utilizzo').value;
            const alimentazione = document.getElementById('alimentazione').value;
            const dimensioni    = document.getElementById('dimensioni').value;
            const neopatentato  = document.getElementById('neopatentato').value;

            if (!budget) {
                mostraErrore('Seleziona almeno il budget per iniziare la ricerca.');
                return;
            }

            configLoading.style.display = 'flex';
            configResult.style.display = 'none';

            const formData = new FormData();
            formData.append('ajax_search', '1');
            formData.append('budget', budget);
            formData.append('utilizzo', utilizzo);
            formData.append('alimentazione', alimentazione);
            formData.append('dimensioni', dimensioni);
            formData.append('neopatentato', neopatentato);

            fetch('acquisto.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                configLoading.style.display = 'none';

                if (data.success) {
                    renderRisultati(data);
                } else {
                    mostraErrore('Si è verificato un errore nella ricerca. Riprova.');
                }
            })
            .catch(error => {
                configLoading.style.display = 'none';
                mostraErrore('Errore di connessione. Verifica la tua connessione e riprova.');
                console.error('Fetch error:', error);
            });
        }

        function renderRisultati(data) {
            configResult.style.display = 'block';
            let html = '';

            if (data.count === 0) {
                html = `
                    <div class="config-no-results">
                        <div class="no-results-icon"><i class="fas fa-search"></i></div>
                        <h4>Nessuna auto trovata</h4>
                        <p>Prova a modificare i filtri per ampliare la ricerca. Ad esempio, rimuovi il filtro sulle dimensioni o sull'alimentazione.</p>
                    </div>
                `;
            } else {
                if (data.fallback) {
                    html += `
                        <div class="config-fallback-msg">
                            <i class="fas fa-info-circle"></i>
                            <span>${data.message}</span>
                        </div>
                    `;
                }

                html += `
                    <div class="results-header">
                        <div class="results-count">
                            <i class="fas fa-check-circle"></i>
                            <span><strong>${data.count}</strong> auto trovata/e per te</span>
                        </div>
                    </div>
                    <div class="auto-cards-grid">
                `;

                data.auto.forEach((auto, index) => {
                    const prezzoFormattato = Number(auto.prezzo).toLocaleString('it-IT', {
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 0
                    });

                    const alimentazioneIcon = getAlimentazioneIcon(auto.alimentazione);
                    const dimensioniLabel = getDimensioniLabel(auto.dimensioni);
                    const alimentazioneLabel = auto.alimentazione.charAt(0).toUpperCase() + auto.alimentazione.slice(1);
                    const neopatLabel = auto.neopatentato == 1;
                    const rapportoKwT = auto.potenza_kw && auto.peso_kg
                        ? (auto.potenza_kw / (auto.peso_kg / 1000)).toFixed(1)
                        : null;

                    html += `
                        <div class="auto-card" style="animation-delay: ${index * 0.1}s">
                            <div class="auto-card-header">
                                <div class="auto-card-brand">
                                    <h3>${escapeHtml(auto.marca)} ${escapeHtml(auto.modello)}</h3>
                                    ${auto.versione ? `<span class="auto-version">${escapeHtml(auto.versione)}</span>` : ''}
                                </div>
                                <div class="auto-card-price">
                                    <span class="price-value">${prezzoFormattato}€</span>
                                    <span class="price-label">Prezzo indicativo</span>
                                </div>
                            </div>

                            <div class="auto-card-tags">
                                <span class="auto-tag tag-fuel">
                                    <i class="${alimentazioneIcon}"></i> ${alimentazioneLabel}
                                </span>
                                <span class="auto-tag tag-size">
                                    <i class="fas fa-car"></i> ${dimensioniLabel}
                                </span>
                                <span class="auto-tag tag-year">
                                    <i class="fas fa-calendar-alt"></i> ${auto.anno}
                                </span>
                                ${neopatLabel ? '<span class="auto-tag tag-neo"><i class="fas fa-id-card"></i> Neopatentato OK</span>' : ''}
                            </div>

                            <div class="auto-card-specs">
                                ${auto.potenza_kw ? `
                                    <div class="spec-item">
                                        <i class="fas fa-bolt"></i>
                                        <div>
                                            <span class="spec-value">${auto.potenza_kw} kW</span>
                                            <span class="spec-label">Potenza</span>
                                        </div>
                                    </div>
                                ` : ''}
                                ${auto.peso_kg ? `
                                    <div class="spec-item">
                                        <i class="fas fa-weight-hanging"></i>
                                        <div>
                                            <span class="spec-value">${auto.peso_kg} kg</span>
                                            <span class="spec-label">Peso</span>
                                        </div>
                                    </div>
                                ` : ''}
                                ${rapportoKwT ? `
                                    <div class="spec-item">
                                        <i class="fas fa-tachometer-alt"></i>
                                        <div>
                                            <span class="spec-value">${rapportoKwT} kW/t</span>
                                            <span class="spec-label">Rapporto P/P</span>
                                        </div>
                                    </div>
                                ` : ''}
                                ${auto.autonomia_elettrica ? `
                                    <div class="spec-item">
                                        <i class="fas fa-battery-full"></i>
                                        <div>
                                            <span class="spec-value">${auto.autonomia_elettrica} km</span>
                                            <span class="spec-label">Autonomia EV</span>
                                        </div>
                                    </div>
                                ` : ''}
                            </div>

                            <div class="auto-card-utilizzo">
                                <span class="utilizzo-label"><i class="fas fa-road"></i> Utilizzo consigliato:</span>
                                <span class="utilizzo-value">${formatUtilizzo(auto.utilizzo)}</span>
                            </div>
                        </div>
                    `;
                });

                html += '</div>';
            }

            configResult.innerHTML = html;
        }

        function mostraErrore(msg) {
            configResult.style.display = 'block';
            configResult.innerHTML = `
                <div class="config-error-msg">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>${msg}</span>
                </div>
            `;
        }

        function getAlimentazioneIcon(tipo) {
            const icons = {
                'benzina':   'fas fa-gas-pump',
                'diesel':    'fas fa-gas-pump',
                'gpl':       'fas fa-fire',
                'ibrido':    'fas fa-leaf',
                'elettrico': 'fas fa-bolt'
            };
            return icons[tipo] || 'fas fa-gas-pump';
        }

        function getDimensioniLabel(dim) {
            const labels = {
                'city': 'Citycar',
                'util': 'Utilitaria',
                'berl': 'Berlina',
                'suv':  'SUV / Crossover',
                'sw':   'Station Wagon'
            };
            return labels[dim] || dim;
        }

        function formatUtilizzo(utilizzo) {
            const labels = {
                'citta': 'Città',
                'extra': 'Extraurbano',
                'auto':  'Autostrada',
                'misto': 'Misto'
            };
            return utilizzo.split(',').map(u => labels[u.trim()] || u.trim()).join(', ');
        }

        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

       
        const sidebarLinks = document.querySelectorAll('.sidebar-link');
        const sections = document.querySelectorAll('.content-section');

        window.addEventListener('scroll', () => {
            let current = '';
            sections.forEach(section => {
                const sectionTop = section.offsetTop - 150;
                if (window.scrollY >= sectionTop) {
                    current = section.getAttribute('id');
                }
            });
            sidebarLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === '#' + current) {
                    link.classList.add('active');
                }
            });
        });

        const navbar = document.getElementById('navbar');
        window.addEventListener('scroll', () => {
            navbar.classList.toggle('scrolled', window.scrollY > 50);
        });

      
        const navToggle = document.getElementById('navToggle');
        const navMenu = document.getElementById('navMenu');
        if (navToggle && navMenu) {
            navToggle.addEventListener('click', () => {
                navToggle.classList.toggle('active');
                navMenu.classList.toggle('open');
            });
        }

        const backToTop = document.getElementById('backToTop');
        if (backToTop) {
            window.addEventListener('scroll', () => {
                backToTop.classList.toggle('visible', window.scrollY > 500);
            });
            backToTop.addEventListener('click', () => {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        }

    
        document.querySelectorAll('.nav-dropdown').forEach(dropdown => {
            dropdown.addEventListener('click', function(e) {
                if (window.innerWidth <= 900) {
                    e.preventDefault();
                    this.classList.toggle('open');
                }
            });
        });
    });
    </script>
</body>
</html>