<?php
session_start();
require_once 'db_connection.php';
$id_libro = $_GET['id'];

$query = "SELECT L.*, A.titolo, A.autore, A.materia, U.nome, U.cognome FROM Libri L 
          JOIN AnagraficaLibri A ON L.IdAnag = A.IdAnag 
          JOIN Utenti U ON L.IdVenditore = U.IdUtente WHERE L.IdLibro = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_libro);
$stmt->execute();
$libro = $stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $libro['titolo']; ?></title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .gallery { display: flex; gap: 15px; overflow-x: auto; padding-bottom: 10px; margin-bottom: 20px; scroll-snap-type: x mandatory; }
        .gallery img { width: 80%; max-width: 350px; height: 400px; object-fit: cover; border-radius: 20px; flex-shrink: 0; scroll-snap-align: start; }
        .cond-badge { background: #fdf5e6; color: #d2691e; padding: 5px 15px; border-radius: 20px; font-weight: bold; font-size: 0.9rem; border: 1px solid #ffe4b5; }
        .detail-card { background: white; padding: 30px; border-radius: 30px; box-shadow: var(--shadow); max-width: 900px; margin: 40px auto; display: flex; gap: 30px; }
        @media (max-width: 768px) { .detail-card { flex-direction: column; } .gallery img { width: 100%; } }
    </style>
</head>
<body>
    <header class="header-nav">
        <a href="compra.php"><i class="fa-solid fa-arrow-left"></i> Indietro</a>
    </header>

    <div class="detail-card">
        <div style="flex:1">
            <div class="gallery">
                <?php 
                $imgs = explode(",", $libro['immagine']);
                foreach($imgs as $i) echo "<img src='$i'>";
                ?>
            </div>
        </div>
        <div style="flex:1">
            <span class="cond-badge"><?php echo $libro['condizione']; ?></span>
            <h1 style="margin:10px 0;"><?php echo $libro['titolo']; ?></h1>
            <p>di <?php echo $libro['autore']; ?></p>
            <div style="font-size: 2.5rem; font-family: 'Arial Black'; margin: 20px 0;"><?php echo number_format($libro['prezzo'], 2); ?> €</div>
            
            <div style="background:#f9f9f9; padding:15px; border-radius:15px; margin-bottom:20px;">
                <small>Venditore</small><br><strong><?php echo $libro['nome']." ".$libro['cognome']; ?></strong>
            </div>

            <?php if($_SESSION['id'] != $libro['IdVenditore']): ?>
                <a href="chat.php?with=<?php echo $libro['IdVenditore']; ?>" class="btn-submit" style="display:block; text-align:center; text-decoration:none;">CONTATTA</a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>