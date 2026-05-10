<?php
session_start();
require_once 'db_connection.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id_profilo = $_GET['id'];

// Recupero dati utente
$stmt_utente = $conn->prepare("SELECT nome, cognome FROM Utenti WHERE IdUtente = ?");
$stmt_utente->bind_param("i", $id_profilo);
$stmt_utente->execute();
$utente = $stmt_utente->get_result()->fetch_assoc();

if (!$utente) die("Utente non trovato.");

// Recupero la media delle stelle
$stmt_media = $conn->prepare("SELECT AVG(NStelle) as media, COUNT(*) as totale FROM Feedback WHERE IdDestinatario = ?");
$stmt_media->bind_param("i", $id_profilo);
$stmt_media->execute();
$statistiche = $stmt_media->get_result()->fetch_assoc();
$media = round($statistiche['media'] ?? 0, 1);
$totale_recensioni = $statistiche['totale'];

// Recupero l'elenco dei feedback con il nome del mittente
$stmt_feed = $conn->prepare("
    SELECT F.messaggio, F.NStelle, F.data, U.nome, U.cognome 
    FROM Feedback F 
    JOIN Utenti U ON F.IdMittente = U.IdUtente 
    WHERE F.IdDestinatario = ? 
    ORDER BY F.IdFeedback DESC
");
$stmt_feed->bind_param("i", $id_profilo);
$stmt_feed->execute();
$feedback_list = $stmt_feed->get_result();
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Profilo di <?php echo htmlspecialchars($utente['nome']); ?> - Debook</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .profile-container { width: 90%; max-width: 800px; margin: 40px auto; background: white; padding: 40px; border-radius: 20px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        .profile-header { text-align: center; border-bottom: 2px solid var(--bg-page); padding-bottom: 20px; margin-bottom: 30px; }
        .rating-badge { background: var(--accent-beige); display: inline-block; padding: 10px 20px; border-radius: 50px; font-size: 1.2rem; margin-top: 10px; }
        .rating-badge i { color: #f39c12; }
        
        .feedback-card { background: var(--bg-page); padding: 20px; border-radius: 15px; margin-bottom: 15px; }
        .feedback-header { display: flex; justify-content: space-between; font-family: Arial; font-size: 0.9rem; color: #666; margin-bottom: 10px; }
        .stars-display i { color: #ccc; }
        .stars-display i.active { color: #f39c12; }
        .feedback-text { font-family: Arial; line-height: 1.5; color: var(--dark-text); }
    </style>
</head>
<body>
    <header class="header-nav">
        <a href="index.php" class="logo-link"><img src="immagini/tastologo.png" alt="Debook Logo"></a>
    </header>

    <div class="profile-container">
        <?php if(isset($_GET['msg'])) echo "<p style='color: green; text-align:center; font-family: Arial;'>".htmlspecialchars($_GET['msg'])."</p>"; ?>

        <div class="profile-header">
            <h1><?php echo htmlspecialchars($utente['nome'] . " " . $utente['cognome']); ?></h1>
            <div class="rating-badge">
                <i class="fa-solid fa-star"></i> 
                <strong><?php echo $media; ?> / 5</strong> 
                <span style="font-family: Arial; font-size: 0.9rem; font-weight: normal;">(<?php echo $totale_recensioni; ?> recensioni)</span>
            </div>
            <?php if(isset($_SESSION['loggedin']) && $_SESSION['id'] != $id_profilo): ?>
                <br>
                <a href="lascia_feedback.php?id_utente=<?php echo $id_profilo; ?>" class="btn-submit" style="display: inline-block; width: auto; padding: 10px 20px; margin-top: 20px; text-decoration: none; font-size: 1rem;">Lascia un Feedback</a>
            <?php endif; ?>
        </div>

        <h3>Recensioni ricevute</h3>
        <div style="margin-top: 20px;">
            <?php if ($feedback_list->num_rows > 0): ?>
                <?php while($f = $feedback_list->fetch_assoc()): ?>
                    <div class="feedback-card">
                        <div class="feedback-header">
                            <span>Da: <strong><?php echo htmlspecialchars($f['nome']); ?></strong></span>
                            <span><?php echo date('d/m/Y', strtotime($f['data'])); ?></span>
                        </div>
                        <div class="stars-display" style="margin-bottom: 10px;">
                            <?php 
                                for($i=1; $i<=5; $i++) {
                                    echo $i <= $f['NStelle'] ? '<i class="fa-solid fa-star active"></i>' : '<i class="fa-solid fa-star"></i>';
                                }
                            ?>
                        </div>
                        <p class="feedback-text"><?php echo nl2br(htmlspecialchars($f['messaggio'])); ?></p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="font-family: Arial; color: #777; text-align: center;">Nessun feedback ancora ricevuto.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>