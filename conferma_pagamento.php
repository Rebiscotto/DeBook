<?php
session_start();
require_once 'db_connection.php';

// Disabilita la visualizzazione di errori testuali che rompono il JSON
error_reporting(0);
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['id_libro']) && isset($_SESSION['id'])) {
    $id_libro = intval($data['id_libro']);
    
    // Aggiorniamo lo stato
    $update = $conn->prepare("UPDATE Libri SET stato = 'venduto' WHERE IdLibro = ?");
    $update->bind_param("i", $id_libro);
    
    if ($update->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Errore database']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Dati mancanti']);
}
exit;