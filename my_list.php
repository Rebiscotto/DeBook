<?php
session_start();
require_once 'db_connection.php';

// Protezione della pagina
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: login.php");
    exit;
}

$id_utente = $_SESSION["id"];

// Recuperiamo solo i libri con stato 'disponibile'
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
        body { background-color: var(--bg-page); margin: 0; padding: 0; font-family: Arial, sans-serif; }
        .container { width: 95%; max-width: 900px; margin: 40px auto; }
        
        .list-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        
        /* CARD LIBRO */
        .book-item {
            background: white;
            padding: 20px;
            border-radius: 25px;
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 20px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.05);
            transition: 0.3s;
        }
        
        .book-item img { 
            width: 80px; 
            height: 110px; 
            object-fit: cover; 
            border-radius: 15px; 
            flex-shrink: 0;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .book-details { flex: 1; min-width: 0; }
        .book-details h3 { 
            color: var(--dark-text); 
            margin: 0 0 5px 0; 
            font-size: 1.2rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .book-details p { font-size: 0.9rem; color: #666; margin: 0; }

        /* AZIONI (Tasti) */
        .actions { 
            display: flex; 
            gap: 12px; 
            align-items: center; 
            flex-shrink: 0;
        }
        
        .btn-action {
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: bold;
            padding: 8px 15px;
            border-radius: 50px;
            transition: 0.2s;
            text-align: center;
        }

        .btn-view { background: #f0f7ff; color: #0288d1; border: 1px solid #0288d1; }
        .btn-edit { background: #fffdf0; color: #f39c12; border: 1px solid #f39c12; }
        .btn-delete { background: #fff5f5; color: #ff0000; border: 1px solid #ff0000; }

        .btn-action:hover { opacity: 0.7; transform: translateY(-2px); }

        /* HEADER NAV */
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
            .book-item { 
                flex-direction: column; 
                text-align: center; 
                padding: 25px;
            }
            
            .book-item img {
                width: 120px;
                height: 160px;
                margin-bottom: 10px;
            }

            .actions { 
                width: 100%; 
                flex-direction: column; 
                gap: 10px;
                margin-top: 15px;
                padding-top: 15px;
                border-top: 1px solid #eee;
            }

            .btn-action {
                width: 100%;
                padding: 12px;
                font-size: 1rem;
            }
            
            .book-details h3 {
                white-space: normal; /* Permette al titolo di andare a capo su mobile se lungo */
                font-size: 1.3rem;
            }
        }
    </style>
</head>
<body>
    <header class="header-nav">
        <a href="index.php"><img src="immagini/tastologo.png" alt="Debook Logo" style="height: 35px;"></a>
        <div>
            <a href="dashboard.php" style="text-decoration: none; color: var(--dark-text); font-weight: bold;">
                <i class="fa-solid fa-gauge-high"></i> Dashboard
            </a>
        </div>
    </header>

    <div class="container">
        <div class="list-header">
            <h1>I miei annunci</h1>
            <a href="vendi.php" class="btn-submit" style="width: auto; padding: 10px 20px; text-decoration: none; border-radius: 50px;">
                <i class="fa-solid fa-plus"></i> Nuovo
            </a>
        </div>

        <?php if(isset($_GET['msg'])): ?>
            <p style='color: #27ae60; background: #e8f5e9; padding: 15px; border-radius: 15px; font-weight: bold; margin-bottom: 25px;'>
                <i class="fa-solid fa-check-circle"></i> <?php echo htmlspecialchars($_GET['msg']); ?>
            </p>
        <?php endif; ?>

        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="book-item">
                    <?php 
                        $immagini = explode(',', $row['immagine']);
                        $prima_img = !empty($immagini[0]) ? $immagini[0] : 'immagini/placeholder.jpg';
                    ?>
                    <img src="<?php echo htmlspecialchars($prima_img); ?>" alt="Copertina">
                    
                    <div class="book-details">
                        <h3><?php echo htmlspecialchars($row['titolo']); ?></h3>
                        <p><?php echo htmlspecialchars($row['autore']); ?> | <strong><?php echo htmlspecialchars($row['materia']); ?></strong></p>
                    </div>

                    <div class="actions">
                        <a href="book_details.php?id=<?php echo $row['IdLibro']; ?>" class="btn-action btn-view">
                            <i class="fa-solid fa-eye"></i> Visualizza
                        </a>
                        
                        <a href="modifica_libro.php?id=<?php echo $row['IdLibro']; ?>" class="btn-action btn-edit">
                            <i class="fa-solid fa-pen"></i> Modifica
                        </a>
                        
                        <a href="elimina_libro.php?id=<?php echo $row['IdLibro']; ?>" 
                           onclick="return confirm('Sei sicuro di voler eliminare questo annuncio?');"
                           class="btn-action btn-delete">
                            <i class="fa-solid fa-trash"></i> Elimina
                        </a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div style="text-align: center; padding: 60px; background: white; border-radius: 30px; box-shadow: var(--shadow);">
                <i class="fa-solid fa-book-open" style="font-size: 3rem; color: #eee; margin-bottom: 20px;"></i>
                <p style="color: #777; font-size: 1.1rem; margin-bottom: 20px;">Non hai ancora caricato nessun libro.</p>
                <a href="vendi.php" class="btn-submit" style="text-decoration:none; padding: 12px 25px; border-radius: 50px; display:inline-block;">Vendi il tuo primo libro</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>