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
            --bg-light: #e8e8e8; /* Grigio chiarissimo dello sfondo */
            --accent-beige: #e6d3c0; /* Beige dei bottoni e banner */
            --text-dark: #222;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', 'Segoe UI', sans-serif; }

        body { 
            background-color: var(--bg-light); 
            color: var(--text-dark);
            line-height: 1.4;
        }

        /* Header */
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 5%;
        }

        .logo img { height: 60px; }

        /* Banner Titolo */
        .hero-banner {
            background-color: white;
            margin: 0 5%;
            padding: 20px;
            border-radius: 25px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }

        .hero-banner h1 {
            background-color: var(--accent-beige);
            display: inline-block;
            padding: 15px 40px;
            border-radius: 20px;
            font-size: 2.2rem;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 1px;
            max-width: 90%;
        }

        /* Fascia Sottotitolo */
        .sub-hero {
            background-color: var(--accent-beige);
            margin-top: 30px;
            padding: 30px 10%;
            text-align: center;
            font-style: italic;
            font-size: 1.1rem;
        }

        /* Sezione Main (Bottoni e Immagine) */
        .main-content {
            display: flex;
            justify-content: space-around;
            align-items: center;
            padding: 50px 5%;
            flex-wrap: wrap;
        }

        .action-btn {
            background-color: var(--accent-beige);
            text-decoration: none;
            color: var(--text-dark);
            font-size: 2.5rem;
            font-weight: 900;
            padding: 40px 60px;
            border-radius: 80px; /* Effetto ovoidale */
            transition: transform 0.3s;
            text-transform: uppercase;
        }

        .action-btn:hover { transform: scale(1.05); }

        .center-illustration img {
            max-width: 350px;
            height: auto;
        }

        /* Footer / Scopri di più */
        .footer-section {
            text-align: center;
            padding-bottom: 50px;
        }

        .btn-scopri {
            background-color: var(--accent-beige);
            border: none;
            padding: 10px 30px;
            border-radius: 15px;
            font-weight: bold;
            cursor: pointer;
            font-size: 1rem;
        }

        /* Dropdown utente stile minimal */
        .user-menu { position: relative; }
        .user-icon { font-size: 2rem; cursor: pointer; }
        .dropdown {
            position: absolute; right: 0; top: 40px;
            background: white; border-radius: 10px;
            display: none; flex-direction: column; min-width: 150px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1); z-index: 100;
        }
        .dropdown.active { display: flex; }
        .dropdown a { padding: 15px; text-decoration: none; color: black; border-bottom: 1px solid #eee; }

        /* Contenuto Extra */
        #extraContainer {
            max-width: 800px;
            margin: 20px auto;
            background: white;
            padding: 30px;
            border-radius: 20px;
            display: none;
        }
    </style>
</head>
<body>

    <header>
        <div class="logo">
            <img src="immagini/tastologo.png" alt="Debook Logo">
        </div>
        <div class="user-menu-container">
            <i class="fa-solid fa-circle-user user-icon" id="userBtn"></i>
            <div class="dropdown-menu" id="userDropdown">
                <?php if($is_logged): ?>
                    <a href="#">Profilo (<?php echo htmlspecialchars($nome_utente); ?>)</a>
                    <a href="logout.php" style="color: red;">Logout</a>
                <?php else: ?>
                    <a href="login.php">Accedi</a>
                    <a href="register.php">Registrati</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <div class="hero-banner">
        <h1>Il sapere non ha prezzo<br>ma qui costa pochissimo</h1>
    </div>

    <div class="sub-hero">
        <p>"Il sapere costa meno se è condiviso." Perché pagare di più? Qui trovi libri usati venduti direttamente da altri studenti. È il modo più intelligente per svuotare gli zaini e riempire le menti senza svuotare il portafoglio.</p>
        <p style="margin-top: 15px; font-weight: bold;">Prezzi da studente, per gli studenti.</p>
    </div>

    <main class="main-content">
        <a href="compra.php" class="action-btn">Compra</a>

        <div class="center-illustration">
            <img src="immagini/home.jpg" alt="Libreria">
        </div>

        <a href="Vendi.php" class="action-btn">Vendi</a>
    </main>

    <div class="footer-section">
        <button class="btn-scopri" id="showBtn">Scopri di più</button>
        
        <div id="extraContainer">
            <section style="font-family: 'Segoe UI', Roboto, Arial, sans-serif; line-height: 1.6; color: #333; max-width: 650px; margin: 20px auto; padding: 20px; border-radius: 12px; background-color: #f9f9f9; border: 1px solid #eee;">

    <h2 style="color: #2c3e50; font-size: 1.5rem; text-align: center; margin-bottom: 20px; border-bottom: 2px solid #3498db; pb: 10px; padding-bottom: 10px;">
        Perché nasce <strong>DeBook</strong>?
    </h2>

    <p style="font-size: 1rem; margin-bottom: 20px;">
        L’idea di DeBook nasce da un’osservazione semplice: <strong>il paradosso dei libri scolastici</strong>. 
        Mentre molte famiglie affrontano spese ingenti ogni anno, migliaia di volumi in ottime condizioni restano inutilizzati sugli scaffali di chi ha terminato gli studi. 
        Un potenziale sprecato, sia per il portafoglio che per l'ambiente.
    </p>

    <div style="background: #fff; padding: 15px; border-left: 4px solid #3498db; margin-bottom: 25px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
        <h3 style="margin-top: 0; color: #2980b9; font-size: 1.1rem;">Oltre il prestito, verso la comunità</h3>
        <p style="margin-bottom: 0; font-size: 0.95rem;">
            DeBook colma il vuoto tra la necessità di possedere un manuale e il desiderio di farlo in modo <strong>sostenibile</strong>. 
            Ci siamo chiesti: <em>perché non portare l'efficienza di piattaforme come Vinted direttamente tra i banchi di scuola?</em>
        </p>
    </div>

    <h3 style="text-align: center; font-size: 1.2rem; color: #2c3e50;">La nostra Visione</h3>
    
    <ul style="list-style: none; padding: 0;">
        <li style="margin-bottom: 15px; display: flex; align-items: flex-start;">
            <span style="background: #e1f5fe; color: #0288d1; padding: 5px 10px; border-radius: 50%; margin-right: 12px; font-weight: bold;">🌱</span>
            <span><strong>Economia Circolare:</strong> Riduciamo lo spreco di carta e l'impatto ambientale incentivando il riuso sistematico.</span>
        </li>
        <li style="margin-bottom: 15px; display: flex; align-items: flex-start;">
            <span style="background: #e8f5e9; color: #388e3c; padding: 5px 10px; border-radius: 50%; margin-right: 12px; font-weight: bold;">💰</span>
            <span><strong>Accessibilità:</strong> Rendiamo il diritto allo studio meno gravoso, permettendo di recuperare parte dell'investimento iniziale.</span>
        </li>
        <li style="margin-bottom: 15px; display: flex; align-items: flex-start;">
            <span style="background: #fff3e0; color: #f57c00; padding: 5px 10px; border-radius: 50%; margin-right: 12px; font-weight: bold;">🤝</span>
            <span><strong>Fiducia e Sicurezza:</strong> Scambi a mano tra compagni di scuola. Niente costi di spedizione, solo trasparenza e volti noti.</span>
        </li>
    </ul>

    <p style="text-align: center; font-style: italic; margin-top: 25px; color: #555; border-top: 1px solid #eee; padding-top: 15px;">
        Scegliere DeBook significa credere in una scuola dove la <strong>collaborazione tra pari</strong> genera valore reale.
    </p>

</section>
            <button class="btn-scopri" id="hideBtn" style="margin-top: 20px;">Vedi meno</button>
        </div>
    </div>

    <script>
        // Gestione Menu Dropdown (Omino)
        const btn = document.getElementById('userBtn');
        const menu = document.getElementById('userDropdown');
        
        btn.onclick = (e) => {
            menu.classList.toggle('active');
            e.stopPropagation();
        };

        // Chiude il menu se clicchi fuori
        window.onclick = () => menu.classList.remove('active');

        // Gestione Logica Espandi/Riduci Spiegazione
        const showBtn = document.getElementById('showBtn');
        const hideBtn = document.getElementById('hideBtn');
        const extraContainer = document.getElementById('extraContainer');

        showBtn.onclick = () => {
            showBtn.style.display = 'none';
            extraContainer.style.display = 'block';
            // Scroll fluido verso la nuova sezione
            extraContainer.scrollIntoView({ behavior: 'smooth' });
        };

        hideBtn.onclick = () => {
            extraContainer.style.display = 'none';
            showBtn.style.display = 'block';
            // Ritorna in cima alla pagina con animazione
            window.scrollTo({ top: 0, behavior: 'smooth' });
        };
    </script>
</body>
</html>