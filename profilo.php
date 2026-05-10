<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION["loggedin"])) { header("Location: login.php"); exit; }

// 1. Identifichiamo quale profilo mostrare (il nostro o quello di un altro)
$id_profilo = isset($_GET['id']) ? intval($_GET['id']) : $_SESSION['id'];

// 2. Recupero dati anagrafici dell'utente
$stmt = $conn->prepare("SELECT nome, cognome, email FROM Utenti WHERE IdUtente = ?");
$stmt->bind_param("i", $id_profilo);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// 3. RECUPERO MEDIA E TOTALE FEEDBACK (Colonne minuscole come da tuo screenshot)
$query_f = "SELECT AVG(NStelle) as media, COUNT(*) as totale FROM Feedback WHERE idDestinatario = ?";
$stmt_f = $conn->prepare($query_f);
$stmt_f->bind_param("i", $id_profilo);
$stmt_f->execute();
$feedback_data = $stmt_f->get_result()->fetch_assoc();

$media = round($feedback_data['media'], 1);
$totale_recensioni = $feedback_data['totale'];
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Profilo di <?php echo htmlspecialchars($user['nome']); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .profile-container { max-width: 600px; margin: 40px auto; padding: 20px; }
        .profile-card { background: white; padding: 30px; border-radius: 25px; box-shadow: var(--shadow); text-align: center; }
        .stars-container { color: #f39c12; font-size: 1.5rem; margin: 10px 0; }
        .feedback-section { margin-top: 30px; text-align: left; }
        .feedback-card { background: #f9f9f9; padding: 15px; border-radius: 15px; margin-bottom: 10px; border-left: 5px solid var(--accent-beige); }
        .no-feedback { color: #888; font-style: italic; text-align: center; }
    </style>
</head>
<body>

    <header class="header-nav">
        <a href="index.php" class="logo-link"><img src="immagini/tastologo.png" alt="Logo"></a>
        <a href="index.php" style="text-decoration:none; color:black; font-family:Arial; font-weight:bold;">Torna alla Home</a>
    </header>

    <div class="profile-container">
        <div class="profile-card">
            <div style="font-size: 4rem; color: var(--accent-beige);"><i class="fa-solid fa-circle-user"></i></div>
            <h1 style="font-family:'Arial Black';"><?php echo htmlspecialchars($user['nome'] . " " . $user['cognome']); ?></h1>
            
            <div class="stars-container">
                <?php 
                if ($totale_recensioni > 0) {
                    for($i=1; $i<=5; $i++) {
                        echo ($i <= floor($media)) ? "★" : "☆";
                    }
                    echo " <span style='color:black; font-size:1rem;'>($media)</span>";
                } else {
                    echo "<span style='color:#ccc; font-size:1rem;'>Nessuna recensione</span>";
                }
                ?>
            </div>
            <p style="color: #666; font-family: Arial;"><?php echo $totale_recensioni; ?> feedback ricevuti</p>

            <div class="feedback-section">
                <h3 style="border-bottom: 2px solid #eee; padding-bottom: 10px; margin-bottom: 15px;">Dicono di <?php echo htmlspecialchars($user['nome']); ?>:</h3>
                
                <?php
                // Query per i singoli messaggi (collegando il nome di chi ha lasciato il feedback)
                $q_msg = "SELECT f.*, u.nome FROM Feedback f 
                          JOIN Utenti u ON f.idMittente = u.IdUtente 
                          WHERE f.idDestinatario = ? 
                          ORDER BY f.data DESC";
                $st_msg = $conn->prepare($q_msg);
                $st_msg->bind_param("i", $id_profilo);
                $st_msg->execute();
                $res_msg = $st_msg->get_result();

                if ($res_msg->num_rows > 0):
                    while($row_fb = $res_msg->fetch_assoc()): ?>
                        <div class="feedback-card">
                            <div style="display:flex; justify-content:space-between; margin-bottom:5px;">
                                <strong><?php echo htmlspecialchars($row_fb['nome']); ?></strong>
                                <span style="color:#f39c12;"><?php echo str_repeat("★", $row_fb['NStelle']); ?></span>
                            </div>
                            <p style="margin:0; font-family:Arial; color:#444;"><?php echo htmlspecialchars($row_fb['messaggio']); ?></p>
                            <small style="color:#aaa;"><?php echo date('d/m/Y', strtotime($row_fb['data'])); ?></small>
                        </div>
                    <?php endwhile;
                else: ?>
                    <p class="no-feedback">Questo utente non ha ancora ricevuto commenti.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

</body>
</html>