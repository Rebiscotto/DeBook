<?php
session_start();
$is_logged = isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true;
$nome_utente = $is_logged ? $_SESSION["nome"] : "";
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debook - Home</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --dark: #1a1a1a;
            --grey-side: #e0e0e0;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Arial Black', 'Arial', sans-serif; }

        body { background-color: #fff; overflow: hidden; height: 100vh; position: relative; }

        /* Header e Navbar */
        .header-nav {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 30px;
            z-index: 1000;
        }

        .logo-link img { height: 70px; cursor: pointer; transition: transform 0.2s; }
        .logo-link img:hover { transform: scale(1.05); }

        .user-menu-container { position: relative; }
        .user-icon { font-size: 2.2rem; color: var(--dark); cursor: pointer; }

        .dropdown-menu {
            position: absolute;
            right: 0;
            top: 50px;
            background: white;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            display: none;
            flex-direction: column;
            min-width: 180px;
        }
        .dropdown-menu.active { display: flex; }
        .dropdown-menu a { padding: 12px; text-decoration: none; color: var(--dark); font-family: 'Arial', sans-serif; font-size: 0.9rem; border-bottom: 1px solid #eee; }

        /* Layout */
        .wrapper { display: flex; height: 100vh; width: 100%; }

        .side {
            width: 15%;
            background-color: var(--grey-side);
            display: flex;
            justify-content: center;
            align-items: center;
            text-decoration: none;
            transition: 0.3s;
        }
        .side:hover { background-color: #d0d0d0; }
        .side h2 { writing-mode: vertical-rl; transform: rotate(180deg); font-size: 3rem; color: var(--dark); }

        .center-content {
            width: 70%;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 110px 40px 20px 40px;
            text-align: center;
            overflow-y: auto; /* Permette lo scroll se il testo è lungo */
        }

        .hero-text h1 { font-size: 3.2rem; color: var(--dark); line-height: 1; margin-bottom: 15px; }
        .hero-text p { font-family: 'Arial', sans-serif; font-size: 0.9rem; color: #444; max-width: 550px; margin-bottom: 20px; }

        .image-container img { max-height: 40vh; width: auto; object-fit: contain; margin-top: 20px; }

        .footer-text {
            font-family: 'Arial', sans-serif;
            font-weight: bold;
            text-decoration: underline;
            margin: 15px 0;
            cursor: pointer;
            color: var(--dark);
        }
    </style>
</head>
<body>

    <header class="header-nav">
        <a href="index.php" class="logo-link">
            <img src="immagini/tastologo.jpg" alt="Debook Logo">
        </a>
        <div class="user-menu-container">
            <i class="fa-solid fa-circle-user user-icon" id="userBtn"></i>
            <div class="dropdown-menu" id="userDropdown">
                <?php if($is_logged): ?>
                    <a href="#">Profilo di <?php echo htmlspecialchars($nome_utente); ?></a>
                    <a href="#"><i class="fa-solid fa-book"></i> I miei libri</a>
                    <a href="logout.php" style="color: red;">Logout</a>
                <?php else: ?>
                    <a href="login.php">Accedi</a>
                    <a href="register.php">Registrati</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <div class="wrapper">
        <a href="vendi.php" class="side"><h2>VENDI</h2></a>

        <div class="center-content">
            <div class="hero-text">
                <h1>IL SAPERE NON HA PREZZO<br>MA QUI COSTA POCHISSIMO</h1>
                <h2>"Il sapere costa meno se è condiviso." Perché pagare di più? Qui trovi libri usati venduti direttamente da altri studenti.</h2>
            </div>
                    <div class="image-container">
                <img src="https://previews.123rf.com/images/jemastock/jemastock2208/jemastock220802856/191195860-mom-and-son-buying-supplies.jpg" alt="Studenti in biblioteca">
            </div>
            <p id="showBtn" class="footer-text">Scopri di più</p>

            <div id="extraContainer" style="display: none;">
                <div id="extraText">
                    <p style="font-family: 'Arial', sans-serif; font-size: 0.9rem; color: #444; max-width: 550px; margin: 0 auto;">
                        Perché nasce DeBook: Trasformare la spesa scolastica in valore condiviso
L’idea di DeBook nasce da un’osservazione semplice, quanto comune a migliaia di famiglie e studenti: il paradosso dei libri scolastici. Ogni anno, l’inizio della scuola porta con sé l’onere di una spesa economica rilevante per l’acquisto dei manuali. Eppure, mentre molte famiglie faticano a sostenere questi costi, migliaia di libri in perfette condizioni rimangono fermi sugli scaffali di chi ha appena concluso un ciclo di studi. Questi volumi, carichi di conoscenza ma destinati a prendere polvere, rappresentano un potenziale sprecato, sia economico che ambientale.

Oltre il prestito, verso la comunità
Nonostante esistano sistemi di prestito o comodato d’uso, la realtà scolastica ci dice che la maggior parte degli studenti continua a preferire l’acquisto del proprio manuale. DeBook nasce per colmare il vuoto tra la necessità di possedere un libro e il desiderio di farlo in modo sostenibile e accessibile.

Abbiamo guardato ai modelli di economia circolare più evoluti, come Vinted, e ci siamo chiesti: perché non portare questa efficienza all’interno della scuola?

La nostra Visione
DeBook non è solo una piattaforma di compravendita; è un progetto che mira a trasformare l’istituto scolastico in una comunità collaborativa. La nostra missione si fonda su tre pilastri:

Economia Circolare: Incentiviamo il riuso, riducendo lo spreco di carta e l'impatto ambientale legato alla produzione di nuovi volumi.

Accessibilità: Vogliamo che il diritto allo studio sia meno gravoso, permettendo agli studenti di recuperare parte dell’investimento e ad altri di acquistare testi a prezzi contenuti.

Fiducia e Sicurezza: Abbiamo scelto di circoscrivere l’app all’ambiente scolastico. Gli scambi avvengono esclusivamente a mano, tra compagni di scuola, eliminando i costi di spedizione e garantendo la massima trasparenza.

Scegliere DeBook significa credere in una scuola dove la collaborazione tra pari genera valore reale, dove un libro che ha già insegnato molto può continuare a farlo tra le mani di un altro studente.
                    </p>
                </div>
                <p id="hideBtn" class="footer-text" style="margin-top: 15px;">Vedi meno</p>
            </div>
           
        </div>

        <a href="compra.php" class="side"><h2>COMPRA</h2></a>
    </div>

    <script>
        // Gestione Menu Omino
const btn = document.getElementById('userBtn');
const menu = document.getElementById('userDropdown');
btn.onclick = (e) => {
    menu.classList.toggle('active');
    e.stopPropagation();
};
window.onclick = () => menu.classList.remove('active');

// --- LOGICA ESPANDI/RIDUCI AGGIORNATA ---
const showBtn = document.getElementById('showBtn');     // Tasto "Scopri di più"
const hideBtn = document.getElementById('hideBtn');     // Tasto "Vedi meno"
const extraContainer = document.getElementById('extraContainer'); // Contenitore testo + tasto chiudi

// Quando premo "Scopri di più"
showBtn.onclick = () => {
    showBtn.style.display = 'none';          // Scompare il tasto "Scopri di più"
    extraContainer.style.display = 'block';  // Appare il blocco (testo + tasto "Vedi meno")
};

// Quando premo "Vedi meno"
hideBtn.onclick = () => {
    extraContainer.style.display = 'none';   // Scompare tutto il blocco extra
    showBtn.style.display = 'block';         // Riappare il tasto "Scopri di più"
    
    // Torna su dolcemente all'inizio della sezione
    document.querySelector('.center-content').scrollTo({ top: 0, behavior: 'smooth' });
};
    </script>
</body>
</html>