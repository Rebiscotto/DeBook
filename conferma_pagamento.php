<?php
session_start();
require_once 'db_connection.php';

header('Content-Type: application/json');

// Leggiamo l'ID inviato dal checkout
$id_libro = isset($_POST['id_libro']) ? intval($_POST['id_libro']) : 0;

if ($id_libro === 0) {
    echo json_encode(['success' => false, 'error' => 'Dati del libro non ricevuti']);
    exit;
}

// 1. Verifichiamo disponibilità
$check = $conn->prepare("SELECT stato FROM Libri WHERE IdLibro = ?");
$check->bind_param("i", $id_libro);
$check->execute();
$ris = $check->get_result()->fetch_assoc();

if (!$ris || $ris['stato'] === 'venduto') {
    echo json_encode(['success' => false, 'error' => 'Libro non trovato o già venduto']);
    exit;
}

// 2. Aggiorniamo a VENDUTO
$update = $conn->prepare("UPDATE Libri SET stato = 'venduto' WHERE IdLibro = ?");
$update->bind_param("i", $id_libro);

if ($update->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $conn->error]);
}

$conn->close();