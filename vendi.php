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
        
        /* Stile per il tasto Dashboard nell'header */
        .btn-dashboard-nav {
            text-decoration: none;
            color: var(--dark-text);
            font-family: Arial, sans-serif;
            font-weight: bold;
            font-size: 0.9rem;
            background: #f0f0f0;
            padding: 8px 15px;
            border-radius: 50px;
            transition: 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .btn-dashboard-nav:hover {
            background: #e0e0e0;
            transform: translateY(-2px);
        }

        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; text-align: left; }
        .full-width { grid-column: span 2; }
        .input-group label { font-weight: bold; font-size: 0.8rem; color: #666; display: block; margin-bottom: 8px; }
        .input-group input, .input-group select { width: 100%; padding: 12px; border: 2px solid #eee; border-radius: 12px; outline: none; box-sizing: border-box; }

        .condition-list { display: flex; flex-direction: column; gap: 10px; margin-top: 10px; }
        .condition-option { display: flex; align-items: center; padding: 15px; border: 2px solid #eee; border-radius: 12px; cursor: pointer; transition: 0.2s; background: #fafafa; position: relative; }
        .condition-option input { margin-right: 15px; transform: scale(1.2); }
        .condition-option:hover { border-color: var(--accent-beige); }
        
        .condition-option:has(input:checked) { border-color: var(--dark-text); background: #fff; box-shadow: var(--shadow); }
        
        .cond-text strong { display: block; color: var(--dark-text); }
        .cond-text span { font-size: 0.8rem; color: #888; font-family: Arial; }

        @media (max-width: 600px) { 
            .form-grid { grid-template-columns: 1fr; } 
            .full-width { grid-column: span 1; }
            .header-nav { padding: 10px 20px; }
        }
    </style>
</head>
<body>
    <header class="header-nav">
        <a href="index.php" class="logo-link"><img src="immagini/tastologo.png" alt="Debook Logo"></a>
        <a href="dashboard.php" class="btn-dashboard-nav">
            <i class="fa-solid fa-house-user"></i> Dashboard
        </a>
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
                    <label>AUTORE (Scriverli uno di seguito all'altro con spazi)</label>
                    <input type="text" name="autore" required placeholder="Es. Dante Alighieri">
                </div>
                <div class="input-group">
                    <label>MATERIA (Scrivere correttamente con la lettera maiuscola)</label>
                    <input type="text" name="materia" required placeholder="Es. Letteratura Italiana">
                </div>
                <div class="input-group">
                    <label>ISBN</label>
                    <input type="text" name="isbn" required placeholder="Es. 9788800000000">
                </div>
                <div class="input-group">
                    <label>PREZZO (€)</label>
                    <input type="number" name="prezzo" step="0.01" required placeholder="Es. 15.50">
                </div>

                <div class="input-group full-width">
                    <label>CONDIZIONI FISICHE DEL LIBRO</label>
                    <select name="condizione" required>
                        <option value="" disabled selected>Scegli lo stato del libro...</option>
                        <option value="Come Nuovo">Come Nuovo (Nessun segno)</option>
                        <option value="Ottimo">Ottimo (quasi nessun segno)</option>
                        <option value="Buono">Buono (Sottolineature o note con leggeri segni di usura)</option>
                        <option value="Discreto">Discreto (Alcuni segni di usura)</option>
                        <option value="Usurato">Usurato (Molte scritte o copertina rovinata)</option>
                    </select>
                </div>

                <div class="input-group full-width">
                    <label>VERSIONE DIGITALE (E-BOOK)</label>
                    <div class="condition-list">
                        <label class="condition-option">
                            <input type="radio" name="digitale_usato" value="0" checked>
                            <div class="cond-text">
                                <strong>Codice Disponibile</strong>
                                <span>Il codice per scaricare il libro digitale NON è mai stato usato.</span>
                            </div>
                        </label>
                        <label class="condition-option">
                            <input type="radio" name="digitale_usato" value="1">
                            <div class="cond-text">
                                <strong>Codice Già Usato</strong>
                                <span>Il codice è già stato riscattato, è scaduto o non è presente.</span>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="input-group full-width">
                    <label>FOTO DEL LIBRO (si consiglia di inserire prima la copertina per motivi di visualizzazione)</label>
                    <input type="file" name="immagini[]" accept="image/*" multiple required>
                </div>
            </div>
            <button type="submit" class="btn-submit" style="width:100%; margin-top:30px;">PUBBLICA ORA</button>
        </form>
    </div>
</body>
</html>