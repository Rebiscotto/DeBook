<?php
session_start();
if (!isset($_SESSION["loggedin"])) { header("Location: login.php"); exit; }
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
        body { background-color: var(--bg-page); padding-bottom: 50px; }
        .sell-card { background: white; padding: 40px; border-radius: 30px; box-shadow: var(--shadow); width: 95%; max-width: 700px; margin: 40px auto; text-align: center; }
        
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; text-align: left; }
        .full-width { grid-column: span 2; }
        .input-group label { font-weight: bold; font-size: 0.8rem; color: #666; display: block; margin-bottom: 8px; }
        .input-group input, .input-group select { width: 100%; padding: 12px; border: 2px solid #eee; border-radius: 12px; outline: none; }

        /* Stile Lista Condizioni */
        .condition-list { display: flex; flex-direction: column; gap: 10px; margin-top: 10px; }
        .condition-option { display: flex; align-items: center; padding: 15px; border: 2px solid #eee; border-radius: 12px; cursor: pointer; transition: 0.2s; background: #fafafa; }
        .condition-option input { margin-right: 15px; transform: scale(1.2); }
        .condition-option:hover { border-color: var(--accent-beige); }
        .condition-option:has(input:checked) { border-color: var(--dark-text); background: #fff; box-shadow: var(--shadow); }
        .cond-text strong { display: block; color: var(--dark-text); }
        .cond-text span { font-size: 0.8rem; color: #888; font-family: Arial; }

        @media (max-width: 600px) { .form-grid { grid-template-columns: 1fr; } .full-width { grid-column: span 1; } }
    </style>
</head>
<body>
    <header class="header-nav">
        <a href="index.php" class="logo-link"><img src="immagini/tastologo.png" alt="Debook Logo"></a>
        <a href="index.php" style="text-decoration:none; color:black; font-family:Arial;">Annulla</a>
    </header>

    <div class="sell-card">
        <h1>Metti in vendita</h1>
        <form action="vendi_controller.php" method="POST" enctype="multipart/form-data">
            <div class="form-grid">
                <div class="input-group full-width">
                    <label>TITOLO DEL LIBRO</label>
                    <input type="text" name="titolo" required placeholder="Es. Divina Commedia">
                </div>
                <div class="input-group">
                    <label>AUTORE</label>
                    <input type="text" name="autore" required>
                </div>
                <div class="input-group">
                    <label>MATERIA</label>
                    <input type="text" name="materia" required>
                </div>
                <div class="input-group">
                    <label>ISBN</label>
                    <input type="text" name="isbn">
                </div>
                <div class="input-group">
                    <label>PREZZO (€)</label>
                    <input type="number" name="prezzo" step="0.01" required>
                </div>

                <div class="input-group full-width">
                    <label>CONDIZIONI DEL LIBRO</label>
                    <div class="condition-list">
                        <label class="condition-option">
                            <input type="radio" name="condizione" value="Come Nuovo" required>
                            <div class="cond-text"><strong>Come Nuovo</strong><span>Nessun segno, perfetto.</span></div>
                        </label>
                        <label class="condition-option">
                            <input type="radio" name="condizione" value="Ottimo">
                            <div class="cond-text"><strong>Ottimo</strong><span>Piccoli segni di usura, interno pulito.</span></div>
                        </label>
                        <label class="condition-option">
                            <input type="radio" name="condizione" value="Buono">
                            <div class="cond-text"><strong>Buono</strong><span>Sottolineature a matita, legali usura.</span></div>
                        </label>
                        <label class="condition-option">
                            <input type="radio" name="condizione" value="Usurato">
                            <div class="cond-text"><strong>Usurato</strong><span>Molte scritte, copertina rovinata.</span></div>
                        </label>
                    </div>
                </div>

                <div class="input-group full-width">
                    <label>FOTO (Puoi caricarne più di una)</label>
                    <input type="file" name="immagini[]" accept="image/*" multiple required>
                </div>
            </div>
            <button type="submit" class="btn-submit" style="width:100%; margin-top:30px;">PUBBLICA ORA</button>
        </form>
    </div>
</body>
</html>