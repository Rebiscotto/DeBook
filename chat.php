<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION["loggedin"])) {
    header("Location: login.php");
    exit;
}

$id_utente = $_SESSION["id"];
$chat_con = isset($_GET['with']) ? intval($_GET['with']) : null;

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
        body { 
            background-color: var(--bg-page); 
            margin: 0; 
            padding: 0;
            font-family: 'Segoe UI', Arial, sans-serif; 
            height: 100vh; 
            display: flex; 
            flex-direction: column; 
            overflow: hidden; 
        }
        
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
            position: relative;
        }

        /* Sidebar */
        .chat-sidebar { 
            width: 35%; 
            border-right: 1px solid #eee; 
            display: flex; 
            flex-direction: column; 
            background: #fff;
        }
        .sidebar-header { 
            padding: 20px; 
            border-bottom: 1px solid #eee; 
            display: flex; 
            align-items: center; 
            justify-content: space-between; 
        }
        .conversations-list { overflow-y: auto; flex: 1; }
        
        .user-item { 
            display: flex; align-items: center; padding: 15px 20px; 
            text-decoration: none; color: #333; border-bottom: 1px solid #f9f9f9; transition: 0.2s;
        }
        .user-item:hover { background: #f5f5f5; }
        .user-item.active { background: #f0ebe3; font-weight: bold; }
        .user-item i { font-size: 2rem; margin-right: 15px; color: #bbb; }

        /* Area Chat Centrale */
        .chat-main { 
            width: 65%; 
            display: flex; 
            flex-direction: column; 
            background: #fff; 
            height: 100%;
        }
        .chat-header { 
            padding: 15px 20px; 
            display: flex; 
            align-items: center; 
            border-bottom: 1px solid #eee; 
            background: #fff;
        }
        .btn-back-chat { 
            display: none; 
            margin-right: 15px; font-size: 1.2rem; color: #333; cursor: pointer; 
        }

        .messages-area { 
            flex: 1; 
            padding: 20px; 
            overflow-y: auto; 
            background: #fdfbf9; 
            display: flex; 
            flex-direction: column; 
            gap: 10px; 
        }
        
        .msg { max-width: 75%; padding: 10px 15px; border-radius: 15px; font-size: 0.95rem; line-height: 1.4; position: relative; }
        .msg img { max-width: 100%; border-radius: 10px; margin-top: 5px; cursor: pointer; }
        .sent { background: #e2d9c8; align-self: flex-end; border-bottom-right-radius: 2px; }
        .received { background: #f0f0f0; align-self: flex-start; border-bottom-left-radius: 2px; }

        .chat-footer { 
            padding: 15px; 
            background: #fff; 
            border-top: 1px solid #eee; 
            display: flex; 
            align-items: center; 
            gap: 10px; 
        }
        .chat-footer input { flex: 1; padding: 12px 20px; border-radius: 25px; border: 1px solid #ddd; outline: none; font-size: 16px; }
        .btn-send { background: #333; color: #fff; border: none; width: 45px; height: 45px; border-radius: 50%; cursor: pointer; flex-shrink: 0; }

        /* --- LOGICA MOBILE --- */
        @media (max-width: 768px) {
            header.header-nav { display: none; }
            .chat-container { 
                width: 100%; 
                height: 100vh; 
                height: -webkit-fill-available;
                margin: 0; 
                border-radius: 0; 
            }
            
            <?php if (!$chat_con): ?>
                .chat-sidebar { width: 100%; border: none; }
                .chat-main { display: none; }
            <?php else: ?>
                .chat-sidebar { display: none; }
                .chat-main { width: 100%; }
                .btn-back-chat { display: block; }
                /* Padding extra per evitare il taglio su iOS/Android */
                .chat-footer { padding-bottom: calc(15px + env(safe-area-inset-bottom)); } 
            <?php endif; ?>
            
            .msg { max-width: 85%; }
        }

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
            <div class="sidebar-header">
                <a href="index.php">
                    <img src="immagini/tastologo.png" alt="Debook" style="height:25px;">
                </a>
                <span style="font-weight: bold;">Chat</span>
            </div>
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
                    <p style="padding:40px 20px; color:#999; text-align:center;">Nessuna conversazione.<br><small>Contatta un venditore per iniziare!</small></p>
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
                    <label for="foto_chat" style="cursor:pointer; color:#666; padding: 5px;">
                        <i class="fa-solid fa-paperclip" style="font-size: 1.2rem;"></i>
                    </label>
                    <input type="file" id="foto_chat" name="foto_chat" accept="image/*" style="display:none;">
                    <input type="text" name="messaggio" placeholder="Scrivi un messaggio..." autocomplete="off">
                    <button type="submit" class="btn-send"><i class="fa-solid fa-paper-plane"></i></button>
                </form>
            <?php else: ?>
                <div style="flex:1; display:flex; flex-direction:column; align-items:center; justify-content:center; color:#ccc; text-align:center; padding: 20px;">
                    <i class="fa-solid fa-comments" style="font-size:5rem; margin-bottom:15px; color:#eee;"></i>
                    <p style="color:#999;">Seleziona una conversazione per iniziare</p>
                    <a href="index.php" style="margin-top: 15px; color: #666; text-decoration: none; border: 1px solid #ddd; padding: 10px 20px; border-radius: 20px;">Torna alla Home</a>
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
                .then(() => { 
                    msgForm.reset(); 
                    loadMessages(); 
                });
            });
            
            document.getElementById("foto_chat").addEventListener('change', function() {
                if(this.files.length > 0) {
                    msgForm.dispatchEvent(new Event('submit'));
                }
            });
        }
    </script>
</body>
</html>