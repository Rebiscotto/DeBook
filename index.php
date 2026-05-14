<?php
session_start();
require_once 'db_connection.php';

// Controllo accesso: se non è loggato va alla presentazione
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: schermata.php");
    exit;
}

$id_utente = $_SESSION["id"];
$nome_utente = $_SESSION["nome"] ?? "Utente";
$cognome_utente = $_SESSION["cognome"] ?? "";
$email_utente = $_SESSION["email"] ?? "";

// CONTEGGIO MESSAGGI NON LETTI PER NOTIFICA
$sql_messaggi = "SELECT COUNT(*) as non_letti FROM Messaggi WHERE IdDestinatario = ? AND letto = 0";
$stmt_msg = $conn->prepare($sql_messaggi);
$stmt_msg->bind_param("i", $id_utente);
$stmt_msg->execute();
$res_msg = $stmt_msg->get_result();
$non_letti = $res_msg->fetch_assoc()['non_letti'] ?? 0;
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debook - Area Personale</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        body { background-color: var(--bg-page); overflow-x: hidden; margin: 0; padding: 0; }

        /* HEADER FIX */
        .header-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            background: var(--white);
            box-shadow: var(--shadow);
            position: relative;
            z-index: 3000;
        }

        /* STILE NOTIFICA MESSAGGI */
        .notification-dot {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: #e74c3c;
            color: white;
            font-size: 0.75rem;
            font-weight: bold;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            border: 2px solid white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            z-index: 10;
        }

        .center-content {
            width: 100%;
            max-width: 1000px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 40px 20px;
            text-align: center;
            box-sizing: border-box;
        }

        .banner-header {
            background-color: var(--white);
            padding: 15px;
            border-radius: 60px;
            margin-bottom: 40px;
            width: 100%;
            box-shadow: var(--shadow);
            box-sizing: border-box;
        }
        .banner-inner {
            background-color: var(--accent-beige);
            padding: 25px 20px;
            border-radius: 50px;
        }
        .banner-inner h1 {
            font-size: clamp(1.1rem, 4vw, 2rem);
            color: var(--dark-text);
            text-transform: uppercase;
            margin: 0;
        }

        .action-row {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 20px;
            width: 100%;
            margin-bottom: 50px;
        }

        .btn-main {
            flex: 1;
            max-width: 200px;
            background-color: var(--white);
            border: 4px solid var(--dark-text);
            color: var(--dark-text);
            padding: 20px 10px;
            border-radius: 30px;
            font-family: 'Arial Black', sans-serif;
            font-size: 1.4rem;
            text-decoration: none;
            transition: 0.3s;
            box-shadow: var(--shadow);
        }
        .btn-main:hover {
            background-color: var(--dark-text);
            color: var(--white);
            transform: scale(1.05);
        }

        .main-img-container {
            flex: 1.5;
            max-width: 400px;
        }
        .main-img-container img {
            width: 100%;
            height: auto;
            border-radius: 20px;
            mix-blend-mode: multiply;
        }

        .user-dropdown {
            position: absolute; 
            top: 60px; 
            right: 0; 
            background: white; 
            border-radius: 15px; 
            box-shadow: var(--shadow); 
            width: 260px; 
            display: none; 
            flex-direction: column; 
            z-index: 2000;
            border: 1px solid #eee;
            overflow: hidden;
        }
        
        .user-dropdown.active { display: flex; }

        @media (max-width: 768px) {
            .action-row { flex-direction: column; }
            .btn-main { width: 100%; max-width: none; order: 2; }
            .main-img-container { order: 1; margin-bottom: 20px; }
            .user-dropdown { right: 5px; width: 220px; }
        }

        .dropdown-links a { 
            padding: 15px; 
            text-decoration: none; 
            color: var(--dark-text); 
            display: flex; 
            align-items: center; 
            gap: 12px; 
            font-family: Arial, sans-serif; 
            border-bottom: 1px solid #eee; 
            font-size: 0.95rem;
        }
        .dropdown-links a:hover { background-color: var(--accent-beige); }
        .dropdown-links a:last-child { border-bottom: none; }

        .info-section {
            background: var(--white);
            padding: 40px;
            border-radius: 30px;
            box-shadow: var(--shadow);
            text-align: left;
            width: 100%;
            margin-bottom: 40px;
            box-sizing: border-box;
        }
        .info-section h2 {
            border-bottom: 3px solid var(--accent-beige);
            padding-bottom: 10px;
            margin-bottom: 20px;
            text-align: center;
        }
        .value-list { list-style: none; padding: 0; }
        .value-list li { margin-bottom: 15px; display: flex; align-items: flex-start; gap: 15px; font-family: Arial; }

    </style>
</head>
<body>

    <header class="header-nav">
        <a href="index.php" class="logo-link"><img src="immagini/tastologo.png" alt="Debook Logo" style="height: 40px;"></a>
        
        <div style="position: relative;">
            <i class="fa-solid fa-circle-user" style="font-size: 2.5rem; cursor: pointer; color: var(--dark-text);" id="userBtn"></i>
            
            <?php if ($non_letti > 0): ?>
                <div class="notification-dot"><?php echo $non_letti; ?></div>
            <?php endif; ?>

            <div class="user-dropdown" id="userDropdown">
                <div style="padding: 15px; background: #f8f8f8; border-bottom: 1px solid #eee; font-family: Arial; font-size: 0.9rem;">
                    <strong><?php echo htmlspecialchars($nome_utente . " " . $cognome_utente); ?></strong>
                </div>
                <div class="dropdown-links">
                    <a href="profilo.php"><i class="fa-solid fa-user-gear"></i> Il mio Profilo</a>
                    <a href="dashboard.php"><i class="fa-solid fa-gauge-high"></i> Dashboard</a>
                    <a href="my_list.php"><i class="fa-solid fa-book"></i> I miei Libri</a>
                    <a href="chat.php">
                        <i class="fa-solid fa-comments"></i> Messaggi 
                        <?php if($non_letti > 0) echo "<strong>($non_letti)</strong>"; ?>
                    </a>
                    <a href="miei_ordini.php"><i class="fa-solid fa-cart-shopping"></i> I miei Acquisti</a>
                    <a href="logout.php" style="color: #d32f2f;"><i class="fa-solid fa-right-from-bracket"></i> Esci</a>
                </div>
            </div>
        </div>
    </header>

    <div class="center-content">
        <div class="banner-header">
            <div class="banner-inner">
                <h1>IL SAPERE NON HA PREZZO<br>MA QUI COSTA POCHISSIMO</h1>
            </div>
        </div>

        <div class="action-row">
            <a href="vendi.php" class="btn-main">VENDI</a>
            <div class="main-img-container">
                <img src="immagini/home.jpg" alt="Debook Students">
            </div>
            <a href="compra.php" class="btn-main">COMPRA</a>
        </div>

        <div class="info-section">
            <h2>Perché nasce DeBook?</h2>
            <p style="font-family: Arial; margin-bottom: 20px;">
                L’idea di DeBook nasce per risolvere il <strong>paradosso dei libri scolastici</strong>: 
                spese ingenti per le famiglie ogni anno, mentre migliaia di volumi restano inutilizzati negli armadi.
            </p>

            <div style="background: var(--bg-page); padding: 20px; border-left: 5px solid var(--accent-beige); margin-bottom: 25px; border-radius: 0 15px 15px 0;">
                <h3 style="margin-top: 0; font-size: 1.1rem;">Dalla scuola, per la scuola</h3>
                <p style="font-family: Arial; font-size: 0.95rem; margin: 0;">
                    DeBook trasforma l'istituto in una comunità collaborativa, portando la semplicità dello scambio online tra i corridoi.
                </p>
            </div>

            <ul class="value-list">
                <li><span>🌱</span> <strong>Economia Circolare:</strong> Allunghiamo la vita dei libri e riduciamo l'impatto ambientale.</li>
                <li><span>💰</span> <strong>Accessibilità:</strong> Abbattiamo i costi scolastici rendendo lo studio alla portata di tutti.</li>
                <li><span>🤝</span> <strong>Sicurezza:</strong> Scambi diretti e a mano tra compagni di istituto.</li>
            </ul>
        </div>

        <a href="dashboard.php" class="btn-submit" style="text-decoration: none; display: inline-block;">VAI ALLA TUA DASHBOARD</a>
    </div>

    <script>
        const btn = document.getElementById('userBtn');
        const menu = document.getElementById('userDropdown');
        
        btn.onclick = (e) => { 
            menu.classList.toggle('active'); 
            e.stopPropagation(); 
        };

        window.onclick = (e) => {
            if (!menu.contains(e.target) && e.target !== btn) {
                menu.classList.remove('active');
            }
        };
    </script>
</body>
</html>