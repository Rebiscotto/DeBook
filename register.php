<?php
// Gestione della registrazione (logica backend)
$messaggio = ""; 

// Attiviamo i report degli errori
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        require_once "db_connection.php"; 

        $name = trim($_POST['nome']);
        $surname = trim($_POST['cognome']);
        $email = trim($_POST['email']);
        $pass = password_hash($_POST['password'], PASSWORD_BCRYPT);

        // Controllo RF-01 (SRS): Dominio scolastico
        if (!preg_match("/@itisgalileiroma\.it$/", $email) && !preg_match("/@scuola\.it$/", $email)) {
            $messaggio = "<div class='error-msg'>Usa l'email istituzionale per registrarti.</div>";
        } else {
            // Nota: ho messo 'Password' con la P maiuscola per coincidere con il tuo file Utenti.sql
            $stmt = $conn->prepare("INSERT INTO Utenti (nome, cognome, email, Password) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $surname, $email, $pass);

            if ($stmt->execute()) {
                $messaggio = "<div class='success-msg'>Registrazione completata! <a href='login.php' style='color:inherit; font-weight:bold;'>Accedi ora</a></div>";
            }
            $stmt->close();
        }
        $conn->close();

    } catch (Exception $e) {
        // Gestione errore email duplicata
        if ($e->getCode() == 1062) {
            $messaggio = "<div class='error-msg'>Questa email è già registrata.</div>";
        } else {
            $messaggio = "<div class='error-msg'>Errore: " . $e->getMessage() . "</div>";
        }
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
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: var(--bg-page);
            padding: 20px;
        }

        .register-card {
            background-color: var(--white);
            padding: 40px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            width: 100%;
            max-width: 400px;
            text-align: center;
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .register-card img {
            height: 60px;
            margin-bottom: 20px;
        }

        h1 {
            font-family: 'Arial Black', sans-serif;
            font-size: 1.3rem;
            color: var(--dark-text);
            margin-bottom: 25px;
            text-transform: uppercase;
        }

        .input-group {
            margin-bottom: 15px;
            text-align: left;
        }

        .input-group label {
            font-family: 'Arial', sans-serif;
            font-size: 0.8rem;
            font-weight: bold;
            color: #777;
            margin-left: 5px;
            display: block;
            margin-bottom: 5px;
        }

        .input-group input {
            width: 100%;
            padding: 12px;
            border: 2px solid var(--bg-page);
            border-radius: 12px;
            font-size: 1rem;
            outline: none;
            transition: border-color 0.3s;
        }

        .input-group input:focus {
            border-color: var(--accent-beige);
        }

        .register-btn {
            background-color: var(--accent-beige);
            color: var(--dark-text);
            padding: 15px;
            border: none;
            border-radius: 50px;
            font-size: 1.1rem;
            font-family: 'Arial Black', sans-serif;
            width: 100%;
            cursor: pointer;
            transition: transform 0.2s;
            margin-top: 10px;
            text-transform: uppercase;
        }

        .register-btn:hover {
            transform: scale(1.02);
            background-color: #dfc4ab;
        }

        .login-redirect {
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid var(--bg-page);
        }

        .login-redirect p {
            font-family: 'Arial', sans-serif;
            font-size: 0.9rem;
            color: #777;
            margin-bottom: 15px;
        }

        .login-link {
            display: inline-block;
            width: 100%;
            padding: 12px;
            background-color: transparent;
            color: var(--dark-text);
            border: 2px solid var(--dark-text);
            border-radius: 50px;
            font-size: 0.9rem;
            font-family: 'Arial Black', sans-serif;
            text-decoration: none;
            transition: all 0.3s;
        }

        .login-link:hover {
            background-color: var(--dark-text);
            color: var(--white);
        }

        /* Messaggi di stato */
        .success-msg, .error-msg {
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-family: 'Arial', sans-serif;
            font-size: 0.9rem;
        }
        .success-msg { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error-msg { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>

    <div class="register-card">
        <a href="schermata.php"><img src="immagini/tastologo.png" alt="Debook Logo"></a>

        <h1>Crea Account</h1>

        <?php if(!empty($messaggio)) echo $messaggio; ?>

        <form action="register.php" method="POST">
            <div class="input-group">
                <label>NOME</label>
                <input type="text" name="nome" placeholder="Es. Mario" required>
            </div>
            <div class="input-group">
                <label>COGNOME</label>
                <input type="text" name="cognome" placeholder="Es. Rossi" required>
            </div>
            <div class="input-group">
                <label>EMAIL ISTITUZIONALE</label>
                <input type="email" name="email" placeholder="nome.stud@itisgalileiroma.it" required>
            </div>
            <div class="input-group">
                <label>PASSWORD</label>
                <input type="password" name="password" placeholder="Scegli una password sicura" required>
            </div>
            
            <button type="submit" class="register-btn">Registrati</button>
        </form>

        <div class="login-redirect">
            <p>Hai già un account?</p>
            <a href="login.php" class="login-link">ACCEDI</a>
        </div>
    </div>

</body>
</html>