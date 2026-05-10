<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION["loggedin"])) { header("Location: login.php"); exit; }

$id_destinatario = isset($_GET['to']) ? intval($_GET['to']) : null;
if (!$id_destinatario) { header("Location: index.php"); exit; }

// Recuperiamo il nome del venditore per mostrarlo nel titolo
$st_u = $conn->prepare("SELECT nome FROM Utenti WHERE IdUtente = ?");
$st_u->bind_param("i", $id_destinatario);
$st_u->execute();
$dest = $st_u->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debook - Lascia Feedback</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .feedback-box { 
            max-width: 500px; 
            margin: 60px auto; 
            background: white; 
            padding: 40px; 
            border-radius: 30px; 
            box-shadow: var(--shadow); 
            text-align: center; 
            position: relative; /* Necessario per il tasto indietro assoluto */
        }

        /* Stile per il tasto Indietro */
        .btn-back-feedback {
            position: absolute;
            top: 20px;
            left: 20px;
            background: #f0f0f0;
            border: none;
            padding: 8px 12px;
            border-radius: 50px;
            cursor: pointer;
            color: #555;
            font-size: 0.85rem;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: 0.3s;
            font-family: Arial, sans-serif;
        }

        .btn-back-feedback:hover {
            background: #e2e2e2;
            color: #000;
        }

        .star-rating { display: flex; flex-direction: row-reverse; justify-content: center; gap: 10px; margin: 25px 0; }
        .star-rating input { display: none; }
        .star-rating label { font-size: 3rem; color: #ddd; cursor: pointer; transition: 0.2s; }
        .star-rating input:checked ~ label, .star-rating label:hover, .star-rating label:hover ~ label { color: #f39c12; }
        
        textarea { 
            width: 100%; 
            border: 2px solid #eee; 
            border-radius: 15px; 
            padding: 15px; 
            outline: none; 
            font-family: Arial; 
            resize: none; 
            margin-bottom: 20px; 
            box-sizing: border-box; /* Evita che la textarea esca dai bordi */
        }

        .btn-submit {
            cursor: pointer;
            transition: transform 0.2s;
        }
        .btn-submit:hover {
            transform: scale(1.02);
        }
    </style>
</head>
<body>

    <div class="feedback-box">
        <button onclick="history.back()" class="btn-back-feedback">
            <i class="fa-solid fa-arrow-left"></i> Indietro
        </button>

        <h2 style="font-family:'Arial Black'; margin-top: 15px;">VALUTA <?php echo strtoupper($dest['nome']); ?></h2>
        
        <form action="lascia_feedback_controller.php" method="POST">
            <input type="hidden" name="IdDestinatario" value="<?php echo $id_destinatario; ?>">
            
            <div class="star-rating">
                <input type="radio" id="star5" name="voto" value="5" required><label for="star5">★</label>
                <input type="radio" id="star4" name="voto" value="4"><label for="star4">★</label>
                <input type="radio" id="star3" name="voto" value="3"><label for="star3">★</label>
                <input type="radio" id="star2" name="voto" value="2"><label for="star2">★</label>
                <input type="radio" id="star1" name="voto" value="1"><label for="star1">★</label>
            </div>

            <textarea name="commento" rows="4" placeholder="Com'è andata la trattativa?" required></textarea>
            
            <button type="submit" class="btn-submit" style="width:100%; font-weight: bold;">INVIA FEEDBACK</button>
        </form>
    </div>

</body>
</html>