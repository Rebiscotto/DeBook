<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION["loggedin"]) || $_SERVER["REQUEST_METHOD"] != "POST") { die("Accesso negato"); }

$id_mittente = $_SESSION["id"];
$id_destinatario = $_POST['id_destinatario'];
$testo = trim($_POST['messaggio']);

if (!empty($testo)) {
    // CHIAVE E IV FISSI (Non cambiarli tra i file)
    $key = "Debook_Secret_2026_Safe";
    $iv = "1234567890123456"; // 16 caratteri esatti

    $criptato = openssl_encrypt($testo, 'aes-256-cbc', $key, 0, $iv);

    $stmt = $conn->prepare("INSERT INTO Messaggi (IdMittente, IdDestinatario, testo_criptato) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $id_mittente, $id_destinatario, $criptato);
    $stmt->execute();
}

header("Location: chat.php?with=" . $id_destinatario);
exit;
?>