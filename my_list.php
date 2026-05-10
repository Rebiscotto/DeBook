<?php
session_start();
require_once 'db_connection.php';

// Protezione della pagina: accesso solo per utenti loggati
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: login.php");
    exit;
}

$id_utente = $_SESSION["id"];

// Query per recuperare solo i libri caricati dall'utente corrente
$query = "SELECT L.IdLibro, L.immagine, A.titolo, A.autore, A.materia 
          FROM Libri L
          JOIN AnagraficaLibri A ON L.IdAnag = A.IdAnag
          WHERE L.IdVenditore = ? 
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
    <title>Debook - I miei Libri</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .container { width: 90%; max-width: 1000px; margin: 40px auto; }
        .list-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        
        .book-item {
            background: white;
            padding: 20px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 15px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }
        .book-item img { width: 80px; height: 110px; object-fit: cover; border-radius: 10px; }
        .book-details { flex: 1; }
        .book-details h3 { color: var(--dark-text); margin-bottom: 5px; }
        .book-details p { font-family: Arial; font-size: 0.9rem; color: #666; }

        .actions { display: flex; gap: 10px; }
        .btn-delete { 
            background: #ffeded; 
            color: #d32f2f; 
            padding: 10px 15px; 
            border-radius: 10px; 
            text-decoration: none; 
            font-size: 0.9rem;
            border: 1px solid #ffcdd2;
        }
        .btn-delete:hover { background: #ffe0e0; }
    </style>
</head>
<body>
    <header class="header-nav">
        <a href="index.php" class="logo-link"><img src="immagini/tastologo.png" alt="Debook Logo"></a>
        <div style="font-family: Arial;">
            <a href="dashboard.php" style="text-decoration: none; color: var(--dark-text); margin-right: 15px;">Dashboard</a>
            <a href="logout.php" style="color: #d32f2f; text-decoration: none;">Esci</a>
        </div>
    </header>

    <div class="container">
        <div class="list-header">
            <h1>Gestisci i tuoi annunci</h1>
            <a href="vendi.php" class="btn-submit" style="width: auto; padding: 10px 20px; font-size: 1rem; text-decoration: none;">
                <i class="fa-solid fa-plus"></i> Nuovo Annuncio
            </a>
        </div>

        <?php if(isset($_GET['msg'])) echo "<p style='color: green; font-family: Arial; margin-bottom: 20px;'>".htmlspecialchars($_GET['msg'])."</p>"; ?>

        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="book-item">
                    <img src="<?php echo htmlspecialchars($row['immagine']); ?>" alt="Copertina">
                    <div class="book-details">
                        <h3><?php echo htmlspecialchars($row['titolo']); ?></h3>
                        <p><?php echo htmlspecialchars($row['autore']); ?> | <strong><?php echo htmlspecialchars($row['materia']); ?></strong></p>
                    </div>
                    <div class="actions">
                        <a href="book_details.php?id=<?php echo $row['IdLibro']; ?>" style="color: #0288d1; font-family: Arial; font-size: 0.9rem; margin-right: 10px; text-decoration: none;">Visualizza</a>
                        <a href="elimina_libro.php?id=<?php echo $row['IdLibro']; ?>" class="btn-delete" onclick="return confirm('Sei sicuro di voler eliminare questo annuncio?');">
                            <i class="fa-solid fa-trash"></i> Elimina
                        </a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div style="text-align: center; padding: 50px; background: white; border-radius: 20px;">
                <p style="font-family: Arial; color: #777;">Non hai ancora caricato nessun libro.</p>
                <a href="vendi.php" style="color: var(--dark-text); font-weight: bold;">Inizia ora!</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>