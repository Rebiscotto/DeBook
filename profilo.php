<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION["loggedin"])) { header("Location: login.php"); exit; }

// Se nell'URL c'è un ID, vediamo il profilo di qualcun altro, altrimenti il nostro
$id_profilo = isset($_GET['id']) ? intval($_GET['id']) : $_SESSION['id'];

// 1. Recupero dati utente
$stmt = $conn->prepare("SELECT nome, cognome, email FROM Utenti WHERE IdUtente = ?");
$stmt->bind_param("i", $id_profilo);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// 2. Calcolo Media Feedback

$query_f = "SELECT AVG(NStelle) as media, COUNT(*) as totale FROM Feedback WHERE idDestinatario = ?";
$stmt_f = $conn->prepare($query_f);
$stmt_f->bind_param("i", $id_profilo);
$stmt_f->execute();
$feedback = $stmt_f->get_result()->fetch_assoc();
$media = round($feedback['media'], 1);
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Profilo - <?php echo $user['nome']; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .profile-card { background: white; max-width: 600px; margin: 50px auto; padding: 40px; border-radius: 30px; box-shadow: var(--shadow); text-align: center; }
        .avatar { width: 100px; height: 100px; background: var(--accent-beige); border-radius: 50%; margin: 0 auto 20px; display: flex; align-items: center; justify-content: center; font-size: 3rem; color: white; }
        .stars { color: #f39c12; font-size: 1.5rem; margin: 10px 0; }
        .feedback-list { text-align: left; margin-top: 30px; border-top: 1px solid #eee; padding-top: 20px; }
        .fb-item { background: #f9f9f9; padding: 15px; border-radius: 15px; margin-bottom: 10px; font-size: 0.9rem; }
    </style>
</head>
<body>
    <header class="header-nav">
        <a href="index.php"><img src="immagini/tastologo.png" alt="Logo" style="height:40px;"></a>
        <a href="index.php" style="text-decoration:none; color:black; font-family:Arial;">Home</a>
    </header>

    <div class="profile-card">
        <div class="avatar"><?php echo strtoupper(substr($user['nome'], 0, 1)); ?></div>
        <h1 style="font-family:'Arial Black';"><?php echo $user['nome'] . " " . $user['cognome']; ?></h1>
        
        <div class="stars">
            <?php 
            for($i=1; $i<=5; $i++) {
                echo ($i <= $media) ? "★" : "☆";
            }
            ?>
            <span style="color:black; font-size: 1rem; font-family: Arial;">(<?php echo $media; ?>)</span>
        </div>
        <p style="color:#777;"><?php echo $feedback['totale']; ?> recensioni ricevute</p>

        <div class="feedback-list">
            <h3 style="font-size: 1rem; margin-bottom: 15px;">Ultime Recensioni:</h3>
            <?php
            $q_list = "SELECT F.*, U.nome FROM Feedback F JOIN Utenti U ON F.IdMittente = U.IdUtente WHERE IdDestinatario = ? ORDER BY DataFeedback DESC LIMIT 5";
            $st_list = $conn->prepare($q_list);
            $st_list->bind_param("i", $id_profilo);
            $st_list->execute();
            $res_list = $st_list->get_result();
            
            if($res_list->num_rows > 0):
                while($fb = $res_list->fetch_assoc()): ?>
                    <div class="fb-item">
                        <strong><?php echo $fb['nome']; ?>:</strong> <?php echo htmlspecialchars($fb['messaggio']); ?>
                        <div style="color:#f39c12; font-size: 0.8rem;"><?php echo str_repeat("★", $fb['NStelle']); ?></div>
                    </div>
                <?php endwhile;
            else: echo "<p style='color:#ccc;'>Nessun commento ancora.</p>"; endif; ?>
        </div>
    </div>
</body>
</html>