<?php
session_start();
require_once 'db_connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION["id"])) {
    echo json_encode(['non_letti' => 0]);
    exit;
}

$id_utente = $_SESSION["id"];
$sql = "SELECT COUNT(*) as totale FROM Messaggi WHERE IdDestinatario = ? AND letto = 0";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_utente);
$stmt->execute();
$res = $stmt->get_result()->fetch_assoc();

echo json_encode(['non_letti' => (int)$res['totale']]);
$conn->close();