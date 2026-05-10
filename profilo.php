<?php
// ... parte iniziale del file profilo.php ...

// Query per la media e il totale usando i tuoi nomi
$query_f = "SELECT AVG(NStelle) as media, COUNT(*) as totale FROM Feedback WHERE IdDestinatario = ?";
$stmt_f = $conn->prepare($query_f);
$stmt_f->bind_param("i", $id_profilo);
$stmt_f->execute();
$res_f = $stmt_f->get_result()->fetch_assoc();

$media = round($res_f['media'], 1);
$totale = $res_f['totale'];

// Query per i messaggi usando i tuoi nomi
$q_msg = "SELECT f.NStelle, f.messaggio, f.data, u.nome 
          FROM Feedback f 
          JOIN Utenti u ON f.IdMittente = u.IdUtente 
          WHERE f.IdDestinatario = ? 
          ORDER BY f.data DESC";
$stmt_m = $conn->prepare($q_msg);
$stmt_m->bind_param("i", $id_profilo);
$stmt_m->execute();
$res_m = $stmt_m->get_result();

// ... poi nel resto del codice HTML usa $fb['messaggio'] e $fb['NStelle'] ...
?>