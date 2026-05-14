<?php
session_start();
require_once 'db_connection.php';

header('Content-Type: application/json');

$id_libro = isset($_POST['id_libro']) ? intval($_POST['id_libro']) : 0;
$id_acquirente = $_SESSION['id'];
$nome_acquirente = $_SESSION['nome'] ?? 'Un utente'; // Fallback se nome non settato

if ($id_libro === 0 || !$id_acquirente) {
    echo json_encode(['success' => false, 'error' => 'Dati mancanti']);
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

// 2. Aggiornamento stato libro
$update = $conn->prepare("UPDATE Libri SET stato = 'venduto', IdAcquirente = ? WHERE IdLibro = ?");
$update->bind_param("ii", $id_acquirente, $id_libro);

if ($update->execute()) {
    
    // --- CRITTOGRAFIA MESSAGGIO ---
    $testo_notifica = "SISTEMA: $nome_acquirente ha acquistato il tuo libro '$titolo_libro'. Contattalo!";
    
    $key = "Debook_Secret_2026_Safe"; // DEVE ESSERE LA STESSA DI CHAT.PHP
    $iv = "1234567890123456";        // DEVE ESSERE LA STESSA DI CHAT.PHP
    $testo_criptato = openssl_encrypt($testo_notifica, 'AES-256-CBC', $key, 0, $iv);
    // ------------------------------

    // 3. Invio messaggio criptato
    $msg_query = "INSERT INTO Messaggi (IdMittente, IdDestinatario, testo, data_invio, letto) VALUES (?, ?, ?, NOW(), 0)";
    $msg_stmt = $conn->prepare($msg_query);
    $msg_stmt->bind_param("iis", $id_acquirente, $id_venditore, $testo_criptato);
    
    if($msg_stmt->execute()){
        echo json_encode(['success' => true]);
    } else {
        // Se fallisce il messaggio, mandiamo comunque success true perché il libro è venduto
        // Ma logghiamo l'errore per te
        echo json_encode(['success' => true, 'msg_error' => $conn->error]);
    }
} else {
    echo json_encode(['success' => false, 'error' => $conn->error]);
}
$conn->close();