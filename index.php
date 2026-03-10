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
        /* TAVOLOZZA COLORI */
        :root {
            --bg-page: #e2e2e2;      /* Grigio chiaro sfondo */
            --accent-beige: #e9d5c3; /* Beige bottoni e banner */
            --white: #ffffff;
            --dark-text: #2c2e34;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Arial Black', sans-serif; }

        body { 
            background-color: var(--bg-page); 
            height: 100vh; 
            overflow: hidden; 
        }

        /* NAVBAR */
        .header-nav {
            position: absolute;
            top: 0; width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 30px;
            z-index: 1000;
        }

        .logo-link img { height: 60px; transition: 0.2s; }
        .user-icon { font-size: 2.2rem; color: var(--dark-text); cursor: pointer; }

        .dropdown-menu {
            position: absolute; right: 0; top: 50px;
            background: white; border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            display: none; flex-direction: column; min-width: 200px;
            overflow: hidden;
        }
        .dropdown-menu.active { display: flex; }
        .dropdown-menu a { 
            padding: 15px; text-decoration: none; color: var(--dark-text); 
            font-family: 'Arial', sans-serif; font-size: 0.9rem; border-bottom: 1px solid #eee; 
        }

        /* LAYOUT PRINCIPALE */
        .wrapper { display: flex; height: 100vh; width: 100%; }

        /* Colonne laterali (Vendi/Compra) trasformate in bottoni ovali nell'immagine */
        .side {
            width: 20%;
            display: flex;
            justify-content: center;
            align-items: center;
            text-decoration: none;
        }

        .btn-pill {
            background-color: var(--accent-beige);
            color: var(--dark-text);
            padding: 40px 30px;
            border-radius: 100px; /* Forma ovale perfetta */
            writing-mode: horizontal-tb;
            font-size: 2rem;
            transition: 0.3s;
            border: none;
            width: 80%;
            text-align: center;
        }
        .btn-pill:hover { transform: scale(1.05); background-color: #dfc4ab; }

        /* CONTENUTO CENTRALE */
        .center-content {
            width: 60%;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 40px;
            overflow-y: auto;
            scrollbar-width: none; /* Nasconde scrollbar Firefox */
        }
        .center-content::-webkit-scrollbar { display: none; } /* Nasconde scrollbar Chrome */

        /* Banner Titolo */
        .banner-header {
            background-color: var(--white);
            padding: 15px;
            border-radius: 60px;
            margin-bottom: 30px;
            width: 90%;
        }

        .banner-inner {
            background-color: var(--accent-beige);
            padding: 25px 20px;
            border-radius: 50px;
        }

        .banner-inner h1 {
            font-size: 2.2rem;
            color: var(--dark-text);
            line-height: 1.1;
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
            font-size: 1.05rem;
            line-height: 1.5;
            color: #444;
        }

        .description-strip .quote { font-style: italic; margin-bottom: 10px; display: block; }
        .description-strip .bold-sub { font-weight: bold; margin-top: 15px; display: block; }

        /* Immagine Libreria */
        .image-container img {
            max-width: 350px;
            height: auto;
            mix-blend-mode: multiply; /* Aiuta a fondere lo sfondo dell'immagine */
        }

        .footer-text {
            background-color: var(--accent-beige);
            padding: 8px 25px;
            border-radius: 20px;
            font-family: 'Arial', sans-serif;
            font-weight: bold;
            cursor: pointer;
            margin: 20px 0;
            display: inline-block;
        }

        /* Sezione extra */
        #extraContainer { width: 100%; padding-bottom: 50px; }
    </style>
</head>
<body>

    <header class="header-nav">
        <a href="index.php" class="logo-link">
            <img src="immagini/tastologo.png" alt="Debook Logo">
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
        <div class="side">
            <a href="Vendi.php" class="btn-pill">VENDI</a>
        </div>

        <div class="center-content" style="text-align: center;"> 
    
    <div class="banner-header" style="display: flex; justify-content: center; margin-bottom: 30px;">
        <div class="banner-inner" style="width: 100%; text-align: center;">
            <h1 style="margin: 0;">IL SAPERE NON HA PREZZO<br>MA QUI COSTA POCHISSIMO</h1>
        </div>
    </div>

    <div class="description-strip" style="text-align: center; width: 100%;">
        <span class="quote" style="display: block; font-style: italic; margin-bottom: 10px;">"Il sapere costa meno se è condiviso."</span>
        <p style="margin: 0 auto; max-width: 800px;">
            Perché pagare di più? Qui trovi libri usati venduti direttamente da altri studenti. 
            È il modo più intelligente per svuotare gli zaini e riempire le menti senza svuotare il portafoglio.
        </p>
        <span class="bold-sub" style="display: block; font-weight: bold; margin-top: 15px;">Prezzi da studente, per gli studenti.</span>
    </div>

    <div class="image-container" style="margin: 20px 0;">
        <img src="immagini/home.jpg" alt="Libreria" style="max-width: 350px; height: auto;">
    </div>

    <p id="showBtn" class="footer-text" style="display: inline-block; cursor: pointer;">Scopri di più</p>

    <div id="extraContainer" style="display: none; width: 100%;">
        <section style="font-family: 'Arial', sans-serif; line-height: 1.6; color: #333; max-width: 650px; margin: 20px auto; padding: 30px; text-align: center; background: white; border-radius: 20px;">
            <h2 style="color: #2c3e50; margin-bottom: 20px;">Perché nasce DeBook?</h2>
            <p>L’idea di DeBook nasce da un’osservazione semplice: <strong>il paradosso dei libri scolastici</strong>. Mentre molte famiglie affrontano spese ingenti ogni anno, migliaia di volumi restano inutilizzati.</p>
            <p style="margin-top:15px;">Scegliere DeBook significa credere in una scuola dove la <strong>collaborazione tra pari</strong> genera valore reale.</p>
        </section>
        <p id="hideBtn" class="footer-text" style="display: inline-block; cursor: pointer; margin-top: 15px;">Vedi meno</p>
    </div>
</div>

        <div class="side">
            <a href="compra.php" class="btn-pill">COMPRA</a>
        </div>
    </div>

    <script>
        // Gestione Dropdown
        const btn = document.getElementById('userBtn');
        const menu = document.getElementById('userDropdown');
        btn.onclick = (e) => {
            menu.classList.toggle('active');
            e.stopPropagation();
        };
        window.onclick = () => menu.classList.remove('active');

        // Gestione Espandi/Riduci
        const showBtn = document.getElementById('showBtn');
        const hideBtn = document.getElementById('hideBtn');
        const extraContainer = document.getElementById('extraContainer');

        showBtn.onclick = () => {
            showBtn.style.display = 'none';
            extraContainer.style.display = 'block';
        };

        hideBtn.onclick = () => {
            extraContainer.style.display = 'none';
            showBtn.style.display = 'inline-block';
            document.querySelector('.center-content').scrollTo({ top: 0, behavior: 'smooth' });
        };
    </script>
</body>
</html>