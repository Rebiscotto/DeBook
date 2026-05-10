<?php
session_start();
require_once 'db_connection.php';

// 1. GESTIONE FILTRI E RICERCA
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$materia_filter = isset($_GET['materia']) ? $conn->real_escape_string($_GET['materia']) : '';

// 2. COSTRUZIONE QUERY DINAMICA
$query = "SELECT L.*, A.titolo, A.autore, A.materia, U.nome as nome_venditore 
          FROM Libri L 
          JOIN AnagraficaLibri A ON L.IdAnag = A.IdAnag 
          JOIN Utenti U ON L.IdVenditore = U.IdUtente 
          WHERE L.stato = 'disponibile'"; // Trucco per aggiungere condizioni facilmente

if ($search != '') {
    $query .= " AND (A.titolo LIKE '%$search%' OR A.autore LIKE '%$search%')";
}

if ($materia_filter != '') {
    $query .= " AND A.materia = '$materia_filter'";
}

$query .= " ORDER BY L.IdLibro DESC";
$result = $conn->query($query);

// Recupero materie per il menu a tendina (per averle dinamiche)
$materie_query = "SELECT DISTINCT materia FROM AnagraficaLibri ORDER BY materia ASC";
$materie_result = $conn->query($materie_query);
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
        .container { max-width: 1200px; margin: 0 auto; padding: 40px 20px; }

        /* BARRA DI RICERCA E FILTRI */
        .filter-section {
            background: white;
            padding: 25px;
            border-radius: 25px;
            box-shadow: var(--shadow);
            margin-bottom: 40px;
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            align-items: center;
        }
        .filter-section input, .filter-section select {
            padding: 12px 20px;
            border-radius: 50px;
            border: 2px solid #eee;
            outline: none;
            font-size: 1rem;
            font-family: Arial;
        }
        .filter-section input[type="text"] { flex: 2; min-width: 250px; }
        .filter-section select { flex: 1; min-width: 150px; }
        .btn-filter {
            background: var(--dark-text);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 50px;
            cursor: pointer;
            font-weight: bold;
            transition: 0.3s;
        }
        .btn-filter:hover { opacity: 0.8; }

        /* GRID LIBRI */
        .market-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 30px;
        }
        .book-card {
            background: white; padding: 20px; border-radius: 25px;
            box-shadow: var(--shadow); text-align: center;
            transition: 0.3s; display: flex; flex-direction: column;
            border: 1px solid #eee;
        }
        .book-card:hover { transform: translateY(-10px); }
        .book-card img { width: 100%; height: 250px; object-fit: cover; border-radius: 15px; margin-bottom: 15px; }
        
        .materia-badge { background: #f0f0f0; padding: 5px 12px; border-radius: 50px; font-size: 0.75rem; color: #555; font-weight: bold; }
        .price-tag { font-size: 1.5rem; font-family: 'Arial Black'; color: var(--dark-text); margin: 15px 0; }
        .seller-link { color: #888; text-decoration: none; font-size: 0.85rem; }
        .seller-link:hover { text-decoration: underline; color: var(--dark-text); }
    </style>
</head>
<body>

    <header class="header-nav">
        <a href="index.php" class="logo-link"><img src="immagini/tastologo.png" alt="Logo"></a>
        <div class="nav-right">
            <a href="vendi.php" class="btn-submit" style="padding: 10px 20px;">VENDI UN LIBRO</a>
        </div>
    </header>

    <div class="container">
        <h1 style="font-family:'Arial Black'; text-align:center; margin-bottom:30px;">MERCATINO DEBOOK</h1>

        <form method="GET" action="compra.php" class="filter-section">
            <input type="text" name="search" placeholder="Cerca titolo o autore..." value="<?php echo htmlspecialchars($search); ?>">
            
            <select name="materia">
                <option value="">Tutte le materie</option>
                <?php while($mat = $materie_result->fetch_assoc()): ?>
                    <option value="<?php echo htmlspecialchars($mat['materia']); ?>" <?php if($materia_filter == $mat['materia']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($mat['materia']); ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <button type="submit" class="btn-filter"><i class="fa-solid fa-magnifying-glass"></i> CERCA</button>
            <?php if($search != '' || $materia_filter != ''): ?>
                <a href="compra.php" style="color:red; text-decoration:none; font-size:0.8rem;">Resetta</a>
            <?php endif; ?>
        </form>
        
        <div class="market-grid">
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <div class="book-card">
                        <?php 
                            $immagini = explode(',', $row['immagine']);
                            $copertina = !empty($immagini[0]) ? $immagini[0] : 'immagini/placeholder.jpg';
                        ?>
                        <img src="<?php echo htmlspecialchars($copertina); ?>" alt="Copertina">
                        
                        <div><span class="materia-badge"><?php echo htmlspecialchars($row['materia']); ?></span></div>
                        
                        <h3 style="margin: 10px 0 5px; font-size:1.1rem;"><?php echo htmlspecialchars($row['titolo']); ?></h3>
                        <p style="color:#666; font-size:0.9rem;">di <?php echo htmlspecialchars($row['autore']); ?></p>
                        
                        <p style="margin-top:10px;">
                            <a href="profilo.php?id=<?php echo $row['IdVenditore']; ?>" class="seller-link">
                                <i class="fa-solid fa-user"></i> <?php echo htmlspecialchars($row['nome_venditore']); ?>
                            </a>
                        </p>

                        <div class="price-tag"><?php echo number_format($row['prezzo'], 2); ?> €</div>

                        <a href="book_details.php?id=<?php echo $row['IdLibro']; ?>" class="btn-submit" style="display:block; text-decoration:none; margin-top:auto;">DETTAGLI</a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div style="grid-column: 1/-1; text-align:center; padding:50px;">
                    <i class="fa-solid fa-face-frown" style="font-size:3rem; color:#ccc;"></i>
                    <p style="margin-top:20px; color:#777;">Nessun libro trovato con questi filtri.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>