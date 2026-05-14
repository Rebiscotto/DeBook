<?php
session_start();
require_once 'db_connection.php';

header('Content-Type: application/json');

// 1. Controllo dati e sessione
$id_libro = isset($_POST['id_libro']) ? intval($_POST['id_libro']) : 0;
$id_acquirente = $_SESSION['id'] ?? 0;
$nome_acquirente = $_SESSION['nome'] ?? 'Un utente';

if ($id_libro === 0 || $id_acquirente === 0) {
    echo json_encode(['success' => false, 'error' => 'Sessione scaduta o dati mancanti']);
    exit;
}

// 2. Recupero info libro e venditore prima della modifica
// Selezioniamo anche IdVenditore e Titolo per poter inviare il messaggio in chat
$query_info = "SELECT L.IdVenditore, A.titolo 
               FROM Libri L 
               JOIN AnagraficaLibri A ON L.IdAnag = A.IdAnag 
               WHERE L.IdLibro = ? AND L.stato = 'disponibile'";

$stmt_info = $conn->prepare($query_info);
$stmt_info->bind_param("i", $id_libro);
$stmt_info->execute();
$info = $stmt_info->get_result()->fetch_assoc();

if (!$info) {
    echo json_encode(['success' => false, 'error' => 'Libro non disponibile o già venduto']);
    exit;
}

$id_venditore = $info['IdVenditore'];
$titolo_libro = $info['titolo'];

// 3. Esecuzione transazione: Aggiorniamo stato e IdAcquirente
// Questo farà apparire il libro in "I miei ordini" e lo toglierà dal mercatino/venditore
$update = $conn->prepare("UPDATE Libri SET stato = 'venduto', IdAcquirente = ? WHERE IdLibro = ?");
$update->bind_param("ii", $id_acquirente, $id_libro);

if ($update->execute()) {
    
    // 4. Invio messaggio automatico in Chat
    // Il messaggio parte dall'acquirente verso il venditore
    $testo_notifica = "SISTEMA: L'utente $nome_acquirente ha acquistato il tuo libro '$titolo_libro'. Contattalo per organizzare lo scambio!";
    
    // Inseriamo il messaggio con 'letto = 0' per attivare il pallino rosso sulla index del venditore
    $msg_query = "INSERT INTO Messaggi (IdMittente, IdDestinatario, testo, data_invio, letto) VALUES (?, ?, ?, NOW(), 0)";
    $msg_stmt = $conn->prepare($msg_query);
    $msg_stmt->bind_param("iis", $id_acquirente, $id_venditore, $testo_notifica);
    $msg_stmt->execute();

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $conn->error]);
}

$conn->close();