<?php
session_start();
require_once 'db_connection.php';

// 1. Controllo sicurezza e parametri
if (!isset($_SESSION["loggedin"]) || !isset($_GET['with'])) {
    exit();
}

$id_utente = $_SESSION["id"];
$chat_con = intval($_GET['with']);

// 2. Configurazione Crittografia (Identica a chat.php e send_message.php)
$key = "Debook_Secret_2026_Safe";
$iv = "1234567890123456"; 

// 3. Query per recuperare i messaggi
$q = "SELECT * FROM Messaggi 
      WHERE (IdMittente = ? AND IdDestinatario = ?) 
      OR (IdMittente = ? AND IdDestinatario = ?) 
      ORDER BY data_invio ASC";

$st = $conn->prepare($q);
$st->bind_param("iiii", $id_utente, $chat_con, $chat_con, $id_utente);
$st->execute();
$res = $st->get_result();

// 4. Generazione dell'HTML
while($m = $res->fetch_assoc()) {
    $is_mio = ($m['IdMittente'] == $id_utente);
    $is_eliminato = (isset($m['eliminato']) && $m['eliminato'] == 1);
    
    if (!$is_eliminato) {
        $testo = openssl_decrypt($m['testo_criptato'], 'aes-256-cbc', $key, 0, $iv);
    } else {
        $testo = "Questo messaggio è stato eliminato";
    }

    $classe_msg = "msg " . ($is_mio ? "sent" : "received");
    if ($is_eliminato) $classe_msg .= " msg-deleted";

    echo '<div class="' . $classe_msg . '">';
    
    // --- LOGICA PER LE IMMAGINI ---
    if (!$is_eliminato && strpos($testo, "FILE_IMAGE:") === 0) {
        // Estraiamo il percorso della foto togliendo il prefisso
        $percorso_foto = str_replace("FILE_IMAGE:", "", $testo);
        
        echo '<span>';
        echo '<img src="' . htmlspecialchars($percorso_foto) . '" 
                   style="max-width: 250px; border-radius: 15px; cursor: pointer; display: block; margin-bottom: 5px;" 
                   onclick="window.open(this.src)">';
        echo '</span>';
    } else {
        // --- LOGICA PER IL TESTO NORMALE ---
        echo '<span>';
        if ($is_eliminato) echo '<i class="fa-solid fa-ban"></i> ';
        echo htmlspecialchars($testo);
        echo '</span>';
    }

    // Tasto elimina (solo se mio e non già eliminato)
    if ($is_mio && !$is_eliminato) {
        // Se è una foto, puoi cambiare l'icona o tenerla uguale
        echo '<a href="elimina_messaggio.php?id=' . $m['IdMessaggio'] . '" 
                 onclick="return confirm(\'Vuoi eliminare questo messaggio?\')" 
                 class="btn-delete" style="margin-left: 10px;">
                 <i class="fa-solid fa-trash" style="font-size: 0.8rem;"></i>
              </a>';
    }
    
    echo '</div>';
}
?>