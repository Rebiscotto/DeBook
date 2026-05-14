<?php
session_start();
require_once 'db_connection.php';

// 1. Controllo sicurezza
if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_SESSION["id"])) {
    header("Location: index.php");
    exit;
}

$id_utente = $_SESSION["id"];
$id_libro = intval($_POST['id_libro']);
$id_anag = intval($_POST['id_anag']);

// 2. Recupero e sanificazione dati
$titolo    = $conn->real_escape_string($_POST['titolo']);
$autore    = $conn->real_escape_string($_POST['autore']);
$materia   = $conn->real_escape_string($_POST['materia']);
$isbn      = $conn->real_escape_string($_POST['isbn']);
$prezzo    = floatval($_POST['prezzo']);
$condizione = $conn->real_escape_string($_POST['condizione']);
$digitale  = intval($_POST['digitale_usato']);

// 3. AGGIORNAMENTO TABELLA ANAGRAFICA (Dati universali del libro)
$sql_anag = "UPDATE AnagraficaLibri SET 
             titolo = ?, 
             autore = ?, 
             materia = ?, 
             codISBN = ? 
             WHERE IdAnag = ?";

$stmt_anag = $conn->prepare($sql_anag);
$stmt_anag->bind_param("ssssi", $titolo, $autore, $materia, $isbn, $id_anag);
$stmt_anag->execute();

// 4. AGGIORNAMENTO TABELLA LIBRI (Dati della tua vendita)
// Verifichiamo anche l'IdVenditore per sicurezza extra
$sql_libro = "UPDATE Libri SET 
              prezzo = ?, 
              condizione = ?, 
              digitale_usato = ? 
              WHERE IdLibro = ? AND IdVenditore = ?";

$stmt_libro = $conn->prepare($sql_libro);
$stmt_libro->bind_param("dsiii", $prezzo, $condizione, $digitale, $id_libro, $id_utente);
$stmt_libro->execute();

// 5. GESTIONE NUOVE IMMAGINI (Solo se caricate)
if (!empty($_FILES['immagini']['name'][0])) {
    $cartella_destinazione = "uploads/";
    $percorsi_immagini = [];

    foreach ($_FILES['immagini']['tmp_name'] as $key => $tmp_name) {
        if ($_FILES['immagini']['error'][$key] === UPLOAD_ERR_OK) {
            $nome_file = time() . "_" . basename($_FILES['immagini']['name'][$key]);
            $percorso_completo = $cartella_destinazione . $nome_file;

            if (move_uploaded_file($tmp_name, $percorso_completo)) {
                $percorsi_immagini[] = $percorso_completo;
            }
        }
    }

    // Se almeno una foto è stata caricata con successo, aggiorniamo il database
    if (!empty($percorsi_immagini)) {
        $stringa_immagini = implode(',', $percorsi_immagini);
        $sql_img = "UPDATE Libri SET immagine = ? WHERE IdLibro = ? AND IdVenditore = ?";
        $stmt_img = $conn->prepare($sql_img);
        $stmt_img->bind_param("sii", $stringa_immagini, $id_libro, $id_utente);
        $stmt_img->execute();
    }
}

// 6. Ritorno alla lista con messaggio di successo
header("Location: my_list.php?msg=Annuncio aggiornato correttamente!");
exit;
?>