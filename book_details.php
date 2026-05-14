<?php
session_start();
require_once 'db_connection.php';

if(!isset($_GET['id'])) { header("Location: compra.php"); exit; }
$id_libro = intval($_GET['id']);

// Query aggiornata con i nomi corretti: codISBN e condizione
$query = "SELECT L.*, A.titolo, A.autore, A.materia, A.codISBN, U.nome as nome_venditore, U.IdUtente 
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
    <title><?php echo htmlspecialchars($libro['titolo']); ?> - Debook</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f7f6; margin: 0; padding: 0; }
        .container-detail { 
            max-width: 1000px; margin: 40px auto; padding: 30px; 
            display: flex; gap: 40px; background: white; 
            border-radius: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); 
        }
        .image-section { flex: 1; text-align: center; }
        .book-img { width: 100%; max-width: 350px; border-radius: 20px; box-shadow: var(--shadow); }
        
        .info-section { flex: 1.2; }
        .back-link { display: inline-flex; align-items: center; gap: 8px; margin-bottom: 20px; text-decoration: none; color: #7f8c8d; font-weight: bold; transition: 0.3s; }
        .back-link:hover { color: #333; }
        
        .price { font-size: 2.8rem; font-family: 'Arial Black'; color: #2ecc71; margin: 15px 0; }
        
        .specs { width: 100%; margin: 20px 0; border-collapse: collapse; }
        .specs td { padding: 12px; border-bottom: 1px solid #eee; font-family: Arial; }
        .label { font-weight: bold; color: #95a5a6; width: 35%; }

        .btn-buy { display: block; background: #27ae60; color: white; padding: 18px; text-align: center; border-radius: 50px; text-decoration: none; font-weight: bold; margin-bottom: 12px; font-size: 1.1rem; transition: 0.3s; }
        .btn-buy:hover { background: #219150; transform: translateY(-2px); }
        
        .btn-chat { display: block; background: #34495e; color: white; padding: 18px; text-align: center; border-radius: 50px; text-decoration: none; font-weight: bold; transition: 0.3s; }
        .btn-chat:hover { background: #2c3e50; transform: translateY(-2px); }
        
        @media (max-width: 768px) { .container-detail { flex-direction: column; margin: 20px; padding: 20px; } }
    </style>
</head>
<body>
    <div class="container-detail">
        <div class="image-section">
            <?php 
            $imgs = explode(",", $libro['immagine']); 
            $img_path = htmlspecialchars($imgs[0]);
            ?>
            <img src="<?php echo $img_path; ?>" class="book-img" alt="Copertina">
        </div>

        <div class="info-section">
            <a href="compra.php" class="back-link"><i class="fa-solid fa-arrow-left"></i> Torna al Mercatino</a>
            
            <h1 style="margin: 0; color: #2c3e50;"><?php echo htmlspecialchars($libro['titolo']); ?></h1>
            <p style="font-size: 1.2rem; color: #7f8c8d; margin-top: 5px;">di <?php echo htmlspecialchars($libro['autore']); ?></p>
            
            <div class="price"><?php echo number_format($libro['prezzo'], 2); ?> €</div>

            <table class="specs">
                <tr><td class="label">Materia</td><td><?php echo htmlspecialchars($libro['materia']); ?></td></tr>
                <tr><td class="label">ISBN</td><td><?php echo htmlspecialchars($libro['codISBN'] ?? 'N/D'); ?></td></tr>
                <tr><td class="label">Condizione</td><td><strong><?php echo htmlspecialchars($libro['condizione'] ?? 'Usato'); ?></strong></td></tr>
                <tr><td class="label">Venditore</td><td><i class="fa-solid fa-user-circle"></i> <?php echo htmlspecialchars($libro['nome_venditore']); ?></td></tr>
                <tr>
    <td class="specs-label">Libro Digitale</td>
    <td>
        <?php if($libro['digitale_usato'] == 0): ?>
            <span style="color: #27ae60; font-weight: bold;"><i class="fa-solid fa-check-circle"></i> Codice Disponibile</span>
        <?php else: ?>
            <span style="color: #e74c3c; font-weight: bold;"><i class="fa-solid fa-circle-xmark"></i> Codice Già Usato</span>
        <?php endif; ?>
    </td>
</tr>
            </table>

            <div class="actions" style="margin-top: 30px;">
                <?php if(isset($_SESSION['id'])): ?>
                    <?php if($_SESSION['id'] != $libro['IdUtente']): ?>
                        <a href="checkout.php?id_libro=<?php echo $id_libro; ?>" class="btn-buy">
                            <i class="fa-solid fa-credit-card"></i> COMPRA ORA
                        </a>
                        <a href="chat.php?with=<?php echo $libro['IdUtente']; ?>&id_libro=<?php echo $id_libro; ?>" class="btn-chat">
                            <i class="fa-solid fa-comments"></i> CONTATTA IL VENDITORE
                        </a>
                    <?php else: ?>
                        <div style="background: #fff3cd; color: #856404; padding: 15px; border-radius: 15px; text-align: center; border: 1px solid #ffeeba;">
                            <i class="fa-solid fa-info-circle"></i> Questo annuncio è tuo. Puoi gestirlo dalla tua area personale.
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="login.php" class="btn-buy">ACCEDI PER ACQUISTARE</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>