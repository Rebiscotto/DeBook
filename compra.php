<?php
session_start();
require_once 'db_connection.php';

// Controllo sessione (opzionale per la sola visualizzazione, ma consigliato)
$is_logged = isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true;

// Recupero parametri di ricerca dai filtri
$search = $_GET['search'] ?? '';
$materia_filtro = $_GET['materia'] ?? '';

// Costruzione della query con filtri
$query = "SELECT L.*, A.titolo, A.autore, A.materia, U.nome as venditore 
          FROM Libri L
          JOIN AnagraficaLibri A ON L.IdAnag = A.IdAnag
          JOIN Utenti U ON L.IdVenditore = U.IdUtente
          WHERE (A.titolo LIKE ? OR A.autore LIKE ?)";

$params = ["%$search%", "%$search%"];
$types = "ss";

if ($materia_filtro != '') {
    $query .= " AND A.materia = ?";
    $params[] = $materia_filtro;
    $types .= "s";
}

$query .= " ORDER BY L.IdLibro DESC"; // I più recenti prima

$stmt = $conn->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// Recupero elenco materie per il filtro dropdown
$materie_res = $conn->query("SELECT DISTINCT materia FROM AnagraficaLibri");
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Debook - Compra Libri</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .search-section { background: var(--accent-beige); width: 100%; padding: 30px; text-align: center; }
        .filter-bar { display: flex; justify-content: center; gap: 10px; margin-top: 15px; flex-wrap: wrap; }
        .filter-bar input, .filter-bar select { padding: 10px; border-radius: 10px; border: none; font-family: Arial; }
        
        .market-container { 
            display: grid; 
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); 
            gap: 25px; 
            width: 90%; 
            max-width: 1200px; 
            margin: 40px auto; 
        }

        .book-card { 
            background: white; 
            border-radius: 20px; 
            overflow: hidden; 
            box-shadow: 0 5px 15px rgba(0,0,0,0.08); 
            transition: 0.3s; 
        }
        .book-card:hover { transform: translateY(-5px); }
        .book-img { width: 100%; height: 200px; object-fit: cover; background: #f0f0f0; }
        .book-content { padding: 20px; }
        .book-title { font-size: 1.2rem; color: var(--dark-text); margin-bottom: 5px; }
        .book-info { font-family: Arial; font-size: 0.9rem; color: #666; margin-bottom: 15px; }
        .book-tag { 
            display: inline-block; 
            background: var(--bg-page); 
            padding: 5px 10px; 
            border-radius: 5px; 
            font-size: 0.8rem; 
            margin-right: 5px; 
        }
        .btn-view { 
            display: block; 
            background: var(--accent-beige); 
            text-align: center; 
            padding: 12px; 
            text-decoration: none; 
            color: var(--dark-text); 
            border-radius: 10px; 
            font-weight: bold; 
        }
    </style>
</head>
<body>
    <header class="header-nav">
        <a href="index.php" class="logo-link"><img src="immagini/tastologo.png" alt="Debook Logo"></a>
        <div style="font-family: Arial;">
            <?php if($is_logged): ?>
                <a href="dashboard.php" style="text-decoration: none; color: var(--dark-text); margin-right: 15px;">Dashboard</a>
            <?php else: ?>
                <a href="login.php" style="text-decoration: none; color: var(--dark-text); margin-right: 15px;">Accedi</a>
            <?php endif; ?>
        </div>
    </header>

    <section class="search-section">
        <h1>Trova i tuoi libri di testo</h1>
        <form class="filter-bar" method="GET" action="compra.php">
            <input type="text" name="search" placeholder="Cerca titolo o autore..." value="<?php echo htmlspecialchars($search); ?>" style="width: 300px;">
            <select name="materia">
                <option value="">Tutte le materie</option>
                <?php while($m = $materie_res->fetch_assoc()): ?>
                    <option value="<?php echo $m['materia']; ?>" <?php if($materia_filtro == $m['materia']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($m['materia']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <button type="submit" class="btn-submit" style="width: auto; padding: 10px 25px; margin-top: 0;">Filtra</button>
        </form>
    </section>

    <div class="market-container">
        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="book-card">
                    <img src="<?php echo htmlspecialchars($row['immagine']); ?>" alt="Copertina" class="book-img">
                    <div class="book-content">
                        <div class="book-tag"><?php echo htmlspecialchars($row['materia']); ?></div>
                        <h3 class="book-title"><?php echo htmlspecialchars($row['titolo']); ?></h3>
                        <p class="book-info">di <?php echo htmlspecialchars($row['autore']); ?></p>
                        <p class="book-info" style="font-size: 0.8rem;">
                            <i class="fa-solid fa-user"></i> Venditore: <?php echo htmlspecialchars($row['venditore']); ?>
                        </p>
                        <a href="book_details.php?id=<?php echo $row['IdLibro']; ?>" class="btn-view">Vedi Dettagli</a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="grid-column: 1/-1; text-align: center; font-family: Arial; margin-top: 50px;">
                Nessun libro trovato con i filtri selezionati.
            </p>
        <?php endif; ?>
    </div>
</body>
</html>