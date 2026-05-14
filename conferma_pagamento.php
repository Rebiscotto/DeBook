<?php
session_start();
require_once 'db_connection.php';

header('Content-Type: application/json');

$id_libro = isset($_POST['id_libro']) ? intval($_POST['id_libro']) : 0;
$id_acquirente = $_SESSION['id'] ?? 0;
$nome_acquirente = $_SESSION['nome'] ?? 'Un utente';

if ($id_libro === 0 || $id_acquirente === 0) {
    echo json_encode(['success' => false, 'error' => 'Dati sessione mancanti']);
    exit;
}

// 1. Info libro e venditore
$query_info = "SELECT L.IdVenditore, A.titolo FROM Libri L 
               JOIN AnagraficaLibri A ON L.IdAnag = A.IdAnag 
               WHERE L.IdLibro = ? AND L.stato = 'disponibile'";
$stmt_info = $conn->prepare($query_info);
$stmt_info->bind_param("i", $id_libro);
$stmt_info->execute();
$info = $stmt_info->get_result()->fetch_assoc();

if (!$info) {
    echo json_encode(['success' => false, 'error' => 'Libro non disponibile']);
    exit;
}

$id_venditore = $info['IdVenditore'];
$titolo_libro = $info['titolo'];

// 2. Aggiornamento stato libro e assegnazione acquirente
$update = $conn->prepare("UPDATE Libri SET stato = 'venduto', IdAcquirente = ? WHERE IdLibro = ?");
$update->bind_param("ii", $id_acquirente, $id_libro);

if ($update->execute()) {
    
    // --- PREPARAZIONE MESSAGGIO CRIPTATO ---
    $testo_notifica = "SISTEMA: $nome_acquirente ha acquistato il tuo libro '$titolo_libro'. Contattalo per la consegna!";
    
    $key = "Debook_Secret_2026_Safe"; // Deve essere uguale a chat.php
    $iv = "1234567890123456";        // Deve essere uguale a chat.php
    $criptato = openssl_encrypt($testo_notifica, 'AES-256-CBC', $key, 0, $iv);

    // 3. Invio messaggio (Usando il nome colonna TESTO_CRIPTATO)
    // Controlla che anche 'data_invio' e 'letto' siano corretti come nomi
    $msg_query = "INSERT INTO Messaggi (IdMittente, IdDestinatario, testo_criptato, data_invio, letto) VALUES (?, ?, ?, NOW(), 0)";
    $msg_stmt = $conn->prepare($msg_query);
    $msg_stmt->bind_param("iis", $id_acquirente, $id_venditore, $criptato);
    $msg_stmt->execute();

    // Risposta per il checkout.php
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $conn->error]);
}
$conn->close();