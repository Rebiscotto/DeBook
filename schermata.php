Perfetto, ho rinominato concettualmente il file e pulito ulteriormente il codice per renderlo una "Landing Page" (pagina di atterraggio) solida e professionale.

Ecco il codice completo per il tuo file schermata.php. Ho aggiunto un piccolo tocco di stile al logo per far capire all'utente che è cliccabile e ricarica la pagina.

PHP
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debook - Benvenuto</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --dark: #1a1a1a;
            --blue-accent: #3498db;
            --light-grey: #f9f9f9;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Arial Black', 'Arial', sans-serif; }

        body { background-color: #fff; min-height: 100vh; position: relative; overflow-x: hidden; }

        /* Header e Navbar */
        .header-nav {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 30px;
            z-index: 1000;
            background-color: rgba(255,255,255,0.95);
            backdrop-filter: blur(5px);
            border-bottom: 1px solid #eee;
        }

        /* Logo che ricarica la pagina */
        .logo-link img { height: 60px; cursor: pointer; transition: transform 0.2s; }
        .logo-link img:hover { transform: scale(1.05); }

        .user-menu-container { position: relative; }
        .user-icon { font-size: 2.2rem; color: var(--dark); cursor: pointer; transition: color 0.2s; }
        .user-icon:hover { color: var(--blue-accent); }

        /* Menu Dropdown - Solo Login e Registrazione */
        .dropdown-menu {
            position: absolute;
            right: 0;
            top: 55px;
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
            display: none;
            flex-direction: column;
            min-width: 180px;
            overflow: hidden;
        }
        .dropdown-menu.active { display: flex; }
        .dropdown-menu a { 
            padding: 15px 20px; 
            text-decoration: none; 
            color: var(--dark); 
            font-family: 'Arial', sans-serif; 
            font-size: 1rem; 
            border-bottom: 1px solid #eee;
            transition: background 0.2s;
        }
        .dropdown-menu a:last-child { border-bottom: none; }
        .dropdown-menu a:hover { background-color: #f5f5f5; }

        /* Layout Centrale */
        .center-content {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 140px 20px 60px 20px;
            text-align: center;
        }

        .hero-text h1 { font-size: 3.5rem; color: var(--dark); line-height: 1.1; margin-bottom: 20px; text-transform: uppercase; }
        .hero-text h2 { font-family: 'Arial', sans-serif; font-size: 1.2rem; color: #555; max-width: 700px; margin: 0 auto 30px auto; font-weight: normal; }

        .image-container img { max-height: 35vh; width: auto; object-fit: contain; margin-bottom: 30px; border-radius: 15px; }

        .footer-text {
            font-family: 'Arial', sans-serif;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-decoration: underline;
            margin: 20px 0;
            cursor: pointer;
            color: var(--blue-accent);
        }

        #extraContainer { width: 100%; max-width: 800px; margin-top: 20px; }
    </style>
</head>
<body>

    <header class="header-nav">
        <a href="schermata.php" class="logo-link">
            <img src="immagini/tastologo.png" alt="Debook Logo">
        </a>
        
        <div class="user-menu-container">
            <i class="fa-solid fa-circle-user user-icon" id="userBtn"></i>
            <div class="dropdown-menu" id="userDropdown">
                <a href="login.php"><i class="fa-solid fa-right-to-bracket"></i> Accedi</a>
                <a href="register.php"><i class="fa-solid fa-user-plus"></i> Registrati</a>
            </div>
        </div>
    </header>

    <div class="center-content">
        <div class="hero-text">
            <h1>IL SAPERE NON HA PREZZO<br>MA QUI COSTA POCHISSIMO</h1>
            <h2>"Il sapere costa meno se è condiviso." Entra a far parte della prima community scolastica dedicata al riuso dei libri.</h2>
        </div>
        
        <div class="image-container">
            <img src="immagini/home.jpg" alt="Studenti e libri">
        </div>

        <p id="showBtn" class="footer-text">Scopri il progetto DeBook</p>

        <div id="extraContainer" style="display: none;">
            <div id="extraText">
                <section style="font-family: 'Segoe UI', sans-serif; line-height: 1.6; color: #333; text-align: left; padding: 30px; border-radius: 15px; background-color: var(--light-grey); border: 1px solid #eee; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">

                    <h2 style="color: #2c3e50; font-size: 1.6rem; text-align: center; margin-bottom: 20px; border-bottom: 3px solid var(--blue-accent); padding-bottom: 10px;">
                        Perché nasce <strong>DeBook</strong>?
                    </h2>

                    <p style="font-size: 1.05rem; margin-bottom: 20px;">
                        L’idea di DeBook nasce per risolvere il <strong>paradosso dei libri scolastici</strong>: 
                        spese ingenti per le famiglie ogni anno, mentre migliaia di volumi in ottimo stato restano inutilizzati negli armadi di chi ha concluso gli studi. 
                    </p>

                    <div style="background: #fff; padding: 20px; border-left: 5px solid var(--blue-accent); margin-bottom: 25px;">
                        <h3 style="margin-top: 0; color: var(--blue-accent); font-size: 1.2rem;">Dalla scuola, per la scuola</h3>
                        <p style="margin-bottom: 0; font-size: 1rem;">
                            DeBook trasforma l'istituto in una comunità collaborativa. Portiamo la semplicità dello scambio online direttamente tra i corridoi scolastici.
                        </p>
                    </div>

                    <h3 style="text-align: center; font-size: 1.3rem; color: #2c3e50; margin-bottom: 20px;">I Nostri Valori</h3>
                    
                    <ul style="list-style: none; padding: 0;">
                        <li style="margin-bottom: 18px; display: flex; align-items: flex-start;">
                            <span style="font-size: 1.5rem; margin-right: 15px;">🌱</span>
                            <span><strong>Economia Circolare:</strong> Allunghiamo la vita dei libri e riduciamo l'impatto ambientale.</span>
                        </li>
                        <li style="margin-bottom: 18px; display: flex; align-items: flex-start;">
                            <span style="font-size: 1.5rem; margin-right: 15px;">💰</span>
                            <span><strong>Accessibilità:</strong> Abbattiamo i costi scolastici rendendo lo studio alla portata di tutti.</span>
                        </li>
                        <li style="margin-bottom: 18px; display: flex; align-items: flex-start;">
                            <span style="font-size: 1.5rem; margin-right: 15px;">🤝</span>
                            <span><strong>Sicurezza:</strong> Scambi diretti e a mano tra compagni di istituto.</span>
                        </li>
                    </ul>
                </section>
            </div>
            <p id="hideBtn" class="footer-text">Chiudi spiegazione</p>
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