<?php
session_start();
require_once 'db_connection.php';

// Protezione della pagina: accesso solo per utenti loggati
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: login.php");
    exit;
}

$id_utente = $_SESSION["id"];

// MODIFICA: Recuperiamo solo i libri con stato 'disponibile'. 
// Quando un libro viene venduto, sparisce da questa gestione.
$query = "SELECT L.IdLibro, L.immagine, A.titolo, A.autore, A.materia 
          FROM Libri L
          JOIN AnagraficaLibri A ON L.IdAnag = A.IdAnag
          WHERE L.IdVenditore = ? AND L.stato = 'disponibile'
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
        body { background-color: var(--bg-page); }
        .container { width: 90%; max-width: 1000px; margin: 40px auto; }
        .list-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        
        .book-item {
            background: white;
            padding: 20px;
            border-radius: 25px;
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 15px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.05);
            transition: 0.3s;
        }
        .book-item:hover { transform: translateY(-3px); }
        
        .book-item img { width: 80px; height: 110px; object-fit: cover; border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .book-details { flex: 1; }
        .book-details h3 { color: var(--dark-text); margin-bottom: 5px; font-size: 1.2rem; }
        .book-details p { font-family: Arial; font-size: 0.9rem; color: #666; }

        .actions { display: flex; gap: 15px; align-items: center; }
        
        .btn-view { color: #0288d1; font-family: Arial; font-size: 0.95rem; text-decoration: none; font-weight: bold; }
        .btn-view:hover { text-decoration: underline; }

        .btn-delete { 
            background: #ffeded; 
            color: #d32f2f; 
            padding: 10px 18px; 
            border-radius: 12px; 
            text-decoration: none; 
            font-size: 0.9rem;
            border: 1px solid #ffcdd2;
            font-weight: bold;
            transition: 0.2s;
        }
        .btn-delete:hover { background: #d32f2f; color: white; }

        .header-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 40px;
            background: white;
            box-shadow: var(--shadow);
        }
    </style>
</head>
<body>
    <header class="header-nav">
        <a href="index.php" class="logo-link"><img src="immagini/tastologo.png" alt="Debook Logo" style="height: 40px;"></a>
        <div style="font-family: Arial;">
            <a href="dashboard.php" style="text-decoration: none; color: var(--dark-text); font-weight: bold;">
                <i class="fa-solid fa-gauge-high"></i> Dashboard
            </a>
        </div>
    </header>

    <div class="container">
        <div class="list-header">
            <h1>Libri in vendita</h1>
            <a href="vendi.php" class="btn-submit" style="width: auto; padding: 12px 25px; font-size: 1rem; text-decoration: none; border-radius: 50px;">
                <i class="fa-solid fa-plus"></i> Nuovo Annuncio
            </a>
        </div>

        <?php if(isset($_GET['msg'])) echo "<p style='color: #27ae60; font-family: Arial; font-weight: bold; margin-bottom: 20px; background: #e8f5e9; padding: 10px; border-radius: 10px;'>".htmlspecialchars($_GET['msg'])."</p>"; ?>

        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="book-item">
                    <?php 
                        $immagini = explode(',', $row['immagine']);
                        $prima_img = $immagini[0];
                    ?>
                    <img src="<?php echo htmlspecialchars($prima_img); ?>" alt="Copertina">
                    <div class="book-details">
                        <h3><?php echo htmlspecialchars($row['titolo']); ?></h3>
                        <p><?php echo htmlspecialchars($row['autore']); ?> | <strong><?php echo htmlspecialchars($row['materia']); ?></strong></p>
                    </div>
                    <div class="actions">
    <a href="book_details.php?id=<?php echo $row['IdLibro']; ?>" class="btn-view">Visualizza</a>
    <a href="modifica_libro.php?id=<?php echo $row['IdLibro']; ?>" style="color: #f39c12; font-family: Arial; font-size: 0.95rem; margin-right: 10px; text-decoration: none; font-weight: bold;">Modifica</a>
    
    <a href="elimina_libro.php?id=<?php echo $row['IdLibro']; ?>" class="btn-delete" onclick="return confirm('Sei sicuro?');">
        <i class="fa-solid fa-trash-can"></i> Elimina
    </a>
</div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div style="text-align: center; padding: 60px; background: white; border-radius: 30px; box-shadow: var(--shadow);">
                <i class="fa-solid fa-book-open" style="font-size: 3rem; color: #eee; margin-bottom: 20px;"></i>
                <p style="font-family: Arial; color: #777; font-size: 1.1rem;">Non hai annunci attivi al momento.</p>
                <br>
                <a href="vendi.php" style="color: var(--dark-text); font-weight: bold; text-decoration: none;">Carica il tuo primo libro →</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>