<?php
session_start();
require_once "db_connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION["id"])) {
    $id_utente = $_SESSION["id"];
    $titolo = trim($_POST['titolo']);
    $autore = trim($_POST['autore']);
    $materia = trim($_POST['materia']);
    $isbn = trim($_POST['isbn']);
    $prezzo = $_POST['prezzo'];

    // Gestione Upload Immagine
    $target_dir = "uploads/libri/";
    if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
    
    $file_ext = pathinfo($_FILES["immagine"]["name"], PATHINFO_EXTENSION);
    $filename = "book_" . time() . "_" . uniqid() . "." . $file_ext;
    $target_file = $target_dir . $filename;

    if (move_uploaded_file($_FILES["immagine"]["tmp_name"], $target_file)) {
        
        // 1. Inserimento/Recupero Anagrafica
        $stmt_anag = $conn->prepare("INSERT INTO AnagraficaLibri (codISBN, titolo, materia, autore) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE IdAnag=LAST_INSERT_ID(IdAnag)");
        $stmt_anag->bind_param("ssss", $isbn, $titolo, $materia, $autore);
        $stmt_anag->execute();
        $id_anag = $conn->insert_id;

        // Se insert_id è 0 (record già esistente senza DUPLICATE KEY funzionante), lo cerchiamo
        if ($id_anag == 0) {
            $res = $conn->query("SELECT IdAnag FROM AnagraficaLibri WHERE codISBN = '$isbn' OR titolo = '$titolo' LIMIT 1");
            $id_anag = $res->fetch_assoc()['IdAnag'];
        }

        // 2. Inserimento nella tabella Libri con il PREZZO
        $stmt_libro = $conn->prepare("INSERT INTO Libri (immagine, prezzo, IdAnag, IdVenditore) VALUES (?, ?, ?, ?)");
        $stmt_libro->bind_param("sdii", $target_file, $prezzo, $id_anag, $id_utente);

        if ($stmt_libro->execute()) {
            header("Location: my_list.php?msg=Annuncio creato!");
        } else {
            echo "Errore caricamento libro: " . $conn->error;
        }
    } else {
        echo "Errore nel caricamento della foto.";
    }
}
?>