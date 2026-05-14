<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: login.php");
    exit;
}

$id_utente = $_SESSION["id"];

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debook - I miei Libri</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* RESET E VARIABILI LOCALI PER EVITARE CONFLITTI CON STYLE.CSS */
        body { background-color: #f4f7f6; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .container { width: 95%; max-width: 1000px; margin: 40px auto; }
        
        .list-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        
        /* CARD LIBRO - FORZIAMO IL LAYOUT */
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
            visibility: visible !important;
            opacity: 1 !important;
        }
        
        .book-content {
            display: flex;
            align-items: center;
            gap: 20px;
            flex: 1;
            min-width: 0;
        }

        .book-item img { 
            width: 70px; 
            height: 100px; 
            object-fit: cover; 
            border-radius: 12px; 
            flex-shrink: 0;
        }

        .book-details { flex: 1; min-width: 0; }
        .book-details h3 { 
            color: #333; 
            margin: 0 0 5px 0; 
            font-size: 1.1rem;
            word-wrap: break-word;
        }
        .book-details p { font-size: 0.85rem; color: #666; margin: 0; }

        /* SEZIONE AZIONI - SEMPRE VISIBILE */
        .actions { 
            display: flex !important; /* Forza la visibilità */
            gap: 10px !important; 
            align-items: center !important; 
            flex-shrink: 0 !important;
            visibility: visible !important;
            opacity: 1 !important;
        }
        
        .btn-action {
            text-decoration: none !important;
            font-size: 0.8rem !important;
            font-weight: bold !important;
            padding: 8px 12px !important;
            border-radius: 50px !important;
            display: inline-block !important; /* Evita che spariscano */
            text-align: center;
            white-space: nowrap;
            transition: 0.2s;
        }

        .btn-view { background: #f0f7ff; color: #0288d1; border: 1px solid #0288d1; }
        .btn-edit { background: #fffdf0; color: #f39c12; border: 1px solid #f39c12; }
        .btn-cancella { background: #fff5f5; color: #ff0000; border: 1px solid #ff0000; }

        .btn-action:hover { opacity: 0.7; transform: translateY(-2px); }

        .header-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 5%;
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        /* --- RESPONSIVE MOBILE --- */
        @media (max-width: 768px) {
            .book-item { flex-direction: column !important; text-align: center; }
            .book-content { flex-direction: column; }
            .actions { 
                width: 100% !important; 
                flex-direction: column !important; 
                border-top: 1px solid #eee;
                padding-top: 15px;
            }
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
            <h1 style="margin:0;">I miei annunci</h1>
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
                        <a href="book_details.php?id=<?php echo $row['IdLibro']; ?>" class="btn-action btn-view">
                            <i class="fa-solid fa-eye"></i> Visualizza
                        </a>
                        <a href="modifica_libro.php?id=<?php echo $row['IdLibro']; ?>" class="btn-action btn-edit">
                            <i class="fa-solid fa-pen"></i> Modifica
                        </a>
                        <a href="elimina_libro.php?id=<?php echo $row['IdLibro']; ?>" 
                           onclick="return confirm('Sei sicuro?');"
                           class="btn-action btn-cancella">
                            <i class="fa-solid fa-trash"></i> Elimina
                        </a>
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