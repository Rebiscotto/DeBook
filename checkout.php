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
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Checkout - Debook</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://www.paypal.com/sdk/js?client-id=ATmdhu6fk7cVs8sfbvpMRBKNV2309B8cBbpauv9wJIIhgeR2GtAfywE6L8vU61EBOAWxqwXye4Q3opvF&currency=EUR"></script>
    <style>
        body { 
            background-color: #f4f7f6; 
            font-family: Arial, sans-serif; 
            margin: 0; 
            padding: 0; 
        }
        
        .checkout-box { 
            max-width: 500px; 
            width: 90%; /* Occupa quasi tutto lo schermo su mobile */
            margin: 40px auto; 
            padding: 30px; 
            background: white; 
            border-radius: 30px; 
            box-shadow: 0 15px 35px rgba(0,0,0,0.1); 
            text-align: center;
            box-sizing: border-box; /* Impedisce al padding di rompere la larghezza */
        }

        .price { 
            font-size: 2.2rem; 
            color: #27ae60; 
            font-weight: bold; 
            margin: 20px 0; 
            font-family: 'Arial Black', sans-serif; 
        }

        #paypal-button-container { 
            margin-top: 20px; 
            width: 100%;
        }
        
        .btn-back {
            display: inline-block;
            margin-top: 25px;
            text-decoration: none;
            color: #7f8c8d;
            font-weight: bold;
            font-size: 0.95rem;
            transition: 0.3s;
            padding: 10px;
        }
        .btn-back:hover { color: #333; }

        /* AGGIUSTAMENTI PER MOBILE */
        @media (max-width: 480px) {
            .checkout-box {
                margin: 20px auto;
                padding: 20px;
            }
            h2 { font-size: 1.4rem; }
            h3 { font-size: 1.1rem; }
            .price { font-size: 1.8rem; }
        }
    </style>
</head>
<body>
    <div class="checkout-box">
        <h2>Riepilogo Ordine</h2>
        <p style="color: #666; margin-bottom: 5px;">Stai acquistando:</p>
        <h3 style="margin-top: 0; line-height: 1.2;"><?php echo htmlspecialchars($libro['titolo']); ?></h3>
        <p>Venditore: <strong><?php echo htmlspecialchars($libro['venditore']); ?></strong></p>
        
        <div class="price"><?php echo number_format($libro['prezzo'], 2); ?> €</div>

        <div id="paypal-button-container"></div>

        <a href="book_details.php?id=<?php echo $id_libro; ?>" class="btn-back">
            <i class="fa-solid fa-arrow-left"></i> Annulla e torna al libro
        </a>
    </div>

    <script>
        paypal.Buttons({
            // Rende il bottone responsive automaticamente
            style: {
                layout: 'vertical',
                color:  'gold',
                shape:  'pill',
                label:  'paypal'
            },
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
                    const formData = new FormData();
                    formData.append('id_libro', '<?php echo $id_libro; ?>');

                    return fetch('conferma_pagamento.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(res => {
                        if(res.success) {
                            alert('PAGAMENTO EFFETTUATO! Il libro è tuo. Verrai reindirizzato al mercatino.');
                            window.location.href = 'compra.php';
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
                alert("Errore tecnico PayPal. Verifica di usare un account Sandbox.");
                console.error(err);
            }
        }).render('#paypal-button-container');
    </script>
</body>
</html>