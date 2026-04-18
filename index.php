<?php
session_start();
$is_logged = isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true;

// Recupero dati dalla sessione
$nome_utente = $is_logged ? $_SESSION["nome"] : "";
$nome_utente = $is_logged ? $_SESSION["cognome"] : "";
$email_utente = $is_logged ? ($_SESSION["email"] ?? "Non disponibile") : "";
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
            --bg-page: #e2e2e2;
            --accent-beige: #e9d5c3;
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

        /* DROPDOWN MENU */
        .user-menu-container { position: relative; }

        .dropdown-menu {
            position: absolute; 
            right: 0; 
            top: 55px;
            background: white; 
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            display: none; 
            flex-direction: column; 
            min-width: 260px;
            overflow: hidden;
            z-index: 1001;
        }
        
        .dropdown-menu.active { display: flex; }

        /* Box dati utente dentro la tendina */
        .user-info-box {
            padding: 20px;
            background-color: #fcfaf8;
            border-bottom: 2px solid var(--accent-beige);
        }

        .user-info-box p {
            font-family: 'Arial', sans-serif;
            font-size: 0.85rem;
            color: #777;
            margin-bottom: 8px;
            text-transform: none;
        }

        .user-info-box strong {
            color: var(--dark-text);
            display: block;
            font-size: 1rem;
            margin-top: 2px;
        }

        .dropdown-menu a { 
            padding: 15px 20px; 
            text-decoration: none; 
            color: var(--dark-text); 
            font-family: 'Arial', sans-serif; 
            font-size: 0.95rem; 
            border-bottom: 1px solid #eee;
            transition: background 0.2s;
        }

        .dropdown-menu a:hover { background-color: #f9f9f9; }
        .dropdown-menu a i { margin-right: 10px; width: 20px; text-align: center; }

        /* LAYOUT PRINCIPALE */
        .wrapper { display: flex; height: 100vh; width: 100%; }

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
            border-radius: 100px;
            font-size: 2rem;
            transition: 0.3s;
            border: none;
            width: 80%;
            text-align: center;
            cursor: pointer;
        }
        .btn-pill:hover { transform: scale(1.05); background-color: #dfc4ab; }

        .center-content {
            width: 60%;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 80px; /* Aumentato per non coprire col logo */
            overflow-y: auto;
            scrollbar-width: none;
        }
        .center-content::-webkit-scrollbar { display: none; }

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

        .image-container img {
            max-width: 350px;
            height: auto;
            mix-blend-mode: multiply;
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
                    <div class="user-info-box">
                        <p>Nome: <strong><?php echo htmlspecialchars($nome_utente); ?></strong></p>
                        <p>Cognome: <strong><?php echo htmlspecialchars($cognome_utente); ?></strong> </p>
                        <p>Email: <strong><?php echo htmlspecialchars($email_utente); ?></strong></p>
                    </div>
                    
                    <a href="#"><i class="fa-solid fa-book"></i> I miei libri</a>
                    <a href="logout.php" style="color: #d9534f;"><i class="fa-solid fa-right-from-bracket"></i> Esci</a>
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
                <span class="quote" style="display: block; font-style: italic; margin-bottom: 10px; font-family: 'Arial', sans-serif;">"Il sapere costa meno se è condiviso."</span>
                <p style="margin: 0 auto; max-width: 800px;">
                    Perché pagare di più? Qui trovi libri usati venduti direttamente da altri studenti. 
                    È il modo più intelligente per svuotare gli zaini e riempire le menti senza svuotare il portafoglio.
                </p>
                <span class="bold-sub" style="display: block; font-weight: bold; margin-top: 15px; font-family: 'Arial', sans-serif;">Prezzi da studente, per gli studenti.</span>
            </div>

            <div class="image-container" style="margin: 20px 0;">
                <img src="immagini/home2.png" alt="Libreria">
            </div>

            <p id="showBtn" class="footer-text">Scopri di più</p>

            <div id="extraContainer" style="display: none;">
                <section style="font-family: 'Segoe UI', Arial, sans-serif; line-height: 1.6; color: #333; max-width: 650px; margin: 20px auto; padding: 20px; border-radius: 12px; background-color: #f9f9f9; border: 1px solid #eee; text-align: left;">
                    <h2 style="color: #2c3e50; font-size: 1.5rem; text-align: center; margin-bottom: 20px; border-bottom: 2px solid var(--accent-beige); padding-bottom: 10px;">
                        Perché nasce <strong>DeBook</strong>?
                    </h2>
                    <p style="margin-bottom: 20px;">
                        L’idea di DeBook nasce da un’osservazione semplice: il paradosso dei libri scolastici. 
                        Migliaia di volumi in ottime condizioni restano inutilizzati, mentre le famiglie affrontano spese ingenti ogni anno.
                    </p>
                    <div style="background: #fff; padding: 15px; border-left: 4px solid var(--accent-beige); margin-bottom: 25px;">
                        <h3 style="margin-top: 0; color: #2c2e34; font-size: 1.1rem;">Oltre il prestito, verso la comunità</h3>
                        <p style="font-size: 0.95rem;">Perché non portare l'efficienza tra i banchi di scuola? Scambi a mano, trasparenza e volti noti.</p>
                    </div>
                </section>
                <p id="hideBtn" class="footer-text">Vedi meno</p>
            </div>
        </div>

        <div class="side">
            <a href="compra.php" class="btn-pill">COMPRA</a>
        </div>
    </div>

    <script>
        // Gestione Dropdown Utente
        const userBtn = document.getElementById('userBtn');
        const userDropdown = document.getElementById('userDropdown');

        userBtn.onclick = (e) => {
            userDropdown.classList.toggle('active');
            e.stopPropagation(); // Evita che il click chiuda subito il menu
        };

        // Chiudi la tendina cliccando ovunque fuori
        window.onclick = (event) => {
            if (userDropdown.classList.contains('active')) {
                // Se il click NON è sul bottone e NON è dentro la tendina
                if (!userDropdown.contains(event.target) && event.target !== userBtn) {
                    userDropdown.classList.remove('active');
                }
            }
        };

        // Gestione Espandi/Riduci Contenuto
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