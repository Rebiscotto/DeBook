<?php
session_start();
$is_logged = isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true;
$nome_utente = $is_logged ? $_SESSION["nome"] : "";

// Scommenta queste righe se vuoi che solo gli utenti registrati possano comprare
/*
if(!$is_logged) {
    header("Location: login.php");
    exit;
}
*/
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debook - Compra un libro</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --dark: #1a1a1a;
            --buy-color: #2980b9; /* Un tocco di blu per differenziare l'acquisto dalla vendita */
        }

        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Arial Black', 'Arial', sans-serif; }

        body { 
            background-color: #f4f4f4; 
            min-height: 100vh; 
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* --- STILI HEADER (Identici alla Home e a Vendi) --- */
        .header-nav {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 30px;
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            z-index: 1000;
        }

        .logo-link img { height: 70px; cursor: pointer; transition: transform 0.2s; }
        .logo-link img:hover { transform: scale(1.05); }

        .user-menu-container { position: relative; }
        .user-icon { font-size: 2.2rem; color: var(--dark); cursor: pointer; }

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
        .dropdown-menu a { padding: 12px; text-decoration: none; color: var(--dark); font-family: 'Arial', sans-serif; font-size: 0.9rem; border-bottom: 1px solid #eee; }

        /* --- STILI FORM DI ACQUISTO --- */
        .form-container {
            background-color: #ffffff;
            width: 90%;
            max-width: 600px;
            margin-top: 50px;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }

        h1 {
            font-size: 28px;
            text-transform: uppercase;
            text-align: center;
            margin-bottom: 30px;
            color: var(--dark);
        }

        form { display: flex; flex-direction: column; gap: 20px; }

        .form-group { display: flex; flex-direction: column; gap: 8px; }

        label { font-family: 'Arial', sans-serif; font-weight: bold; font-size: 16px; color: #333; }

        input[type="text"] {
            width: 100%;
            padding: 12px;
            font-family: 'Arial', sans-serif;
            font-size: 15px;
            border: 2px solid #ddd;
            border-radius: 6px;
            outline: none;
            transition: border-color 0.3s;
        }

        input[type="text"]:focus { border-color: var(--buy-color); }

        button {
            background-color: var(--dark);
            color: #fff;
            font-size: 18px;
            padding: 15px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-transform: uppercase;
            margin-top: 10px;
            transition: background-color 0.3s;
        }

        button:hover { background-color: var(--buy-color); }

        .nota { font-family: 'Arial', sans-serif; font-size: 12px; color: #777; margin-top: -3px; }
    </style>
</head>
<body>

    <header class="header-nav">
        <a href="index.php" class="logo-link">
            <img src="immagini/tastologo.png" alt="Debook Logo">
        </a>
       
    </header>

    <div class="form-container">
        <h1>Trova il tuo libro</h1>

        <form action="#" method="GET">
            
            <div class="form-group">
                <label for="materia">Quale materia ti serve?</label>
                <input type="text" id="materia" name="materia" placeholder="Es. Fisica, Filosofia, Latino..." required>
            </div>

            <div class="form-group">
                <label for="classe">Classe e Sezione</label>
                <input type="text" id="classe" name="classe" placeholder="Es. 3A, 4B Informatica, 5C..." required>
                <span class="nota">Ci aiuta a trovare l'edizione esatta adottata dai tuoi professori.</span>
            </div>

            <div class="form-group">
                <label for="indirizzo">Indirizzo di Consegna / Incontro</label>
                <input type="text" id="indirizzo" name="indirizzo" placeholder="Es. Via Roma 10, Milano (MI)" required>
                <span class="nota">Inserisci la via per lo scambio a mano o per l'eventuale spedizione.</span>
            </div>

            <button type="submit">Cerca Libri Disponibili</button>
            
        </form>
    </div>

    <script>
        // Gestione Menu Omino (Uguale alle altre pagine)
        const btn = document.getElementById('userBtn');
        const menu = document.getElementById('userDropdown');
        btn.onclick = (e) => {
            menu.classList.toggle('active');
            e.stopPropagation();
        };
        window.onclick = () => menu.classList.remove('active');
    </script>
</body>
</html>