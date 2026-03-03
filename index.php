<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Benvenuto nel mio Sito</title>
    <style>
        body { font-family: sans-serif; text-align: center; padding-top: 50px; }
        .container { max-width: 600px; margin: auto; border: 1px solid #ddd; padding: 20px; border-radius: 10px; }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin: 10px;
            text-decoration: none;
            border-radius: 5px;
            color: white;
            font-weight: bold;
        }
        .btn-login { background-color: #007BFF; }
        .btn-register { background-color: #28a745; }
        .btn:hover { opacity: 0.8; }
    </style>
</head>
<body>

    <div class="container">
        <h1>Benvenuto sulla nostra piattaforma</h1>
        <p>Accedi per gestire il tuo profilo o registrati se sei un nuovo utente.</p>
        
        <hr>

        <a href="login.php" class="btn btn-login">Accedi (Login)</a>

        <a href="register.php" class="btn btn-register">Registrati ora</a>
    </div>

</body>
</html>

<?php
session_start();
?>

<?php if (isset($_SESSION['user_id'])): ?>
    <p>Ciao, sei già loggato! <a href="dashboard.php">Vai alla tua area riservata</a></p>
<?php else: ?>
    <a href="login.php" class="btn btn-login">Accedi</a>
    <a href="register.php" class="btn btn-register">Registrati</a>
<?php endif; ?>