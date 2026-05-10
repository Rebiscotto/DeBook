<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION["loggedin"])) {
    die("Accesso negato.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_utente = $_SESSION["id"];
    $titolo = trim($_POST['titolo']);
    $autore = trim($_POST['autore']);
    $materia = trim($_POST['materia']);
    $isbn = trim($_POST['isbn']);
    
    // 1. Gestione Upload Immagine (RF-05)
    $target_dir = "uploads/libri/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $file_extension = pathinfo($_FILES["immagine"]["name"], PATHINFO_EXTENSION);
    $new_filename = "libro_" . time() . "_" . uniqid() . "." . $file_extension;
    $target_file = $target_dir . $new_filename;

    if (move_uploaded_file($_FILES["immagine"]["tmp_name"], $target_file)) {
        
        // 2. Inserimento in AnagraficaLibri
        // Usiamo INSERT IGNORE o controlliamo se l'ISBN esiste già per evitare duplicati
        $stmt_anag = $conn->prepare("INSERT INTO AnagraficaLibri (codISBN, titolo, materia, autore) VALUES (?, ?, ?, ?)");
        $stmt_anag->bind_param("ssss", $isbn, $titolo, $materia, $autore);
        $stmt_anag->execute();
        
        // Recuperiamo l'ID dell'anagrafica appena inserita (o esistente)
        $id_anag = $conn->insert_id;
        if($id_anag == 0) { // Se l'ISBN esisteva già e non ha inserito nulla
            $stmt_check = $conn->prepare("SELECT IdAnag FROM AnagraficaLibri WHERE codISBN = ? OR titolo = ?");
            $stmt_check->bind_param("ss", $isbn, $titolo);
            $stmt_check->execute();
            $id_anag = $stmt_check->get_result()->fetch_assoc()['IdAnag'];
        }

        // 3. Inserimento in Libri (collegando l'utente venditore)
        $stmt_libro = $conn->prepare("INSERT INTO Libri (immagine, IdAnag, IdVenditore) VALUES (?, ?, ?)");
        $stmt_libro->bind_param("sii", $target_file, $id_anag, $id_utente);

        if ($stmt_libro->execute()) {
            header("Location: dashboard.php?msg=Annuncio pubblicato con successo!");
        } else {
            echo "Errore salvataggio libro: " . $stmt_libro->error;
        }
    } else {
        echo "Errore durante il caricamento dell'immagine.";
    }
}
$conn->close();
?>