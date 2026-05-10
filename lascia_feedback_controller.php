<?php
session_start();
require_once 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['id'])) {
    $mittente = $_SESSION['id'];
    $destinatario = $_POST['id_destinatario'];
    $voto = intval($_POST['voto']);
    $commento = trim($_POST['commento']);

    $stmt = $conn->prepare("INSERT INTO Feedback (IdMittente, IdDestinatario, Voto, Commento) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $mittente, $destinatario, $voto, $commento);
    $stmt->execute();

    header("Location: profilo.php?id=" . $destinatario);
    exit;
}