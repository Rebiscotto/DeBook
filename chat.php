<?php
session_start();
require_once 'db_connection.php';

// Controllo accesso: solo utenti loggati
if (!isset($_SESSION["loggedin"])) {
    header("Location: login.php");
    exit;
}

$id_utente = $_SESSION["id"];
$chat_con = isset($_GET['with']) ? intval($_GET['with']) : null;

// Configurazione Crittografia
$key = "Debook_Secret_2026_Safe";
$iv = "1234567890123456"; 

// 1. Recupero dati dell'interlocutore
$interlocutore = null;
if ($chat_con) {
    $stmt = $conn->prepare("SELECT nome, cognome FROM Utenti WHERE IdUtente = ?");
    $stmt->bind_param("i", $chat_con);
    $stmt->execute();
    $interlocutore = $stmt->get_result()->fetch_assoc();
}

// 2. Recupero lista delle conversazioni attive
$query_lista = "SELECT DISTINCT U.IdUtente, U.nome, U.cognome 
                FROM Utenti U 
                JOIN Messaggi M ON (U.IdUtente = M.IdMittente OR U.IdUtente = M.IdDestinatario) 
                WHERE (M.IdMittente = ? OR M.IdDestinatario = ?) AND U.IdUtente != ?";
$stmt_l = $conn->prepare($query_lista);
$stmt_l->bind_param("iii", $id_utente, $id_utente, $id_utente);
$stmt_l->execute();
$lista_chat = $stmt_l->get_result();
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
        body { background-color: var(--bg-page); margin: 0; }
        .chat-layout { display: flex; width: 95%; max-width: 1100px; height: 85vh; margin: 20px auto; background: white; border-radius: 20px; box-shadow: var(--shadow); overflow: hidden; }

        /* SIDEBAR */
        .chat-sidebar { width: 30%; background: #f9f9f9; border-right: 1px solid #ddd; overflow-y: auto; }
        .sidebar-header { padding: 15px; background: #eee; font-weight: bold; font-family: Arial; }
        .user-item { display: block; padding: 15px; text-decoration: none; color: #333; border-bottom: 1px solid #eee; font-family: Arial; transition: 0.2s; }
        .user-item:hover, .user-item.active { background: var(--accent-beige); font-weight: bold; }

        /* AREA MESSAGGI */
        .chat-main { width: 70%; display: flex; flex-direction: column; background: #fff; }
        .chat-header-top { padding: 15px 25px; border-bottom: 1px solid #eee; font-family: Arial; display: flex; justify-content: space-between; align-items: center; }
        .messages-area { flex: 1; padding: 20px; overflow-y: auto; background: #fafafa; display: flex; flex-direction: column; gap: 10px; }
        
        .msg { max-width: 75%; padding: 12px 18px; border-radius: 20px; font-family: Arial; font-size: 0.95rem; position: relative; }
        .sent { background: var(--accent-beige); align-self: flex-end; border-bottom-right-radius: 5px; }
        .received { background: #e2e2e2; align-self: flex-start; border-bottom-left-radius: 5px; }

        /* Messaggio Eliminato */
        .msg-deleted { opacity: 0.6; font-style: italic; color: #888; }
        
        .btn-delete { color: #ff4d4d; font-size: 0.7rem; margin-left: 8px; text-decoration: none; opacity: 0; transition: 0.3s; }
        .msg:hover .btn-delete { opacity: 1; }

        /* INPUT BAR */
        .chat-input-bar { padding: 15px; border-top: 1px solid #eee; display: flex; gap: 10px; align-items: center; }
        .chat-input-bar input { flex: 1; padding: 12px 20px; border-radius: 30px; border: 2px solid #eee; outline: none; }
        .btn-send-chat { background-color: var(--dark-text); color: white; border: none; border-radius: 50%; width: 45px; height: 45px; cursor: pointer; flex-shrink: 0; }

        .btn-vota { font-size: 0.75rem; background: var(--accent-beige); padding: 8px 15px; border-radius: 50px; text-decoration: none; color: black; font-weight: bold; border: 1px solid #ddd; }

        @media (max-width: 768px) {
            .chat-layout { flex-direction: column; width: 100%; height: 90vh; margin: 0; }
            .chat-sidebar { width: 100%; height: 15%; display: flex; overflow-x: auto; }
            .chat-main { width: 100%; height: 85%; }
        }
    </style>
</head>
<body>

    <header class="header-nav">
        <a href="index.php" class="logo-link"><img src="immagini/tastologo.png" alt="Debook Logo"></a>
        <div class="nav-right">
            <a href="index.php" style="text-decoration:none; color:black; margin-right: 15px;"><i class="fa-solid fa-house"></i></a>
        </div>
    </header>

    <div class="chat-layout">
        <div class="chat-sidebar">
            <div class="sidebar-header">Conversazioni</div>
            <?php while($l = $lista_chat->fetch_assoc()): ?>
                <a href="chat.php?with=<?php echo $l['IdUtente']; ?>" class="user-item <?php echo ($chat_con == $l['IdUtente']) ? 'active' : ''; ?>">
                    <i class="fa-solid fa-user-circle"></i> <?php echo htmlspecialchars($l['nome']); ?>
                </a>
            <?php endwhile; ?>
        </div>

        <div class="chat-main">
            <?php if ($interlocutore): ?>
                <div class="chat-header-top">
                    <div>
                        <a href="profilo.php?id=<?php echo $chat_con; ?>" style="text-decoration:none; color:black; display: flex; align-items: center; gap: 10px;">
                            <i class="fa-solid fa-circle-user" style="font-size: 1.5rem;"></i>
                            <strong><?php echo htmlspecialchars($interlocutore['nome'] . " " . $interlocutore['cognome']); ?></strong>
                            <small style="color:#888;">(Vedi Profilo)</small>
                        </a>
                    </div>
                    <a href="lascia_feedback.php?to=<?php echo $chat_con; ?>" class="btn-vota">
                        <i class="fa-solid fa-star"></i> Vota Venditore
                    </a>
                </div>

                <div class="messages-area" id="chatBox">
                    <?php
                    $q = "SELECT * FROM Messaggi WHERE (IdMittente = ? AND IdDestinatario = ?) OR (IdMittente = ? AND IdDestinatario = ?) ORDER BY data_invio ASC";
                    $st = $conn->prepare($q);
                    $st->bind_param("iiii", $id_utente, $chat_con, $chat_con, $id_utente);
                    $st->execute();
                    $res = $st->get_result();

                    while($m = $res->fetch_assoc()):
                        $is_mio = ($m['IdMittente'] == $id_utente);
                        $is_eliminato = (isset($m['eliminato']) && $m['eliminato'] == 1);
                        
                        // Decriptazione solo se non eliminato
                        if (!$is_eliminato) {
                            $testo = openssl_decrypt($m['testo_criptato'], 'aes-256-cbc', $key, 0, $iv);
                        } else {
                            $testo = "Questo messaggio è stato eliminato";
                        }
                    ?>
                        <div class="msg <?php echo $is_mio ? 'sent' : 'received'; ?> <?php echo $is_eliminato ? 'msg-deleted' : ''; ?>">
                            <span>
                                <?php if($is_eliminato) echo '<i class="fa-solid fa-ban"></i> '; ?>
                                <?php echo htmlspecialchars($testo); ?>
                            </span>

                            <?php if ($is_mio && !$is_eliminato): ?>
                                <a href="elimina_messaggio.php?id=<?php echo $m['IdMessaggio']; ?>" 
                                   onclick="return confirm('Vuoi eliminare questo messaggio?')" 
                                   class="btn-delete">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endwhile; ?>
                </div>

                <form class="chat-input-bar" action="send_message.php" method="POST">
                    <input type="hidden" name="id_destinatario" value="<?php echo $chat_con; ?>">
                    <input type="text" name="messaggio" placeholder="Scrivi un messaggio..." required autocomplete="off">
                    <button type="submit" class="btn-send-chat"><i class="fa-solid fa-paper-plane"></i></button>
                </form>

            <?php else: ?>
                <div style="flex:1; display:flex; flex-direction:column; align-items:center; justify-content:center; color:#ccc;">
                    <i class="fa-solid fa-comments" style="font-size:4rem; margin-bottom:15px;"></i>
                    <p>Seleziona una chat per iniziare</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        const chatBox = document.getElementById("chatBox");
        if(chatBox) chatBox.scrollTop = chatBox.scrollHeight;

        <?php if($chat_con): ?>
            function loadMessages() {
                fetch('fetch_messages.php?with=<?php echo $chat_con; ?>')
                .then(response => response.text())
                .then(html => {
                    if (chatBox.innerHTML !== html) {
                        chatBox.innerHTML = html;
                        chatBox.scrollTop = chatBox.scrollHeight;
                    }
                });
            }
            setInterval(loadMessages, 3000);

            document.querySelector(".chat-input-bar").addEventListener('submit', function(e) {
                e.preventDefault();
                let formData = new FormData(this);
                fetch('send_message.php', { method: 'POST', body: formData })
                .then(() => {
                    this.querySelector("input[name='messaggio']").value = '';
                    loadMessages();
                });
            });
        <?php endif; ?>
    </script>
</body>
</html>