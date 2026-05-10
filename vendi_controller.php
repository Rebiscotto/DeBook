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
    $condizione = $_POST['condizione'];

    // 1. Gestione Anagrafica
    $stmt_anag = $conn->prepare("INSERT INTO AnagraficaLibri (codISBN, titolo, materia, autore) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE IdAnag=LAST_INSERT_ID(IdAnag)");
    $stmt_anag->bind_param("ssss", $isbn, $titolo, $materia, $autore);
    $stmt_anag->execute();
    $id_anag = $conn->insert_id ?: $conn->query("SELECT IdAnag FROM AnagraficaLibri WHERE codISBN='$isbn' OR titolo='$titolo' LIMIT 1")->fetch_assoc()['IdAnag'];

    // 2. Upload Multiplo
    $percorsi = [];
    $target_dir = "uploads/libri/";
    if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

    foreach ($_FILES['immagini']['name'] as $key => $val) {
        if ($_FILES['immagini']['error'][$key] == 0) {
            $ext = pathinfo($_FILES['immagini']['name'][$key], PATHINFO_EXTENSION);
            $name = "book_" . time() . "_" . uniqid() . "." . $ext;
            if (move_uploaded_file($_FILES['immagini']['tmp_name'][$key], $target_dir . $name)) {
                $percorsi[] = $target_dir . $name;
            }
        }
    }
    $immagini_str = implode(",", $percorsi);

    // 3. Inserimento Libro (ENUM Condizione)
    $stmt_libro = $conn->prepare("INSERT INTO Libri (immagine, prezzo, condizione, IdAnag, IdVenditore) VALUES (?, ?, ?, ?, ?)");
    $stmt_libro->bind_param("sdsii", $immagini_str, $prezzo, $condizione, $id_anag, $id_utente);

    if ($stmt_libro->execute()) {
        header("Location: index.php?success=1");
    } else {
        echo "Errore: " . $conn->error;
    }
}