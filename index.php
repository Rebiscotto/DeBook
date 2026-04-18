<?php
session_start();
$is_logged = isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true;

// Recupero dati dalla sessione - Usiamo l'operatore null coalescing per evitare errori
$nome_utente = $is_logged ? ($_SESSION["nome"] ?? "N/D") : "";
$cognome_utente = $is_logged ? ($_SESSION["cognome"] ?? "Dato non disponibile") : "";
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
        :root {
            --bg-page: #e2e2e2;
            --accent-beige: #e9d5c3;
            --white: #ffffff;
            --dark-text: #2c2e34;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Arial Black', sans-serif; }
        body { background-color: var(--bg-page); height: 100vh; overflow: hidden; }

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

        .user-info-box {
            padding: 20px;
            background-color: #fcfaf8;
            border-bottom: 2px solid var(--accent-beige);
            text-align: left;
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
            font-family: 'Arial', sans-serif; /* Più leggibile per i dati */
        }

        .dropdown-menu a { 
            padding: 15px 20px; 
            text-decoration: none; 
            color: var(--dark-text); 
            font-family: 'Arial', sans-serif; 
            font-size: 0.95rem; 
            border-bottom: 1px solid #eee;
        }

        .dropdown-menu a:hover { background-color: #f9f9f9; }

        .wrapper { display: flex; height: 100vh; width: 100%; }
        .side { width: 20%; display: flex; justify-content: center; align-items: center; text-decoration: none; }
        .btn-pill {
            background-color: var(--accent-beige);
            color: var(--dark-text);
            padding: 40px 30px;
            border-radius: 100px;
            font-size: 2rem;
            border: none;
            width: 80%;
            text-align: center;
            cursor: pointer;
            text-decoration: none;
        }

        .center-content {
            width: 60%;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 80px;
            overflow-y: auto;
            scrollbar-width: none;
        }
        .center-content::-webkit-scrollbar { display: none; }

        .banner-header { background-color: var(--white); padding: 15px; border-radius: 60px; margin-bottom: 30px; width: 90%; }
        .banner-inner { background-color: var(--accent-beige); padding: 25px 20px; border-radius: 50px; }
        .banner-inner h1 { font-size: 2.2rem; color: var(--dark-text); line-height: 1.1; }

        .description-strip { background-color: var(--accent-beige); width: 100%; padding: 30px 10%; margin-bottom: 30px; }
        .image-container img { max-width: 350px; height: auto; mix-blend-mode: multiply; }
        .footer-text { background-color: var(--accent-beige); padding: 8px 25px; border-radius: 20px; font-family: 'Arial', sans-serif; font-weight: bold; cursor: pointer; margin: 20px 0; display: inline-block; }
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
                        <p>Cognome: <strong><?php echo htmlspecialchars($cognome_utente); ?></strong></p>
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
        <div class="side"><a href="Vendi.php" class="btn-pill">VENDI</a></div>

        <div class="center-content" style="text-align: center;"> 
            <div class="banner-header">
                <div class="banner-inner">
                    <h1>IL SAPERE NON HA PREZZO<br>MA QUI COSTA POCHISSIMO</h1>
                </div>
            </div>

            <div class="description-strip">
                <p>"Il sapere costa meno se è condiviso."</p>
                <p>Scegli DeBook per un'economia circolare tra studenti.</p>
            </div>

            <div class="image-container">
                <img src="immagini/home2.png" alt="Libreria">
            </div>

            <p id="showBtn" class="footer-text">Scopri il progetto DeBook</p>
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

        <div class="side"><a href="compra.php" class="btn-pill">COMPRA</a></div>
    </div>

    <script>
        const userBtn = document.getElementById('userBtn');
        const userDropdown = document.getElementById('userDropdown');

        userBtn.onclick = (e) => {
            userDropdown.classList.toggle('active');
            e.stopPropagation();
        };

        window.onclick = (event) => {
            if (userDropdown.classList.contains('active')) {
                if (!userDropdown.contains(event.target) && event.target !== userBtn) {
                    userDropdown.classList.remove('active');
                }
            }
        };

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
        };
    </script>
</body>
</html>