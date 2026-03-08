
Ecco il codice aggiornato. Ho inserito l'immagine del logo (Screenshot 2026-03-08 215514.png) in alto a sinistra. Il logo è racchiuso in un tag <a> che punta a index.php, agendo quindi come un tasto che ricarica la home page.

File: index.php con Logo cliccabile
PHP
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

        /* Header che contiene Logo (sinistra) e Omino (destra) */
        .header-nav {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            display: flex;
            justify-content: space-between; /* Spinge gli elementi ai lati opposti */
            align-items: center;
            padding: 15px 30px;
            z-index: 1000;
        }

        /* Stile per il Logo cliccabile */
        .logo-link img {
            height: 70px; /* Dimensione del logo */
            cursor: pointer;
            transition: transform 0.2s ease;
        }

        .logo-link img:hover {
            transform: scale(1.05); /* Effetto ingrandimento al passaggio del mouse */
        }

        .user-menu-container { position: relative; }

        .user-icon {
            font-size: 2.2rem;
            color: var(--dark);
            cursor: pointer;
        }

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

        .side h2 {
            writing-mode: vertical-rl;
            transform: rotate(180deg);
            font-size: 3rem;
            color: var(--dark);
        }

        .center-content {
            width: 70%;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 110px 40px 20px 40px; /* Spazio per non coprire il contenuto col logo */
            text-align: center;
        }

        .hero-text h1 {
            font-size: 3.2rem;
            color: var(--dark);
            line-height: 1;
            margin-bottom: 15px;
        }

        .hero-text p {
            font-family: 'Arial', sans-serif;
            font-size: 0.9rem;
            color: #444;
            max-width: 550px;
            margin-bottom: 20px;
        }

        .image-container img {
            max-height: 45vh;
            width: auto;
            object-fit: contain;
        }

        .footer-text {
            font-family: 'Arial', sans-serif;
            font-weight: bold;
            text-decoration: underline;
            margin-top: 15px;
            cursor: pointer;
        }
    </style>
</head>
<body>

    <header class="header-nav">
        <a href="index.php" class="logo-link">
            <img src="Screenshot 2026-03-08 215514.png" alt="Debook Logo">
        </a>

        <div class="user-menu-container">
            <i class="fa-solid fa-circle-user user-icon" id="userBtn"></i>
            <div class="dropdown-menu" id="userDropdown">
                <?php if($is_logged): ?>
                    <a href="#">Profilo (<?php echo htmlspecialchars($nome_utente); ?>)</a>
                    <a href="logout.php" style="color: red;">Esci (Logout)</a>
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
                <p>
                    "Il sapere costa meno se è condiviso." Perché pagare di più? Qui trovi libri usati venduti direttamente da altri studenti. È il modo più intelligente per svuotare gli zaini e riempire le menti.
                </p>
            </div>

            <div class="image-container">
                <img src="ritaglio.png" alt="Studenti in biblioteca">
            </div>

            <p class="footer-text">Scopri di più</p>
        </div>

        <a href="compra.php" class="side"><h2>COMPRA</h2></a>
    </div>

    <script>
        const btn = document.getElementById('userBtn');
        const menu = document.getElementById('userDropdown');

        // Mostra/Nasconde la tendina al click sull'omino
        btn.addEventListener('click', (e) => {
            menu.classList.toggle('active');
            e.stopPropagation();
        });

        // Chiude la tendina cliccando ovunque fuori
        window.onclick = () => menu.classList.remove('active');
    </script>
</body>
</html>