<?php
session_start();
require_once 'db_connection.php';

// 1. Protezione accesso
if (!isset($_SESSION["loggedin"]) || $_SERVER["REQUEST_METHOD"] != "POST") { 
    die("Accesso negato"); 
}

$id_mittente = $_SESSION["id"];
$id_destinatario = intval($_POST['id_destinatario']);
$testo = isset($_POST['messaggio']) ? trim($_POST['messaggio']) : "";
$percorso_foto = null;

// 2. Gestione Upload Immagine
if (isset($_FILES['foto_chat']) && $_FILES['foto_chat']['error'] == 0) {
    $cartella = "uploads/chat/";
    
    // Crea la cartella se non esiste
    if (!is_dir($cartella)) {
        mkdir($cartella, 0777, true);
    }

    $estensione = pathinfo($_FILES['foto_chat']['name'], PATHINFO_EXTENSION);
    $nome_file = uniqid("IMG_") . "." . $estensione;
    $target = $cartella . $nome_file;

    if (move_uploaded_file($_FILES['foto_chat']['tmp_name'], $target)) {
        $percorso_foto = $target;
    }
}

// 3. Invio: procediamo se c'è del testo OPPURE se è stata caricata una foto
if (!empty($testo) || $percorso_foto) {
    
    // Se c'è una foto, il contenuto salvato sarà il percorso preceduto dal marker
    // Se c'è sia testo che foto, in questa versione vince la foto (tipico delle chat semplici)
    $contenuto_da_criptare = $testo;
    if ($percorso_foto) {
        $contenuto_da_criptare = "FILE_IMAGE:" . $percorso_foto;
    }

    // Configurazione Crittografia (Deve essere identica a fetch_messages.php)
    $key = "Debook_Secret_2026_Safe";
    $iv = "1234567890123456"; 

    $criptato = openssl_encrypt($contenuto_da_criptare, 'aes-256-cbc', $key, 0, $iv);

    // 4. Query di inserimento
    // Assicurati che i nomi delle colonne (testo_criptato, data_invio, letto) siano corretti
    $sql = "INSERT INTO Messaggi (IdMittente, IdDestinatario, testo_criptato, data_invio, letto) 
            VALUES (?, ?, ?, NOW(), 0)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $id_mittente, $id_destinatario, $criptato);
    
    if ($stmt->execute()) {
        // Successo
    } else {
        // Errore database (puoi loggarlo per debug se necessario)
        // error_log($conn->error);
    }
}

// Nota: Non usiamo il redirect header() perché la chiamata arriva via fetch/AJAX
$conn->close();
?>