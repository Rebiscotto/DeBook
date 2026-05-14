<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION["loggedin"])) { header("Location: login.php"); exit; }

// AGGIORNATO: Ora legge 'id_venditore' come inviato dalla chat
$id_destinatario = isset($_GET['id_venditore']) ? intval($_GET['id_venditore']) : null;

if (!$id_destinatario) { 
    // Se non c'è l'ID, torna alla chat invece che alla index per non disorientare l'utente
    header("Location: chat.php"); 
    exit; 
}

// Recuperiamo il nome del venditore
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
        body { background-color: #f4f7f6; margin: 0; padding: 0; }
        
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
            font-family: Arial, sans-serif;
        }

        .star-rating { display: flex; flex-direction: row-reverse; justify-content: center; gap: 5px; margin: 25px 0; }
        .star-rating input { display: none; }
        .star-rating label { font-size: 2.8rem; color: #ddd; cursor: pointer; transition: 0.2s; }
        .star-rating input:checked ~ label, .star-rating label:hover, .star-rating label:hover ~ label { color: #f39c12; }
        
        textarea { 
            width: 100%; 
            border: 2px solid #eee; 
            border-radius: 15px; 
            padding: 15px; 
            outline: none; 
            font-family: Arial; 
            font-size: 16px; /* Evita lo zoom su iPhone */
            resize: none; 
            margin-bottom: 20px; 
            box-sizing: border-box; 
        }

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
        }

        @media (max-width: 480px) {
            .feedback-box { margin: 20px auto; padding: 50px 20px 30px; }
            .star-rating label { font-size: 2.2rem; }
        }
    </style>
</head>
<body>

    <div class="feedback-box">
        <button onclick="history.back()" class="btn-back-feedback">
            <i class="fa-solid fa-arrow-left"></i>
        </button>

        <h2 style="font-family:'Arial Black'; margin-top: 20px; text-transform: uppercase;">
            Valuta <?php echo htmlspecialchars($dest['nome']); ?>
        </h2>
        <p style="color: #888; font-size: 0.9rem;">La tua opinione aiuta la community di Debook.</p>
        
        <form action="lascia_feedback_controller.php" method="POST">
            <input type="hidden" name="IdDestinatario" value="<?php echo $id_destinatario; ?>">
            
            <div class="star-rating">
                <input type="radio" id="star5" name="voto" value="5" required><label for="star5">★</label>
                <input type="radio" id="star4" name="voto" value="4"><label for="star4">★</label>
                <input type="radio" id="star3" name="voto" value="3"><label for="star3">★</label>
                <input type="radio" id="star2" name="voto" value="2"><label for="star2">★</label>
                <input type="radio" id="star1" name="voto" value="1"><label for="star1">★</label>
            </div>

            <textarea name="commento" rows="4" placeholder="Com'è andata la trattativa? Il venditore è stato affidabile?" required></textarea>
            
            <button type="submit" class="btn-submit">INVIA FEEDBACK</button>
        </form>
    </div>

</body>
</html>