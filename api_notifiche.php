<?php
session_start();
require_once 'db_connection.php';

// Impostiamo l'header per rispondere in formato JSON
header('Content-Type: application/json');

// 1. Controllo se l'utente è loggato
if (!isset($_SESSION["id"])) {
    echo json_encode(['non_letti' => 0]);
    exit;
}

$id_utente = $_SESSION["id"];

// 2. Controllo se stiamo chiedendo le notifiche per una chat specifica o totali
// Se nella chiamata JS passi ?with=ID, conterrà solo i non letti di quell'utente
$chat_con = isset($_GET['with']) ? intval($_GET['with']) : null;

if ($chat_con) {
    // Conta i messaggi non letti solo da un mittente specifico
    $sql = "SELECT COUNT(*) as totale FROM Messaggi WHERE IdDestinatario = ? AND IdMittente = ? AND letto = 0";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $id_utente, $chat_con);
} else {
    // Conta TUTTI i messaggi non letti dell'utente
    $sql = "SELECT COUNT(*) as totale FROM Messaggi WHERE IdDestinatario = ? AND letto = 0";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_utente);
}

$stmt->execute();
$res = $stmt->get_result()->fetch_assoc();

// 3. Risposta JSON
echo json_encode([
    'non_letti' => (int)$res['totale'],
    'status' => 'success'
]);

$stmt->close();
$conn->close();
?>