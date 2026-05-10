<?php
session_start();
require_once 'db_connection.php';

// 1. Controllo Accesso
if (!isset($_SESSION["loggedin"]) || !isset($_GET['id_libro'])) {
    header("Location: login.php");
    exit;
}

$id_libro = intval($_GET['id_libro']);
$id_acquirente = $_SESSION["id"];

// 2. Recupero dettagli del libro e del venditore
$query = "SELECT L.*, A.titolo, U.nome as venditore, U.IdUtente as id_venditore 
          FROM Libri L 
          JOIN AnagraficaLibri A ON L.IdAnag = A.IdAnag
          JOIN Utenti U ON L.IdVenditore = U.IdUtente
          WHERE L.IdLibro = ? AND L.stato = 'disponibile'";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_libro);
$stmt->execute();
$libro = $stmt->get_result()->fetch_assoc();

// Se il libro non esiste o è già stato venduto
if (!$libro) {
    echo "<script>alert('Spiacenti, questo libro non è più disponibile.'); window.location.href='compra.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debook - Checkout</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script src="https://www.paypal.com/sdk/js?client-id=ATmdhu6fk7cVs8sfbvpMRBKNV2309B8cBbpauv9wJIIhgeR2GtAfywE6L8vU61EBOAWxqwXye4Q3opvF&currency=EUR"></script>

    <style>
        body { background-color: #f4f7f6; font-family: Arial, sans-serif; }
        .checkout-container { max-width: 600px; margin: 40px auto; background: white; padding: 40px; border-radius: 25px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
        
        .summary-box { background: #f9f9f9; padding: 20px; border-radius: 15px; border: 1px solid #eee; margin-bottom: 25px; }
        .summary-box h3 { margin: 5px 0; color: #333; }
        .price-display { font-size: 1.8rem; font-weight: bold; color: #27ae60; margin-top: 10px; }

        .option-group { margin-bottom: 25px; }
        .option-group label { display: block; margin-bottom: 10px; font-weight: bold; color: #555; }
        
        select { width: 100%; padding: 12px; border-radius: 10px; border: 2px solid #eee; font-size: 1rem; outline: none; transition: 0.3s; }
        select:focus { border-color: var(--accent-beige); }

        .payment-methods { display: flex; gap: 20px; margin-top: 10px; background: #fff; padding: 10px; border-radius: 12px; }
        .payment-methods label { font-weight: normal; cursor: pointer; display: flex; align-items: center; gap: 8px; }

        #paypal-button-container { display: none; margin-top: 20px; min-height: 150px; }
        .btn-submit { width: 100%; padding: 15px; border-radius: 50px; font-weight: bold; font-size: 1rem; cursor: pointer; border: none; background: #34495e; color: white; transition: 0.3s; }
        .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
    </style>
</head>
<body>

<div class="checkout-container">
    <h2 style="text-align: center; margin-bottom: 30px;">Completa l'Acquisto</h2>
    
    <div class="summary-box">
        <p style="color: #888; font-size: 0.8rem; margin: 0;">RIEPILOGO ORDINE</p>
        <h3><?php echo htmlspecialchars($libro['titolo']); ?></h3>
        <p>Venditore: <strong><?php echo htmlspecialchars($libro['venditore']); ?></strong></p>
        <div class="price-display"><?php echo number_format($libro['prezzo'], 2); ?> €</div>
    </div>

    <form id="checkoutForm" action="process_checkout.php" method="POST">
        <input type="hidden" name="id_libro" value="<?php echo $id_libro; ?>">

        <div class="option-group">
            <label for="punto_ritiro"><i class="fa-solid fa-location-dot"></i> Dove vuoi incontrare il venditore?</label>
            <select name="punto_ritiro" id="punto_ritiro" required>
                <option value="Atrio Principale">Atrio Principale</option>
                <option value="Biblioteca Scolastica">Biblioteca Scolastica</option>
                <option value="Cortile Esterno">Cortile Esterno</option>
                <option value="Laboratorio Informatica">Laboratorio Informatica</option>
            </select>
        </div>

        <div class="option-group">
            <label><i class="fa-solid fa-credit-card"></i> Metodo di Pagamento</label>
            <div class="payment-methods">
                <label>
                    <input type="radio" name="metodo" value="Contanti" checked onclick="togglePayment('cash')"> 
                    <i class="fa-solid fa-money-bill-wave"></i> Contanti
                </label>
                <label>
                    <input type="radio" name="metodo" value="PayPal" onclick="togglePayment('paypal')"> 
                    <i class="fa-brands fa-paypal" style="color: #003087;"></i> PayPal
                </label>
            </div>
        </div>

        <button type="submit" id="btnConfirmCash" class="btn-submit">
            CONFERMA ORDINE
        </button>
    </form>

    <div id="paypal-button-container"></div>
</div>

<script>
    // Funzione per mostrare/nascondere i bottoni di pagamento
    function togglePayment(method) {
        const cashBtn = document.getElementById('btnConfirmCash');
        const paypalContainer = document.getElementById('paypal-button-container');

        if(method === 'paypal') {
            cashBtn.style.display = 'none';
            paypalContainer.style.display = 'block';
        } else {
            cashBtn.style.display = 'block';
            paypalContainer.style.display = 'none';
        }
    }

    // Configurazione PayPal Smart Buttons
    paypal.Buttons({
        // 1. Creazione dell'ordine
        createOrder: function(data, actions) {
            return actions.order.create({
                purchase_units: [{
                    amount: {
                        value: '<?php echo $libro['prezzo']; ?>'
                    },
                    description: 'Acquisto libro: <?php echo addslashes($libro['titolo']); ?>'
                }]
            });
        },

        // 2. Cattura del pagamento dopo l'approvazione dell'utente
     onApprove: function(data, actions) {
    return actions.order.capture().then(function(details) {
        return fetch('conferma_pagamento.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ "id_libro": <?php echo $id_libro; ?> })
        })
        .then(response => response.text()) // Leggiamo come testo per vedere errori PHP
        .then(text => {
            try {
                const res = JSON.parse(text);
                if(res.success) {
                    alert('PAGAMENTO OK!');
                    window.location.href = 'profilo.php';
                } else {
                    alert('Errore Server: ' + res.error);
                }
            } catch(e) {
                // Se c'è un errore PHP (es. un punto e virgola mancante), lo vedrai qui
                alert("Errore critico del server (controlla il file PHP): " + text);
            }
        });
    });
},
        // Gestione cancellazione
        onCancel: function (data) {
            alert("Hai annullato il pagamento. Puoi riprovare o scegliere Contanti.");
        },

        // Gestione errori SDK
        onError: function (err) {
            console.error("Errore SDK PayPal:", err);
            alert("Si è verificato un errore tecnico con PayPal. Riprova più tardi.");
        }
    }).render('#paypal-button-container');
</script>

</body>
</html>