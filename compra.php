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
    <title>DEBOOK - Trova il tuo libro</title>
    <style>
        /* Stili generali sincronizzati con la pagina Vendi */
        body {
            font-family: Arial, sans-serif;
            background-color: #e6e6e6; /* Sfondo grigio chiaro uniforme */
            margin: 0;
            display: flex;
            justify-content: center;
            min-height: 100vh;
        }

        /* Contenitore bianco centrale */
        .container {
            background-color: #ffffff;
            width: 70%;
            max-width: 900px;
            padding: 40px;
            box-shadow: 0 0 15px rgba(0,0,0,0.05);
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* Stile Logo */
        .logo-link img {
            height: 70px;
            cursor: pointer;
            transition: transform 0.2s;
            margin-bottom: 20px;
        }
        
        .logo-link img:hover {
            transform: scale(1.05);
        }

        h1 {
            font-size: 36px;
            font-weight: 900;
            text-transform: uppercase;
            margin-bottom: 40px;
            text-align: center;
            color: #000;
        }

        /* Stile del form sincronizzato */
        form {
            width: 100%;
            max-width: 500px;
            display: flex;
            flex-direction: column;
            gap: 25px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        label {
            font-weight: bold;
            font-size: 18px;
            color: #000;
        }

        input[type="text"] {
            width: 100%;
            padding: 12px 15px;
            font-size: 16px;
            border: 2px solid #ccc;
            border-radius: 8px;
            box-sizing: border-box;
            outline: none;
            transition: border-color 0.3s;
        }

        input[type="text"]:focus {
            border-color: #000;
        }

        /* Bottone di invio nero come in Vendi */
        button {
            background-color: #000;
            color: #fff;
            font-size: 20px;
            font-weight: 900;
            padding: 15px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-transform: uppercase;
            margin-top: 20px;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #333;
        }

        .nota {
            font-size: 12px;
            color: #666;
            margin-top: -5px;
            line-height: 1.4;
        }
    </style>
</head>
<body>

    <div class="container">
        <a href="index.php" class="logo-link">
            <img src="immagini/tastologo.png" alt="Debook Logo">
        </a>

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

            <button type="submit">Cerca Libri Disponibili</button>
            
        </form>
    </div>
 
</body>
</html>