<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: login.php");
    exit;
}

$id_utente = $_SESSION["id"];

$sql_libri = "SELECT COUNT(*) as totale FROM Libri WHERE IdVenditore = ? AND stato = 'disponibile'";
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <title>Debook - Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            background-color: var(--bg-page);
            margin: 0;
            padding: 0;
        }

        .header-nav {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 40px;
            box-sizing: border-box;
            background: white;
            box-shadow: var(--shadow);
        }

        .dashboard-grid {
            display: grid;
            /* Layout Desktop */
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            width: 90%;
            max-width: 1000px;
            margin: 40px auto;
        }

        .card {
            background: white;
            padding: 40px 30px;
            border-radius: 25px;
            text-align: center;
            box-shadow: 0 8px 25px rgba(0,0,0,0.05);
            transition: transform 0.3s, box-shadow 0.3s;
            text-decoration: none;
            color: var(--dark-text);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 35px rgba(0,0,0,0.1);
        }

        .card i {
            font-size: 3.5rem; /* Icone leggermente più grandi */
            color: var(--accent-beige);
            margin-bottom: 20px;
        }

        .user-info {
            background: var(--accent-beige);
            width: 100%;
            padding: 40px 20px;
            text-align: center;
            font-family: 'Arial', sans-serif;
            box-sizing: border-box;
        }

        .user-info h2 {
            margin: 0;
            font-size: 2.2rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* --- STILI MOBILE --- */
        @media (max-width: 768px) {
            .header-nav {
                padding: 15px 20px;
            }
            
            .user-info h2 {
                font-size: 1.8rem;
            }

            .dashboard-grid {
                /* Una sola colonna per avere elementi grandi */
                grid-template-columns: 1fr; 
                width: 92%;
                margin: 20px auto;
                gap: 20px;
            }

            .card {
                padding: 30px 20px;
            }

            .card i {
                font-size: 3rem;
            }

            .card h3 {
                font-size: 1.4rem;
                margin: 10px 0;
            }
        }

        .logo-link img { height: 40px; }
    </style>
</head>
<body>
    <header class="header-nav">
        <a href="index.php" class="logo-link"><img src="immagini/tastologo.png" alt="Debook Logo"></a>
        <div style="font-family: Arial; font-size: 1.1rem;">
            <strong><?php echo htmlspecialchars($_SESSION["nome"]); ?></strong>
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
            <p>Hai <strong><?php echo $conta_libri; ?></strong> annunci attivi</p>
            <a href="my_list.php" class="btn-submit" style="display:inline-block; margin-top:15px; font-size:1rem; text-decoration:none; width: auto; padding: 12px 30px; border-radius: 50px; background: var(--dark-text); color: white;">Gestisci</a>
        </div>

        <a href="vendi.php" class="card">
            <i class="fa-solid fa-circle-plus"></i>
            <h3>Vendi un Libro</h3>
            <p>Carica un nuovo annuncio</p>
        </a>

        <a href="chat.php" class="card">
            <i class="fa-solid fa-comments"></i>
            <h3>Messaggi</h3>
            <p>Contatta acquirenti o venditori</p>
        </a>

        <a href="miei_ordini.php" class="card">
            <i class="fa-solid fa-cart-shopping"></i>
            <h3>I miei Acquisti</h3>
            <p>Storico degli ordini</p>
        </a>
    </div>
</body>
</html>