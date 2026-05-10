<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION["loggedin"])) { header("Location: login.php"); exit; }

$id_utente = $_SESSION["id"];
$chat_con = isset($_GET['with']) ? intval($_GET['with']) : null;
$key = "Debook_Secret_2026_Safe";
$iv = "1234567890123456";

$interlocutore = null;
if ($chat_con) {
    $stmt = $conn->prepare("SELECT nome, cognome FROM Utenti WHERE IdUtente = ?");
    $stmt->bind_param("i", $chat_con);
    $stmt->execute();
    $interlocutore = $stmt->get_result()->fetch_assoc();
}

$lista_chat = $conn->query("SELECT DISTINCT U.IdUtente, U.nome FROM Utenti U JOIN Messaggi M ON (U.IdUtente = M.IdMittente OR U.IdUtente = M.IdDestinatario) WHERE (M.IdMittente = $id_utente OR M.IdDestinatario = $id_utente) AND U.IdUtente != $id_utente");
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Debook - Messaggi</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .chat-layout { display: flex; width: 95%; max-width: 1100px; height: 85vh; margin: 20px auto; background: white; border-radius: 20px; box-shadow: var(--shadow); overflow: hidden; }
        .chat-sidebar { width: 30%; background: #f9f9f9; border-right: 1px solid #ddd; overflow-y: auto; }
        .chat-main { width: 70%; display: flex; flex-direction: column; }
        .messages-area { flex: 1; padding: 20px; overflow-y: auto; background: #fafafa; display: flex; flex-direction: column; gap: 10px; }
        
        /* Bolle messaggi: Destra = Io (Beige), Sinistra = Altri (Grigio) */
        .msg { max-width: 75%; padding: 12px 18px; border-radius: 20px; font-family: Arial; font-size: 0.95rem; line-height: 1.4; }
        .sent { background: var(--accent-beige); align-self: flex-end; border-bottom-right-radius: 5px; }
        .received { background: #e2e2e2; align-self: flex-start; border-bottom-left-radius: 5px; }

        .chat-input-bar { padding: 15px; border-top: 1px solid #eee; display: flex; gap: 10px; background: white; }
        .chat-input-bar input { flex: 1; padding: 15px; border-radius: 30px; border: 2px solid #eee; outline: none; font-size: 16px; }

        .user-item { display: block; padding: 15px; text-decoration: none; color: #333; border-bottom: 1px solid #eee; }
        .user-item.active { background: var(--accent-beige); font-weight: bold; }

        /* MOBILE OPTIMIZATION */
        @media (max-width: 768px) {
            .chat-layout { flex-direction: column; width: 100%; height: 90vh; margin: 0; border-radius: 0; }
            .chat-sidebar { width: 100%; height: 15%; border-right: none; display: flex; overflow-x: auto; }
            .chat-main { width: 100%; height: 85%; }
            .user-item { border-bottom: none; border-right: 1px solid #ddd; min-width: 150px; padding: 10px; text-align: center; }
            .chat-input-bar { padding: 10px; position: sticky; bottom: 0; }
        }
    </style>
</head>
<body>
    <header class="header-nav">
        <a href="index.php" class="logo-link"><img src="immagini/tastologo.png" alt="Debook Logo"></a>
        <a href="index.php" style="text-decoration:none; color:black; font-family:Arial;"><i class="fa-solid fa-house"></i></a>
    </header>

    <div class="chat-layout">
        <div class="chat-sidebar">
            <?php while($l = $lista_chat->fetch_assoc()): ?>
                <a href="chat.php?with=<?php echo $l['IdUtente']; ?>" class="user-item <?php echo ($chat_con == $l['IdUtente']) ? 'active' : ''; ?>">
                    <?php echo htmlspecialchars($l['nome']); ?>
                </a>
            <?php endwhile; ?>
        </div>

        <div class="chat-main">
            <?php if ($interlocutore): ?>
                <div style="padding:15px; border-bottom:1px solid #eee; font-weight:bold; font-family:Arial;">
                    Chat con <?php echo htmlspecialchars($interlocutore['nome']); ?>
                </div>
                <div class="messages-area" id="chatBox">
                    <?php
                    $q = "SELECT * FROM Messaggi WHERE (IdMittente = ? AND IdDestinatario = ?) OR (IdMittente = ? AND IdDestinatario = ?) ORDER BY data_invio ASC";
                    $st = $conn->prepare($q);
                    $st->bind_param("iiii", $id_utente, $chat_con, $chat_con, $id_utente);
                    $st->execute();
                    $res = $st->get_result();
                    while($m = $res->fetch_assoc()):
                        $dec = openssl_decrypt($m['testo_criptato'], 'aes-256-cbc', $key, 0, $iv);
                    ?>
                        <div class="msg <?php echo ($m['IdMittente'] == $id_utente) ? 'sent' : 'received'; ?>">
                            <?php echo htmlspecialchars($dec); ?>
                        </div>
                    <?php endwhile; ?>
                </div>
                <form class="chat-input-bar" action="send_message.php" method="POST">
                    <input type="hidden" name="id_destinatario" value="<?php echo $chat_con; ?>">
                    <input type="text" name="messaggio" placeholder="Scrivi qui..." required autocomplete="off">
                    <button type="submit" class="btn-submit" style="padding:10px 20px; border-radius:50%;"><i class="fa-solid fa-paper-plane"></i></button>
                </form>
            <?php else: ?>
                <div style="flex:1; display:flex; align-items:center; justify-content:center; color:#ccc; font-family:Arial;">Seleziona una chat</div>
            <?php endif; ?>
        </div>
    </div>
    <script>var d = document.getElementById("chatBox"); if(d) d.scrollTop = d.scrollHeight;</script>
</body>
</html>