<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION["loggedin"]) || !isset($_GET['with'])) {
    exit;
}

$id_utente = $_SESSION["id"];
$chat_con = intval($_GET['with']);
$key = "Debook_Secret_2026_Safe";
$iv = "1234567890123456";

// Recupera i messaggi aggiornati
$q = "SELECT * FROM Messaggi WHERE (IdMittente = ? AND IdDestinatario = ?) OR (IdMittente = ? AND IdDestinatario = ?) ORDER BY data_invio ASC";
$st = $conn->prepare($q);
$st->bind_param("iiii", $id_utente, $chat_con, $chat_con, $id_utente);
$st->execute();
$res = $st->get_result();

if($res->num_rows == 0) {
    echo "<div style='text-align:center; color:#ccc; font-family:Arial; margin-top:20px;'>Nessun messaggio. Scrivi per iniziare!</div>";
    exit;
}

while($m = $res->fetch_assoc()):
    $dec = openssl_decrypt($m['testo_criptato'], 'aes-256-cbc', $key, 0, $iv);
?>
    <div class="msg <?php echo ($m['IdMittente'] == $id_utente) ? 'sent' : 'received'; ?>">
        <?php echo htmlspecialchars($dec); ?>
    </div>
<?php endwhile; ?>