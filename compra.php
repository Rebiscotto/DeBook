<?php
session_start();
require_once 'db_connection.php';

// Verifichiamo se l'utente è loggato
$is_logged = isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true;

// Query per recuperare i libri, l'anagrafica e il nome del venditore
// Selezioniamo L.* per avere immagine, prezzo e condizione
$query = "SELECT L.*, A.titolo, A.autore, A.materia, U.nome as nome_venditore 
          FROM Libri L 
          JOIN AnagraficaLibri A ON L.IdAnag = A.IdAnag 
          JOIN Utenti U ON L.IdVenditore = U.IdUtente 
          ORDER BY L.IdLibro DESC";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debook - Mercatino</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        body { background-color: var(--bg-page); }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .market-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 30px;
            margin-top: 30px;
        }

        .book-card {
            background: white;
            padding: 20px;
            border-radius: 25px;
            box-shadow: var(--shadow);
            text-align: center;
            transition: transform 0.3s;
            display: flex;
            flex-direction: column;
            border: 1px solid #eee;
        }

        .book-card:hover { transform: translateY(-10px); }

        /* Container immagine per mantenere le proporzioni */
        .book-card img {
            width: 100%;
            height: 250px;
            object-fit: cover; /* Fondamentale per non deformare la copertina */
            border-radius: 15px;
            margin-bottom: 15px;
            background-color: #f9f9f9;
        }

        .materia-badge {
            background: #f0f0f0;
            display: inline-block;
            padding: 5px 12px;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: bold;
            margin-bottom: 10px;
            color: #555;
        }

        .cond-label {
            font-size: 0.8rem;
            color: #d2691e;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .price-tag {
            font-size: 1.5rem;
            font-family: 'Arial Black', sans-serif;
            color: var(--dark-text);
            margin: 15px 0;
        }

        .seller-info {
            font-family: Arial;
            font-size: 0.85rem;
            color: #888;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

    <header class="header-nav">
        <a href="index.php" class="logo-link"><img src="immagini/tastologo.png" alt="Debook Logo"></a>
        <div class="nav-right">
            <a href="index.php" style="text-decoration:none; color:black; margin-right:20px;">Home</a>
            <a href="vendi.php" class="btn-submit" style="padding: 10px 20px;">VENDI</a>
        </div>
    </header>

    <div class="container">
        <h1 style="font-family:'Arial Black'; text-align:center; text-transform:uppercase;">Libri Disponibili</h1>
        
        <div class="market-grid">
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <div class="book-card">
                        
                        <?php 
                            // GESTIONE COPERTINA: prendiamo solo la prima immagine della lista
                            $immagini = explode(',', $row['immagine']);
                            $copertina = !empty($immagini[0]) ? $immagini[0] : 'immagini/placeholder.jpg';
                        ?>
                        
                        <img src="<?php echo htmlspecialchars($copertina); ?>" alt="Libro">
                        
                        <div>
                            <span class="materia-badge"><?php echo htmlspecialchars($row['materia']); ?></span>
                        </div>

                        <div class="cond-label">
                            <i class="fa-solid fa-star"></i> <?php echo htmlspecialchars($row['condizione']); ?>
                        </div>
                        
                        <h3 style="margin-bottom:5px; font-size:1.1rem;"><?php echo htmlspecialchars($row['titolo']); ?></h3>
                        <p style="font-family:Arial; color:#666; font-size:0.9rem; margin-bottom:10px;">di <?php echo htmlspecialchars($row['autore']); ?></p>
                        
                        <p class="seller-info">
                        <i class="fa-solid fa-user"></i> 
                        <a href="profilo.php?id=<?php echo $row['IdVenditore']; ?>" style="color:#888; text-decoration:none;">
                        <?php echo htmlspecialchars($row['nome_venditore']); ?>
                        </a>
                        </p>

                        <div class="price-tag">
                            <?php echo number_format($row['prezzo'], 2); ?> €
                        </div>

                        <a href="book_details.php?id=<?php echo $row['IdLibro']; ?>" class="btn-submit" style="display:block; text-decoration:none; margin-top:auto;">VEDI DETTAGLI</a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div style="grid-column: 1/-1; text-align:center; padding:50px;">
                    <i class="fa-solid fa-book-open" style="font-size:3rem; color:#ccc;"></i>
                    <p style="margin-top:20px; font-family:Arial; color:#777;">Non ci sono ancora libri in vendita. Sii il primo!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>