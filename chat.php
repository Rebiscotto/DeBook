<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION["loggedin"])) { header("Location: login.php"); exit; }

$id_utente = $_SESSION["id"];
$chat_con = isset($_GET['with']) ? intval($_GET['with']) : null;
$encryption_key = "DeBook_Secret_Key_2026"; 

$errore_db = "";
$interlocutore = null;
$lista_chat = null;

try {
    // 1. RECUPERO INTERLOCUTORE
    if ($chat_con) {
        $stmt = $conn->prepare("SELECT nome, cognome FROM Utenti WHERE IdUtente = ?");
        if (!$stmt) throw new Exception($conn->error);
        $stmt->bind_param("i", $chat_con);
        $stmt->execute();
        $interlocutore = $stmt->get_result()->fetch_assoc();
    }

    // 2. RECUPERO LISTA CHAT
    // Verifichiamo prima se la tabella esiste
    $check = $conn->query("SHOW TABLES LIKE 'Messaggi'");
    if ($check && $check->num_rows > 0) {
        $q_l = "SELECT DISTINCT U.IdUtente, U.nome, U.cognome 
                FROM Utenti U 
                JOIN Messaggi M ON (U.IdUtente = M.IdMittente OR U.IdUtente = M.IdDestinatario) 
                WHERE (M.IdMittente = ? OR M.IdDestinatario = ?) AND U.IdUtente != ?";
        $st_l = $conn->prepare($q_l);
        if (!$st_l) throw new Exception($conn->error);
        $st_l->bind_param("iii", $id_utente, $id_utente, $id_utente);
        $st_l->execute();
        $lista_chat = $st_l->get_result();
    } else {
        throw new Exception("La tabella 'Messaggi' NON esiste nel database.");
    }

} catch (Exception $e) {
    $errore_db = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debook - Chat</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        body { background-color: var(--bg-page); }
        .chat-layout { display: flex; width: 95%; max-width: 1100px; height: 80vh; margin: 20px auto; background: white; border-radius: 20px; box-shadow: var(--shadow); overflow: hidden; }
        .chat-sidebar { width: 30%; background: #f9f9f9; border-right: 1px solid #ddd; overflow-y: auto; }
        .chat-main { width: 70%; display: flex; flex-direction: column; }
        .chat-header { padding: 20px; border-bottom: 1px solid #eee; font-family: Arial; font-weight: bold; }
        .messages-area { flex: 1; padding: 20px; overflow-y: auto; background: #fafafa; display: flex; flex-direction: column; gap: 10px; }
        .chat-input-bar { padding: 20px; border-top: 1px solid #eee; display: flex; gap: 10px; }
        .chat-input-bar input { flex: 1; padding: 12px; border-radius: 50px; border: 2px solid #ddd; outline: none; }
        .user-item { display: block; padding: 15px; text-decoration: none; color: #333; border-bottom: 1px solid #eee; font-family: Arial; }
        .user-item:hover, .user-item.active { background: var(--accent-beige); }
        .msg { max-width: 70%; padding: 12px; border-radius: 15px; font-family: Arial; font-size: 0.9rem; }
        .sent { background: var(--accent-beige); align-self: flex-end; border-bottom-right-radius: 2px; }
        .received { background: #e2e2e2; align-self: flex-start; border-bottom-left-radius: 2px; }
    </style>
</head>
<body>

    <header class="header-nav">
        <a href="index.php" class="logo-link"><img src="immagini/tastologo.png" alt="Debook Logo"></a>
        <a href="index.php" style="text-decoration:none; color:black; font-family:Arial;"><i class="fa-solid fa-house"></i> Home</a>
    </header>

    <div class="chat-layout">
        <div class="chat-sidebar">
            <div style="padding:15px; background:#eee; font-weight:bold; font-family:Arial;">Le tue Chat</div>
            <?php if($lista_chat): while($l = $lista_chat->fetch_assoc()): ?>
                <a href="chat.php?with=<?php echo $l['IdUtente']; ?>" class="user-item <?php echo ($chat_con == $l['IdUtente']) ? 'active' : ''; ?>">
                    <i class="fa-solid fa-user-circle"></i> <?php echo htmlspecialchars($l['nome']." ".$l['cognome']); ?>
                </a>
            <?php endwhile; endif; ?>
        </div>

        <div class="chat-main">
            
            <?php if ($errore_db != ""): ?>
                <div style="margin: 30px; padding: 20px; background: #ffebee; color: #c62828; border-radius: 10px; border: 1px solid #ffcdd2; font-family: Arial;">
                    <h3 style="margin-top: 0;"><i class="fa-solid fa-triangle-exclamation"></i> Errore Database</h3>
                    <p>Il server ha restituito questo errore:</p>
                    <code style="display:block; background: white; padding: 10px; margin-top: 10px; border-radius: 5px;">
                        <?php echo htmlspecialchars($errore_db); ?>
                    </code>
                </div>

            <?php elseif ($chat_con && $interlocutore): ?>
                
                <div class="chat-header">
                    Chat con <?php echo htmlspecialchars($interlocutore['nome'] . " " . $interlocutore['cognome']); ?>
                </div>

                <div class="messages-area" id="chatBox">
                    <?php
                    try {
                        $q_m = "SELECT * FROM Messaggi WHERE (IdMittente = ? AND IdDestinatario = ?) OR (IdMittente = ? AND IdDestinatario = ?) ORDER BY data_invio ASC";
                        $st_m = $conn->prepare($q_m);
                        if (!$st_m) throw new Exception($conn->error);
                        $st_m->bind_param("iiii", $id_utente, $chat_con, $chat_con, $id_utente);
                        $st_m->execute();
                        $res_m = $st_m->get_result();

                        if($res_m->num_rows == 0) {
                            echo "<p style='text-align:center; color:#999; margin-top:20px; font-family:Arial;'>Invia un messaggio per iniziare.</p>";
                        }

                        while($m = $res_m->fetch_assoc()):
                            $text = openssl_decrypt($m['testo_criptato'], 'aes-256-cbc', $encryption_key, 0, str_repeat("0", 16));
                        ?>
                            <div class="msg <?php echo ($m['IdMittente'] == $id_utente) ? 'sent' : 'received'; ?>">
                                <?php echo htmlspecialchars($text); ?>
                            </div>
                        <?php endwhile; 
                    } catch (Exception $e) {
                        echo "<p style='color:red;'>Errore caricamento messaggi: " . $e->getMessage() . "</p>";
                    }
                    ?>
                </div>

                <form class="chat-input-bar" action="send_message.php" method="POST">
                    <input type="hidden" name="id_destinatario" value="<?php echo $chat_con; ?>">
                    <input type="text" name="messaggio" placeholder="Scrivi qui..." required autocomplete="off">
                    <button type="submit" class="btn-submit" style="padding:10px 20px;"><i class="fa-solid fa-paper-plane"></i></button>
                </form>

            <?php else: ?>
                <div style="flex:1; display:flex; align-items:center; justify-content:center; color:#ccc; font-family:Arial;">
                    Seleziona una chat o controlla l'URL.
                </div>
            <?php endif; ?>
        </div>
    </div>
    <script>var cb = document.getElementById("chatBox"); if(cb) cb.scrollTop = cb.scrollHeight;</script>
</body>
</html>