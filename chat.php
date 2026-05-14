<?php
session_start();
require_once 'db_connection.php';

// 1. Controllo accesso
if (!isset($_SESSION["loggedin"])) {
    header("Location: login.php");
    exit;
}

$id_utente = $_SESSION["id"];
$chat_con = isset($_GET['with']) ? intval($_GET['with']) : null;
$id_libro_contesto = isset($_GET['id_libro']) ? intval($_GET['id_libro']) : null;

// --- NOTIFICHE ---
if ($chat_con) {
    $sql_update = "UPDATE Messaggi SET letto = 1 WHERE IdDestinatario = ? AND IdMittente = ? AND letto = 0";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("ii", $id_utente, $chat_con);
    $stmt_update->execute();
}

// Configurazione Crittografia
$key = "Debook_Secret_2026_Safe";
$iv = "1234567890123456"; 

// 2. Recupero interlocutore e contesto
$interlocutore = null;
$libro_info = null;

if ($chat_con) {
    $stmt = $conn->prepare("SELECT nome, cognome FROM Utenti WHERE IdUtente = ?");
    $stmt->bind_param("i", $chat_con);
    $stmt->execute();
    $interlocutore = $stmt->get_result()->fetch_assoc();
    
    if ($id_libro_contesto) {
        $res_l = $conn->query("SELECT L.*, A.titolo FROM Libri L JOIN AnagraficaLibri A ON L.IdAnag = A.IdAnag WHERE L.IdLibro = $id_libro_contesto");
        $libro_info = $res_l->fetch_assoc();
    }
}

// 3. Lista conversazioni
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
        body { background-color: var(--bg-page); margin: 0; font-family: Arial, sans-serif; overflow: hidden; }
        
        .chat-layout { 
            display: flex; 
            width: 95%; 
            max-width: 1100px; 
            height: 85vh; 
            margin: 20px auto; 
            background: white; 
            border-radius: 20px; 
            box-shadow: var(--shadow); 
            overflow: hidden; 
        }
        
        .chat-sidebar { width: 30%; background: #f9f9f9; border-right: 1px solid #ddd; overflow-y: auto; }
        .sidebar-header { padding: 15px; background: #eee; font-weight: bold; }
        .user-item { display: block; padding: 15px; text-decoration: none; color: #333; border-bottom: 1px solid #eee; }
        .user-item.active { background: var(--accent-beige); font-weight: bold; }

        .chat-main { width: 70%; display: flex; flex-direction: column; background: #fff; position: relative; }
        .chat-header-top { padding: 15px 25px; border-bottom: 1px solid #eee; background: #fff; z-index: 10; }
        
        .messages-area { flex: 1; padding: 20px; overflow-y: auto; background: #fafafa; display: flex; flex-direction: column; gap: 8px; }
        
        /* Correzione immagini nei messaggi */
        .msg { max-width: 70%; padding: 10px 15px; border-radius: 18px; font-size: 0.95rem; position: relative; }
        .msg img { max-width: 100%; max-height: 300px; object-fit: cover; border-radius: 10px; cursor: zoom-in; display: block; }
        
        .sent { background: var(--accent-beige); align-self: flex-end; border-bottom-right-radius: 4px; }
        .received { background: #e2e2e2; align-self: flex-start; border-bottom-left-radius: 4px; }
        
        /* --- STILE LIGHTBOX --- */
        .lightbox-overlay {
            display: none; position: fixed; z-index: 9999; top: 0; left: 0;
            width: 100%; height: 100%; background: rgba(0, 0, 0, 0.9);
            align-items: center; justify-content: center;
        }
        .lightbox-overlay img { max-width: 90%; max-height: 85%; border-radius: 10px; }
        .lb-actions { position: absolute; top: 20px; right: 20px; display: flex; gap: 20px; }
        .lb-btn {
            color: white; font-size: 30px; text-decoration: none; cursor: pointer;
            background: rgba(255,255,255,0.1); width: 50px; height: 50px;
            display: flex; align-items: center; justify-content: center; border-radius: 50%;
        }

        .chat-input-bar { padding: 15px; border-top: 1px solid #eee; display: flex; gap: 10px; align-items: center; background: #fff; }
        .chat-input-bar input[type="text"] { flex: 1; padding: 12px 20px; border-radius: 30px; border: 2px solid #eee; outline: none; font-size: 16px; }
        .btn-send-chat { background: var(--dark-text); color: white; border: none; border-radius: 50%; width: 45px; height: 45px; cursor: pointer; }

        /* --- FIX RESPONSIVE --- */
        @media (max-width: 768px) {
            body { overflow: auto; }
            .chat-layout { flex-direction: column; width: 100%; height: 92vh; margin: 0; border-radius: 0; }
            .chat-sidebar { 
                width: 100%; height: 80px; display: flex; flex-direction: row; 
                overflow-x: auto; overflow-y: hidden; border-right: none; border-bottom: 1px solid #ddd;
            }
            .sidebar-header { display: none; }
            .user-item { min-width: 120px; padding: 10px; border-bottom: none; border-right: 1px solid #eee; text-align: center; }
            .chat-main { width: 100%; flex: 1; }
            .msg { max-width: 85%; }
        }
    </style>
</head>
<body>

    <div id="chatLightbox" class="lightbox-overlay" onclick="closeImage()">
        <div class="lb-actions">
            <a id="downloadBtn" href="" download class="lb-btn" onclick="event.stopPropagation();"><i class="fa-solid fa-download"></i></a>
            <div class="lb-btn" onclick="closeImage()"><i class="fa-solid fa-xmark"></i></div>
        </div>
        <img id="lightboxImg" src="" onclick="event.stopPropagation();">
    </div>

    <header class="header-nav" style="padding: 10px 5%; background: #fff; position: sticky; top: 0; z-index: 1000;">
        <a href="index.php"><img src="immagini/tastologo.png" alt="Logo" style="height:35px;"></a>
        <a href="index.php" style="text-decoration:none; color:black; font-weight: bold;"><i class="fa-solid fa-house"></i> Home</a>
    </header>

    <div class="chat-layout">
        <div class="chat-sidebar">
            <?php while($l = $lista_chat->fetch_assoc()): ?>
                <a href="chat.php?with=<?php echo $l['IdUtente']; ?>" class="user-item <?php echo ($chat_con == $l['IdUtente']) ? 'active' : ''; ?>">
                    <i class="fa-solid fa-user-circle"></i><br><?php echo htmlspecialchars($l['nome']); ?>
                </a>
            <?php endwhile; ?>
        </div>

        <div class="chat-main">
            <?php if ($interlocutore): ?>
                <div class="chat-header-top">
                    <strong><i class="fa-solid fa-circle"></i> <?php echo htmlspecialchars($interlocutore['nome'] . " " . $interlocutore['cognome']); ?></strong>
                </div>

                <div class="messages-area" id="chatBox"></div>

                <form class="chat-input-bar" id="msgForm" enctype="multipart/form-data">
                    <input type="hidden" name="id_destinatario" value="<?php echo $chat_con; ?>">
                    <label for="foto_chat" style="cursor:pointer; color:#888; font-size:1.5rem;"><i class="fa-solid fa-paperclip"></i></label>
                    <input type="file" id="foto_chat" name="foto_chat" accept="image/*" style="display:none;">
                    <input type="text" name="messaggio" placeholder="Scrivi un messaggio..." autocomplete="off">
                    <button type="submit" class="btn-send-chat"><i class="fa-solid fa-paper-plane"></i></button>
                </form>
            <?php else: ?>
                <div style="flex:1; display:flex; flex-direction:column; align-items:center; justify-content:center; color:#ccc; text-align:center; padding: 20px;">
                    <i class="fa-solid fa-comments" style="font-size:4rem; margin-bottom:10px;"></i>
                    <p>Seleziona una conversazione in alto per iniziare a chattare.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        const chatBox = document.getElementById("chatBox");
        const msgForm = document.getElementById("msgForm");
        const fileInput = document.getElementById("foto_chat");

        function openImage(src) {
            const lb = document.getElementById('chatLightbox');
            const img = document.getElementById('lightboxImg');
            const dlBtn = document.getElementById('downloadBtn');
            img.src = src; dlBtn.href = src;
            lb.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function closeImage() {
            document.getElementById('chatLightbox').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        function loadMessages() {
            if(!chatBox) return;
            fetch('fetch_messages.php?with=<?php echo $chat_con; ?>')
            .then(response => response.text())
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

        function inviaMessaggio() {
            let formData = new FormData(msgForm);
            fetch('send_message.php', { method: 'POST', body: formData })
            .then(() => {
                msgForm.reset();
                loadMessages();
            });
        }

        if(msgForm) {
            msgForm.addEventListener('submit', (e) => { e.preventDefault(); inviaMessaggio(); });
        }

        if(fileInput) {
            fileInput.addEventListener('change', function() {
                if (this.files && this.files[0]) inviaMessaggio();
            });
        }

        document.addEventListener('keydown', (e) => { if (e.key === "Escape") closeImage(); });
    </script>
</body>
</html>