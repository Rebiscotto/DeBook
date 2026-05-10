<?php
session_start();
require_once 'db_connection.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: compra.php");
    exit;
}

$id_libro = $_GET['id'];
$is_logged = isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true;

// Query completa
$query = "SELECT L.*, A.titolo, A.autore, A.materia, A.codISBN, U.nome, U.cognome, U.IdUtente as IdVenditore
          FROM Libri L
          JOIN AnagraficaLibri A ON L.IdAnag = A.IdAnag
          JOIN Utenti U ON L.IdVenditore = U.IdUtente
          WHERE L.IdLibro = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_libro);
$stmt->execute();
$libro = $stmt->get_result()->fetch_assoc();

if (!$libro) die("Libro non trovato.");
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debook - <?php echo htmlspecialchars($libro['titolo']); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        body { background-color: var(--bg-page); }

        .detail-container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .detail-card {
            background: white;
            display: flex;
            gap: 40px;
            padding: 40px;
            border-radius: 30px;
            box-shadow: var(--shadow);
        }

        .image-box { flex: 1; }
        .image-box img {
            width: 100%;
            border-radius: 20px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }

        .info-box { flex: 1.2; text-align: left; display: flex; flex-direction: column; }

        .price-large {
            font-size: 2.8rem;
            font-family: 'Arial Black', sans-serif;
            color: var(--dark-text);
            margin: 20px 0;
        }

        .materia-label {
            display: inline-block;
            background: var(--accent-beige);
            padding: 6px 18px;
            border-radius: 50px;
            font-weight: bold;
            font-size: 0.9rem;
            margin-bottom: 15px;
        }

        .seller-card {
            background: #f8f8f8;
            padding: 20px;
            border-radius: 15px;
            border-left: 5px solid var(--accent-beige);
            margin-top: 20px;
        }

        @media (max-width: 768px) {
            .detail-card { flex-direction: column; padding: 20px; }
        }
    </style>
</head>
<body>

    <header class="header-nav">
        <a href="index.php" class="logo-link"><img src="immagini/tastologo.png" alt="Debook Logo"></a>
        <a href="compra.php" style="text-decoration:none; color:black; font-family:Arial;"><i class="fa-solid fa-arrow-left"></i> Torna al mercatino</a>
    </header>

    <div class="detail-container">
        <div class="detail-card">
            <div class="image-box">
                <img src="<?php echo htmlspecialchars($libro['immagine']); ?>" alt="Foto libro">
            </div>

            <div class="info-box">
                <span class="materia-label"><?php echo htmlspecialchars($libro['materia']); ?></span>
                <h1 style="font-family:'Arial Black'; font-size: 2rem; margin-bottom: 5px;"><?php echo htmlspecialchars($libro['titolo']); ?></h1>
                <p style="font-family:Arial; font-size:1.2rem; color:#666;">Autore: <?php echo htmlspecialchars($libro['autore']); ?></p>

                <div class="price-large">
                    <?php echo number_format($libro['prezzo'], 2); ?> €
                </div>

                <p style="font-family:Arial; color:#999;">ISBN: <?php echo htmlspecialchars($libro['codISBN'] ?: 'Non disponibile'); ?></p>

                <div class="seller-card">
                    <p style="font-family:Arial; font-size:0.8rem; margin-bottom:5px; color:#555;">VENDITORE:</p>
                    <strong style="font-size:1.1rem;"><?php echo htmlspecialchars($libro['nome'] . " " . $libro['cognome']); ?></strong>
                </div>

                <div style="margin-top: 30px;">
                    <?php if($is_logged): ?>
                        <?php if($_SESSION['id'] != $libro['IdVenditore']): ?>
                            <a href="chat.php?with=<?php echo $libro['IdVenditore']; ?>" class="btn-submit" style="display:block; text-decoration:none; text-align:center;">
                                <i class="fa-solid fa-comments"></i> CONTATTA IL VENDITORE
                            </a>
                        <?php else: ?>
                            <div style="background:#e3f2fd; color:#0d47a1; padding:15px; border-radius:10px; text-align:center; font-family:Arial;">
                                <i class="fa-solid fa-user-check"></i> Questo è un tuo annuncio
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <a href="login.php" class="btn-submit" style="display:block; text-decoration:none; text-align:center; background:#555;">
                            ACCEDI PER COMPRARE
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

</body>
</html>