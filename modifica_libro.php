<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION["loggedin"]) || !isset($_GET['id'])) { header("Location: login.php"); exit; }

$id_libro = intval($_GET['id']);
$id_utente = $_SESSION["id"];

// Verifichiamo che il libro appartenga effettivamente all'utente loggato
$query = "SELECT L.*, A.titolo, A.autore, A.materia, A.codISBN 
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
    <title>Modifica Annuncio - Debook</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .edit-card { background: white; padding: 40px; border-radius: 30px; box-shadow: var(--shadow); width: 95%; max-width: 700px; margin: 40px auto; }
        .input-group { margin-bottom: 20px; text-align: left; }
        .input-group label { font-weight: bold; display: block; margin-bottom: 8px; color: #666; }
        .input-group input, .input-group select { width: 100%; padding: 12px; border: 2px solid #eee; border-radius: 12px; box-sizing: border-box; }
        .current-img { width: 100px; border-radius: 10px; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="edit-card">
        <h1>Modifica Annuncio</h1>
        <form action="modifica_controller.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id_libro" value="<?php echo $libro['IdLibro']; ?>">
            
            <div class="input-group">
                <label>TITOLO (Sola lettura)</label>
                <input type="text" value="<?php echo htmlspecialchars($libro['titolo']); ?>" disabled>
            </div>

            <div class="input-group">
                <label>PREZZO (€)</label>
                <input type="number" name="prezzo" step="0.01" value="<?php echo $libro['prezzo']; ?>" required>
            </div>

            <div class="input-group">
                <label>CONDIZIONE FISICA</label>
                <select name="condizione" required>
                    <option value="Come Nuovo" <?php if($libro['condizione'] == "Come Nuovo") echo "selected"; ?>>Come Nuovo</option>
                    <option value="Ottimo" <?php if($libro['condizione'] == "Ottimo") echo "selected"; ?>>Ottimo</option>
                    <option value="Buono" <?php if($libro['condizione'] == "Buono") echo "selected"; ?>>Buono</option>
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
                <label>FOTO ATTUALE</label>
                <img src="<?php echo explode(',', $libro['immagine'])[0]; ?>" class="current-img"><br>
                <label style="margin-top:10px;">Carica nuove foto per sostituirle (Opzionale)</label>
                <input type="file" name="immagini[]" multiple accept="image/*">
            </div>

            <button type="submit" class="btn-submit" style="width:100%;">SALVA MODIFICHE</button>
            <a href="my_list.php" style="display:block; text-align:center; margin-top:15px; color:#666; text-decoration:none;">Annulla</a>
        </form>
    </div>
</body>
</html>