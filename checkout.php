<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION["loggedin"]) || !isset($_GET['id_libro'])) {
    header("Location: login.php");
    exit;
}

$id_libro = intval($_GET['id_libro']);
$id_acquirente = $_SESSION["id"];

$query = "SELECT L.*, A.titolo, U.nome as venditore, U.IdUtente as id_venditore 
          FROM Libri L 
          JOIN AnagraficaLibri A ON L.IdAnag = A.IdAnag
          JOIN Utenti U ON L.IdVenditore = U.IdUtente
          WHERE L.IdLibro = ? AND L.stato = 'disponibile'";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_libro);
$stmt->execute();
$libro = $stmt->get_result()->fetch_assoc();

if (!$libro) {
    echo "<script>alert('Libro non disponibile.'); window.location.href='compra.php';</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Checkout - Debook</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://www.paypal.com/sdk/js?client-id=ATmdhu6fk7cVs8sfbvpMRBKNV2309B8cBbpauv9wJIIhgeR2GtAfywE6L8vU61EBOAWxqwXye4Q3opvF&currency=EUR"></script>
    <style>
        .checkout-box { max-width: 500px; margin: 50px auto; padding: 30px; background: white; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); text-align: center; }
        .price { font-size: 2rem; color: #27ae60; font-weight: bold; margin: 20px 0; }
        #paypal-button-container { margin-top: 20px; }
    </style>
</head>
<body>
    <div class="checkout-box">
        <h2>Riepilogo Ordine</h2>
        <p>Stai acquistando: <strong><?php echo htmlspecialchars($libro['titolo']); ?></strong></p>
        <p>Venditore: <?php echo htmlspecialchars($libro['venditore']); ?></p>
        <div class="price"><?php echo number_format($libro['prezzo'], 2); ?> €</div>

        <div id="paypal-button-container"></div>
    </div>

    <script>
        paypal.Buttons({
            createOrder: function(data, actions) {
                return actions.order.create({
                    purchase_units: [{
                        amount: {
                            value: '<?php echo number_format($libro['prezzo'], 2, '.', ''); ?>'
                        }
                    }]
                });
            },
            onApprove: function(data, actions) {
                return actions.order.capture().then(function(details) {
                    alert("Pagamento approvato! Aggiorno il database...");

                    const formData = new FormData();
                    formData.append('id_libro', '<?php echo $id_libro; ?>');

                    return fetch('conferma_pagamento.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(res => {
                        if(res.success) {
                            alert('COMPLIMENTI! Acquisto completato.');
                            window.location.href = 'profilo.php';
                        } else {
                            alert('Errore Server: ' + res.error);
                        }
                    })
                    .catch(err => {
                        alert("Errore di connessione al database.");
                    });
                });
            },
            onError: function(err) {
                alert("Errore tecnico PayPal. Verifica di usare un account Sandbox e di non essere il venditore del libro.");
                console.error(err);
            }
        }).render('#paypal-button-container');
    </script>
</body>
</html>