<?php
session_start();
require_once 'db_connection.php';

header('Content-Type: application/json');

// 1. Ricezione dati
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!isset($_SESSION["id"]) || !isset($data['id_libro'])) {
    echo json_encode(['success' => false, 'error' => 'Dati mancanti o sessione scaduta']);
    exit;
}

$id_libro = intval($data['id_libro']);
$id_acquirente = $_SESSION["id"];
$nome_acquirente = $_SESSION["nome"]; // Assicurati che 'nome' sia salvato in sessione al login

// 2. Recupero info libro e venditore
$query_info = "SELECT L.IdVenditore, A.titolo FROM Libri L 
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

// 3. Aggiornamento stato libro
$update = $conn->prepare("UPDATE Libri SET stato = 'venduto' WHERE IdLibro = ?");
$update->bind_param("i", $id_libro);

if ($update->execute()) {
    
    // 4. INVIO MESSAGGIO AUTOMATICO IN CHAT
    $testo_notifica = "SISTEMA: L'utente $nome_acquirente ha acquistato il tuo libro '$titolo_libro' tramite PayPal. Il pagamento è confermato. Contattalo per la consegna!";
    
    // Crittografia (Deve essere uguale alla tua chat)
    $key = "Debook_Secret_2026_Safe";
    $iv = "1234567890123456";
    $testo_criptato = openssl_encrypt($testo_notifica, 'aes-256-cbc', $key, 0, $iv);

    $msg_query = "INSERT INTO Messaggi (IdMittente, IdDestinatario, testo_criptato, data_invio) VALUES (?, ?, ?, NOW())";
    $msg_stmt = $conn->prepare($msg_query);
    $msg_stmt->bind_param("iis", $id_acquirente, $id_venditore, $testo_criptato);
    $msg_stmt->execute();

    // Risposta finale di successo
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $conn->error]);
}

$conn->close();