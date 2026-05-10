<?php
session_start();
$is_logged = isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true;

// Recupero dati dalla sessione
$nome_utente = $is_logged ? ($_SESSION["nome"] ?? "Utente") : "";
$cognome_utente = $is_logged ? ($_SESSION["cognome"] ?? "") : "";
$email_utente = $is_logged ? ($_SESSION["email"] ?? "") : "";
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debook - Home</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        /* Stili specifici solo per il layout split-screen della Home */
        body {
            height: 100vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .main-container {
            display: flex;
            flex: 1;
            width: 100%;
        }

        .side {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            transition: flex 0.5s ease;
        }

        .side:first-child { background-color: var(--bg-page); }
        .side:last-child { background-color: var(--accent-beige); }

        .side:hover { flex: 1.2; }

        .btn-pill {
            padding: 20px 60px;
            font-size: 2rem;
            font-family: 'Arial Black', sans-serif;
            text-decoration: none;
            color: var(--dark-text);
            border: 5px solid var(--dark-text);
            border-radius: 100px;
            transition: all 0.3s;
            background: transparent;
        }

        .btn-pill:hover {
            background-color: var(--dark-text);
            color: var(--white);
            transform: scale(1.1);
        }

        /* Dropdown personalizzato per la Home */
        .user-dropdown {
            position: absolute;
            top: 70px;
            right: 30px;
            background: var(--white);
            border-radius: 15px;
            box-shadow: var(--shadow);
            width: 250px;
            display: none;
            flex-direction: column;
            z-index: 2000;
            overflow: hidden;
        }

        .user-dropdown.active { display: flex; }

        .user-info-header {
            padding: 15px;
            background: #f8f8f8;
            border-bottom: 1px solid #eee;
            text-align: left;
        }

        .dropdown-links a {
            padding: 12px 15px;
            text-decoration: none;
            color: var(--dark-text);
            font-family: 'Arial', sans-serif;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: background 0.2s;
        }

        .dropdown-links a:hover { background-color: var(--accent-beige); }

        @media (max-width: 768px) {
            .main-container { flex-direction: column; }
            .btn-pill { font-size: 1.5rem; padding: 15px 40px; }
        }
    </style>
</head>
<body>

    <header class="header-nav">
        <a href="index.php" class="logo-link">
            <img src="immagini/tastologo.png" alt="Debook Logo">
        </a>

        <?php if ($is_logged): ?>
            <div style="position: relative;">
                <i class="fa-solid fa-circle-user" style="font-size: 2.5rem; cursor: pointer;" id="userBtn"></i>
                
                <div class="user-dropdown" id="userDropdown">
                    <div class="user-info-header">
                        <div style="font-weight: bold; font-family: Arial;"><?php echo htmlspecialchars($nome_utente . " " . $cognome_utente); ?></div>
                        <div style="font-size: 0.75rem; color: #777; font-family: Arial;"><?php echo htmlspecialchars($email_utente); ?></div>
                    </div>
                    <div class="dropdown-links">
                        <a href="dashboard.php"><i class="fa-solid fa-gauge"></i> Dashboard</a>
                        <a href="my_list.php"><i class="fa-solid fa-book"></i> I miei Libri in vendita</a>
                        <a href="miei_ordini.php"><i class="fa-solid fa-cart-shopping"></i> I miei Acquisti</a>
                        <a href="chat.php"><i class="fa-solid fa-comments"></i> Messaggi</a>
                        <hr style="border: 0; border-top: 1px solid #eee;">
                        <a href="logout.php" style="color: #d32f2f;"><i class="fa-solid fa-right-from-bracket"></i> Esci</a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <a href="schermata.php" class="btn-submit" style="font-size: 0.8rem; padding: 10px 20px;">ACCEDI</a>
        <?php endif; ?>
    </header>

    <div class="main-container">
        <div class="side">
            <a href="vendi.php" class="btn-pill">VENDI</a>
        </div>

        <div class="side">
            <a href="compra.php" class="btn-pill">COMPRA</a>
        </div>
    </div>

    <script>
        const userBtn = document.getElementById('userBtn');
        const userDropdown = document.getElementById('userDropdown');

        if (userBtn) {
            userBtn.onclick = (e) => {
                userDropdown.classList.toggle('active');
                e.stopPropagation();
            };
        }

        window.onclick = (event) => {
            if (userDropdown && userDropdown.classList.contains('active')) {
                if (!userDropdown.contains(event.target) && event.target !== userBtn) {
                    userDropdown.classList.remove('active');
                }
            }
        };
    </script>
</body>
</html>