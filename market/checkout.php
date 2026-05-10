<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION["loggedin"]) || !isset($_GET['id_libro'])) {
    header("Location: login.php");
    exit;
}

$id_libro = $_GET['id_libro'];
$id_acquirente = $_SESSION["id"];

// Recupero dettagli del libro e del prezzo (se presente)
$query = "SELECT L.*, A.titolo, A.materia, U.nome as venditore 
          FROM Libri L 
          JOIN AnagraficaLibri A ON L.IdAnag = A.IdAnag
          JOIN Utenti U ON L.IdVenditore = U.IdUtente
          WHERE L.IdLibro = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_libro);
$stmt->execute();
$libro = $stmt->get_result()->fetch_assoc();

if (!$libro) die("Libro non trovato.");
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Debook - Checkout</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .checkout-container { width: 90%; max-width: 600px; margin: 40px auto; background: white; padding: 40px; border-radius: 20px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        .summary-box { background: var(--bg-page); padding: 20px; border-radius: 15px; margin-bottom: 25px; }
        .option-group { margin-bottom: 25px; }
        .option-group label { display: block; margin-bottom: 10px; font-weight: bold; }
        select, input[type="text"] { width: 100%; padding: 12px; border-radius: 10px; border: 2px solid var(--bg-page); font-family: Arial; }
    </style>
</head>
<body>
    <header class="header-nav">
        <a href="index.php" class="logo-link"><img src="immagini/tastologo.png" alt="Debook Logo"></a>
    </header>

    <div class="checkout-container">
        <h2>Conferma lo Scambio</h2>
        
        <div class="summary-box">
            <p style="font-family: Arial; font-size: 0.9rem; color: #666;">Stai acquistando:</p>
            <h3 style="margin: 5px 0;"><?php echo htmlspecialchars($libro['titolo']); ?></h3>
            <p style="font-family: Arial;">Venditore: <strong><?php echo htmlspecialchars($libro['venditore']); ?></strong></p>
        </div>

        <form action="process_checkout.php" method="POST">
            <input type="hidden" name="id_libro" value="<?php echo $id_libro; ?>">

            <div class="option-group">
                <label for="punto_ritiro">Punto di incontro (Area Scolastica)</label>
                <select name="punto_ritiro" id="punto_ritiro" required>
                    <option value="">-- Seleziona un luogo --</option>
                    <option value="Atrio Principale">Atrio Principale</option>
                    <option value="Biblioteca Scolastica">Biblioteca Scolastica</option>
                    <option value="Cortile Esterno">Cortile Esterno</option>
                    <option value="Laboratorio Informatica">Laboratorio Informatica</option>
                </select>
            </div>

            <div class="option-group">
                <label>Metodo di Pagamento</label>
                <div style="display: flex; gap: 20px; font-family: Arial;">
                    <label style="font-weight: normal;"><input type="radio" name="metodo" value="Contanti" checked> Contanti (de visu)</label>
                    <label style="font-weight: normal;"><input type="radio" name="metodo" value="Digitale"> Digitale (Stripe)</label>
                </div>
            </div>

            <button type="submit" class="btn-submit">Conferma Ordine</button>
        </form>
    </div>
</body>
</html>