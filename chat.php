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

// --- INIZIO VERSIONE PRECISA NOTIFICHE ---
if ($chat_con) {
    // Segna come letti SOLO i messaggi inviati dall'utente con cui sto parlando ora
    $sql_update = "UPDATE Messaggi SET letto = 1 WHERE IdDestinatario = ? AND IdMittente = ? AND letto = 0";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("ii", $id_utente, $chat_con);
    $stmt_update->execute();
}
// --- FINE VERSIONE PRECISA ---

// Configurazione Crittografia
$key = "Debook_Secret_2026_Safe";
$iv = "1234567890123456"; 

// 2. Recupero dati interlocutore e libro contestuale
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

// 3. Recupero lista conversazioni attive
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
        body { background-color: var(--bg-page); margin: 0; font-family: Arial, sans-serif; }
        .chat-layout { display: flex; width: 95%; max-width: 1100px; height: 85vh; margin: 20px auto; background: white; border-radius: 20px; box-shadow: var(--shadow); overflow: hidden; }
        
        .chat-sidebar { width: 30%; background: #f9f9f9; border-right: 1px solid #ddd; overflow-y: auto; }
        .sidebar-header { padding: 15px; background: #eee; font-weight: bold; }
        .user-item { display: block; padding: 15px; text-decoration: none; color: #333; border-bottom: 1px solid #eee; }
        .user-item.active { background: var(--accent-beige); font-weight: bold; }

        .chat-main { width: 70%; display: flex; flex-direction: column; background: #fff; position: relative; }
        .chat-header-top { padding: 15px 25px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; background: #fff; z-index: 10; }
        
        .context-bar { background: #f1f1f1; padding: 10px 20px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #ddd; font-size: 0.85rem; }
        .btn-buy-now { background: #27ae60; color: white; padding: 5px 15px; border-radius: 50px; text-decoration: none; font-weight: bold; transition: 0.2s; }
        .btn-buy-now:hover { background: #219150; }

        .messages-area { flex: 1; padding: 20px; overflow-y: auto; background: #fafafa; display: flex; flex-direction: column; gap: 8px; }
        .msg { max-width: 70%; padding: 10px 15px; border-radius: 18px; font-size: 0.95rem; position: relative; display: flex; align-items: center; justify-content: space-between; gap: 10px; }
        .sent { background: var(--accent-beige); align-self: flex-end; border-bottom-right-radius: 4px; }
        .received { background: #e2e2e2; align-self: flex-start; border-bottom-left-radius: 4px; }
        .msg img { max-width: 200px; border-radius: 10px; display: block; margin-bottom: 5px; }

        .btn-delete { color: #ff4d4d; font-size: 0.75rem; text-decoration: none; display: none; }
        .msg.sent:hover .btn-delete { display: inline-block; }
        .msg-deleted { opacity: 0.6; font-style: italic; color: #888; background: #f0f0f0 !important; }

        .chat-input-bar { padding: 15px; border-top: 1px solid #eee; display: flex; gap: 10px; align-items: center; }
        .chat-input-bar input[type="text"] { flex: 1; padding: 12px 20px; border-radius: 30px; border: 2px solid #eee; outline: none; }
        .btn-send-chat { background: var(--dark-text); color: white; border: none; border-radius: 50%; width: 45px; height: 45px; cursor: pointer; }
        
        @media (max-width: 768px) {
            .chat-layout { flex-direction: column; width: 100%; height: 90vh; margin: 0; }
            .chat-sidebar { width: 100%; height: 15%; display: flex; }
            .chat-main { width: 100%; height: 85%; }
        }
    </style>
</head>
<body>

    <header class="header-nav" style="padding: 10px 5%;">
        <a href="index.php"><img src="immagini/tastologo.png" alt="Logo" style="height:35px;"></a>
        <a href="index.php" style="text-decoration:none; color:black;"><i class="fa-solid fa-house"></i> Home</a>
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
                    <a href="profilo.php?id=<?php echo $chat_con; ?>" style="text-decoration:none; color:black; display:flex; align-items:center; gap:8px;">
                        <i class="fa-solid fa-circle-user" style="font-size:1.4rem;"></i>
                        <strong><?php echo htmlspecialchars($interlocutore['nome'] . " " . $interlocutore['cognome']); ?></strong>
                    </a>
                    <a href="lascia_feedback.php?to=<?php echo $chat_con; ?>" class="btn-vota"><i class="fa-solid fa-star"></i> Vota</a>
                </div>

                <?php if ($libro_info): ?>
                <div class="context-bar">
                    <span>Interesse per: <strong><?php echo htmlspecialchars($libro_info['titolo']); ?></strong></span>
                    <a href="checkout.php?id_libro=<?php echo $id_libro_contesto; ?>" class="btn-buy-now">
                        <i class="fa-solid fa-cart-shopping"></i> COMPRA ORA
                    </a>
                </div>
                <?php endif; ?>

                <div class="messages-area" id="chatBox"></div>

                <form class="chat-input-bar" id="msgForm" enctype="multipart/form-data">
                    <input type="hidden" name="id_destinatario" value="<?php echo $chat_con; ?>">
                    
                    <label for="foto_chat" style="cursor:pointer; color:#888; font-size:1.2rem;">
                        <i class="fa-solid fa-paperclip"></i>
                    </label>
                    <input type="file" id="foto_chat" name="foto_chat" accept="image/*" style="display:none;">
                    
                    <input type="text" name="messaggio" placeholder="Scrivi..." autocomplete="off">
                    <button type="submit" class="btn-send-chat"><i class="fa-solid fa-paper-plane"></i></button>
                </form>

            <?php else: ?>
                <div style="flex:1; display:flex; flex-direction:column; align-items:center; justify-content:center; color:#ccc;">
                    <i class="fa-solid fa-comments" style="font-size:4rem; margin-bottom:10px;"></i>
                    <p>Seleziona una chat</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        const chatBox = document.getElementById("chatBox");
        const msgForm = document.getElementById("msgForm");
        const fileInput = document.getElementById("foto_chat");

        function loadMessages() {
            if(!chatBox) return;
            fetch('fetch_messages.php?with=<?php echo $chat_con; ?>')
            .then(response => response.text())
            .then(html => {
                const cleanCurrent = chatBox.innerHTML.trim();
                const cleanNew = html.trim();
                if (cleanCurrent !== cleanNew) {
                    const isAtBottom = chatBox.scrollTop + chatBox.clientHeight >= chatBox.scrollHeight - 50;
                    chatBox.innerHTML = html;
                    if (isAtBottom) chatBox.scrollTop = chatBox.scrollHeight;
                }
            });
        }

        if(<?php echo $chat_con ? 'true' : 'false'; ?>) {
            loadMessages();
            setInterval(loadMessages, 3000);
        }

        // Funzione per inviare il form (sia testo che file)
        function inviaMessaggio() {
            let formData = new FormData(msgForm);
            fetch('send_message.php', { method: 'POST', body: formData })
            .then(() => {
                msgForm.reset();
                loadMessages();
            });
        }

        // Invio tramite tasto o Enter
        if(msgForm) {
            msgForm.addEventListener('submit', function(e) {
                e.preventDefault();
                inviaMessaggio();
            });
        }

        // INVIO IMMEDIATO QUANDO SI SELEZIONA UNA FOTO
        if(fileInput) {
            fileInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    inviaMessaggio();
                }
            });
        }
    </script>
</body>
</html>