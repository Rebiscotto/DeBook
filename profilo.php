<?php
session_start();
require_once 'db_connection.php';

// Controllo se l'utente è loggato
if (!isset($_SESSION["loggedin"])) { 
    header("Location: login.php"); 
    exit; 
}

// 1. Identifichiamo l'ID del profilo da mostrare (il nostro o quello di un altro tramite ?id=...)
$id_profilo = isset($_GET['id']) ? intval($_GET['id']) : $_SESSION['id'];

// 2. Recupero dati anagrafici dell'utente
$stmt = $conn->prepare("SELECT nome, cognome FROM Utenti WHERE IdUtente = ?");
$stmt->bind_param("i", $id_profilo);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) { 
    die("Utente non trovato."); 
}

// 3. Recupero Media e Totale Feedback (Colonne: NStelle)
$query_f = "SELECT IFNULL(AVG(NStelle), 0) as media, COUNT(*) as totale FROM Feedback WHERE IdDestinatario = ?";
$stmt_f = $conn->prepare($query_f);
$stmt_f->bind_param("i", $id_profilo);
$stmt_f->execute();
$res_f = $stmt_f->get_result()->fetch_assoc();

$media = round($res_f['media'], 1);
$totale_recensioni = $res_f['totale'];
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profilo di <?php echo htmlspecialchars($user['nome']); ?> - DeBook</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .profile-container { max-width: 700px; margin: 40px auto; padding: 20px; font-family: Arial, sans-serif; }
        .profile-card { background: white; padding: 40px; border-radius: 30px; box-shadow: var(--shadow); text-align: center; }
        
        .avatar-circle { width: 100px; height: 100px; background: var(--accent-beige); color: var(--dark-text); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 3rem; margin: 0 auto 20px; font-family: 'Arial Black'; }
        
        .stars-display { color: #f39c12; font-size: 1.8rem; margin: 10px 0; }
        .star-empty { color: #ddd; }
        
        .feedback-section { margin-top: 40px; text-align: left; }
        .feedback-card { background: #fdfdfd; border: 1px solid #eee; padding: 20px; border-radius: 20px; margin-bottom: 15px; position: relative; }
        .feedback-name { font-weight: bold; font-size: 1rem; color: var(--dark-text); }
        .feedback-date { font-size: 0.8rem; color: #aaa; display: block; margin-top: 5px; }
        .feedback-text { margin-top: 10px; color: #555; line-height: 1.4; }
        
        .no-feedback { color: #bbb; font-style: italic; text-align: center; margin-top: 20px; }
    </style>
</head>
<body>

    <header class="header-nav">
        <a href="index.php" class="logo-link"><img src="immagini/tastologo.png" alt="Logo"></a>
        <a href="index.php" style="text-decoration:none; color:var(--dark-text); font-weight:bold;">HOME</a>
    </header>

    <div class="profile-container">
        <div class="profile-card">
            <div class="avatar-circle"><?php echo strtoupper(substr($user['nome'], 0, 1)); ?></div>
            <h1 style="font-family:'Arial Black'; margin:0;"><?php echo htmlspecialchars($user['nome'] . " " . $user['cognome']); ?></h1>
            
            <div class="stars-display">
                <?php 
                if ($totale_recensioni > 0) {
                    for($i=1; $i<=5; $i++) {
                        echo ($i <= floor($media)) ? "★" : "<span class='star-empty'>☆</span>";
                    }
                    echo "<div style='color:black; font-size:1.1rem; margin-top:5px;'>$media / 5</div>";
                } else {
                    echo "<span style='color:#ccc; font-size:1.2rem;'>Nessun feedback ricevuto</span>";
                }
                ?>
            </div>
            <p style="color:#888;"><?php echo $totale_recensioni; ?> recensioni totali</p>

            <div class="feedback-section">
                <h2 style="font-family:'Arial Black'; font-size: 1.2rem; border-bottom: 2px solid var(--accent-beige); padding-bottom: 10px; margin-bottom: 25px;">COSA DICONO GLI UTENTI</h2>

                <?php
                // Query per i singoli messaggi (Colonne: messaggio, NStelle, data)
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
                        <div class="feedback-card">
                            <div style="display:flex; justify-content:space-between; align-items: center;">
                                <span class="feedback-name"><?php echo htmlspecialchars($fb['nome']); ?></span>
                                <span style="color:#f39c12; font-size: 0.9rem;"><?php echo str_repeat("★", $fb['NStelle']); ?></span>
                            </div>
                            
                            <p class="feedback-text">"<?php echo htmlspecialchars($fb['messaggio']); ?>"</p>
                            
                            <span class="feedback-date">
                                <i class="fa-regular fa-calendar-days"></i> 
                                <?php 
                                // FIX DATA 1970: Controlliamo se la data esiste
                                if (!empty($fb['data']) && $fb['data'] != "0000-00-00 00:00:00") {
                                    echo date('d/m/Y', strtotime($fb['data'])); 
                                } else {
                                    echo "Data non disponibile";
                                }
                                ?>
                            </span>
                        </div>
                    <?php endwhile;
                else: ?>
                    <p class="no-feedback">Questo utente non ha ancora ricevuto recensioni.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

</body>
</html>