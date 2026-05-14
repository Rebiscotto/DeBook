<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION["loggedin"])) { 
    header("Location: login.php"); 
    exit; 
}

// 1. Identifichiamo l'ID del profilo da mostrare
$id_profilo = isset($_GET['id']) ? intval($_GET['id']) : $_SESSION['id'];

// 2. Recupero dati utente
$stmt = $conn->prepare("SELECT IdUtente, nome, cognome FROM Utenti WHERE IdUtente = ?");
$stmt->bind_param("i", $id_profilo);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) { die("Utente non trovato."); }

// 3. Media e Totale Feedback
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
    <title>Profilo di <?php echo htmlspecialchars($user['nome']); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .profile-container { max-width: 700px; margin: 40px auto; padding: 20px; font-family: Arial, sans-serif; }
        .profile-card { background: white; padding: 40px; border-radius: 30px; box-shadow: var(--shadow); text-align: center; position: relative; }
        
        /* Navigazione Superiore */
        .profile-nav { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            margin-bottom: 30px;
        }

        .btn-nav { 
            background: #f8f9fa; 
            border: 1px solid #eee; 
            padding: 10px 18px; 
            border-radius: 50px; 
            cursor: pointer; 
            color: #555; 
            text-decoration: none; 
            font-size: 0.9rem;
            font-weight: bold;
            transition: 0.3s; 
        }
        .btn-nav:hover { background: #eee; color: #000; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }

        .avatar-circle { width: 90px; height: 90px; background: var(--accent-beige); color: var(--dark-text); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; margin: 0 auto 15px; font-family: 'Arial Black'; }
        
        .stars-display { color: #f39c12; font-size: 1.8rem; margin: 10px 0; }
        .feedback-section { margin-top: 40px; text-align: left; }
        .feedback-card { background: #fdfdfd; border: 1px solid #eee; padding: 20px; border-radius: 20px; margin-bottom: 15px; }
        
        .btn-contact { 
            display: inline-block; background-color: var(--dark-text); color: white; 
            padding: 15px 30px; border-radius: 50px; text-decoration: none; 
            font-weight: bold; margin-top: 20px; transition: 0.3s; 
        }
        .btn-contact:hover { transform: scale(1.05); background-color: #333; }
    </style>
</head>
<body>

    <div class="profile-container">
        <div class="profile-card">
            
            <div class="profile-nav">
                <a href="javascript:history.back()" class="btn-nav">
                    <i class="fa-solid fa-arrow-left"></i> Indietro
                </a>
                <a href="dashboard.php" class="btn-nav">
                    <i class="fa-solid fa-house-user"></i> Dashboard
                </a>
            </div>

            <div class="avatar-circle"><?php echo strtoupper(substr($user['nome'], 0, 1)); ?></div>
            <h1 style="font-family:'Arial Black'; margin:0;"><?php echo htmlspecialchars($user['nome'] . " " . $user['cognome']); ?></h1>
            
            <div class="stars-display">
                <?php 
                if ($totale_recensioni > 0) {
                    for($i=1; $i<=5; $i++) echo ($i <= floor($media)) ? "★" : "☆";
                    echo "<div style='color:black; font-size:1rem; margin-top:5px;'>$media / 5</div>";
                } else {
                    echo "<span style='color:#ccc; font-size:1rem;'>Nessun feedback</span>";
                }
                ?>
            </div>
            <p style="color:#888;"><?php echo $totale_recensioni; ?> recensioni ricevute</p>

            <?php if($id_profilo != $_SESSION['id']): ?>
                <a href="chat.php?with=<?php echo $id_profilo; ?>" class="btn-contact">
                    <i class="fa-solid fa-paper-plane"></i> CONTATTA IL VENDITORE
                </a>
            <?php endif; ?>

            <div class="feedback-section">
                <h2 style="font-family:'Arial Black'; font-size: 1.1rem; border-bottom: 2px solid var(--accent-beige); padding-bottom: 10px; margin-bottom: 20px;">DICONO DI <?php echo strtoupper($user['nome']); ?></h2>

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
                        <div class="feedback-card">
                            <div style="display:flex; justify-content:space-between; align-items:center;">
                                <strong><?php echo htmlspecialchars($fb['nome']); ?></strong>
                                <span style="color:#f39c12;"><?php echo str_repeat("★", $fb['NStelle']); ?></span>
                            </div>
                            <p style="margin:10px 0; color:#555;">"<?php echo htmlspecialchars($fb['messaggio']); ?>"</p>
                            <small style="color:#aaa;">
                                <i class="fa-regular fa-calendar"></i> 
                                <?php echo (!empty($fb['data']) && $fb['data'] != "0000-00-00 00:00:00") ? date('d/m/Y', strtotime($fb['data'])) : "Data n.d."; ?>
                            </small>
                        </div>
                    <?php endwhile;
                else: ?>
                    <p style="text-align:center; color:#ccc;">Ancora nessuna recensione.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

</body>
</html>