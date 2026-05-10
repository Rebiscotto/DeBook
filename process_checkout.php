<?php
session_start();
require_once 'db_connection.php';

// 1. Controllo sicurezza
if (!isset($_SESSION["loggedin"]) || $_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: login.php");
    exit;
}

$id_acquirente = $_SESSION["id"];
$nome_acquirente = $_SESSION["nome"]; // Assumendo che il nome sia in sessione
$id_libro = intval($_POST['id_libro']);
$punto_ritiro = $conn->real_escape_string($_POST['punto_ritiro']);
$metodo = $conn->real_escape_string($_POST['metodo']);

// 2. Recupero info sul libro e sul venditore
$query_info = "SELECT L.IdVenditore, A.titolo, L.prezzo 
               FROM Libri L 
               JOIN AnagraficaLibri A ON L.IdAnag = A.IdAnag 
               WHERE L.IdLibro = ?";
$stmt_info = $conn->prepare($query_info);
$stmt_info->bind_param("i", $id_libro);
$stmt_info->execute();
$info = $stmt_info->get_result()->fetch_assoc();

if (!$info) {
    die("Errore: Libro non trovato.");
}

$id_venditore = $info['IdVenditore'];
$titolo_libro = $info['titolo'];

// 3. AGGIORNAMENTO DATABASE: Segnamo il libro come venduto
// (Assicurati di aver aggiunto la colonna 'stato' alla tabella Libri come indicato prima)
$query_update = "UPDATE Libri SET stato = 'venduto' WHERE IdLibro = ?";
$stmt_update = $conn->prepare($query_update);
$stmt_update->bind_param("i", $id_libro);

if ($stmt_update->execute()) {
    
    // 4. MESSAGGIO AUTOMATICO IN CHAT
    // Prepariamo il testo del messaggio
    $testo_notifica = "Ciao! Ho confermato l'acquisto di '$titolo_libro'. Ci vediamo a: $punto_ritiro. Metodo scelto: $metodo.";
    
    // Configurazione Crittografia (Deve essere IDENTICA alla tua chat)
    $key = "Debook_Secret_2026_Safe";
    $iv = "1234567890123456";
    $testo_criptato = openssl_encrypt($testo_notifica, 'aes-256-cbc', $key, 0, $iv);

    // Inserimento del messaggio automatico
    $query_msg = "INSERT INTO Messaggi (IdMittente, IdDestinatario, testo_criptato, data_invio) VALUES (?, ?, ?, NOW())";
    $stmt_msg = $conn->prepare($query_msg);
    $stmt_msg->bind_param("iis", $id_acquirente, $id_venditore, $testo_criptato);
    $stmt_msg->execute();

    // 5. Redirect alla chat o a una pagina di successo
    // Reindirizziamo l'utente direttamente nella chat con il venditore per concludere l'accordo
    header("Location: chat.php?with=$id_venditore&success=1");
    exit;

} else {
    echo "Errore durante la conferma dell'ordine: " . $conn->error;
}
?>