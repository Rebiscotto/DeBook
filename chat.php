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

// Configurazione Crittografia (Deve essere IDENTICA a send_message.php)
$key = "Debook_Secret_2026_Safe";
$iv = "1234567890123456"; // 16 caratteri

// 1. Recupero dati dell'interlocutore
$interlocutore = null;
if ($chat_con) {
    $stmt = $conn->prepare("SELECT nome, cognome FROM Utenti WHERE IdUtente = ?");
    $stmt->bind_param("i", $chat_con);
    $stmt->execute();
    $interlocutore = $stmt->get_result()->fetch_assoc();
}

// 2. Recupero lista delle conversazioni attive per la sidebar
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
        .btn-vota { font-size: 0.7rem; background: var(--accent-beige); padding: 5px 12px; border-radius: 10px; text-decoration: none; color: black; font-weight: bold; }
        
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

        /* SIDEBAR */
        .chat-sidebar { width: 30%; background: #f9f9f9; border-right: 1px solid #ddd; overflow-y: auto; }
        .sidebar-header { padding: 15px; background: #eee; font-weight: bold; font-family: Arial; }
        .user-item { display: block; padding: 15px; text-decoration: none; color: #333; border-bottom: 1px solid #eee; font-family: Arial; transition: 0.2s; }
        .user-item:hover, .user-item.active { background: var(--accent-beige); font-weight: bold; }

        /* AREA MESSAGGI */
        .chat-main { width: 70%; display: flex; flex-direction: column; background: #fff; }
        .chat-header { padding: 15px 25px; border-bottom: 1px solid #eee; font-family: Arial; font-weight: bold; display: flex; justify-content: space-between; }
        .messages-area { flex: 1; padding: 20px; overflow-y: auto; background: #fafafa; display: flex; flex-direction: column; gap: 10px; }
        
        .msg { max-width: 75%; padding: 12px 18px; border-radius: 20px; font-family: Arial; font-size: 0.95rem; line-height: 1.4; }
        .sent { background: var(--accent-beige); align-self: flex-end; border-bottom-right-radius: 5px; }
        .received { background: #e2e2e2; align-self: flex-start; border-bottom-left-radius: 5px; }

        /* INPUT BAR FISSA */
        .chat-input-bar { padding: 15px; border-top: 1px solid #eee; display: flex; gap: 10px; align-items: center; background: white; }
        .chat-input-bar input { flex: 1; padding: 12px 20px; border-radius: 30px; border: 2px solid #eee; outline: none; font-size: 16px; font-family: Arial; }

        /* TASTO INVIO ROTONDO - Corretto per Mobile */
        .btn-send-chat {
            background-color: var(--dark-text);
            color: white;
            border: none;
            border-radius: 50%;
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            flex-shrink: 0; /* Impedisce la deformazione su mobile */
            transition: transform 0.2s;
        }
        .btn-send-chat:hover { transform: scale(1.1); }

        /* =======================================
           RESPONSIVE MOBILE
           ======================================= */
        @media (max-width: 768px) {
            .chat-layout { flex-direction: column; width: 100%; height: 90vh; margin: 0; border-radius: 0; }
            .chat-sidebar { width: 100%; height: 15%; border-right: none; border-bottom: 2px solid #ddd; display: flex; overflow-x: auto; }
            .user-item { border-bottom: none; border-right: 1px solid #eee; min-width: 140px; padding: 10px; text-align: center; font-size: 0.8rem; }
            .chat-main { width: 100%; height: 85%; }
            .chat-input-bar { padding: 10px; }
        }
    </style>
</head>
<body>

    <header class="header-nav">
        <a href="index.php" class="logo-link"><img src="immagini/tastologo.png" alt="Debook Logo"></a>
        <a href="index.php" style="text-decoration:none; color:black;"><i class="fa-solid fa-house"></i></a>
        <a href="lascia_feedback.php?to=<?php echo $chat_con; ?>" class="btn-vota">Vota Venditore</a>
    </header>

    <div class="chat-layout">
        <div class="chat-sidebar">
            <div class="sidebar-header">Chat</div>
            <?php while($l = $lista_chat->fetch_assoc()): ?>
                <a href="chat.php?with=<?php echo $l['IdUtente']; ?>" class="user-item <?php echo ($chat_con == $l['IdUtente']) ? 'active' : ''; ?>">
                    <i class="fa-solid fa-user-circle"></i> <?php echo htmlspecialchars($l['nome']); ?>
                </a>
            <?php endwhile; ?>
        </div>

        <div class="chat-main">
            <?php if ($interlocutore): ?>
                <div class="chat-header">
                    <span>Chat con <?php echo htmlspecialchars($interlocutore['nome'] . " " . $interlocutore['cognome']); ?></span>
                    <span style="font-size: 0.7rem; color: #27ae60;"><i class="fa-solid fa-lock"></i> Crittografata</span>
                </div>

                <div class="messages-area" id="chatBox">
                    <?php
                    // Recupero messaggi ordinati per data (ASC = vecchi sopra, nuovi sotto)
                    $q = "SELECT * FROM Messaggi WHERE (IdMittente = ? AND IdDestinatario = ?) OR (IdMittente = ? AND IdDestinatario = ?) ORDER BY data_invio ASC";
                    $st = $conn->prepare($q);
                    $st->bind_param("iiii", $id_utente, $chat_con, $chat_con, $id_utente);
                    $st->execute();
                    $res = $st->get_result();

                    while($m = $res->fetch_assoc()):
                        // Decriptazione
                        $dec = openssl_decrypt($m['testo_criptato'], 'aes-256-cbc', $key, 0, $iv);
                    ?>
                        <div class="msg <?php echo ($m['IdMittente'] == $id_utente) ? 'sent' : 'received'; ?>">
                            <?php echo htmlspecialchars($dec); ?>
                        </div>
                    <?php endwhile; ?>
                </div>

                <form class="chat-input-bar" action="send_message.php" method="POST">
                    <input type="hidden" name="id_destinatario" value="<?php echo $chat_con; ?>">
                    <input type="text" name="messaggio" placeholder="Scrivi un messaggio..." required autocomplete="off">
                    <button type="submit" class="btn-send-chat"><i class="fa-solid fa-paper-plane"></i></button>
                </form>

            <?php else: ?>
                <div style="flex:1; display:flex; flex-direction:column; align-items:center; justify-content:center; color:#ccc; font-family:Arial;">
                    <i class="fa-solid fa-comments" style="font-size:4rem; margin-bottom:15px;"></i>
                    <p>Seleziona una conversazione</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    
        <script>
        const chatBox = document.getElementById("chatBox");
        const chatForm = document.querySelector(".chat-input-bar");
        const msgInput = document.querySelector("input[name='messaggio']");
        
        // 1. Richiesta Permesso Notifiche al primo caricamento
        document.addEventListener('DOMContentLoaded', function() {
            if ("Notification" in window && Notification.permission !== "granted" && Notification.permission !== "denied") {
                Notification.requestPermission();
            }
            if(chatBox) chatBox.scrollTop = chatBox.scrollHeight;
        });

        <?php if($chat_con): ?>
            let messageCount = chatBox ? chatBox.children.length : 0;

            // 2. Funzione per caricare i messaggi in tempo reale
            function loadMessages() {
                fetch('fetch_messages.php?with=<?php echo $chat_con; ?>')
                .then(response => response.text())
                .then(html => {
                    // Contiamo quanti messaggi arrivano dal server
                    let tempDiv = document.createElement('div');
                    tempDiv.innerHTML = html;
                    let newCount = tempDiv.querySelectorAll('.msg').length;

                    // Se ci sono messaggi NUOVI
                    if (newCount > messageCount) {
                        chatBox.innerHTML = html;
                        chatBox.scrollTop = chatBox.scrollHeight; // Scroll automatico in basso
                        
                        // Controllo per le NOTIFICHE: l'ultimo messaggio è di chi lo riceve?
                        let lastMsg = tempDiv.lastElementChild;
                        if(lastMsg && lastMsg.classList.contains('received')) {
                            // Lancia la notifica del telefono/PC
                            if ("Notification" in window && Notification.permission === "granted") {
                                new Notification("Debook", {
                                    body: "Hai ricevuto un nuovo messaggio!",
                                    icon: "immagini/tastologo.png"
                                });
                            }
                        }
                        messageCount = newCount;
                    } else if (chatBox.innerHTML.trim() === "") {
                        // Se la chat era vuota al caricamento iniziale
                        chatBox.innerHTML = html;
                    }
                });
            }

            // Esegui il controllo ogni 2.5 secondi
            setInterval(loadMessages, 2500);

            // 3. Invio Messaggio SENZA ricaricare la pagina (AJAX)
            if(chatForm) {
                chatForm.addEventListener('submit', function(e) {
                    e.preventDefault(); // Blocca il ricaricamento della pagina
                    
                    let formData = new FormData(chatForm);
                    
                    fetch('send_message.php', {
                        method: 'POST',
                        body: formData
                    }).then(() => {
                        msgInput.value = ''; // Svuota la casella di testo
                        loadMessages(); // Ricarica subito la chat per far apparire il tuo messaggio
                    });
                });
            }
        <?php endif; ?>
    </script>
    
</body>
</html>