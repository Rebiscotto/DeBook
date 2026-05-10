<?php
// Sposta session_start() in cima e assicurati che non ci siano spazi prima di <?php
session_start();
require_once 'db_connection.php';

header('Content-Type: application/json');

$input = file_get_contents('php://input');
$data = json_decode($input, true);

// LOG DI DEBUG: Se vuoi vedere cosa arriva, controlla i log del server
// Ma per ora semplifichiamo il controllo sessione

if (!isset($data['id_libro'])) {
    echo json_encode(['success' => false, 'error' => 'ID libro non ricevuto dal checkout']);
    exit;
}

$id_libro = intval($data['id_libro']);

// Rimuoviamo temporaneamente il controllo sessione ID per vedere se è quello che blocca
// $id_acquirente = $_SESSION["id"]; 

// 1. Recupero info
$query_info = "SELECT L.IdVenditore, A.titolo FROM Libri L 
               JOIN AnagraficaLibri A ON L.IdAnag = A.IdAnag 
               WHERE L.IdLibro = ? AND L.stato = 'disponibile'";
$stmt_info = $conn->prepare($query_info);
$stmt_info->bind_param("i", $id_libro);
$stmt_info->execute();
$info = $stmt_info->get_result()->fetch_assoc();

if (!$info) {
    echo json_encode(['success' => false, 'error' => 'Libro non trovato o già venduto (ID: '.$id_libro.')']);
    exit;
}

$id_venditore = $info['IdVenditore'];
$titolo_libro = $info['titolo'];

// 2. Aggiornamento stato
$update = $conn->prepare("UPDATE Libri SET stato = 'venduto' WHERE IdLibro = ?");
$update->bind_param("i", $id_libro);

if ($update->execute()) {
    // Il messaggio in chat lo riattiveremo appena confermi che questo funziona
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $conn->error]);
}
$conn->close();