<?php
session_start();
require_once 'db_connection.php';

// Controllo sicurezza
if (!isset($_SESSION["loggedin"])) {
    header("Location: login.php");
    exit;
}

$id_utente = $_SESSION["id"];

// Query: recuperiamo i libri acquistati dall'utente loggato
$query = "SELECT L.IdLibro, L.prezzo, L.immagine, A.titolo, U.nome as venditore, U.IdUtente as IdVenditore
          FROM Libri L
          JOIN AnagraficaLibri A ON L.IdAnag = A.IdAnag
          JOIN Utenti U ON L.IdVenditore = U.IdUtente
          WHERE L.IdAcquirente = ?
          ORDER BY L.IdLibro DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_utente);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debook - I miei Acquisti</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: var(--bg-page); margin: 0; padding: 0; font-family: Arial, sans-serif; }
        .container { width: 90%; max-width: 900px; margin: 40px auto; }
        
        .order-card { 
            background: white; 
            padding: 20px; 
            border-radius: 25px; 
            margin-bottom: 20px; 
            display: flex; 
            gap: 20px;
            align-items: center;
            box-shadow: 0 8px 20px rgba(0,0,0,0.05);
            transition: 0.3s;
        }
        
        .book-thumb { 
            width: 80px; 
            height: 110px; 
            object-fit: cover; 
            border-radius: 12px; 
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .order-info { flex: 1; }
        .order-info h3 { margin: 0 0 5px 0; color: var(--dark-text); font-size: 1.2rem; }
        .order-info p { font-size: 0.9rem; color: #666; margin: 3px 0; }
        
        .order-meta { text-align: right; min-width: 140px; }
        
        .status-badge { 
            display: inline-block; 
            padding: 6px 15px; 
            background: #e8f5e9; 
            color: #2e7d32; 
            border-radius: 50px; 
            font-size: 0.75rem; 
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .price-tag { font-size: 1.5rem; font-weight: bold; color: #27ae60; margin-bottom: 10px; display: block; }
        
        .header-nav { display: flex; justify-content: space-between; align-items: center; padding: 15px 5%; background: white; box-shadow: var(--shadow); }
        .logo-link img { height: 35px; }

        /* --- MEDIA QUERIES PER MOBILE --- */
        @media (max-width: 768px) {
            .container { width: 95%; margin-top: 20px; }
            
            .order-card { 
                flex-direction: column; /* Impilamento verticale */
                text-align: center; 
                padding: 25px;
            }

            .book-thumb { 
                width: 120px; 
                height: 160px; 
                margin-bottom: 10px;
            }

            .order-meta { 
                width: 100%; 
                text-align: center; 
                border-top: 1px solid #eee;
                padding-top: 15px;
                margin-top: 10px;
            }

            .price-tag { font-size: 1.8rem; }

            .btn-contatta-mobile {
                width: 100%;
                display: block;
                padding: 15px !important;
                font-size: 1.1rem !important;
                border-radius: 50px !important;
            }
        }
    </style>
</head>
<body>
    <header class="header-nav">
        <a href="index.php" class="logo-link"><img src="immagini/tastologo.png" alt="Debook Logo"></a>
        <a href="dashboard.php" style="text-decoration: none; color: var(--dark-text); font-weight: bold;">
            <i class="fa-solid fa-gauge-high"></i> <span class="hide-mobile">Dashboard</span>
        </a>
    </header>

    <div class="container">
        <h1 style="text-align: center; margin-bottom: 10px; font-family: 'Arial Black', sans-serif;">I miei Acquisti</h1>
        <p style="text-align: center; color: #777; margin-bottom: 40px;">
            Qui trovi tutti i libri che hai comprato su Debook.
        </p>

        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="order-card">
                    <?php 
                        $imgs = explode(',', $row['immagine']);
                        $copertina = !empty($imgs[0]) ? $imgs[0] : 'immagini/placeholder.jpg';
                    ?>
                    <img src="<?php echo htmlspecialchars($copertina); ?>" class="book-thumb" alt="Libro">
                    
                    <div class="order-info">
                        <div class="status-badge"><i class="fa-brands fa-paypal"></i> Pagato</div>
                        <h3><?php echo htmlspecialchars($row['titolo']); ?></h3>
                        <p>Venditore: <strong><?php echo htmlspecialchars($row['venditore']); ?></strong></p>
                        <p style="color: #27ae60;"><i class="fa-solid fa-circle-check"></i> Transazione completata</p>
                    </div>

                    <div class="order-meta">
                        <span class="price-tag"><?php echo number_format($row['prezzo'], 2); ?>€</span>
                        <a href="chat.php?with=<?php echo $row['IdVenditore']; ?>" class="btn-submit btn-contatta-mobile" style="text-decoration: none; padding: 10px 20px; border-radius: 12px; font-weight: bold;">
                            <i class="fa-solid fa-comments"></i> Contatta
                        </a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div style="text-align: center; padding: 60px; background: white; border-radius: 30px; box-shadow: var(--shadow);">
                <i class="fa-solid fa-cart-shopping" style="font-size: 3rem; color: #ccc; margin-bottom: 20px; display: block;"></i>
                <p style="color: #777; font-size: 1.1rem;">Non hai ancora effettuato acquisti.</p>
                <br>
                <a href="compra.php" class="btn-submit" style="padding: 15px 30px; text-decoration: none; border-radius: 50px;">Sfoglia il mercatino</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>