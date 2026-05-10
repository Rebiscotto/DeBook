<?php
session_start();
require_once 'db_connection.php';

// Verifichiamo se l'utente è loggato per personalizzare l'header
$is_logged = isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true;

// Query per recuperare i libri, l'anagrafica e il nome del venditore
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
    <title>Debook - Compra</title>
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
        }

        .book-card:hover { transform: translateY(-10px); }

        .book-card img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            border-radius: 15px;
            margin-bottom: 15px;
        }

        .materia-badge {
            background: #e2e2e2;
            display: inline-block;
            padding: 5px 15px;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: bold;
            margin-bottom: 10px;
        }

        /* Stile per il PREZZO nella card */
        .price-tag-card {
            font-size: 1.5rem;
            font-family: 'Arial Black', sans-serif;
            color: var(--dark-text);
            margin: 15px 0;
        }

        .seller-note {
            font-family: Arial;
            font-size: 0.85rem;
            color: #777;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

    <header class="header-nav">
        <a href="index.php" class="logo-link"><img src="immagini/tastologo.png" alt="Debook Logo"></a>
        <div class="nav-right">
            <a href="index.php" style="text-decoration:none; color:var(--dark-text); font-family:Arial; margin-right:20px;">Home</a>
            <a href="vendi.php" class="btn-submit" style="padding: 10px 20px; font-size:0.8rem;">VENDI UN LIBRO</a>
        </div>
    </header>

    <div class="container">
        <h1 style="font-family:'Arial Black'; text-transform:uppercase; text-align:center;">Mercatino Libri</h1>
        
        <div class="market-grid">
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <div class="book-card">
                        <img src="<?php echo htmlspecialchars($row['immagine']); ?>" alt="Libro">
                        
                        <div>
                            <span class="materia-badge"><?php echo htmlspecialchars($row['materia']); ?></span>
                        </div>
                        
                        <h3 style="margin-bottom:5px;"><?php echo htmlspecialchars($row['titolo']); ?></h3>
                        <p style="font-family:Arial; color:#666; font-size:0.9rem; margin-bottom:10px;">di <?php echo htmlspecialchars($row['autore']); ?></p>
                        
                        <p class="seller-note">
                            <i class="fa-solid fa-user"></i> Venditore: <?php echo htmlspecialchars($row['nome_venditore']); ?>
                        </p>

                        <div class="price-tag-card">
                            <?php echo number_format($row['prezzo'], 2); ?> €
                        </div>

                        <a href="book_details.php?id=<?php echo $row['IdLibro']; ?>" class="btn-submit" style="display:block; text-decoration:none; margin-top:auto;">Vedi Dettagli</a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="text-align:center; grid-column: 1/-1; padding:50px;">Nessun libro disponibile al momento.</p>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>