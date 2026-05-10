<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION["loggedin"]) || !isset($_GET['id_libro'])) {
    header("Location: login.php");
    exit;
}

$id_libro = $_GET['id_libro'];
$id_acquirente = $_SESSION["id"];

$query = "SELECT L.*, A.titolo, U.nome as venditore, U.IdUtente as id_venditore 
          FROM Libri L 
          JOIN AnagraficaLibri A ON L.IdAnag = A.IdAnag
          JOIN Utenti U ON L.IdVenditore = U.IdUtente
          WHERE L.IdLibro = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_libro);
$stmt->execute();
$libro = $stmt->get_result()->fetch_assoc();

if (!$libro) die("Libro non trovato.");
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Debook - Checkout</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://www.paypal.com/sdk/js?client-id=ATmdhu6fk7cVs8sfbvpMRBKNV2309B8cBbpauv9wJIIhgeR2GtAfywE6L8vU61EBOAWxqwXye4Q3opvF&currency=EUR"></script>
    
    <style>
        .checkout-container { width: 90%; max-width: 600px; margin: 40px auto; background: white; padding: 40px; border-radius: 20px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); font-family: Arial; }
        .summary-box { background: #f9f9f9; padding: 20px; border-radius: 15px; margin-bottom: 25px; }
        .option-group { margin-bottom: 25px; }
        select { width: 100%; padding: 12px; border-radius: 10px; border: 2px solid #eee; }
        #paypal-button-container { display: none; margin-top: 20px; }
    </style>
</head>
<body>

<div class="checkout-container">
    <h2>Conferma lo Scambio</h2>
    
    <div class="summary-box">
        <h3 style="margin: 0;"><?php echo htmlspecialchars($libro['titolo']); ?></h3>
        <p>Venditore: <strong><?php echo htmlspecialchars($libro['venditore']); ?></strong></p>
        <p style="font-size: 1.5rem; font-weight: bold; color: var(--dark-text);">Prezzo: <?php echo number_format($libro['prezzo'], 2); ?> €</p>
    </div>

    <form id="cashForm" action="process_checkout.php" method="POST">
        <input type="hidden" name="id_libro" value="<?php echo $id_libro; ?>">
        
        <div class="option-group">
            <label>Punto di incontro</label>
            <select name="punto_ritiro" required>
                <option value="Atrio Principale">Atrio Principale</option>
                <option value="Biblioteca Scolastica">Biblioteca Scolastica</option>
            </select>
        </div>

        <div class="option-group">
            <label>Metodo di Pagamento</label>
            <div style="display: flex; gap: 20px; margin-top: 10px;">
                <label><input type="radio" name="metodo" value="Contanti" checked onclick="togglePayment('cash')"> Contanti</label>
                <label><input type="radio" name="metodo" value="Digitale" onclick="togglePayment('paypal')"> PayPal</label>
            </div>
        </div>

        <button type="submit" id="btnConfirmCash" class="btn-submit" style="width: 100%;">Conferma Ordine (Contanti)</button>
    </form>

    <div id="paypal-button-container"></div>
</div>

<script>
    // Funzione per scambiare i bottoni
    function togglePayment(method) {
        if(method === 'paypal') {
            document.getElementById('btnConfirmCash').style.display = 'none';
            document.getElementById('paypal-button-container').style.display = 'block';
        } else {
            document.getElementById('btnConfirmCash').style.display = 'block';
            document.getElementById('paypal-button-container').style.display = 'none';
        }
    }

    // Integrazione PayPal
    paypal.Buttons({
        createOrder: function(data, actions) {
            return actions.order.create({
                purchase_units: [{
                    amount: { value: '<?php echo $libro['prezzo']; ?>' }
                }]
            });
        },
        onApprove: function(data, actions) {
            return actions.order.capture().then(function(details) {
                // Pagamento approvato! Chiamiamo il backend per aggiornare il DB
                fetch('conferma_pagamento.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        id_libro: <?php echo $id_libro; ?>,
                        orderID: data.orderID,
                        payerID: data.payerID
                    })
                })
                .then(response => response.json())
                .then(res => {
                    if(res.success) {
                        alert('Pagamento Riuscito! Il libro è tuo.');
                        window.location.href = 'profilo.php';
                    }
                });
            });
        }
    }).render('#paypal-button-container');
</script>

</body>
</html>