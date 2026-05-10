<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION["loggedin"])) {
    header("Location: login.php");
    exit;
}

$id_utente = $_SESSION["id"];

// Query complessa per unire Ordini, Transazioni, Libri e Anagrafica
$query = "SELECT O.data as data_ordine, T.importo, T.metodo, A.titolo, U.nome as venditore, U.IdUtente as IdVenditore
          FROM Ordini O
          JOIN Transazioni T ON O.IdTransazione = T.IdTransazione
          JOIN Libri L ON L.IdOrdine = O.IdOrdine
          JOIN AnagraficaLibri A ON L.IdAnag = A.IdAnag
          JOIN Utenti U ON L.IdVenditore = U.IdUtente
          WHERE O.IdAcquirente = ?
          ORDER BY O.data DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_utente);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Debook - I miei Acquisti</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .container { width: 90%; max-width: 900px; margin: 40px auto; }
        .order-card { 
            background: white; 
            padding: 25px; 
            border-radius: 20px; 
            margin-bottom: 20px; 
            display: flex; 
            justify-content: space-between; 
            align-items: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        .order-info h3 { margin-bottom: 5px; color: var(--dark-text); }
        .order-info p { font-family: Arial; font-size: 0.9rem; color: #666; }
        .order-meta { text-align: right; font-family: Arial; }
        .status-badge { 
            display: inline-block; 
            padding: 5px 12px; 
            background: #e8f5e9; 
            color: #2e7d32; 
            border-radius: 50px; 
            font-size: 0.8rem; 
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <header class="header-nav">
        <a href="index.php" class="logo-link"><img src="immagini/tastologo.png" alt="Debook Logo"></a>
        <a href="dashboard.php" style="font-family: Arial; text-decoration: none; color: var(--dark-text);">Dashboard</a>
    </header>

    <div class="container">
        <h1>Storico Acquisti</h1>
        <p style="font-family: Arial; margin-bottom: 30px;">Qui puoi vedere i libri che hai comprato e valutare i venditori.</p>

        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="order-card">
                    <div class="order-info">
                        <p style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px;">Ordine del <?php echo date('d/m/Y', strtotime($row['data_ordine'])); ?></p>
                        <h3><?php echo htmlspecialchars($row['titolo']); ?></h3>
                        <p>Venditore: <strong><?php echo htmlspecialchars($row['venditore']); ?></strong></p>
                        <div class="status-badge">Completato</div>
                    </div>
                    <div class="order-meta">
                        <div style="font-size: 1.2rem; font-weight: bold; margin-bottom: 10px;">
                            <?php echo number_format($row['importo'], 2); ?>€
                        </div>
                        <a href="profilo.php?id=<?php echo $row['IdVenditore']; ?>" class="btn-submit" style="font-size: 0.8rem; padding: 8px 15px; text-decoration: none;">Lascia Feedback</a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div style="text-align: center; padding: 50px; background: white; border-radius: 20px;">
                <p style="font-family: Arial; color: #777;">Non hai ancora effettuato acquisti.</p>
                <a href="compra.php" style="color: var(--dark-text); font-weight: bold;">Sfoglia il mercatino</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>