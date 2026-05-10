<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION["loggedin"])) { header("Location: login.php"); exit; }

$id_profilo = isset($_GET['id']) ? intval($_GET['id']) : $_SESSION['id'];

// 1. Recupero dati utente
$stmt = $conn->prepare("SELECT nome, cognome FROM Utenti WHERE IdUtente = ?");
$stmt->bind_param("i", $id_profilo);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) { die("Utente non trovato."); }

// 2. Query Media
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
    <title>Profilo di <?php echo htmlspecialchars($user['nome']); ?></title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .profile-card { background: white; max-width: 600px; margin: 40px auto; padding: 30px; border-radius: 25px; box-shadow: var(--shadow); text-align: center; font-family: Arial; }
        .feedback-item { background: #fdfdfd; border: 1px solid #eee; padding: 15px; border-radius: 15px; margin-bottom: 10px; text-align: left; }
        .stars { color: #f39c12; }
        .date-text { color: #aaa; font-size: 0.8rem; display: block; margin-top: 5px; }
    </style>
</head>
<body>

<div class="profile-card">
    <h1 style="font-family:'Arial Black';"><?php echo $user['nome'] . " " . $user['cognome']; ?></h1>
    <div style="margin: 20px 0;">
        <div class="stars">
            <?php for($i=1; $i<=5; $i++) echo ($i <= floor($media)) ? "★" : "☆"; ?>
            <span style="color:black;"> (<?php echo $media; ?>)</span>
        </div>
        <p><?php echo $res_f['totale']; ?> recensioni ricevute</p>
    </div>

    <div style="text-align: left; margin-top: 30px;">
        <h3>Ultimi Feedback:</h3>
        <?php
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
                    <div style="display:flex; justify-content:space-between;">
                        <strong><?php echo htmlspecialchars($fb['nome']); ?></strong>
                        <span class="stars"><?php echo str_repeat("★", $fb['NStelle']); ?></span>
                    </div>
                    <p style="margin: 5px 0; color: #444;"><?php echo htmlspecialchars($fb['messaggio']); ?></p>
                    <span class="date-text">
                        <i class="fa-regular fa-calendar"></i> 
                        <?php 
                        if (!empty($fb['data']) && $fb['data'] != "0000-00-00 00:00:00") {
                            echo date('d/m/Y H:i', strtotime($fb['data'])); 
                        } else {
                            echo "Data non disponibile";
                        }
                        ?>
                    </span>
                </div>
            <?php endwhile;
        else: echo "<p style='color:#ccc;'>Nessuna recensione.</p>"; endif; ?>
    </div>
</div>

</body>
</html>