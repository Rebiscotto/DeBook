<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION["loggedin"]) || $_SERVER["REQUEST_METHOD"] != "POST") {
    die("Accesso negato.");
}

$id_mittente = $_SESSION["id"];
$id_destinatario = $_POST['id_destinatario'];
$stelle = intval($_POST['stelle']);
$messaggio = trim($_POST['messaggio']);

// Sicurezza: controllo che i valori siano validi
if ($stelle < 1 || $stelle > 5) {
    die("Valutazione non valida.");
}
if ($id_mittente == $id_destinatario) {
    die("Non puoi autovalutarti!");
}

// Inserimento nel database (lasciamo IdTransazione a NULL se non implementato, o lo aggiungiamo se passato via POST)
$stmt = $conn->prepare("INSERT INTO Feedback (messaggio, NStelle, data, IdMittente, IdDestinatario) VALUES (?, ?, CURDATE(), ?, ?)");
$stmt->bind_param("siii", $messaggio, $stelle, $id_mittente, $id_destinatario);

if ($stmt->execute()) {
    header("Location: profilo.php?id=" . $id_destinatario . "&msg=Feedback inviato con successo!");
} else {
    echo "Errore durante il salvataggio del feedback: " . $conn->error;
}

$stmt->close();
$conn->close();
?>