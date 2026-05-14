<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION["loggedin"])) {
    header("Location: login.php");
    exit;
}

$id_utente = $_SESSION["id"];
$chat_con = isset($_GET['with']) ? intval($_GET['with']) : null;
$id_libro_contesto = isset($_GET['id_libro']) ? intval($_GET['id_libro']) : null;

if ($chat_con) {
    $sql_update = "UPDATE Messaggi SET letto = 1 WHERE IdDestinatario = ? AND IdMittente = ? AND letto = 0";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("ii", $id_utente, $chat_con);
    $stmt_update->execute();
}

$interlocutore = null;
if ($chat_con) {
    $stmt = $conn->prepare("SELECT nome, cognome FROM Utenti WHERE IdUtente = ?");
    $stmt->bind_param("i", $chat_con);
    $stmt->execute();
    $interlocutore = $stmt->get_result()->fetch_assoc();
}

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
        body { background-color: var(--bg-page); margin: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; height: 100vh; display: flex; flex-direction: column; }
        
        /* Layout principale */
        .chat-container { 
            display: flex; 
            width: 95%; 
            max-width: 1200px; 
            height: 85vh; 
            margin: auto; 
            background: white; 
            border-radius: 15px; 
            box-shadow: 0 5px 25px rgba(0,0,0,0.1); 
            overflow: hidden;
        }

        /* Sidebar */
        .chat-sidebar { 
            width: 35%; 
            border-right: 1px solid #eee; 
            display: flex; 
            flex-direction: column; 
            background: #fff;
        }
        .sidebar-header { padding: 20px; font-size: 1.4rem; font-weight: bold; border-bottom: 1px solid #eee; }
        .conversations-list { overflow-y: auto; flex: 1; }
        
        .user-item { 
            display: flex; align-items: center; padding: 15px 20px; 
            text-decoration: none; color: #333; border-bottom: 1px solid #f9f9f9; transition: 0.2s;
        }
        .user-item:hover { background: #f5f5f5; }
        .user-item.active { background: #f0ebe3; }
        .user-item i { font-size: 2.2rem; margin-right: 15px; color: #ccc; }

        /* Area Chat Centrale */
        .chat-main { 
            width: 65%; 
            display: flex; 
            flex-direction: column; 
            background: #fff; 
        }
        .chat-header { 
            padding: 15px 20px; 
            display: flex; 
            align-items: center; 
            border-bottom: 1px solid #eee; 
            background: #fff;
        }
        .btn-back-chat { 
            display: none; /* Nascosto su PC */
            margin-right: 15px; font-size: 1.2rem; color: #333; cursor: pointer; 
        }

        .messages-area { flex: 1; padding: 20px; overflow-y: auto; background: #fdfbf9; display: flex; flex-direction: column; gap: 10px; }
        
        /* Nuvolette messaggi */
        .msg { max-width: 75%; padding: 10px 15px; border-radius: 15px; font-size: 0.95rem; line-height: 1.4; }
        .msg img { max-width: 100%; border-radius: 10px; margin-top: 5px; cursor: pointer; }
        .sent { background: #e2d9c8; align-self: flex-end; border-bottom-right-radius: 2px; }
        .received { background: #f0f0f0; align-self: flex-start; border-bottom-left-radius: 2px; }

        .chat-footer { padding: 15px; background: #fff; border-top: 1px solid #eee; display: flex; align-items: center; gap: 10px; }
        .chat-footer input { flex: 1; padding: 12px 20px; border-radius: 25px; border: 1px solid #ddd; outline: none; }
        .btn-send { background: #333; color: #fff; border: none; width: 45px; height: 45px; border-radius: 50%; cursor: pointer; }

        /* --- LOGICA MOBILE (STILE WHATSAPP) --- */
        @media (max-width: 768px) {
            header.header-nav { display: none; } /* Nasconde logo per spazio */
            .chat-container { width: 100%; height: 100vh; margin: 0; border-radius: 0; }
            
            /* Se NON c'è una chat selezionata, mostra la lista */
            <?php if (!$chat_con): ?>
                .chat-sidebar { width: 100%; border: none; }
                .chat-main { display: none; }
            <?php else: ?>
                /* Se C'È una chat selezionata, nascondi la lista e mostra solo la chat */
                .chat-sidebar { display: none; }
                .chat-main { width: 100%; }
                .btn-back-chat { display: block; } /* Mostra tasto indietro */
            <?php endif; ?>
            
            .msg { max-width: 85%; }
        }

        /* Lightbox */
        .lightbox-overlay { display: none; position: fixed; z-index: 9999; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.9); align-items: center; justify-content: center; }
        .lightbox-overlay img { max-width: 90%; max-height: 85%; border-radius: 10px; }
    </style>
</head>
<body>

    <div id="chatLightbox" class="lightbox-overlay" onclick="closeImage()">
        <img id="lightboxImg" src="">
    </div>

    <header class="header-nav" style="padding: 10px 20px; background: #fff; border-bottom: 1px solid #eee;">
        <a href="index.php"><img src="immagini/tastologo.png" alt="Logo" style="height:30px;"></a>
    </header>

    <div class="chat-container">
        <div class="chat-sidebar">
            <div class="sidebar-header">Chat</div>
            <div class="conversations-list">
                <?php if($lista_chat->num_rows > 0): ?>
                    <?php while($l = $lista_chat->fetch_assoc()): ?>
                        <a href="chat.php?with=<?php echo $l['IdUtente']; ?>" class="user-item <?php echo ($chat_con == $l['IdUtente']) ? 'active' : ''; ?>">
                            <i class="fa-solid fa-circle-user"></i>
                            <div>
                                <strong><?php echo htmlspecialchars($l['nome']); ?></strong>
                            </div>
                        </a>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p style="padding:20px; color:#999;">Nessuna conversazione.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="chat-main">
            <?php if ($interlocutore): ?>
                <div class="chat-header">
                    <div class="btn-back-chat" onclick="window.location.href='chat.php'">
                        <i class="fa-solid fa-arrow-left"></i>
                    </div>
                    <strong><?php echo htmlspecialchars($interlocutore['nome'] . " " . $interlocutore['cognome']); ?></strong>
                </div>

                <div class="messages-area" id="chatBox"></div>

                <form class="chat-footer" id="msgForm" enctype="multipart/form-data">
                    <input type="hidden" name="id_destinatario" value="<?php echo $chat_con; ?>">
                    <label for="foto_chat" style="cursor:pointer; color:#666;"><i class="fa-solid fa-paperclip"></i></label>
                    <input type="file" id="foto_chat" name="foto_chat" accept="image/*" style="display:none;">
                    <input type="text" name="messaggio" placeholder="Scrivi..." autocomplete="off">
                    <button type="submit" class="btn-send"><i class="fa-solid fa-paper-plane"></i></button>
                </form>
            <?php else: ?>
                <div style="flex:1; display:flex; flex-direction:column; align-items:center; justify-content:center; color:#ccc;">
                    <i class="fa-solid fa-comments" style="font-size:4rem; margin-bottom:15px;"></i>
                    <p>Seleziona un contatto per chattare</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        const chatBox = document.getElementById("chatBox");
        const msgForm = document.getElementById("msgForm");

        function openImage(src) {
            document.getElementById('lightboxImg').src = src;
            document.getElementById('chatLightbox').style.display = 'flex';
        }
        function closeImage() { document.getElementById('chatLightbox').style.display = 'none'; }

        function loadMessages() {
            if(!chatBox) return;
            fetch('fetch_messages.php?with=<?php echo $chat_con; ?>')
            .then(res => res.text())
            .then(html => {
                const isAtBottom = chatBox.scrollTop + chatBox.clientHeight >= chatBox.scrollHeight - 100;
                chatBox.innerHTML = html;
                if (isAtBottom) chatBox.scrollTop = chatBox.scrollHeight;
            });
        }

        if(<?php echo $chat_con ? 'true' : 'false'; ?>) {
            loadMessages();
            setInterval(loadMessages, 3000);
        }

        if(msgForm) {
            msgForm.addEventListener('submit', (e) => {
                e.preventDefault();
                let formData = new FormData(msgForm);
                fetch('send_message.php', { method: 'POST', body: formData })
                .then(() => { msgForm.reset(); loadMessages(); });
            });
            document.getElementById("foto_chat").addEventListener('change', () => {
                msgForm.dispatchEvent(new Event('submit'));
            });
        }
    </script>
</body>
</html>