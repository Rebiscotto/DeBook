<?php
session_start();
require_once 'db_connection.php';

if(!isset($_GET['id'])) { header("Location: compra.php"); exit; }
$id_libro = intval($_GET['id']);

// Recupero tutti i dettagli, incluse le condizioni e l'anno (se presenti nel DB)
$query = "SELECT L.*, A.titolo, A.autore, A.materia, A.isbn, U.nome as nome_venditore, U.IdUtente 
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
        body { background-color: var(--bg-page); }
        
        .container-detail { 
            max-width: 1100px; 
            margin: 40px auto; 
            padding: 30px; 
            display: flex; 
            gap: 50px; 
            background: white; 
            border-radius: 40px; 
            box-shadow: var(--shadow); 
        }

        /* Colonna Immagine */
        .image-section { flex: 1; text-align: center; }
        .book-img { 
            width: 100%; 
            max-width: 400px; 
            height: auto; 
            border-radius: 20px; 
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }

        /* Colonna Info */
        .info-section { flex: 1.2; display: flex; flex-direction: column; }
        
        .back-link { 
            display: inline-flex; 
            align-items: center; 
            gap: 8px; 
            text-decoration: none; 
            color: #7f8c8d; 
            font-weight: bold; 
            margin-bottom: 20px;
            transition: 0.3s;
        }
        .back-link:hover { color: var(--dark-text); }

        h1 { font-size: 2.2rem; margin-bottom: 10px; color: var(--dark-text); }
        .author { font-size: 1.2rem; color: #7f8c8d; margin-bottom: 20px; }
        
        .price { 
            font-size: 2.8rem; 
            font-family: 'Arial Black', sans-serif; 
            color: #27ae60; 
            margin: 15px 0; 
        }

        /* Tabella Dettagli */
        .specs-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin: 20px 0; 
            background: #fdfcfb;
            border-radius: 15px;
            overflow: hidden;
        }
        .specs-table td { 
            padding: 12px 15px; 
            border-bottom: 1px solid #eee; 
            font-family: Arial, sans-serif; 
        }
        .specs-label { font-weight: bold; color: #95a5a6; width: 30%; }

        /* Bottoni */
        .actions { margin-top: auto; padding-top: 20px; }
        .btn-buy { 
            display: block; 
            background: #27ae60; 
            color: white; 
            padding: 18px; 
            text-align: center; 
            border-radius: 50px; 
            text-decoration: none; 
            font-weight: bold; 
            font-size: 1.1rem;
            margin-bottom: 15px; 
            transition: 0.3s;
        }
        .btn-buy:hover { background: #219150; transform: translateY(-3px); }
        
        .btn-chat { 
            display: block; 
            background: #34495e; 
            color: white; 
            padding: 18px; 
            text-align: center; 
            border-radius: 50px; 
            text-decoration: none; 
            font-weight: bold; 
            transition: 0.3s;
        }
        .btn-chat:hover { background: #2c3e50; transform: translateY(-3px); }

        /* Responsive */
        @media (max-width: 850px) {
            .container-detail { flex-direction: column; margin: 20px; padding: 20px; }
            .book-img { max-width: 100%; }
        }
    </style>
</head>
<body>

    <div class="container-detail">
        <div class="image-section">
            <?php $imgs = explode(",", $libro['immagine']); ?>
            <img src="<?php echo htmlspecialchars($imgs[0]); ?>" class="book-img" alt="Copertina">
        </div>

        <div class="info-section">
            <a href="compra.php" class="back-link">
                <i class="fa-solid fa-arrow-left"></i> Torna al Mercatino
            </a>

            <h1><?php echo htmlspecialchars($libro['titolo']); ?></h1>
            <p class="author">di <?php echo htmlspecialchars($libro['autore']); ?></p>
            
            <div class="price"><?php echo number_format($libro['prezzo'], 2); ?> €</div>

            <table class="specs-table">
                <tr>
                    <td class="specs-label">Materia</td>
                    <td><?php echo htmlspecialchars($libro['materia']); ?></td>
                </tr>
                <tr>
                    <td class="specs-label">ISBN</td>
                    <td><?php echo htmlspecialchars($libro['isbn'] ?? 'Non specificato'); ?></td>
                </tr>
                <tr>
                    <td class="specs-label">Condizioni</td>
                    <td><strong><?php echo htmlspecialchars($libro['condizioni'] ?? 'Ottime'); ?></strong></td>
                </tr>
                <tr>
                    <td class="specs-label">Venditore</td>
                    <td><i class="fa-solid fa-user"></i> <?php echo htmlspecialchars($libro['nome_venditore']); ?></td>
                </tr>
            </table>

            <div class="actions">
                <?php if(isset($_SESSION['id'])): ?>
                    <?php if($_SESSION['id'] != $libro['IdUtente']): ?>
                        <a href="checkout.php?id_libro=<?php echo $id_libro; ?>" class="btn-buy">
                            <i class="fa-solid fa-cart-shopping"></i> COMPRA ORA
                        </a>
                        <a href="chat.php?with=<?php echo $libro['IdUtente']; ?>&id_libro=<?php echo $id_libro; ?>" class="btn-chat">
                            <i class="fa-solid fa-comment-dots"></i> CONTATTA IL VENDITORE
                        </a>
                    <?php else: ?>
                        <div style="background: #f8f9fa; padding: 15px; border-radius: 15px; text-align: center; border: 1px dashed #ccc;">
                            <p style="color: #7f8c8d; font-family: Arial;">Questo è un tuo annuncio.</p>
                            <a href="my_list.php" style="color: var(--dark-text); font-weight: bold;">Gestisci i tuoi libri</a>
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