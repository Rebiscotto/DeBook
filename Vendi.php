<?php
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Debook - Vendi Libro</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .form-container { max-width: 600px; margin-bottom: 50px; }
        .radio-group { display: flex; gap: 20px; margin-top: 10px; font-family: 'Arial', sans-serif; }
        .radio-item { display: flex; align-items: center; gap: 5px; }
    </style>
</head>
<body>
    <header class="header-nav">
        <a href="index.php" class="logo-link"><img src="immagini/tastologo.png" alt="Debook Logo"></a>
        <div style="font-family: Arial;">
            <a href="dashboard.php" style="text-decoration: none; color: var(--dark-text); margin-right: 15px;">Dashboard</a>
            <a href="logout.php" style="color: #d32f2f; text-decoration: none;">Esci</a>
        </div>
    </header>

    <div class="form-container">
        <h2>Metti in vendita un libro</h2>
        <p style="font-family: Arial; margin-bottom: 20px;">Inserisci i dettagli del volume per renderlo visibile agli altri studenti.</p>

        <form action="vendi_controller.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="titolo">Titolo del libro</label>
                <input type="text" id="titolo" name="titolo" required>
            </div>
            
            <div class="form-group">
                <label for="autore">Autore</label>
                <input type="text" id="autore" name="autore" required>
            </div>

            <div style="display: flex; gap: 15px;">
                <div class="form-group" style="flex: 1;">
                    <label for="materia">Materia</label>
                    <input type="text" id="materia" name="materia" required placeholder="es. Matematica">
                </div>
                <div class="form-group" style="flex: 1;">
                    <label for="isbn">Codice ISBN (opzionale)</label>
                    <input type="text" id="isbn" name="isbn" maxlength="13">
                </div>
            </div>

            <div class="form-group">
                <label>Modalità di cessione</label>
                <div class="radio-group">
                    <div class="radio-item">
                        <input type="radio" id="vendita" name="tipo_cessione" value="Vendita" checked>
                        <label for="vendita">Vendita onerosa</label>
                    </div>
                    <div class="radio-item">
                        <input type="radio" id="scambio" name="tipo_cessione" value="Scambio">
                        <label for="scambio">Scambio/Donazione</label>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="immagine">Foto del libro (JPEG/PNG)</label>
                <input type="file" id="immagine" name="immagine" accept="image/jpeg, image/png" required>
                <p style="font-size: 0.8rem; color: #666; font-family: Arial; margin-top: 5px;">
                    Carica una foto che attesti l'integrità del volume.
                </p>
            </div>

            <button type="submit" class="btn-submit">Pubblica Annuncio</button>
        </form>
    </div>
</body>
</html>