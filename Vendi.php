<?php
session_start();
if (!isset($_SESSION["loggedin"])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debook - Vendi Libro</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: var(--bg-page);
        }

        .sell-container {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
        }

        .sell-card {
            background-color: var(--white);
            padding: 40px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            width: 100%;
            max-width: 600px;
            text-align: center;
        }

        h1 {
            font-family: 'Arial Black', sans-serif;
            font-size: 1.5rem;
            margin-bottom: 25px;
            text-transform: uppercase;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            text-align: left;
        }

        .full-width { grid-column: span 2; }

        .input-group { margin-bottom: 15px; }
        
        .input-group label {
            font-family: 'Arial', sans-serif;
            font-size: 0.8rem;
            font-weight: bold;
            color: #777;
            display: block;
            margin-bottom: 5px;
            margin-left: 5px;
        }

        /* Stile file upload */
        input[type="file"] {
            background: #f8f8f8;
            border: 2px dashed var(--accent-beige);
            padding: 10px;
            cursor: pointer;
        }

        @media (max-width: 600px) {
            .form-grid { grid-template-columns: 1fr; }
            .full-width { grid-column: span 1; }
        }
    </style>
</head>
<body>

    <header class="header-nav">
        <a href="index.php" class="logo-link"><img src="immagini/tastologo.png" alt="Debook Logo"></a>
        <a href="dashboard.php" style="font-family: Arial; text-decoration: none; color: var(--dark-text);">Dashboard</a>
    </header>

    <div class="sell-container">
        <div class="sell-card">
            <h1>Metti in vendita</h1>
            
            <form action="vendi_controller.php" method="POST" enctype="multipart/form-data">
                <div class="form-grid">
                    
                    <div class="input-group full-width">
                        <label>TITOLO DEL LIBRO</label>
                        <input type="text" name="titolo" placeholder="Es. I Promessi Sposi" required>
                    </div>

                    <div class="input-group">
                        <label>AUTORE</label>
                        <input type="text" name="autore" placeholder="Es. Alessandro Manzoni" required>
                    </div>

                    <div class="input-group">
                        <label>MATERIA</label>
                        <input type="text" name="materia" placeholder="Es. Italiano" required>
                    </div>

                    <div class="input-group">
                        <label>ISBN (13 cifre)</label>
                        <input type="text" name="isbn" maxlength="13" placeholder="978...">
                    </div>

                    <div class="input-group">
                        <label>PREZZO (€)</label>
                        <input type="number" name="prezzo" step="0.01" min="0" placeholder="0.00" required>
                    </div>

                    <div class="input-group full-width">
                        <label>FOTO DEL LIBRO (Mostra lo stato d'usura)</label>
                        <input type="file" name="immagine" accept="image/*" required>
                    </div>

                </div>

                <button type="submit" class="btn-submit" style="width: 100%; margin-top: 20px;">PUBBLICA ANNUNCIO</button>
            </form>
        </div>
    </div>

</body>
</html>