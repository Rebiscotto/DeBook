<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION["loggedin"]) || !isset($_GET['id'])) { header("Location: login.php"); exit; }

$id_libro = intval($_GET['id']);
$id_utente = $_SESSION["id"];

$query = "SELECT L.*, A.titolo, A.autore, A.materia, A.codISBN, A.IdAnag 
          FROM Libri L 
          JOIN AnagraficaLibri A ON L.IdAnag = A.IdAnag 
          WHERE L.IdLibro = ? AND L.IdVenditore = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $id_libro, $id_utente);
$stmt->execute();
$libro = $stmt->get_result()->fetch_assoc();

if (!$libro) { die("Accesso negato o libro non trovato."); }
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifica Annuncio - Debook</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: var(--bg-page); font-family: Arial, sans-serif; }
        .edit-card { background: white; padding: 30px; border-radius: 25px; box-shadow: var(--shadow); width: 95%; max-width: 700px; margin: 20px auto; }
        .input-group { margin-bottom: 15px; }
        .input-group label { font-weight: bold; display: block; margin-bottom: 5px; color: #555; font-size: 0.9rem; }
        .input-group input, .input-group select { width: 100%; padding: 12px; border: 2px solid #eee; border-radius: 12px; box-sizing: border-box; font-size: 1rem; }
        .current-img { width: 80px; height: 110px; object-fit: cover; border-radius: 10px; margin: 10px 0; border: 1px solid #ddd; }
        .section-title { border-bottom: 2px solid #f4f7f6; padding-bottom: 5px; margin-bottom: 15px; color: var(--dark-text); font-family: 'Arial Black'; font-size: 1.1rem; }
    </style>
</head>
<body>
    <div class="edit-card">
        <h1 style="text-align:center;">Modifica Libro</h1>
        
        <form action="modifica_controller.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id_libro" value="<?php echo $libro['IdLibro']; ?>">
            <input type="hidden" name="id_anag" value="<?php echo $libro['IdAnag']; ?>">

            <div class="section-title">Informazioni Generali</div>
            
            <div class="input-group">
                <label>TITOLO DEL LIBRO</label>
                <input type="text" name="titolo" value="<?php echo htmlspecialchars($libro['titolo']); ?>" required>
            </div>

            <div class="input-group">
                <label>AUTORE (Scriverli uno di seguito all'altro con spazio tra nome e cognome e virgola tra uno e l'altro)</label>
                <input type="text" name="autore" value="<?php echo htmlspecialchars($libro['autore']); ?>" required>
            </div>

            <div class="row" style="display:flex; gap:15px;">
                <div class="input-group" style="flex:1;">
                    <label>MATERIA (Scrivere correttamente con la lettera maiuscola)</label>
                    <input type="text" name="materia" value="<?php echo htmlspecialchars($libro['materia']); ?>" required>
                </div>
                <div class="input-group" style="flex:1;">
                    <label>CODICE ISBN</label>
                    <input type="text" name="isbn" value="<?php echo htmlspecialchars($libro['codISBN']); ?>">
                </div>
            </div>

            <div class="section-title" style="margin-top:20px;">Dettagli Vendita</div>

            <div class="input-group">
                <label>PREZZO DI VENDITA (€)</label>
                <input type="number" name="prezzo" step="0.01" value="<?php echo $libro['prezzo']; ?>" required>
            </div>

            <div class="input-group">
                <label>CONDIZIONE FISICA</label>
                <select name="condizione" required>
                    <option value="Come Nuovo" <?php if($libro['condizione'] == "Come Nuovo") echo "selected"; ?>>Come Nuovo</option>
                    <option value="Ottimo" <?php if($libro['condizione'] == "Ottimo") echo "selected"; ?>>Ottimo</option>
                    <option value="Buono" <?php if($libro['condizione'] == "Buono") echo "selected"; ?>>Buono</option>
                    <option value="Discreto" <?php if($libro['condizione'] == "Discreto") echo "selected"; ?>>Discreto</option>
                    <option value="Usurato" <?php if($libro['condizione'] == "Usurato") echo "selected"; ?>>Usurato</option>
                </select>
            </div>

            <div class="input-group">
                <label>VERSIONE DIGITALE</label>
                <select name="digitale_usato" required>
                    <option value="0" <?php if($libro['digitale_usato'] == 0) echo "selected"; ?>>Codice Disponibile</option>
                    <option value="1" <?php if($libro['digitale_usato'] == 1) echo "selected"; ?>>Codice Già Usato</option>
                </select>
            </div>

            <div class="input-group">
                <label>FOTO ATTUALE</label><br>
                <img src="<?php echo explode(',', $libro['immagine'])[0]; ?>" class="current-img"><br>
                <label>Sostituisci foto (lascia vuoto per mantenere le attuali)</label>
                <input type="file" name="immagini[]" multiple accept="image/*">
            </div>

            <button type="submit" class="btn-submit" style="width:100%; margin-top:20px; padding:15px;">AGGIORNA ANNUNCIO</button>
            <a href="my_list.php" style="display:block; text-align:center; margin-top:15px; color:#666; text-decoration:none;">Annulla e torna indietro</a>
        </form>
    </div>
</body>
</html>