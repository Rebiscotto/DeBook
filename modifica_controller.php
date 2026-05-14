<?php
session_start();
require_once 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION["id"])) {
    $id_libro = intval($_POST['id_libro']);
    $id_utente = $_SESSION["id"];
    $prezzo = floatval($_POST['prezzo']);
    $condizione = $_POST['condizione'];
    $digitale_usato = intval($_POST['digitale_usato']);

    // Gestione Immagini (solo se caricate di nuove)
    if (!empty($_FILES['immagini']['name'][0])) {
        $nomi_file = [];
        $cartella = "uploads/";
        foreach ($_FILES['immagini']['tmp_name'] as $key => $tmp_name) {
            $nuovo_nome = $cartella . uniqid("IMG_") . "." . pathinfo($_FILES['immagini']['name'][$key], PATHINFO_EXTENSION);
            if (move_uploaded_file($tmp_name, $nuovo_nome)) { $nomi_file[] = $nuovo_nome; }
        }
        $stringa_immagini = implode(",", $nomi_file);
        
        // Update con nuove immagini
        $sql = "UPDATE Libri SET prezzo = ?, condizione = ?, digitale_usato = ?, immagine = ? WHERE IdLibro = ? AND IdVenditore = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("dsisii", $prezzo, $condizione, $digitale_usato, $stringa_immagini, $id_libro, $id_utente);
    } else {
        // Update senza cambiare immagini
        $sql = "UPDATE Libri SET prezzo = ?, condizione = ?, digitale_usato = ? WHERE IdLibro = ? AND IdVenditore = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("dsiii", $prezzo, $condizione, $digitale_usato, $id_libro, $id_utente);
    }

    if ($stmt->execute()) {
        header("Location: my_list.php?msg=Annuncio aggiornato!");
    } else {
        echo "Errore: " . $conn->error;
    }
}
$conn->close();