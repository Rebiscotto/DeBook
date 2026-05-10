<?php
session_start();
require_once 'db_connection.php';

// Verifica se è stato passato un ID libro valido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: compra.php");
    exit;
}

$id_libro = $_GET['id'];
$is_logged = isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true;

// Query per recuperare tutti i dettagli del libro, dell'anagrafica e del venditore
$query = "SELECT L.*, A.titolo, A.autore, A.materia, A.codISBN, U.nome, U.cognome, U.email, U.IdUtente as IdVenditore
          FROM Libri L
          JOIN AnagraficaLibri A ON L.IdAnag = A.IdAnag
          JOIN Utenti U ON L.IdVenditore = U.IdUtente
          WHERE L.IdLibro = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_libro);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Libro non trovato.");
}

$libro = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Debook - <?php echo htmlspecialchars($libro['titolo']); ?></title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .detail-container {
            display: flex;
            flex-wrap: wrap;
            gap: 40px;
            width: 90%;
            max-width: 1100px;
            margin: 50px auto;
            background: white;
            padding: 40px;
            border-radius: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }
        .image-gallery { flex: 1; min-width: 300px; }
        .image-gallery img { 
            width: 100%; 
            border-radius: 20px; 
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .info-panel { flex: 1.5; min-width: 300px; display: flex; flex-direction: column; }
        .book-category { 
            display: inline-block; 
            background: var(--accent-beige); 
            padding: 5px 15px; 
            border-radius: 50px; 
            font-size: 0.9rem; 
            margin-bottom: 15px;
            width: fit-content;
        }
        .price-tag { font-size: 2rem; color: var(--dark-text); margin: 20px 0; }
        .seller-box {
            background: var(--bg-page);
            padding: 20px;
            border-radius: 15px;
            margin-top: auto;
            border-left: 5px solid var(--accent-beige);
        }
        .btn-chat {
            background: var(--dark-text);
            color: white;
            padding: 15px 30px;
            border-radius: 50px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-top: 20px;
            font-size: 1.1rem;
            transition: 0.3s;
        }
        .btn-chat:hover { background: #444; }
    </style>
</head>
<body>
    <header class="header-nav">
        <a href="index.php" class="logo-link"><img src="immagini/tastologo.png" alt="Debook Logo"></a>
        <div style="font-family: Arial;">
            <a href="compra.php" style="text-decoration: none; color: var(--dark-text); margin-right: 15px;">Torna al Mercatino</a>
        </div>
    </header>

    <div class="detail-container">
        <div class="image-gallery">
            <img src="<?php echo htmlspecialchars($libro['immagine']); ?>" alt="Foto libro">
        </div>

        <div class="info-panel">
            <span class="book-category"><?php echo htmlspecialchars($libro['materia']); ?></span>
            
            <h1 style="font-size: 2.5rem; line-height: 1.1;"><?php echo htmlspecialchars($libro['titolo']); ?></h1>
            <p style="font-family: Arial; font-size: 1.2rem; color: #555; margin-top: 10px;">
                di <strong><?php echo htmlspecialchars($libro['autore']); ?></strong>
            </p>
            
            <p style="font-family: Arial; margin-top: 20px; color: #777;">
                ISBN: <?php echo $libro['codISBN'] ? htmlspecialchars($libro['codISBN']) : 'Non specificato'; ?>
            </p>

            <div class="seller-box">
                <h4 style="margin-bottom: 5px;">Informazioni sul Venditore</h4>
                <p style="font-family: Arial; font-size: 0.95rem;">
                    Venduto da: <strong><?php echo htmlspecialchars($libro['nome'] . " " . $libro['cognome']); ?></strong>
                </p>
            </div>

            <?php if($is_logged): ?>
                <?php if($_SESSION['id'] != $libro['IdVenditore']): ?>
                    <a href="chat.php?with=<?php echo $libro['IdVenditore']; ?>&book=<?php echo $id_libro; ?>" class="btn-chat">
                        <i class="fa-solid fa-message"></i> Contatta il venditore
                    </a>
                <?php else: ?>
                    <p style="margin-top: 20px; font-family: Arial; color: #0288d1;">Questo è un tuo annuncio.</p>
                <?php endif; ?>
            <?php else: ?>
                <a href="login.php" class="btn-chat" style="background: #999;">
                    Accedi per contattare il venditore
                </a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>