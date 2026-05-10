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
        /* Stili specifici per l'effetto split della Home */
        body { height: 100vh; overflow: hidden; display: flex; flex-direction: column; }
        .main-container { display: flex; flex: 1; width: 100%; }
        .side { flex: 1; display: flex; justify-content: center; align-items: center; transition: flex 0.5s ease; position: relative; }
        .side:first-child { background-color: var(--bg-page); }
        .side:last-child { background-color: var(--accent-beige); }
        .side:hover { flex: 1.3; }
        .btn-pill { 
            padding: 20px 60px; font-size: 2.5rem; font-family: 'Arial Black', sans-serif; 
            text-decoration: none; color: var(--dark-text); border: 6px solid var(--dark-text); 
            border-radius: 100px; transition: 0.3s; background: transparent; z-index: 10;
        }
        .btn-pill:hover { background-color: var(--dark-text); color: var(--white); transform: scale(1.1); }

        .user-dropdown { position: absolute; top: 70px; right: 30px; background: white; border-radius: 15px; box-shadow: var(--shadow); width: 250px; display: none; flex-direction: column; z-index: 2000; overflow: hidden; }
        .user-dropdown.active { display: flex; }
        .dropdown-links a { padding: 12px 15px; text-decoration: none; color: var(--dark-text); display: flex; align-items: center; gap: 10px; font-family: Arial; }
        .dropdown-links a:hover { background-color: var(--accent-beige); }

        @media (max-width: 768px) {
            .main-container { flex-direction: column; }
            .btn-pill { font-size: 1.8rem; padding: 15px 40px; }
        }
    </style>
</head>
<body>

    <header class="header-nav">
        <a href="index.php" class="logo-link"><img src="immagini/tastologo.png" alt="Debook Logo"></a>
        <?php if ($is_logged): ?>
            <div style="position: relative;">
                <i class="fa-solid fa-circle-user" style="font-size: 2.5rem; cursor: pointer;" id="userBtn"></i>
                <div class="user-dropdown" id="userDropdown">
                    <div style="padding: 15px; background: #f8f8f8; border-bottom: 1px solid #eee;">
                        <strong><?php echo htmlspecialchars($nome_utente); ?></strong>
                    </div>
                    <div class="dropdown-links">
                        <a href="dashboard.php"><i class="fa-solid fa-gauge"></i> Dashboard</a>
                        <a href="my_list.php"><i class="fa-solid fa-book"></i> I miei Libri</a>
                        <a href="chat.php"><i class="fa-solid fa-comments"></i> Messaggi</a>
                        <a href="logout.php" style="color: #d32f2f;"><i class="fa-solid fa-right-from-bracket"></i> Esci</a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <a href="schermata.php" class="btn-submit">ACCEDI</a>
        <?php endif; ?>
    </header>

    <div class="main-container">
        <div class="side"><a href="vendi.php" class="btn-pill">VENDI</a></div>
        <div class="side"><a href="compra.php" class="btn-pill">COMPRA</a></div>
    </div>

    <script>
        const userBtn = document.getElementById('userBtn');
        const userDropdown = document.getElementById('userDropdown');
        if(userBtn) {
            userBtn.onclick = (e) => {
                userDropdown.classList.toggle('active');
                e.stopPropagation();
            };
        }
        window.onclick = () => { if(userDropdown) userDropdown.classList.remove('active'); };
    </script>
</body>
</html>