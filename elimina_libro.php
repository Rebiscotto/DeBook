<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION["loggedin"]) || !isset($_GET['id'])) {
    header("Location: login.php");
    exit;
}

$id_libro = $_GET['id'];
$id_utente = $_SESSION["id"];

// Verifichiamo prima che il libro appartenga effettivamente all'utente (sicurezza)
$check = $conn->prepare("SELECT immagine FROM Libri WHERE IdLibro = ? AND IdVenditore = ?");
$check->bind_param("ii", $id_libro, $id_utente);
$check->execute();
$res = $check->get_result();

if ($res->num_rows === 1) {
    $libro = $res->fetch_assoc();
    
    // 1. Elimina il file fisico dal server
    if (file_exists($libro['immagine'])) {
        unlink($libro['immagine']);
    }

    // 2. Elimina il record dal database
    $delete = $conn->prepare("DELETE FROM Libri WHERE IdLibro = ?");
    $delete->bind_param("i", $id_libro);
    
    if ($delete->execute()) {
        header("Location: my_list.php?msg=Annuncio rimosso con successo.");
    } else {
        echo "Errore durante l'eliminazione: " . $conn->error;
    }
} else {
    die("Azione non consentita o libro non trovato.");
}
?>