<?php
session_start();
require_once 'db_connection.php';

// 1. Controllo sicurezza e parametri
if (!isset($_SESSION["loggedin"]) || !isset($_GET['with'])) {
    exit();
}

$id_utente = $_SESSION["id"];
$chat_con = intval($_GET['with']);

// 2. Configurazione Crittografia (Deve essere identica a chat.php e send_message.php)
$key = "Debook_Secret_2026_Safe";
$iv = "1234567890123456"; 

// 3. Query per recuperare i messaggi tra i due utenti
$q = "SELECT * FROM Messaggi 
      WHERE (IdMittente = ? AND IdDestinatario = ?) 
      OR (IdMittente = ? AND IdDestinatario = ?) 
      ORDER BY data_invio ASC";

$st = $conn->prepare($q);
$st->bind_param("iiii", $id_utente, $chat_con, $chat_con, $id_utente);
$st->execute();
$res = $st->get_result();

// 4. Generazione dell'HTML (Deve rispecchiare esattamente la struttura di chat.php)
while($m = $res->fetch_assoc()) {
    $is_mio = ($m['IdMittente'] == $id_utente);
    $is_eliminato = (isset($m['eliminato']) && $m['eliminato'] == 1);
    
    // Decriptazione: se il messaggio è eliminato non serve decriptare il vecchio testo
    if (!$is_eliminato) {
        $testo = openssl_decrypt($m['testo_criptato'], 'aes-256-cbc', $key, 0, $iv);
    } else {
        $testo = "Questo messaggio è stato eliminato";
    }

    // Costruzione della classe CSS
    $classe_msg = "msg " . ($is_mio ? "sent" : "received");
    if ($is_eliminato) {
        $classe_msg .= " msg-deleted";
    }

    // Output HTML
    echo '<div class="' . $classe_msg . '">';
    echo '<span>';
    if ($is_eliminato) {
        echo '<i class="fa-solid fa-ban"></i> ';
    }
    echo htmlspecialchars($testo);
    echo '</span>';

    // Aggiungiamo il tasto elimina solo se il messaggio è mio e non è già stato eliminato
    if ($is_mio && !$is_eliminato) {
        echo '<a href="elimina_messaggio.php?id=' . $m['IdMessaggio'] . '" 
                 onclick="return confirm(\'Vuoi eliminare questo messaggio?\')" 
                 class="btn-delete">
                 <i class="fa-solid fa-trash"></i>
              </a>';
    }
    echo '</div>';
}
?>