<?php
session_start();
require_once 'db_connection.php';

// Riceviamo i dati JSON inviati dal fetch di JavaScript
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['id_libro']) && isset($_SESSION['id'])) {
    $id_libro = intval($data['id_libro']);
    $id_acquirente = $_SESSION['id'];

    // 1. Segnamo il libro come venduto
    $update = $conn->prepare("UPDATE Libri SET stato = 'venduto' WHERE IdLibro = ?");
    $update->bind_param("i", $id_libro);
    
    if ($update->execute()) {
        // 2. Opzionale: Inseriamo una riga in una tabella Transazioni (se l'hai creata)
        // 3. Risposta positiva al frontend
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $conn->error]);
    }
}
?>