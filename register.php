<?php
// Gestione della registrazione (logica backend)
$messaggio = ""; 

// 1. Attiviamo i report degli errori per vedere cosa non va nel database
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        require_once "db_connection.php"; 

        $name = $_POST['nome'];
        $surname = $_POST['cognome'];
        $email = $_POST['email'];
        $pass = password_hash($_POST['password'], PASSWORD_BCRYPT);

        // 3. Preparazione della query
        // NOTA: Se l'errore persiste, controlla che la tabella si chiami davvero 'utenti'
        $stmt = $conn->prepare("INSERT INTO Utenti (nome, cognome, email, password) VALUES (?, ?, ?, ?)");
        
        // Ora bind_param non darà più l'errore "bool" perché il try/catch catturerà il problema prima
        $stmt->bind_param("ssss", $name, $surname, $email, $pass);

        if ($stmt->execute()) {
            $messaggio = "<div class='success-msg'>Registrazione effettuata con successo!</div>";
        } else {
            $messaggio = "<div class='error-msg'>Errore durante l'esecuzione.</div>";
        }

        $stmt->close();
        $conn->close();

    } catch (Exception $e) {
        // Se c'è un errore (tabella mancante, colonna errata, ecc.) lo stampiamo qui
        $messaggio = "<div class='error-msg'>Errore Database: " . $e->getMessage() . "</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debook - Registrazione</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Stili generali e reset */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f4f4f9;
        }

        /* Contenitore principale */
        .login-card {
            background-color: #ffffff;
            padding: 50px 40px;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            width: 100%;
            max-width: 380px;
            text-align: center;
        }

        /* Stile del titolo DEBOOK */
        .logo-title {
            font-size: 2.2rem;
            font-weight: 800;
            letter-spacing: 2px;
            color: #1a1a1a;
            margin-bottom: 20px;
        }

        /* Stile dell'icona del carrello */
        .cart-icon {
            font-size: 4.5rem;
            color: #1a1a1a;
            margin-bottom: 25px;
        }

        /* Stile per il tuo H2 "Crea un Account" */
        h2 {
            font-size: 1.1rem;
            color: #666;
            margin-bottom: 20px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Stile dei gruppi di input */
        .input-group {
            margin-bottom: 15px;
        }

        .input-group input {
            width: 100%;
            padding: 14px;
            border: 1px solid #d1d1d1;
            border-radius: 8px;
            font-size: 1rem;
            outline: none;
            transition: border-color 0.3s ease;
        }

        .input-group input:focus {
            border-color: #1a1a1a;
        }

        /* Stile del bottone di submit */
        .login-btn {
            display: inline-block;
            width: 100%;
            padding: 14px;
            margin-top: 15px;
            background-color: transparent;
            color: #1a1a1a;
            border: 2px solid #1a1a1a;
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .login-btn:hover {
            background-color: #1a1a1a;
            color: #ffffff;
        }

        /* Stili per i messaggi di successo/errore PHP */
        .success-msg {
            color: #155724;
            background-color: #d4edda;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }
        
        .error-msg {
            color: #721c24;
            background-color: #f8d7da;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="logo-title">DEBOOK</div>
        <div class="cart-icon">
            <i class="fa-solid fa-cart-shopping"></i>
        </div>

        <?php if(!empty($messaggio)) echo $messaggio; ?>

        <h2>Crea un Account</h2>
        <form action="register.php" method="POST">
            <div class="input-group">
                <input type="text" name="nome" placeholder="Name" required>
            </div>
            <div class="input-group">
                <input type="text" name="cognome" placeholder="Surname" required>
            </div>
            <div class="input-group">
                <input type="email" name="email" placeholder="Email" required>
            </div>
            
            <div class="input-group">
                <input type="password" name="password" placeholder="Password" required>
            </div>
            
            <button type="submit" class="login-btn">Registrati</button>
        </form>
    </div>

</body>
</html>