<?php
session_start();
require_once 'db_connection.php';

// Controllo sicurezza: se l'utente non è loggato, torna al login
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: login.php");
    exit;
}

$id_utente = $_SESSION["id"];

// Recupero statistiche: Quanti libri ha in vendita l'utente?
$sql_libri = "SELECT COUNT(*) as totale FROM Libri WHERE IdVenditore = ?";
$stmt = $conn->prepare($sql_libri);
$stmt->bind_param("i", $id_utente);
$stmt->execute();
$res_libri = $stmt->get_result();
$conta_libri = $res_libri->fetch_assoc()['totale'];
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Debook - Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            width: 90%;
            max-width: 1000px;
            margin-top: 30px;
        }
        .card {
            background: var(--white);
            padding: 30px;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            transition: transform 0.3s;
            text-decoration: none;
            color: var(--dark-text);
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .card i {
            font-size: 2.5rem;
            color: var(--accent-beige);
            margin-bottom: 15px;
        }
        .user-info {
            background: var(--accent-beige);
            width: 100%;
            padding: 20px;
            text-align: center;
            font-family: 'Arial', sans-serif;
        }
    </style>
</head>
<body>
    <header class="header-nav">
        <a href="index.php" class="logo-link"><img src="immagini/tastologo.png" alt="Debook Logo"></a>
        <div style="font-family: Arial;">
            Benvenuto, <strong><?php echo htmlspecialchars($_SESSION["nome"]); ?></strong> | 
            <a href="logout.php" style="color: #d32f2f; text-decoration: none;">Esci</a>
        </div>
    </header>

    <div class="user-info">
        <h2>Area Personale</h2>
        <p><?php echo htmlspecialchars($_SESSION["email"]); ?></p>
    </div>

    <div class="dashboard-grid">
        <div class="card">
            <i class="fa-solid fa-book"></i>
            <h3>I miei Libri</h3>
            <p>Hai <strong><?php echo $conta_libri; ?></strong> libri in vendita</p>
            <a href="my_list.php" class="btn-submit" style="display:inline-block; margin-top:15px; font-size:1rem; text-decoration:none;">Gestisci</a>
        </div>

        <a href="vendi.php" class="card">
            <i class="fa-solid fa-plus-circle"></i>
            <h3>Vendi un Libro</h3>
            <p>Carica un nuovo annuncio ora</p>
        </a>

        <a href="chat.php" class="card">
            <i class="fa-solid fa-comments"></i>
            <h3>Messaggi</h3>
            <p>Contatta acquirenti o venditori</p>
        </a>

        <a href="miei_ordini.php" class="card">
            <i class="fa-solid fa-cart-shopping"></i>
            <h3>I miei Acquisti</h3>
            <p>Visualizza lo storico ordini</p>
        </a>
    </div>
</body>
</html>