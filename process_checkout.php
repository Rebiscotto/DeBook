<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION["loggedin"]) || $_SERVER["REQUEST_METHOD"] != "POST") {
    die("Accesso negato.");
}

$id_acquirente = $_SESSION["id"];
$id_libro = $_POST['id_libro'];
$metodo = $_POST['metodo'];
$punto_ritiro = $_POST['punto_ritiro'];

// 1. Creazione della Transazione
$importo = 10.00; // Esempio: valore recuperato dall'annuncio nel caso reale
$stmt_trans = $conn->prepare("INSERT INTO Transazioni (importo, metodo) VALUES (?, ?)");
$stmt_trans->bind_param("ds", $importo, $metodo);
$stmt_trans->execute();
$id_transazione = $conn->insert_id;

// 2. Creazione dell'Ordine
$stmt_ordine = $conn->prepare("INSERT INTO Ordini (IdAcquirente, IdTransazione) VALUES (?, ?)");
$stmt_ordine->bind_param("ii", $id_acquirente, $id_transazione);
$stmt_ordine->execute();
$id_ordine = $conn->insert_id;

// 3. Aggiornamento del Libro (collegamento all'ordine per segnarlo come "venduto")
$stmt_update = $conn->prepare("UPDATE Libri SET IdOrdine = ? WHERE IdLibro = ?");
$stmt_update->bind_param("ii", $id_ordine, $id_libro);

if ($stmt_update->execute()) {
    // RF-09: Generazione notifica (simulata con reindirizzamento)
    header("Location: dashboard.php?msg=Ordine completato! Accordati in chat per l'incontro al " . urlencode($punto_ritiro));
} else {
    echo "Errore durante l'elaborazione dell'ordine.";
}

$conn->close();
?>