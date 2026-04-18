
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debook - Benvenuto</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* TAVOLOZZA COLORI (Importata dalla Home) */
        :root {
            --bg-page: #e2e2e2;      /* Grigio chiaro sfondo */
            --accent-beige: #e9d5c3; /* Beige bottoni e banner */
            --white: #ffffff;
            --dark-text: #2c2e34;
        }

        * { 
            box-sizing: border-box; 
            margin: 0; 
            padding: 0; 
            font-family: 'Arial Black', sans-serif; 
        }

        body { 
            background-color: var(--bg-page); 
            min-height: 100vh; 
            position: relative; 
            overflow-x: hidden; 
        }

        /* NAVBAR */
        .header-nav {
            position: absolute;
            top: 0; 
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 30px;
            z-index: 1000;
        }

        .logo-link img { 
            height: 60px; 
            transition: transform 0.2s; 
        }
        .logo-link img:hover { transform: scale(1.05); }

        .user-menu-container { position: relative; }
        .user-icon { 
            font-size: 2.2rem; 
            color: var(--dark-text); 
            cursor: pointer; 
            transition: color 0.2s;
        }
        .user-icon:hover { color: #555; }

        /* Menu Dropdown */
        .dropdown-menu {
            position: absolute; 
            right: 0; 
            top: 50px;
            background: white; 
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            display: none; 
            flex-direction: column; 
            min-width: 180px;
            overflow: hidden;
        }
        .dropdown-menu.active { display: flex; }
        .dropdown-menu a { 
            padding: 15px; 
            text-decoration: none; 
            color: var(--dark-text); 
            font-family: 'Arial', sans-serif; 
            font-size: 0.95rem; 
            border-bottom: 1px solid #eee; 
            transition: background 0.2s;
        }
        .dropdown-menu a:hover { background-color: #f9f9f9; }

        /* LAYOUT PRINCIPALE CENTRATO */
        .center-content {
            width: 100%;
            max-width: 900px; /* Contiene il layout al centro */
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 120px; /* Spazio per la navbar assoluta */
            padding-bottom: 60px;
            text-align: center;
        }

        /* Banner Titolo */
        .banner-header {
            background-color: var(--white);
            padding: 15px;
            border-radius: 60px;
            margin-bottom: 30px;
            width: 90%;
            display: flex; 
            justify-content: center;
        }

        .banner-inner {
            background-color: var(--accent-beige);
            padding: 25px 20px;
            border-radius: 50px;
            width: 100%;
        }

        .banner-inner h1 {
            font-size: 2.2rem;
            color: var(--dark-text);
            line-height: 1.1;
            margin: 0;
            text-transform: uppercase;
        }

        /* Fascia descrittiva */
        .description-strip {
            background-color: var(--accent-beige);
            width: 100%;
            padding: 30px 10%;
            margin-bottom: 30px;
        }

        .description-strip p {
            font-family: 'Arial', sans-serif;
            font-size: 1.1rem;
            line-height: 1.5;
            color: #444;
            max-width: 800px;
            margin: 0 auto;
        }

        .description-strip .quote { 
            font-style: italic; 
            margin-bottom: 15px; 
            display: block; 
            font-weight: bold;
        }

        /* Immagine */
        .image-container img {
            max-height: 35vh;
            width: auto;
            object-fit: contain;
            margin-bottom: 20px;
            border-radius: 15px;
            mix-blend-mode: multiply; /* Aiuta a fondere l'immagine con lo sfondo grigio */
        }

        /* Pulsanti stile "Pillola" della Home */
        .footer-text {
            background-color: var(--accent-beige);
            color: var(--dark-text);
            padding: 15px 35px;
            border-radius: 30px;
            font-family: 'Arial', sans-serif;
            font-weight: bold;
            font-size: 1.1rem;
            text-transform: uppercase;
            cursor: pointer;
            margin: 20px 0;
            display: inline-block;
            transition: transform 0.2s, background-color 0.2s;
        }
        .footer-text:hover { 
            transform: scale(1.05); 
            background-color: #dfc4ab; 
        }

        /* Sezione Extra */
        #extraContainer { 
            width: 100%; 
            padding: 0 20px;
        }
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
        
        <div class="banner-header">
            <div class="banner-inner">
                <h1>IL SAPERE NON HA PREZZO<br>MA QUI COSTA POCHISSIMO</h1>
            </div>
        </div>

        <div class="description-strip">
            <p>
                <span class="quote">"Il sapere costa meno se è condiviso."</span>
                Entra a far parte della prima community scolastica dedicata al riuso dei libri.
            </p>
        </div>
        
        <div class="image-container">
            <img src="immagini/home.jpg" alt="Studenti e libri">
        </div>

        <div id="showBtn" class="footer-text">Scopri il progetto DeBook</div>

        <div id="extraContainer" style="display: none;">
            <section style="font-family: 'Segoe UI', Roboto, Arial, sans-serif; line-height: 1.6; color: #333; max-width: 750px; margin: 20px auto; padding: 30px; border-radius: 20px; background-color: var(--white); border: 1px solid #ddd; box-shadow: 0 5px 15px rgba(0,0,0,0.05); text-align: left;">

                <h2 style="color: var(--dark-text); font-size: 1.6rem; text-align: center; margin-bottom: 20px; border-bottom: 3px solid var(--accent-beige); padding-bottom: 10px;">
                    Perché nasce <strong>DeBook</strong>?
                </h2>

                <p style="font-size: 1.05rem; margin-bottom: 20px;">
                    L’idea di DeBook nasce per risolvere il <strong>paradosso dei libri scolastici</strong>: 
                    spese ingenti per le famiglie ogni anno, mentre migliaia di volumi in ottimo stato restano inutilizzati negli armadi di chi ha concluso gli studi. 
                </p>

                <div style="background: var(--bg-page); padding: 20px; border-left: 5px solid var(--accent-beige); margin-bottom: 25px; border-radius: 0 10px 10px 0;">
                    <h3 style="margin-top: 0; color: var(--dark-text); font-size: 1.2rem;">Dalla scuola, per la scuola</h3>
                    <p style="margin-bottom: 0; font-size: 1rem;">
                        DeBook trasforma l'istituto in una comunità collaborativa. Portiamo la semplicità dello scambio online direttamente tra i corridoi scolastici.
                    </p>
                </div>

                <h3 style="text-align: center; font-size: 1.3rem; color: var(--dark-text); margin-bottom: 20px;">I Nostri Valori</h3>
                
                <ul style="list-style: none; padding: 0;">
                    <li style="margin-bottom: 18px; display: flex; align-items: flex-start;">
                        <span style="background: #e1f5fe; color: #0288d1; padding: 8px; border-radius: 50%; margin-right: 15px; font-size: 1.2rem; line-height: 1;">🌱</span>
                        <span style="padding-top: 5px;"><strong>Economia Circolare:</strong> Allunghiamo la vita dei libri e riduciamo l'impatto ambientale.</span>
                    </li>
                    <li style="margin-bottom: 18px; display: flex; align-items: flex-start;">
                        <span style="background: #e8f5e9; color: #388e3c; padding: 8px; border-radius: 50%; margin-right: 15px; font-size: 1.2rem; line-height: 1;">💰</span>
                        <span style="padding-top: 5px;"><strong>Accessibilità:</strong> Abbattiamo i costi scolastici rendendo lo studio alla portata di tutti.</span>
                    </li>
                    <li style="margin-bottom: 18px; display: flex; align-items: flex-start;">
                        <span style="background: #fff3e0; color: #f57c00; padding: 8px; border-radius: 50%; margin-right: 15px; font-size: 1.2rem; line-height: 1;">🤝</span>
                        <span style="padding-top: 5px;"><strong>Sicurezza:</strong> Scambi diretti e a mano tra compagni di istituto.</span>
                    </li>
                </ul>
            </section>
            
            <div id="hideBtn" class="footer-text" style="font-size: 0.9rem; padding: 10px 25px;">Vedi Meno</div>
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
            extraContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
        };

        hideBtn.onclick = () => {
            extraContainer.style.display = 'none';
            showBtn.style.display = 'inline-block';
            // Ritorna in cima alla pagina con animazione
            window.scrollTo({ top: 0, behavior: 'smooth' });
        };
    </script>
</body>
</html>