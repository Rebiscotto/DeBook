<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION["loggedin"])) { header("Location: login.php"); exit; }

$id_profilo = isset($_GET['id']) ? intval($_GET['id']) : $_SESSION['id'];

// 1. Dati Utente
$stmt = $conn->prepare("SELECT nome, cognome FROM Utenti WHERE IdUtente = ?");
$stmt->bind_param("i", $id_profilo);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// 2. Query Media (Usando NStelle)
$query_f = "SELECT IFNULL(AVG(NStelle), 0) as media, COUNT(*) as totale FROM Feedback WHERE IdDestinatario = ?";
$stmt_f = $conn->prepare($query_f);
$stmt_f->bind_param("i", $id_profilo);
$stmt_f->execute();
$res_f = $stmt_f->get_result()->fetch_assoc();

$media = round($res_f['media'], 1);
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Profilo di <?php echo $user['nome']; ?></title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .profile-card { background: white; max-width: 600px; margin: 40px auto; padding: 30px; border-radius: 25px; box-shadow: var(--shadow); text-align: center; }
        .feedback-item { background: #f9f9f9; padding: 15px; border-radius: 15px; margin-bottom: 10px; text-align: left; }
        .star { color: #f39c12; }
    </style>
</head>
<body>

<div class="profile-card">
    <h1><?php echo $user['nome'] . " " . $user['cognome']; ?></h1>
    
    <div style="margin: 20px 0;">
        <span style="font-size: 1.5rem; color: #f39c12;">
            <?php for($i=1; $i<=5; $i++) echo ($i <= floor($media)) ? "★" : "☆"; ?>
        </span>
        <strong><?php echo $media; ?> / 5</strong>
        <p>(<?php echo $res_f['totale']; ?> recensioni)</p>
    </div>

    <div style="margin-top: 30px; text-align: left;">
        <h3>Recensioni ricevute:</h3>
        <?php
        // Query messaggi usando i tuoi nomi: messaggio, NStelle, data
        $q_msg = "SELECT f.messaggio, f.NStelle, f.data, u.nome 
                  FROM Feedback f 
                  JOIN Utenti u ON f.IdMittente = u.IdUtente 
                  WHERE f.IdDestinatario = ? 
                  ORDER BY f.data DESC";
        $stmt_m = $conn->prepare($q_msg);
        $stmt_m->bind_param("i", $id_profilo);
        $stmt_m->execute();
        $res_m = $stmt_m->get_result();

        if ($res_m->num_rows > 0):
            while($fb = $res_m->fetch_assoc()): ?>
                <div class="feedback-item">
                    <strong><?php echo htmlspecialchars($fb['nome']); ?></strong>
                    <span class="star"><?php echo str_repeat("★", $fb['NStelle']); ?></span>
                    <p><?php echo htmlspecialchars($fb['messaggio']); ?></p>
                    <small><?php echo date('d/m/Y', strtotime($fb['data'])); ?></small>
                </div>
            <?php endwhile;
        else: echo "<p>Ancora nessuna recensione.</p>"; endif; ?>
    </div>
</div>

</body>
</html>