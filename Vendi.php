
        <?php
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DEBOOK - Vendi il tuo libro</title>
    <style>
        /* Stili generali per riprendere il layout della tua homepage */
        body {
            font-family: Arial, sans-serif;
            background-color: #e6e6e6; /* Sfondo grigio chiaro */
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

        /* Header semplice con logo */
        .header {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 50px;
        }

        .logo {
            font-size: 28px;
            font-weight: 900;
            letter-spacing: 1px;
        }

        h1 {
            font-size: 36px;
            font-weight: 900;
            text-transform: uppercase;
            margin-bottom: 40px;
            text-align: center;
        }

        /* Stile del form */
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
        }

        input[type="text"], select {
            width: 100%;
            padding: 12px 15px;
            font-size: 16px;
            border: 2px solid #ccc;
            border-radius: 8px;
            box-sizing: border-box;
            outline: none;
            transition: border-color 0.3s;
        }

        input[type="text"]:focus, select:focus {
            border-color: #000;
        }

        /* Bottone di invio */
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
        }
    </style>
</head>
<body>

    <div class="container">
        <a href="index.php" class="logo-link">
            <img src="immagini/tastologo.png" alt="Debook Logo">
        </a>

        <h1>Inserisci i dati di vendita</h1>

        <form action="#" method="POST">
            
            <div class="form-group">
                <label for="materia">Materia del Libro</label>
                <input type="text" id="materia" name="materia" placeholder="Es. Matematica, Diritto Privato, Storia..." required>
            </div>

            <div class="form-group">
                <label for="condizioni">Condizioni del Libro</label>
                <select id="condizioni" name="condizioni" required>
                    <option value="" disabled selected>Seleziona lo stato d'usura...</option>
                    <option value="nuovo">Come Nuovo (Nessun segno, mai aperto)</option>
                    <option value="ottimo">Ottimo (Pochissimi segni, niente scritte)</option>
                    <option value="buono">Buono (Usato, qualche sottolineatura a matita)</option>
                    <option value="accettabile">Accettabile (Evidenti segni d'uso, evidenziato)</option>
                </select>
            </div>

            <div class="form-group">
                <label for="isbn">Codice ISBN</label>
                <input type="text" id="isbn" name="isbn" placeholder="" 
                       pattern="[A-Z]{2}[0-9]{2}[a-zA-Z0-9]{1,30}" 
                       title="Inserisci un codice ISBN valido " required>
                <span class="nota">L'ISBN è il codice unico del libro, si trova sul retro del libro o all'interno della copertina</span>
            </div>

            <button type="submit">Metti in Vendita</button>
            
        </form>
    </div>
 
</body>
</html>

