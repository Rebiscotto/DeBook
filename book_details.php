<?php
session_start();
require_once 'db_connection.php';

// Verifichiamo che l'ID sia presente
if(!isset($_GET['id'])) { header("Location: compra.php"); exit; }

$id_libro = $_GET['id'];

// Query corretta: usiamo "AS nome_venditore" per far funzionare il tuo codice HTML
$query = "SELECT L.*, A.titolo, A.autore, A.materia, U.nome, U.cognome, U.nome AS nome_venditore 
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($libro['titolo']); ?></title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .gallery { display: flex; gap: 15px; overflow-x: auto; padding-bottom: 10px; margin-bottom: 20px; scroll-snap-type: x mandatory; }
        .gallery img { width: 80%; max-width: 350px; height: 400px; object-fit: cover; border-radius: 20px; flex-shrink: 0; scroll-snap-align: start; }
        .cond-badge { background: #fdf5e6; color: #d2691e; padding: 5px 15px; border-radius: 20px; font-weight: bold; font-size: 0.9rem; border: 1px solid #ffe4b5; display: inline-block; }
        .detail-card { background: white; padding: 30px; border-radius: 30px; box-shadow: var(--shadow); max-width: 900px; margin: 40px auto; display: flex; gap: 30px; }
        .price-tag { font-size: 2.5rem; font-family: 'Arial Black'; margin: 20px 0; color: var(--dark-text); }
        
        /* Tasto indietro stilizzato */
        .btn-back-header { text-decoration: none; color: var(--dark-text); font-weight: bold; display: inline-block; margin: 20px; }

        @media (max-width: 768px) { 
            .detail-card { flex-direction: column; margin: 10px; } 
            .gallery img { width: 100%; } 
        }
    </style>
</head>
<body>
    <header>
        <a href="compra.php" class="btn-back-header"><i class="fa-solid fa-arrow-left"></i> Torna al Mercatino</a>
    </header>

    <div class="detail-card">
        <div style="flex:1">
            <div class="gallery">
                <?php 
                $imgs = explode(",", $libro['immagine']);
                foreach($imgs as $i) {
                    if(!empty(trim($i))) {
                        echo "<img src='".htmlspecialchars(trim($i))."' alt='Immagine libro'>";
                    }
                }
                ?>
            </div>
        </div>

        <div style="flex:1">
            <span class="cond-badge"><?php echo htmlspecialchars($libro['condizione']); ?></span>
            <h1 style="margin:10px 0;"><?php echo htmlspecialchars($libro['titolo']); ?></h1>
            <p style="color: #666; font-size: 1.1rem;">di <strong><?php echo htmlspecialchars($libro['autore']); ?></strong></p>
            <p><small>Materia: <?php echo htmlspecialchars($libro['materia']); ?></small></p>

            <div class="price-tag"><?php echo number_format($libro['prezzo'], 2); ?> €</div>
            
            <div style="background:#f9f9f9; padding:20px; border-radius:15px; margin-bottom:20px; border: 1px solid #eee;">
                <p style="margin:0; font-size:0.9rem; color:#777;">Venduto da:</p>
                <a href="profilo.php?id=<?php echo $libro['IdVenditore']; ?>" style="text-decoration:none; color:var(--dark-text); font-size: 1.1rem;">
                    <div style="display: flex; align-items: center; gap: 10px; margin-top: 5px;">
                        <i class="fa-solid fa-circle-user" style="font-size: 1.5rem; color: var(--accent-beige);"></i>
                        <div>
                            <strong><?php echo htmlspecialchars($libro['nome'] . " " . $libro['cognome']); ?></strong>
                            <div style="color:#f39c12; font-size:0.8rem;">
                                <i class="fa-solid fa-star"></i> Vedi recensioni e affidabilità
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <?php if(isset($_SESSION['id']) && $_SESSION['id'] != $libro['IdVenditore']): ?>
                <a href="chat.php?with=<?php echo $libro['IdVenditore']; ?>" class="btn-submit" style="display:block; text-align:center; text-decoration:none; padding: 15px;">
                    <i class="fa-solid fa-comments"></i> CONTATTA IL VENDITORE
                </a>
            <?php elseif(!isset($_SESSION['id'])): ?>
                <a href="login.php" class="btn-submit" style="display:block; text-align:center; text-decoration:none; background: #ccc;">
                    ACCEDI PER CONTATTARE
                </a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>