<?php
session_start();
require_once 'db_connection.php';

// Verifichiamo se l'utente è loggato per mostrare Accedi o Logout
$is_logged = isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true;
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debook - La tua libreria scolastica</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        body { background-color: var(--bg-page); }

        .hero {
            height: 70vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 0 20px;
        }

        .hero h1 { 
            font-family: 'Arial Black', sans-serif; 
            font-size: 4rem; 
            text-transform: uppercase; 
            margin-bottom: 10px;
            color: var(--dark-text);
        }

        .hero p { 
            font-size: 1.2rem; 
            color: #555; 
            margin-bottom: 40px; 
            max-width: 600px;
        }

        .cta-container { 
            display: flex; 
            gap: 20px; 
        }

        /* Bottoni grandi della Home */
        .btn-big {
            padding: 20px 45px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: bold;
            font-size: 1.2rem;
            transition: transform 0.3s, box-shadow 0.3s;
            font-family: 'Arial Black', sans-serif;
            text-transform: uppercase;
        }

        .btn-buy { 
            background-color: var(--dark-text); 
            color: white; 
        }

        .btn-sell { 
            background-color: var(--accent-beige); 
            color: var(--dark-text); 
        }
        
        .btn-big:hover { 
            transform: translateY(-5px); 
            box-shadow: 0 10px 20px rgba(0,0,0,0.1); 
        }

        @media (max-width: 600px) {
            .hero h1 { font-size: 2.5rem; }
            .cta-container { flex-direction: column; width: 100%; }
            .btn-big { text-align: center; }
        }
    </style>
</head>
<body>

    <header class="header-nav">
        <a href="index.php" class="logo-link">
            <img src="immagini/tastologo.png" alt="Debook Logo">
        </a>
        <div class="nav-right">
            <?php if($is_logged): ?>
                <a href="chat.php" style="font-size: 1.2rem; color: var(--dark-text); margin-right: 20px;">
                    <i class="fa-solid fa-comments"></i>
                </a>
                <a href="logout.php" style="color: #ff4d4d; text-decoration: none; font-weight: bold;">ESCI</a>
            <?php else: ?>
                <a href="login.php" style="text-decoration: none; color: var(--dark-text); font-weight: bold; margin-right: 20px;">ACCEDI</a>
                <a href="register.php" class="btn-submit" style="padding: 10px 20px; text-decoration: none;">REGISTRATI</a>
            <?php endif; ?>
        </div>
    </header>

    <section class="hero">
        <h1>Debook</h1>
        <p>Il modo più semplice e veloce per vendere e comprare i tuoi libri scolastici usati.</p>
        
        <div class="cta-container">
            <a href="compra.php" class="btn-big btn-buy">COMPRA</a>
            <a href="vendi.php" class="btn-big btn-sell">VENDI</a>
        </div>
    </section>

    <section style="padding: 60px 20px; background: white; text-align: center;">
        <h2 style="font-family: 'Arial Black'; margin-bottom: 40px;">COME FUNZIONA</h2>
        <div style="display: flex; justify-content: center; gap: 40px; flex-wrap: wrap; max-width: 1000px; margin: 0 auto;">
            <div style="flex: 1; min-width: 250px;">
                <i class="fa-solid fa-magnifying-glass" style="font-size: 2.5rem; color: var(--accent-beige);"></i>
                <h3 style="margin: 15px 0;">Cerca</h3>
                <p style="color: #777; font-family: Arial;">Trova il libro perfetto per la tua classe tramite titolo o ISBN.</p>
            </div>
            <div style="flex: 1; min-width: 250px;">
                <i class="fa-solid fa-comments" style="font-size: 2.5rem; color: var(--accent-beige);"></i>
                <h3 style="margin: 15px 0;">Contatta</h3>
                <p style="color: #777; font-family: Arial;">Mettiti d'accordo con il venditore tramite la chat integrata.</p>
            </div>
            <div style="flex: 1; min-width: 250px;">
                <i class="fa-solid fa-handshake" style="font-size: 2.5rem; color: var(--accent-beige);"></i>
                <h3 style="margin: 15px 0;">Scambia</h3>
                <p style="color: #777; font-family: Arial;">Incontratevi a scuola o in città per concludere l'affare.</p>
            </div>
        </div>
    </section>

</body>
</html>