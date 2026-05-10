<?php
session_start();
require_once 'db_connection.php';

if(!isset($_GET['id'])) { header("Location: compra.php"); exit; }
$id_libro = intval($_GET['id']);

$query = "SELECT L.*, A.titolo, A.autore, A.materia, U.nome, U.IdUtente 
          FROM Libri L 
          JOIN AnagraficaLibri A ON L.IdAnag = A.IdAnag 
          JOIN Utenti U ON L.IdVenditore = U.IdUtente 
          WHERE L.IdLibro = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_libro);
$stmt->execute();
$libro = $stmt->get_result()->fetch_assoc();

if(!$libro) { die("Libro non trovato."); }
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($libro['titolo']); ?> - Debook</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .container-detail { max-width: 1000px; margin: 40px auto; padding: 20px; display: flex; gap: 40px; background: white; border-radius: 30px; box-shadow: var(--shadow); }
        .book-img { width: 400px; height: 500px; object-fit: cover; border-radius: 20px; }
        .info-section { flex: 1; }
        .price { font-size: 2.5rem; font-family: 'Arial Black'; color: #2ecc71; margin: 20px 0; }
        .btn-buy { display: block; background: #27ae60; color: white; padding: 15px; text-align: center; border-radius: 50px; text-decoration: none; font-weight: bold; margin-bottom: 10px; }
        .btn-chat { display: block; background: #34495e; color: white; padding: 15px; text-align: center; border-radius: 50px; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container-detail">
        <div>
            <?php $imgs = explode(",", $libro['immagine']); ?>
            <img src="<?php echo htmlspecialchars($imgs[0]); ?>" class="book-img">
        </div>
        <div class="info-section">
            <h1><?php echo htmlspecialchars($libro['titolo']); ?></h1>
            <p>Autore: <?php echo htmlspecialchars($libro['autore']); ?></p>
            <div class="price"><?php echo number_format($libro['prezzo'], 2); ?> €</div>
            
            <div class="actions">
                <?php if(isset($_SESSION['id']) && $_SESSION['id'] != $libro['IdUtente']): ?>
                    <a href="checkout.php?id_libro=<?php echo $id_libro; ?>" class="btn-buy">COMPRA ORA</a>
                    <a href="chat.php?with=<?php echo $libro['IdUtente']; ?>&id_libro=<?php echo $id_libro; ?>" class="btn-chat">CONTATTA IL VENDITORE</a>
                <?php else: ?>
                    <p>Accedi per acquistare o visualizza i tuoi annunci.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>