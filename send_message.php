<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION["loggedin"]) || $_SERVER["REQUEST_METHOD"] != "POST") {
    die("Accesso negato.");
}

$id_mittente = $_SESSION["id"];
$id_destinatario = $_POST['id_destinatario'];
$messaggio_chiaro = trim($_POST['messaggio']);

if (empty($messaggio_chiaro)) {
    header("Location: chat.php?with=" . $id_destinatario);
    exit;
}

// RF-08: Crittografia del messaggio
// In produzione, l'IV (Initialization Vector) non dovrebbe essere statico,
// ma per un progetto scolastico la crittografia AES-256 dimostra ampiamente il concetto.
$encryption_key = "DeBook_Secret_Key_2026";
$iv = str_repeat("0", 16); 
$messaggio_criptato = openssl_encrypt($messaggio_chiaro, 'aes-256-cbc', $encryption_key, 0, $iv);

// Inserimento nel database
$stmt = $conn->prepare("INSERT INTO Messaggi (IdMittente, IdDestinatario, testo_criptato) VALUES (?, ?, ?)");
$stmt->bind_param("iis", $id_mittente, $id_destinatario, $messaggio_criptato);

if ($stmt->execute()) {
    header("Location: chat.php?with=" . $id_destinatario);
} else {
    echo "Errore invio messaggio: " . $conn->error;
}

$stmt->close();
$conn->close();
?>