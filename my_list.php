<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: login.php");
    exit;
}

$id_utente = $_SESSION["id"];

// 1. Query per la lista dei libri (Filtra per 'disponibile')
$query = "SELECT L.IdLibro, L.immagine, A.titolo, A.autore, A.materia 
          FROM Libri L
          JOIN AnagraficaLibri A ON L.IdAnag = A.IdAnag
          WHERE L.IdVenditore = ? AND L.stato = 'disponibile'
          ORDER BY L.IdLibro DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_utente);
$stmt->execute();
$result = $stmt->get_result();

// 2. Query per il contatore (Filtra per 'disponibile' per correggere il segnalino)
$query_count = "SELECT COUNT(*) as totale FROM Libri WHERE IdVenditore = ? AND stato = 'disponibile'";
$stmt_count = $conn->prepare($query_count);
$stmt_count->bind_param("i", $id_utente);
$stmt_count->execute();
$res_count = $stmt_count->get_result()->fetch_assoc();
$totale_libri = $res_count['totale'];
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debook - I miei Libri</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f7f6; margin: 0; padding: 0; font-family: 'Segoe UI', sans-serif; }
        .container { width: 95%; max-width: 1000px; margin: 40px auto; }
        
        /* Badge per il contatore */
        .count-badge {
            background: #333;
            color: white;
            padding: 2px 10px;
            border-radius: 10px;
            font-size: 0.9rem;
            vertical-align: middle;
            margin-left: 10px;
        }

        .list-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        
        .book-item {
            background: white !important;
            padding: 20px !important;
            border-radius: 25px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            gap: 20px !important;
            margin-bottom: 20px !important;
            box-shadow: 0 8px 20px rgba(0,0,0,0.05) !important;
        }
        
        .book-content { display: flex; align-items: center; gap: 20px; flex: 1; min-width: 0; }
        .book-item img { width: 70px; height: 100px; object-fit: cover; border-radius: 12px; flex-shrink: 0; }
        .book-details { flex: 1; min-width: 0; }
        .book-details h3 { color: #333; margin: 0 0 5px 0; font-size: 1.1rem; word-wrap: break-word; }
        .book-details p { font-size: 0.85rem; color: #666; margin: 0; }

        .actions { display: flex !important; gap: 10px !important; align-items: center !important; flex-shrink: 0 !important; }
        .btn-action { text-decoration: none !important; font-size: 0.8rem !important; font-weight: bold !important; padding: 8px 12px !important; border-radius: 50px !important; display: inline-block !important; text-align: center; white-space: nowrap; transition: 0.2s; }
        .btn-view { background: #f0f7ff; color: #0288d1; border: 1px solid #0288d1; }
        .btn-edit { background: #fffdf0; color: #f39c12; border: 1px solid #f39c12; }
        .btn-cancella { background: #fff5f5; color: #ff0000; border: 1px solid #ff0000; }

        @media (max-width: 768px) {
            .book-item { flex-direction: column !important; text-align: center; }
            .book-content { flex-direction: column; }
            .actions { width: 100% !important; flex-direction: column !important; border-top: 1px solid #eee; padding-top: 15px; }
            .btn-action { width: 100% !important; box-sizing: border-box; }
        }
    </style>
</head>
<body>
    <header class="header-nav">
        <a href="index.php"><img src="immagini/tastologo.png" alt="Logo" style="height: 35px;"></a>
        <a href="dashboard.php" style="text-decoration: none; color: #333; font-weight: bold;">
            <i class="fa-solid fa-gauge-high"></i> Dashboard
        </a>
    </header>

    <div class="container">
        <div class="list-header">
            <h1 style="margin:0;">
                I miei annunci 
                <span class="count-badge"><?php echo $totale_libri; ?></span>
            </h1>
            <a href="vendi.php" style="background:#333; color:white; padding:10px 20px; text-decoration:none; border-radius:50px; font-weight:bold;">
                <i class="fa-solid fa-plus"></i> Nuovo
            </a>
        </div>

        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="book-item">
                    <div class="book-content">
                        <?php 
                            $immagini = explode(',', $row['immagine']);
                            $prima_img = !empty($immagini[0]) ? $immagini[0] : 'immagini/placeholder.jpg';
                        ?>
                        <img src="<?php echo htmlspecialchars($prima_img); ?>" alt="Copertina">
                        <div class="book-details">
                            <h3><?php echo htmlspecialchars($row['titolo']); ?></h3>
                            <p><?php echo htmlspecialchars($row['autore']); ?> | <strong><?php echo htmlspecialchars($row['materia']); ?></strong></p>
                        </div>
                    </div>
                    <div class="actions">
                        <a href="book_details.php?id=<?php echo $row['IdLibro']; ?>" class="btn-action btn-view"><i class="fa-solid fa-eye"></i> Visualizza</a>
                        <a href="modifica_libro.php?id=<?php echo $row['IdLibro']; ?>" class="btn-action btn-edit"><i class="fa-solid fa-pen"></i> Modifica</a>
                        <a href="elimina_libro.php?id=<?php echo $row['IdLibro']; ?>" onclick="return confirm('Sei sicuro?');" class="btn-action btn-cancella"><i class="fa-solid fa-trash"></i> Elimina</a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div style="text-align: center; padding: 40px; background: white; border-radius: 20px;">
                <p>Nessun annuncio trovato.</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>