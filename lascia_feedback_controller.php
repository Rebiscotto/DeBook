<?php
session_start();
require_once 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['id'])) {
    
    $IdMittente = $_SESSION['id']; 
    $IdDestinatario = intval($_POST['IdDestinatario']); 
    $NStelle = intval($_POST['voto']); 
    $messaggio = trim($_POST['commento']); 
    
    // Creiamo la data e ora attuale nel formato corretto per il database
    $data_attuale = date("Y-m-d H:i:s");
    
    // IdTransazione lo lasciamo NULL se non lo usi
    $IdTransazione = NULL; 

    if($IdDestinatario > 0 && $NStelle > 0) {
        
        // Aggiungiamo 'data' nella query di inserimento
        $sql = "INSERT INTO Feedback (messaggio, NStelle, IdMittente, IdDestinatario, IdTransazione, data) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        
        if ($stmt) {
            // "siiiis" -> string, int, int, int, int, string (per la data)
            $stmt->bind_param("siiiis", $messaggio, $NStelle, $IdMittente, $IdDestinatario, $IdTransazione, $data_attuale);
            
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