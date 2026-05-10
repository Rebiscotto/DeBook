<?php
// Attiviamo gli errori per vedere cosa succede se fallisce ancora
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['id'])) {
    $mittente = $_SESSION['id'];
    $destinatario = intval($_POST['id_destinatario']);
    $voto = intval($_POST['voto']);
    $commento = trim($_POST['commento']);

    if($destinatario > 0 && $voto > 0) {
        // NOTA: Qui uso i nomi delle colonne TUTTI MINUSCOLI come nel tuo screenshot
        $stmt = $conn->prepare("INSERT INTO Feedback (idMittente, idDestinatario, NStelle, messaggio) VALUES (?, ?, ?, ?)");
        
        if ($stmt === false) {
            die("Errore preparazione: " . $conn->error);
        }

        $stmt->bind_param("iiis", $Mittente, $Destinatario, $NStelle, $messaggio);
        
        if ($stmt->execute()) {
            header("Location: profilo.php?id=" . $Destinatario);
            exit;
        } else {
            die("Errore esecuzione: " . $stmt->error);
        }
    } else {
        die("Dati non validi.");
    }
} else {
    die("Accesso negato.");
}
?>