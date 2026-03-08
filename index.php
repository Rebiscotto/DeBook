Ecco il codice aggiornato per la tua index.php. Ho inserito il logo in alto a sinistra come richiesto, rendendolo un tasto cliccabile che ricarica la home page (index.php).

Ho anche pulito leggermente il CSS per assicurarmi che il logo non interferisca con il resto del layout.

File: index.php aggiornato
PHP
<?php
session_start();
// Controllo sessione per gestire l'omino e il menu
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

        /* Navbar Superiore (Logo a sinistra e Omino a destra) */
        .header-nav {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 40px;
            z-index: 1000;
        }

        /* Stile per il Logo cliccabile */
        .logo-home {
            height: 60px; /* Regola l'altezza in base alle tue preferenze */
            cursor: pointer;
            transition: opacity 0.2s;
        }

        .logo-home:hover {
            opacity: 0.8;
        }

        .user-menu-container { position: relative; }

        .user-icon {
            font-size: 2rem;
            color: var(--dark);
            cursor: pointer;
            transition: 0.3s;
        }

        .dropdown-menu {
            position: absolute;
            right: 0;
            top: 45px;
            background: white;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            display: none;
            flex-direction: column;
            min-width: 180px;
        }

        .dropdown-menu.active { display: flex; }

        .dropdown-menu a {
            padding: 12px;
            text-decoration: none;
            color: var(--dark);
            font-family: 'Arial', sans-serif;
            font-size: 0.9rem;
            border-bottom: 1px solid #eee;
        }

        .dropdown-menu a:hover { background: #f5f5f5; }

        /* Struttura a 3 colonne */
        .wrapper {
            display: flex;
            height: 100vh;
            width: 100%;
        }

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

        .side h2 {
            writing-mode: vertical-rl;
            transform: rotate(180deg);
            font-size: 3rem;
            color: var(--dark);
            letter-spacing: 4px;
        }

        .center-content {
            width: 70%;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 100px 40px 20px 40px; /* Padding superiore aumentato per far spazio al logo */
            text-align: center;
        }

        .hero-text h1 {
            font-size: 3.5rem;
            color: var(--dark);
            line-height: 1;
            margin-bottom: 15px;
            text-transform: uppercase;
        }

        .hero-text p {
            font-family: 'Arial', sans-serif;
            font-size: 0.85rem;
            color: #555;
            max-width: 500px;
            margin: 0 auto 20px auto;
        }

        /* Immagine centrale */
        .image-container {
            flex-grow: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
        }

        .image-container img {
            max-height: 50vh;
            width: auto;
            object-fit: contain;
        }

        .footer-text {
            font-family: 'Arial', sans-serif;
            font-weight: bold;
            text-decoration: underline;
            margin-top: 10px;
            cursor: pointer;
        }
    </style>
</head>
<body>

    <header class="header-nav">
        <a href="index.php">
            <img src="Screenshot 2026-03-08 215514.png" alt="Debook Logo" class="logo-home">
        </a>

        <div class="user-menu-container">
            <i class="fa-solid fa-circle-user user-icon" id="userBtn"></i>
            <div class="dropdown-menu" id="userDropdown">
                <?php if($is_logged): ?>
                    <a href="#"><strong>Profilo: <?php echo htmlspecialchars($nome_utente); ?></strong></a>
                    <a href="logout.php" style="color: red;">Logout</a>
                <?php else: ?>
                    <a href="login.php">Accedi</a>
                    <a href="register.php">Registrati</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <div class="wrapper">
        <a href="vendi.php" class="side">
            <h2>VENDI</h2>
        </a>

        <div class="center-content">
            <div class="hero-text">
                <h1>IL SAPERE NON HA PREZZO<br>MA QUI COSTA POCHISSIMO</h1>
                <p>
                    "Il sapere costa meno se è condiviso." Perché pagare di più? Qui trovi libri usati venduti direttamente da altri studenti. È il modo più intelligente per svuotare gli zaini e riempire le menti senza svuotare il portafoglio.
                </p>
            </div>

            <div class="image-container">
                <img src="ritaglio.png" alt="Studenti in biblioteca">
            </div>

            <p class="footer-text">Scopri di più</p>
        </div>

        <a href="compra.php" class="side">
            <h2>COMPRA</h2>
        </a>
    </div>

    <script>
        const btn = document.getElementById('userBtn');
        const menu = document.getElementById('userDropdown');

        btn.addEventListener('click', (e) => {
            menu.classList.toggle('active');
            e.stopPropagation();
        });

        // Chiude il menu se si clicca altrove
        window.onclick = () => menu.classList.remove('active');
    </script>
</body>
</html>