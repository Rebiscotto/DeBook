<?php
session_start();
require_once 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['id'])) {
    
    // Mappatura dati verso le tue colonne del DB
    $IdMittente = $_SESSION['id']; 
    $IdDestinatario = intval($_POST['IdDestinatario']); 
    $NStelle = intval($_POST['voto']); 
    $messaggio = trim($_POST['commento']); 
    
    // Se non hai un sistema di transazioni attivo, mettiamo NULL o 0
    $IdTransazione = NULL; 

    if($IdDestinatario > 0 && $NStelle > 0) {
        
        // Query con i nomi esatti: messaggio, NStelle, IdMittente, IdDestinatario, IdTransazione
        $sql = "INSERT INTO Feedback (messaggio, NStelle, IdMittente, IdDestinatario, IdTransazione) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        
        if ($stmt) {
            // "siiii" -> string (messaggio), int (NStelle), int (IdMittente), int (IdDestinatario), int (IdTransazione)
            $stmt->bind_param("siiii", $messaggio, $NStelle, $IdMittente, $IdDestinatario, $IdTransazione);
            
            if ($stmt->execute()) {
                header("Location: profilo.php?id=" . $IdDestinatario);
                exit;
            } else {
                die("Errore esecuzione: " . $stmt->error);
            }
        } else {
            die("Errore database: " . $conn->error);
        }
    } else {
        die("Dati non validi.");
    }
}
?>