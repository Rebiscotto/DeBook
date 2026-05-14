<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION["loggedin"]) || !isset($_GET['id'])) {
    header("Location: login.php");
    exit;
}

$id_libro = intval($_GET['id']); // Usiamo intval per sicurezza
$id_utente = $_SESSION["id"];

// 1. Verifichiamo appartenenza e recuperiamo i percorsi delle immagini
$check = $conn->prepare("SELECT immagine FROM Libri WHERE IdLibro = ? AND IdVenditore = ?");
$check->bind_param("ii", $id_libro, $id_utente);
$check->execute();
$res = $check->get_result();

if ($res->num_rows === 1) {
    $libro = $res->fetch_assoc();
    
    // 2. Elimina i file fisici dal server (Gestendo le virgole)
    if (!empty($libro['immagine'])) {
        $foto_array = explode(',', $libro['immagine']); // Trasformiamo la stringa in array
        foreach ($foto_array as $foto) {
            $foto = trim($foto); // Puliamo eventuali spazi
            if (!empty($foto) && file_exists($foto)) {
                unlink($foto);
            }
        }
    }

    // 3. Elimina il record dal database
    // Usiamo un blocco try-catch o controlliamo l'errore per le chiavi esterne
    $delete = $conn->prepare("DELETE FROM Libri WHERE IdLibro = ? AND IdVenditore = ?");
    $delete->bind_param("ii", $id_libro, $id_utente);
    
    if ($delete->execute()) {
        header("Location: my_list.php?msg=Annuncio rimosso con successo.");
        exit;
    } else {
        // Errore tipico: Vincolo di chiave esterna (es. ci sono messaggi legati al libro)
        if ($conn->errno == 1451) {
            // Se non puoi eliminarlo perché ci sono messaggi, cambiamo solo lo stato
            $update = $conn->prepare("UPDATE Libri SET stato = 'archiviato' WHERE IdLibro = ?");
            $update->bind_param("i", $id_libro);
            $update->execute();
            header("Location: my_list.php?msg=Annuncio archiviato (non eliminabile perché presente in chat).");
        } else {
            die("Errore database: " . $conn->error);
        }
    }
} else {
    die("Azione non consentita: non sei il proprietario di questo libro o il libro non esiste.");
}
?>