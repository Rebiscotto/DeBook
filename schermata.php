
<?php
session_start();
// Se l'utente è già loggato, non ha senso che veda la landing page, lo mandiamo alla home
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debook - Benvenuto</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        /* Aggiungiamo solo le personalizzazioni specifiche per questa pagina 
           che non sono nel CSS globale */
        body { 
            overflow-x: hidden; 
        }

        .center-content {
            width: 100%;
            max-width: 900px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 40px 20px;
            text-align: center;
        }

        .banner-header {
            background-color: var(--white);
            padding: 15px;
            border-radius: 60px;
            margin-bottom: 30px;
            width: 100%;
            box-shadow: var(--shadow);
        }

        .banner-inner {
            background-color: var(--accent-beige);
            padding: 25px 20px;
            border-radius: 50px;
        }

        .banner-inner h1 {
            font-size: clamp(1.2rem, 5vw, 2.2rem); /* Testo fluido: si rimpicciolisce su mobile */
            color: var(--dark-text);
            line-height: 1.1;
            margin: 0;
            text-transform: uppercase;
        }

        .description-strip {
            background-color: var(--accent-beige);
            width: 100vw; /* Copre tutta la larghezza dello schermo */
            padding: 30px 10%;
            margin-bottom: 30px;
        }

        .image-container img {
            max-width: 100%;
            height: auto;
            max-height: 35vh;
            border-radius: 15px;
            mix-blend-mode: multiply;
        }

        .footer-text {
            background-color: var(--accent-beige);
            color: var(--dark-text);
            padding: 15px 35px;
            border-radius: 30px;
            font-weight: bold;
            text-transform: uppercase;
            cursor: pointer;
            margin: 20px 0;
            display: inline-block;
            transition: transform 0.2s;
            border: none;
        }

        /* Dropdown specifico per chi non è loggato */
        .dropdown-menu {
            position: absolute; 
            right: 30px; 
            top: 70px;
            background: white; 
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            display: none; 
            flex-direction: column; 
            min-width: 180px;
            z-index: 2000;
        }
        .dropdown-menu.active { display: flex; }
        .dropdown-menu a { 
            padding: 15px; 
            text-decoration: none; 
            color: var(--dark-text);
            border-bottom: 1px solid #eee; 
        }
    </style>
</head>
<body>

    <header class="header-nav">
        <a href="schermata.php" class="logo-link">
            <img src="immagini/tastologo.png" alt="Debook Logo">
        </a>
        
        <div class="user-menu-container">
            <i class="fa-solid fa-circle-user" style="font-size: 2.2rem; cursor: pointer;" id="userBtn"></i>
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
            <p style="font-family: Arial; font-size: 1.1rem;">
                <span style="font-style: italic; font-weight: bold; display: block; margin-bottom: 10px;">
                    "Il sapere costa meno se è condiviso."
                </span>
                Entra a far parte della prima community scolastica dedicata al riuso dei libri.
            </p>
        </div>
        
        <div class="image-container">
            <img src="immagini/home.jpg" alt="Studenti e libri">
        </div>

        <div id="showBtn" class="footer-text">Scopri il progetto DeBook</div>

        <div id="extraContainer" style="display: none; width: 100%;">
            <section style="line-height: 1.6; color: #333; max-width: 750px; margin: 20px auto; padding: 30px; border-radius: 20px; background-color: var(--white); box-shadow: var(--shadow); text-align: left;">

                <h2 style="font-size: 1.6rem; text-align: center; margin-bottom: 20px; border-bottom: 3px solid var(--accent-beige); padding-bottom: 10px;">
                    Perché nasce <strong>DeBook</strong>?
                </h2>

                <p style="font-family: Arial; margin-bottom: 20px;">
                    L’idea di DeBook nasce per risolvere il <strong>paradosso dei libri scolastici</strong>: 
                    spese ingenti per le famiglie ogni anno, mentre migliaia di volumi in ottimo stato restano inutilizzati.
                </p>

                <div style="background: var(--bg-page); padding: 20px; border-left: 5px solid var(--accent-beige); margin-bottom: 25px;">
                    <h3 style="margin-top: 0; font-size: 1.1rem;">Dalla scuola, per la scuola</h3>
                    <p style="font-family: Arial; font-size: 0.95rem;">
                        DeBook trasforma l'istituto in una comunità collaborativa. Portiamo lo scambio online tra i corridoi scolastici.
                    </p>
                </div>

                <ul style="list-style: none; padding: 0; font-family: Arial;">
                    <li style="margin-bottom: 15px; display: flex; align-items: center;">
                        <span style="margin-right: 15px;">🌱</span>
                        <span><strong>Economia Circolare:</strong> Riduciamo l'impatto ambientale.</span>
                    </li>
                    <li style="margin-bottom: 15px; display: flex; align-items: center;">
                        <span style="margin-right: 15px;">💰</span>
                        <span><strong>Accessibilità:</strong> Abbattiamo i costi per le famiglie.</span>
                    </li>
                    <li style="margin-bottom: 15px; display: flex; align-items: center;">
                        <span style="margin-right: 15px;">🤝</span>
                        <span><strong>Sicurezza:</strong> Scambi garantiti tra compagni.</span>
                    </li>
                </ul>
            </section>
            
            <div id="hideBtn" class="footer-text" style="font-size: 0.9rem; padding: 10px 25px;">Vedi Meno</div>
        </div>
    </div>

    <script>
        // Gestione Menu Dropdown
        const btn = document.getElementById('userBtn');
        const menu = document.getElementById('userDropdown');
        
        btn.onclick = (e) => {
            menu.classList.toggle('active');
            e.stopPropagation();
        };

        window.onclick = () => menu.classList.remove('active');

        // Logica Espandi/Riduci
        const showBtn = document.getElementById('showBtn');
        const hideBtn = document.getElementById('hideBtn');
        const extraContainer = document.getElementById('extraContainer');

        showBtn.onclick = () => {
            showBtn.style.display = 'none';
            extraContainer.style.display = 'block';
            extraContainer.scrollIntoView({ behavior: 'smooth' });
        };

        hideBtn.onclick = () => {
            extraContainer.style.display = 'none';
            showBtn.style.display = 'inline-block';
            window.scrollTo({ top: 0, behavior: 'smooth' });
        };
    </script>
</body>
</html>