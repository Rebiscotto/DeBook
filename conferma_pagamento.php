<?php
session_start();
require_once 'db_connection.php';

header('Content-Type: application/json');

// IMPORTANTE: Poiché nel checkout usiamo FormData, i dati arrivano in $_POST
// Il vecchio metodo file_get_contents('php://input') qui non funzionerebbe
$id_libro = isset($_POST['id_libro']) ? intval($_POST['id_libro']) : 0;

if ($id_libro === 0) {
    echo json_encode(['success' => false, 'error' => 'ID libro non ricevuto (POST vuoto)']);
    exit;
}

// 1. Recupero info per verificare che il libro sia ancora disponibile
$query_info = "SELECT L.IdVenditore, A.titolo FROM Libri L 
               JOIN AnagraficaLibri A ON L.IdAnag = A.IdAnag 
               WHERE L.IdLibro = ? AND L.stato = 'disponibile'";
               
$stmt_info = $conn->prepare($query_info);
$stmt_info->bind_param("i", $id_libro);
$stmt_info->execute();
$info = $stmt_info->get_result()->fetch_assoc();

if (!$info) {
    echo json_encode(['success' => false, 'error' => 'Libro non trovato o gia venduto (ID: '.$id_libro.')']);
    exit;
}

// 2. Aggiornamento stato del libro in 'venduto'
$update = $conn->prepare("UPDATE Libri SET stato = 'venduto' WHERE IdLibro = ?");
$update->bind_param("i", $id_libro);

if ($update->execute()) {
    // Se tutto va bene, rispondiamo success true
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Errore query: ' . $conn->error]);
}

$conn->close();