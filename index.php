<?php
session_start();
// Controllo se l'utente è loggato
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
            --light-grey: #f4f4f9;
            --grey-side: #d9d9d9;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', sans-serif; }

        body { background-color: #fff; overflow-x: hidden; }

        /* Navbar con Omino e Menu */
        .navbar {
            display: flex;
            justify-content: flex-end;
            padding: 20px 40px;
            position: absolute;
            width: 100%;
            z-index: 100;
        }

        .user-menu-container { position: relative; }

        .user-icon {
            font-size: 1.8rem;
            color: var(--dark);
            cursor: pointer;
            transition: transform 0.2s;
        }

        .user-icon:hover { transform: scale(1.1); }

        /* Menu a Tendina */
        .dropdown-menu {
            position: absolute;
            right: 0;
            top: 40px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            min-width: 180px;
            display: none; /* Nascosto di default */
            flex-direction: column;
            overflow: hidden;
        }

        .dropdown-menu.active { display: flex; }

        .dropdown-menu a {
            padding: 12px 20px;
            text-decoration: none;
            color: var(--dark);
            font-size: 0.9rem;
            border-bottom: 1px solid #eee;
        }

        .dropdown-menu a:hover { background-color: var(--light-grey); }

        /* Layout Principale */
        .main-container {
            display: flex;
            height: 100vh;
            width: 100%;
        }

        /* Colonne Laterali Vendi/Compra */
        .side-panel {
            width: 15%;
            background-color: var(--grey-side);
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            transition: background 0.3s;
            text-decoration: none;
        }

        .side-panel:hover { background-color: #cccccc; }

        .side-panel h2 {
            writing-mode: vertical-rl;
            text-orientation: mixed;
            transform: rotate(180deg);
            font-size: 2.5rem;
            color: var(--dark);
            letter-spacing: 5px;
            font-weight: 800;
        }

        /* Area Centrale */
        .content {
            width: 70%;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 40px;
            text-align: center;
        }

        .header-section h1 {
            font-size: 3rem;
            font-weight: 900;
            line-height: 1.1;
            margin-bottom: 20px;
            letter-spacing: -1px;
        }

        .description {
            max-width: 600px;
            color: #666;
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .hero-image {
            width: 100%;
            max-width: 500px;
            margin-top: auto;
        }

        .hero-image img { width: 100%; height: auto; }

        .footer-link {
            margin-top: 20px;
            text-decoration: underline;
            color: var(--dark);
            font-weight: bold;
            cursor: pointer;
        }
    </style>
</head>
<body>

    <nav class="navbar">
        <div class="user-menu-container">
            <i class="fa-solid fa-circle-user user-icon" id="userBtn"></i>
            <div class="dropdown-menu" id="userDropdown">
                <?php if($is_logged): ?>
                    <a href="#"><i class="fa-solid fa-user"></i> Profilo di <?php echo htmlspecialchars($nome_utente); ?></a>
                    <a href="#"><i class="fa-solid fa-book"></i> I miei libri</a>
                    <a href="logout.php" style="color: #d9534f;"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
                <?php else: ?>
                    <a href="login.php">Accedi</a>
                    <a href="register.php">Registrati</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="main-container">
        <a href="vendi.php" class="side-panel">
            <h2>VENDI</h2>
        </a>

        <div class="content">
            <div class="header-section">
                <h1>IL SAPERE NON HA PREZZO<br>MA QUI COSTA POCHISSIMO</h1>
                <p class="description">
                    "Il sapere costa meno se è condiviso." Perché pagare di più? Qui trovi libri usati venduti direttamente da altri studenti. È il modo più intelligente per svuotare gli zaini e riempire le menti senza svuotare il portafoglio.<br>
                    <strong>Prezzi da studente, per gli studenti.</strong>
                </p>
            </div>

            <div class="hero-image">
                <img src="https://img.freepik.com/free-vector/young-people-library_23-2148530368.jpg" alt="Studenti in libreria">
            </div>

            <p id="showBtn" class="footer-text">Scopri di più</p>

    <div id="hideBtn">
        <p style="font-family: 'Arial', sans-serif; font-size: 0.9rem; color: #444; max-width: 550px; margin: 0 auto;">
            [Qui inserirai il tuo testo lungo successivamente...]
        </p>
        <p id="extraText" class="footer-text" style="margin-top: 15px;">Vedi meno</p>
    </div>
        </div>

        <a href="compra.php" class="side-panel">
            <h2>COMPRA</h2>
        </a>
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

// --- LOGICA ESPANDI/RIDUCI ---
const showBtn = document.getElementById('showBtn');
const hideBtn = document.getElementById('hideBtn');
const extraText = document.getElementById('extraText');

showBtn.onclick = () => {
    extraText.classList.add('active'); // Espande il testo
    showBtn.style.display = 'none';    // Nasconde "Scopri di più"
};

hideBtn.onclick = () => {
    extraText.classList.remove('active'); // Riduce il testo
    showBtn.style.display = 'block';      // Mostra di nuovo "Scopri di più"
    // Torna su dolcemente all'inizio del contenuto
    document.querySelector('.center-content').scrollTo({ top: 0, behavior: 'smooth' });
};
    </script>
</body>
</html>