<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION["loggedin"])) { exit("Accesso negato"); }

$id_venditore = $_SESSION["id"];

// 1. Recupero dati dal form
$titolo = trim($_POST['titolo']);
$autore = trim($_POST['autore']);
$materia = trim($_POST['materia']);
$isbn = trim($_POST['isbn']);
$prezzo = floatval($_POST['prezzo']);
$condizione = $_POST['condizione'];
$digitale_usato = intval($_POST['digitale_usato']); // 0 o 1

// 2. Gestione Caricamento Immagini
$nomi_file = [];
if (isset($_FILES['immagini'])) {
    $cartella_upload = "uploads/";
    if (!is_dir($cartella_upload)) mkdir($cartella_upload, 0777, true);

    foreach ($_FILES['immagini']['tmp_name'] as $key => $tmp_name) {
        $nome_originale = $_FILES['immagini']['name'][$key];
        $estensione = pathinfo($nome_originale, PATHINFO_EXTENSION);
        $nuovo_nome = uniqid("IMG_") . "." . $estensione;
        $percorso_finale = $cartella_upload . $nuovo_nome;

        if (move_uploaded_file($tmp_name, $percorso_finale)) {
            $nomi_file[] = $percorso_finale;
        }
    }
}
$stringa_immagini = implode(",", $nomi_file);

// 3. Inserimento/Recupero in AnagraficaLibri
// Controlliamo se il libro esiste già per ISBN
$check_anag = $conn->prepare("SELECT IdAnag FROM AnagraficaLibri WHERE codISBN = ?");
$check_anag->bind_param("s", $isbn);
$check_anag->execute();
$res_anag = $check_anag->get_result();

if ($res_anag->num_rows > 0) {
    $id_anag = $res_anag->fetch_assoc()['IdAnag'];
} else {
    $ins_anag = $conn->prepare("INSERT INTO AnagraficaLibri (titolo, autore, materia, codISBN) VALUES (?, ?, ?, ?)");
    $ins_anag->bind_param("ssss", $titolo, $autore, $materia, $isbn);
    $ins_anag->execute();
    $id_anag = $ins_anag->insert_id;
}

// 4. Inserimento finale nella tabella Libri (con stato disponibile e digitale_usato)
$sql_libro = "INSERT INTO Libri (IdAnag, IdVenditore, prezzo, condizione, immagine, stato, digitale_usato) 
              VALUES (?, ?, ?, ?, ?, 'disponibile', ?)";

$stmt = $conn->prepare($sql_libro);
$stmt->bind_param("iidssi", $id_anag, $id_venditore, $prezzo, $condizione, $stringa_immagini, $digitale_usato);

if ($stmt->execute()) {
    header("Location: my_list.php?msg=Annuncio pubblicato con successo!");
} else {
    echo "Errore durante la pubblicazione: " . $conn->error;
}

$conn->close();
?>