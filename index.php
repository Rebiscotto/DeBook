<?php
session_start();
require_once 'db_connection.php';

// Verifichiamo se l'utente è loggato per personalizzare il messaggio
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
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header class="header-nav">
        <div class="nav-left">
            <a href="index.php" class="logo-link">
                <img src="immagini/tastologo.png" alt="Debook Logo">
            </a>
        </div>

        <div class="nav-right">
            <?php if($is_logged): ?>
                <a href="profilo.php" class="nav-item">
                    <i class="fa-solid fa-circle-user"></i> Ciao, <?php echo htmlspecialchars($nome_utente); ?>
                </a>
                
                <a href="chat.php" class="nav-item"><i class="fa-solid fa-comments"></i></a>
                
                <a href="logout.php" class="nav-item logout-icon">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </a>
            <?php else: ?>
                <a href="login.php" class="nav-item">Accedi</a>
                <a href="register.php" class="btn-submit" style="padding: 8px 15px; margin-left: 10px;">Registrati</a>
            <?php endif; ?>
        </div>
    </header>

    </body>
</html>