<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION["loggedin"])) { header("Location: login.php"); exit; }

$id_destinatario = isset($_GET['id_venditore']) ? intval($_GET['id_venditore']) : null;

if (!$id_destinatario) { 
    header("Location: chat.php"); 
    exit; 
}

$st_u = $conn->prepare("SELECT nome, cognome FROM Utenti WHERE IdUtente = ?");
$st_u->bind_param("i", $id_destinatario);
$st_u->execute();
$dest = $st_u->get_result()->fetch_assoc();

if (!$dest) { header("Location: chat.php"); exit; }
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Debook - Lascia Feedback</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f7f6; margin: 0; padding: 0; font-family: 'Segoe UI', Arial, sans-serif; }
        
        .feedback-box { 
            max-width: 500px; 
            width: 90%;
            margin: 40px auto; 
            background: white; 
            padding: 40px 20px; 
            border-radius: 30px; 
            box-shadow: var(--shadow); 
            text-align: center; 
            position: relative;
            box-sizing: border-box;
        }

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
        }

        /* STELLE SEMPRE VISIBILI */
        .star-rating { 
            display: flex; 
            flex-direction: row-reverse; 
            justify-content: center; 
            gap: 10px; 
            margin: 25px 0; 
        }
        .star-rating input { display: none; }
        
        /* Icona vuota di default */
        .star-rating label { 
            font-size: 2.8rem; 
            color: #ccc; 
            cursor: pointer; 
            transition: 0.2s; 
        }
        
        /* Cambia colore e riempie la stella al passaggio o selezione */
        .star-rating input:checked ~ label,
        .star-rating label:hover,
        .star-rating label:hover ~ label { 
            color: #f39c12; 
        }

        /* DESCRIZIONE SEPARATA */
        .comment-section {
            text-align: left;
            margin-top: 20px;
        }
        .comment-label {
            display: block;
            font-weight: bold;
            color: #444;
            margin-bottom: 8px;
            margin-left: 5px;
            font-size: 0.95rem;
        }
        
        textarea { 
            width: 100%; 
            border: 2px solid #eee; 
            border-radius: 15px; 
            padding: 15px; 
            outline: none; 
            font-family: inherit; 
            font-size: 16px; 
            resize: none; 
            margin-bottom: 20px; 
            box-sizing: border-box; 
            transition: border-color 0.3s;
        }
        textarea:focus { border-color: #f39c12; }

        .btn-submit {
            background: #333;
            color: white;
            border: none;
            padding: 15px;
            border-radius: 50px;
            width: 100%;
            font-weight: bold;
            cursor: pointer;
            font-size: 1rem;
            transition: 0.3s;
        }
        .btn-submit:hover { background: #000; }

        @media (max-width: 480px) {
            .feedback-box { margin: 20px auto; padding: 60px 20px 30px; }
            .star-rating label { font-size: 2.2rem; }
        }
    </style>
</head>
<body>

    <div class="feedback-box">
        <button onclick="history.back()" class="btn-back-feedback">
            <i class="fa-solid fa-arrow-left"></i>
        </button>

        <h2 style="font-family:'Arial Black'; margin-top: 20px; text-transform: uppercase; color: #333;">
            Valuta <?php echo htmlspecialchars($dest['nome']); ?>
        </h2>
        <p style="color: #888; font-size: 0.9rem;">Assegna un punteggio da 1 a 5 stelle.</p>
        
        <form action="lascia_feedback_controller.php" method="POST">
            <input type="hidden" name="IdDestinatario" value="<?php echo $id_destinatario; ?>">
            
            <div class="star-rating">
                <input type="radio" id="star5" name="voto" value="5" required>
                <label for="star5" class="fa-solid fa-star"></label>
                
                <input type="radio" id="star4" name="voto" value="4">
                <label for="star4" class="fa-solid fa-star"></label>
                
                <input type="radio" id="star3" name="voto" value="3">
                <label for="star3" class="fa-solid fa-star"></label>
                
                <input type="radio" id="star2" name="voto" value="2">
                <label for="star2" class="fa-solid fa-star"></label>
                
                <input type="radio" id="star1" name="voto" value="1">
                <label for="star1" class="fa-solid fa-star"></label>
            </div>

            <div class="comment-section">
                <label class="comment-label" for="commento">Il tuo commento:</label>
                <textarea id="commento" name="commento" rows="4" placeholder="Com'è andata la trattativa? Raccontaci la tua esperienza..." required></textarea>
            </div>
            
            <button type="submit" class="btn-submit">INVIA VALUTAZIONE</button>
        </form>
    </div>

</body>
</html>rrrrr