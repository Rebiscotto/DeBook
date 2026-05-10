<?php
session_start();
require_once 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['id'])) {
    
    // Recupero i dati assicurandomi che i nomi in $_POST siano corretti
    $IdMittente = $_SESSION['id']; 
    $IdDestinatario = intval($_POST['IdDestinatario']); 
    $NStelle = intval($_POST['voto']); // Il numero di stelle dal form
    $messaggio = trim($_POST['commento']); // Il testo del feedback
    
    // Se non gestisci ancora le transazioni, mettiamo un valore fittizio o NULL
    $IdTransazione = NULL; 

    if($IdDestinatario > 0 && $NStelle > 0) {
        
        // Query con i tuoi nomi esatti: IdMittente, IdDestinatario, messaggio, NStelle, IdTransazione
        $sql = "INSERT INTO Feedback (IdMittente, IdDestinatario, messaggio, NStelle, IdTransazione) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        
        if ($stmt) {
            // "iiisi" significa: int, int, int, string, int (o NULL)
            $stmt->bind_param("iiisi", $IdMittente, $IdDestinatario, $NStelle, $messaggio, $IdTransazione);
            
            if ($stmt->execute()) {
                header("Location: profilo.php?id=" . $IdDestinatario);
                exit;
            } else {
                echo "Errore esecuzione: " . $stmt->error;
            }
        } else {
            echo "Errore database: " . $conn->error;
        }
    } else {
        echo "Dati mancanti o non validi.";
    }
}
?>