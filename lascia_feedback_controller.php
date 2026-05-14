<?php
session_start();
require_once 'db_connection.php';

// Impostiamo il fuso orario corretto (opzionale ma consigliato)
date_default_timezone_set('Europe/Rome');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['id'])) {
    
    $IdMittente = $_SESSION['id']; 
    $IdDestinatario = intval($_POST['IdDestinatario']); 
    $NStelle = intval($_POST['voto']); 
    $messaggio = trim($_POST['commento']); 
    $data_attuale = date("Y-m-d H:i:s");
    
    // Gestione IdTransazione: se non esiste nel DB, usiamo null in modo pulito
    $IdTransazione = null; 

    if($IdDestinatario > 0 && $NStelle > 0 && !empty($messaggio)) {
        
        // Prepariamo la query
        $sql = "INSERT INTO Feedback (messaggio, NStelle, IdMittente, IdDestinatario, IdTransazione, data) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        
        if ($stmt) {
            // Nota sui tipi: "siiiis"
            // s = messaggio (string)
            // i = NStelle (int)
            // i = IdMittente (int)
            // i = IdDestinatario (int)
            // i = IdTransazione (può essere nullo)
            // s = data (string)
            $stmt->bind_param("siiiis", $messaggio, $NStelle, $IdMittente, $IdDestinatario, $IdTransazione, $data_attuale);
            
            if ($stmt->execute()) {
                // Dopo il feedback, lo rimandiamo alla chat con un messaggio di successo
                header("Location: chat.php?with=" . $IdDestinatario . "&msg=Feedback inviato!");
                exit;
            } else {
                // Se fallisce per via della Foreign Key su IdTransazione, riproviamo senza quella colonna
                die("Errore nell'invio del feedback. Assicurati di non aver già lasciato un feedback per questa transazione o contatta l'assistenza.");
            }
        } else {
            die("Errore database: " . $conn->error);
        }
    } else {
        die("Per favore, seleziona un voto e scrivi un breve commento.");
    }
} else {
    header("Location: index.php");
    exit;
}
?>