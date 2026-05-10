<?php
session_start();
// Se l'utente NON è loggato, lo rimandiamo alla landing page (schermata.php)
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: schermata.php");
    exit;
}

// Recupero dati dalla sessione
$nome_utente = $_SESSION["nome"] ?? "Utente";
$cognome_utente = $_SESSION["cognome"] ?? "";
$email_utente = $_SESSION["email"] ?? "";
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debook - Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        /* CSS specifico per il layout centrato della Index */
        body {
            overflow-x: hidden;
            background-color: var(--bg-page);
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

        /* Banner Titolo (Stile Schermata.php) */
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
            font-size: clamp(1.2rem, 5vw, 2.2rem);
            color: var(--dark-text);
            text-transform: uppercase;
            margin: 0;
        }

        /* Sezione Azioni (I tasti Vendi e Compra) */
        .action-container {
            display: flex;
            gap: 20px;
            margin-top: 30px;
            width: 100%;
            justify-content: center;
        }

        .btn-action {
            flex: 1;
            max-width: 250px;
            background-color: var(--white);
            border: 5px solid var(--dark-text);
            color: var(--dark-text);
            padding: 25px 10px;
            border-radius: 30px;
            font-family: 'Arial Black', sans-serif;
            font-size: 1.5rem;
            text-decoration: none;
            transition: all 0.3s;
            box-shadow: var(--shadow);
        }

        .btn-action:hover {
            background-color: var(--dark-text);
            color: var(--white);
            transform: scale(1.05);
        }

        .btn-action i {
            display: block;
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        /* Menu Dropdown */
        .user-dropdown {
            position: absolute;
            top: 70px;
            right: 30px;
            background: white;
            border-radius: 15px;
            box-shadow: var(--shadow);
            width: 260px;
            display: none;
            flex-direction: column;
            z-index: 2000;
            overflow: hidden;
            text-align: left;
        }

        .user-dropdown.active { display: flex; }

        .dropdown-links a {
            padding: 15px;
            text-decoration: none;
            color: var(--dark-text);
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
            gap: 12px;
            font-family: Arial, sans-serif;
        }

        .dropdown-links a:hover { background-color: var(--accent-beige); }

        @media (max-width: 600px) {
            .action-container { flex-direction: column; align-items: center; }
            .btn-action { width: 100%; }
        }
    </style>
</head>
<body>

    <header class="header-nav">
        <a href="index.php" class="logo-link">
            <img src="immagini/tastologo.png" alt="Debook Logo">
        </a>
        
        <div style="position: relative;">
            <i class="fa-solid fa-circle-user" style="font-size: 2.5rem; cursor: pointer;" id="userBtn"></i>
            <div class="user-dropdown" id="userDropdown">
                <div style="padding: 15px; background: #f8f8f8; border-bottom: 1px solid #eee;">
                    <div style="font-weight: bold;"><?php echo htmlspecialchars($nome_utente . " " . $cognome_utente); ?></div>
                    <div style="font-size: 0.8rem; color: #777;"><?php echo htmlspecialchars($email_utente); ?></div>
                </div>
                <div class="dropdown-links">
                    <a href="dashboard.php"><i class="fa-solid fa-gauge-high"></i> Dashboard</a>
                    <a href="my_list.php"><i class="fa-solid fa-book"></i> I miei Libri</a>
                    <a href="chat.php"><i class="fa-solid fa-comments"></i> Messaggi</a>
                    <a href="logout.php" style="color: #d32f2f;"><i class="fa-solid fa-right-from-bracket"></i> Esci</a>
                </div>
            </div>
        </div>
    </header>

    <div class="center-content">
        
        <div class="banner-header">
            <div class="banner-inner">
                <h1>IL SAPERE NON HA PREZZO<br>MA QUI COSTA POCHISSIMO</h1>
            </div>
        </div>

        <h2 style="margin: 20px 0; color: #555;">Benvenuto, <?php echo htmlspecialchars($nome_utente); ?>! Cosa vuoi fare oggi?</h2>

        <div class="action-container">
            <a href="vendi.php" class="btn-action">
                <i class="fa-solid fa-plus-circle"></i>
                VENDI
            </a>
            <a href="compra.php" class="btn-action">
                <i class="fa-solid fa-search"></i>
                COMPRA
            </a>
        </div>

        <div style="margin-top: 40px; width: 100%; max-width: 600px;">
            <p style="font-family: Arial; line-height: 1.5; color: #666;">
                Gestisci i tuoi annunci, parla con gli acquirenti e tieni traccia dei tuoi scambi scolastici direttamente dalla tua area personale.
            </p>
            <a href="dashboard.php" class="btn-submit" style="margin-top: 20px; text-decoration: none;">VAI ALLA DASHBOARD</a>
        </div>

    </div>

    <script>
        const userBtn = document.getElementById('userBtn');
        const userDropdown = document.getElementById('userDropdown');

        userBtn.onclick = (e) => {
            userDropdown.classList.toggle('active');
            e.stopPropagation();
        };

        window.onclick = () => {
            if (userDropdown.classList.contains('active')) {
                userDropdown.classList.remove('active');
            }
        };
    </script>
</body>
</html>