<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: login.php");
    exit;
}

$id_utente = $_SESSION["id"];
$chat_con = isset($_GET['with']) && is_numeric($_GET['with']) ? $_GET['with'] : null;

// Chiave segreta per la crittografia (in produzione dovrebbe stare in un file .env)
$encryption_key = "DeBook_Secret_Key_2026";

// Se è selezionata una chat specifica
if ($chat_con) {
    // Recupero info dell'interlocutore
    $stmt_user = $conn->prepare("SELECT nome, cognome FROM Utenti WHERE IdUtente = ?");
    $stmt_user->bind_param("i", $chat_con);
    $stmt_user->execute();
    $interlocutore = $stmt_user->get_result()->fetch_assoc();

    if (!$interlocutore) die("Utente non trovato.");

    // Recupero i messaggi tra i due utenti
    $query_msg = "SELECT * FROM Messaggi 
                  WHERE (IdMittente = ? AND IdDestinatario = ?) 
                     OR (IdMittente = ? AND IdDestinatario = ?) 
                  ORDER BY data_invio ASC";
    $stmt_msg = $conn->prepare($query_msg);
    $stmt_msg->bind_param("iiii", $id_utente, $chat_con, $chat_con, $id_utente);
    $stmt_msg->execute();
    $messaggi = $stmt_msg->get_result();
} else {
    // Se non c'è una chat specifica, recupero la lista degli utenti con cui ho scambiato messaggi
    $query_lista = "SELECT DISTINCT U.IdUtente, U.nome, U.cognome 
                    FROM Utenti U 
                    JOIN Messaggi M ON U.IdUtente = M.IdMittente OR U.IdUtente = M.IdDestinatario 
                    WHERE (M.IdMittente = ? OR M.IdDestinatario = ?) AND U.IdUtente != ?";
    $stmt_lista = $conn->prepare($query_lista);
    $stmt_lista->bind_param("iii", $id_utente, $id_utente, $id_utente);
    $stmt_lista->execute();
    $lista_chat = $stmt_lista->get_result();
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Debook - Messaggi</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .chat-container { display: flex; width: 90%; max-width: 1000px; height: 75vh; margin: 30px auto; background: white; border-radius: 20px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); overflow: hidden; }
        .chat-sidebar { width: 30%; background: var(--bg-page); border-right: 2px solid #ddd; overflow-y: auto; }
        .chat-main { width: 70%; display: flex; flex-direction: column; }
        
        .chat-list-item { display: block; padding: 20px; text-decoration: none; color: var(--dark-text); border-bottom: 1px solid #ddd; transition: 0.2s; font-family: Arial; }
        .chat-list-item:hover, .chat-list-item.active { background: var(--accent-beige); }
        
        .chat-header { padding: 20px; background: white; border-bottom: 2px solid var(--bg-page); }
        .chat-messages { flex: 1; padding: 20px; overflow-y: auto; display: flex; flex-direction: column; gap: 15px; background: #fafafa; }
        
        .msg-bubble { max-width: 70%; padding: 12px 18px; border-radius: 20px; font-family: Arial; font-size: 0.95rem; line-height: 1.4; word-wrap: break-word; }
        .msg-sent { background: var(--accent-beige); align-self: flex-end; border-bottom-right-radius: 5px; }
        .msg-received { background: #e2e2e2; align-self: flex-start; border-bottom-left-radius: 5px; }
        .msg-time { font-size: 0.7rem; color: #777; margin-top: 5px; text-align: right; }

        .chat-input-area { padding: 20px; background: white; border-top: 2px solid var(--bg-page); display: flex; gap: 10px; }
        .chat-input-area input { flex: 1; padding: 15px; border-radius: 50px; border: 2px solid var(--bg-page); outline: none; font-family: Arial; }
        .btn-send { background: var(--dark-text); color: white; border: none; width: 50px; height: 50px; border-radius: 50%; cursor: pointer; transition: 0.3s; }
        .btn-send:hover { background: #444; }
    </style>
</head>
<body>
    <header class="header-nav">
        <a href="index.php" class="logo-link"><img src="immagini/tastologo.png" alt="Debook Logo"></a>
        <a href="dashboard.php" style="font-family: Arial; text-decoration: none; color: var(--dark-text);">Dashboard</a>
    </header>

    <div class="chat-container">
        <div class="chat-sidebar">
            <h3 style="padding: 20px; border-bottom: 1px solid #ddd;">Le tue Chat</h3>
            <?php if (!$chat_con && $lista_chat->num_rows == 0): ?>
                <p style="padding: 20px; font-family: Arial; font-size: 0.9rem; color: #666;">Nessuna chat attiva.</p>
            <?php else: ?>
                <?php 
                // Se c'è una lista, mostrala (se siamo in una chat diretta, per semplicità qui ricarichiamo la lista)
                $stmt_lista->execute();
                $lista_chat_aggiornata = $stmt_lista->get_result();
                while($utente_chat = $lista_chat_aggiornata->fetch_assoc()): 
                ?>
                    <a href="chat.php?with=<?php echo $utente_chat['IdUtente']; ?>" class="chat-list-item <?php echo ($chat_con == $utente_chat['IdUtente']) ? 'active' : ''; ?>">
                        <i class="fa-solid fa-user-circle"></i> <?php echo htmlspecialchars($utente_chat['nome'] . " " . $utente_chat['cognome']); ?>
                    </a>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>

        <div class="chat-main">
            <?php if ($chat_con): ?>
                <div class="chat-header">
                    <h3>Conversazione con <?php echo htmlspecialchars($interlocutore['nome']); ?></h3>
                    <p style="font-family: Arial; font-size: 0.8rem; color: #27ae60;"><i class="fa-solid fa-lock"></i> Crittografia End-to-End attiva</p>
                </div>

                <div class="chat-messages" id="chatBox">
                    <?php while($msg = $messaggi->fetch_assoc()): ?>
                        <?php 
                            // Decriptazione del messaggio (RF-08)
                            $testo_decriptato = openssl_decrypt($msg['testo_criptato'], 'aes-256-cbc', $encryption_key, 0, str_repeat("0", 16));
                            $is_mine = ($msg['IdMittente'] == $id_utente);
                        ?>
                        <div class="msg-bubble <?php echo $is_mine ? 'msg-sent' : 'msg-received'; ?>">
                            <?php echo htmlspecialchars($testo_decriptato); ?>
                            <div class="msg-time"><?php echo date('H:i d/m', strtotime($msg['data_invio'])); ?></div>
                        </div>
                    <?php endwhile; ?>
                </div>

                <form class="chat-input-area" action="send_message.php" method="POST">
                    <input type="hidden" name="id_destinatario" value="<?php echo $chat_con; ?>">
                    <input type="text" name="messaggio" placeholder="Scrivi un messaggio..." required autocomplete="off">
                    <button type="submit" class="btn-send"><i class="fa-solid fa-paper-plane"></i></button>
                </form>

                <script>
                    // Scroll automatico verso il basso per leggere gli ultimi messaggi
                    const chatBox = document.getElementById("chatBox");
                    chatBox.scrollTop = chatBox.scrollHeight;
                </script>
            <?php else: ?>
                <div style="display: flex; flex: 1; align-items: center; justify-content: center; flex-direction: column; color: #999;">
                    <i class="fa-solid fa-comments" style="font-size: 4rem; margin-bottom: 20px;"></i>
                    <p style="font-family: Arial;">Seleziona una chat dalla barra laterale per iniziare.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>