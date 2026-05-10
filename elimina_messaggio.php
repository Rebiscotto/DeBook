<?php
session_start();
require_once 'db_connection.php';

// Verifichiamo che l'utente sia loggato
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    exit("Accesso negato");
}

if (isset($_GET['id'])) {
    $id_messaggio = intval($_GET['id']);
    $id_utente = $_SESSION['id'];

    // UPDATE: Cambiamo il testo e impostiamo il flag 'eliminato'
    // La condizione IdMittente = ? garantisce che solo l'autore possa farlo
    $nuovo_testo = "Questo messaggio è stato eliminato";
    $query = "UPDATE Messaggi SET Testo = ?, eliminato = 1 WHERE IdMessaggio = ? AND IdMittente = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sii", $nuovo_testo, $id_messaggio, $id_utente);
    
    if ($stmt->execute()) {
        // Torna alla chat precedente
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    } else {
        echo "Errore durante la modifica del messaggio.";
    }
}
?>