<?php
session_start();
require_once 'db_connection.php';

// Protezione della pagina: accesso solo per utenti loggati
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
        body { background-color: var(--bg-page); margin: 0; padding: 0; }
        .container { width: 95%; max-width: 1000px; margin: 40px auto; }
        
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
            overflow: hidden; /* Evita fuoriuscite strane */
        }
        
        .book-item img { 
            width: 70px; 
            height: 100px; 
            object-fit: cover; 
            border-radius: 12px; 
            flex-shrink: 0; /* Impedisce alla foto di rimpicciolirsi */
        }

        .book-details { 
            flex: 1; 
            min-width: 0; /* Permette al contenitore di ridursi senza rompere il layout */
        }
        
        .book-details h3 { 
            color: var(--dark-text); 
            margin: 0 0 5px 0; 
            font-size: 1.1rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis; /* Mette i "..." se il titolo è troppo lungo */
        }
        
        .book-details p { font-family: Arial; font-size: 0.85rem; color: #666; margin: 0; }

        /* SEZIONE AZIONI - LA PARTE CRITICA */
        .actions { 
            display: flex; 
            gap: 15px; 
            align-items: center; 
            flex-shrink: 0; /* IMPEDISCE AI TASTI DI SPARIRE */
            margin-left: auto; /* Spinge i tasti a destra */
            padding-left: 10px;
        }
        
       
        .header-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 40px;
            background: white;
            box-shadow: var(--shadow);
        }

        /* FIX PER MOBILE: Se lo schermo è piccolo, i tasti vanno sotto il testo */
        @media (max-width: 768px) {
            .book-item { flex-wrap: wrap; }
            .actions { width: 100%; margin-left: 0; padding-top: 15px; border-top: 1px solid #eee; justify-content: space-around; }
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
            <h1>I miei annunci</h1>
            <a href="vendi.php" class="btn-submit" style="width: auto; padding: 10px 20px; font-size: 1rem; text-decoration: none; border-radius: 50px;">
                <i class="fa-solid fa-plus"></i> Nuovo
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

                    <div class="gestione-annunci-box" style="display: flex !important; gap: 15px !important; align-items: center !important; flex-shrink: 0 !important; margin-left: auto !important;">
    
    <a href="book_details.php?id=<?php echo $row['IdLibro']; ?>" 
       style="color: #0288d1 !important; font-size: 14px !important; font-weight: bold !important; text-decoration: underline !important; font-family: Arial !important; display: inline !important; opacity: 1 !important; visibility: visible !important;">
       Visualizza
    </a>
    
    <a href="modifica_libro.php?id=<?php echo $row['IdLibro']; ?>" 
       style="color: #f39c12 !important; font-size: 14px !important; font-weight: bold !important; text-decoration: underline !important; font-family: Arial !important; display: inline !important; opacity: 1 !important; visibility: visible !important;">
       Modifica
    </a>
    
    <a href="elimina_libro.php?id=<?php echo $row['IdLibro']; ?>" 
       onclick="return confirm('Sei sicuro?');"
       style="color: #ff0000 !important; font-size: 14px !important; font-weight: bold !important; text-decoration: underline !important; font-family: Arial !important; display: inline !important; opacity: 1 !important; visibility: visible !important;">
       Elimina
    </a>
    
</div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div style="text-align: center; padding: 60px; background: white; border-radius: 30px; box-shadow: var(--shadow);">
                <i class="fa-solid fa-book-open" style="font-size: 3rem; color: #eee; margin-bottom: 20px;"></i>
                <p style="font-family: Arial; color: #777; font-size: 1.1rem;">Nessun annuncio attivo.</p>
                <a href="vendi.php" style="color: var(--dark-text); font-weight: bold; text-decoration: none;">Vendi il tuo primo libro</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>