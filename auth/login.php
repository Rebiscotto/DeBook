<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debook - Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        /* Sovrascriviamo o aggiungiamo solo ciò che serve per la login-card */
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: var(--bg-page);
            padding: 20px;
        }

        .login-card {
            background-color: var(--white);
            padding: 40px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            width: 100%;
            max-width: 400px;
            text-align: center;
            /* Animazione d'entrata fluida */
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .login-card img {
            height: 60px;
            margin-bottom: 25px;
        }

        h1 {
            font-family: 'Arial Black', sans-serif;
            font-size: 1.3rem;
            color: var(--dark-text);
            margin-bottom: 30px;
            text-transform: uppercase;
        }

        .input-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .input-group label {
            font-family: 'Arial', sans-serif;
            font-size: 0.85rem;
            font-weight: bold;
            color: #666;
            margin-left: 5px;
            display: block;
            margin-bottom: 5px;
        }

        /* Usiamo lo stile degli input definito per il progetto */
        .input-group input {
            width: 100%;
            padding: 14px;
            border: 2px solid var(--bg-page);
            border-radius: 12px;
            font-size: 1rem;
            font-family: 'Arial', sans-serif;
            outline: none;
            transition: border-color 0.3s;
        }

        .input-group input:focus {
            border-color: var(--accent-beige);
        }

        /* Bottone Login - Stile Pillola della Home */
        .login-btn {
            background-color: var(--accent-beige);
            color: var(--dark-text);
            padding: 15px;
            border: none;
            border-radius: 50px;
            font-size: 1.1rem;
            font-family: 'Arial Black', sans-serif;
            width: 100%;
            cursor: pointer;
            transition: transform 0.2s, background-color 0.2s;
            margin-top: 10px;
        }

        .login-btn:hover {
            transform: scale(1.02);
            background-color: #dfc4ab;
        }

        .register-section {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid var(--bg-page);
        }

        .register-section p {
            font-family: 'Arial', sans-serif;
            font-size: 0.9rem;
            color: #777;
            margin-bottom: 15px;
        }

        /* Bottone Registrati - Stile bordo scuro */
        .register-btn {
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

        .register-btn:hover {
            background-color: var(--dark-text);
            color: var(--white);
        }
    </style>
</head>
<body>

    <div class="login-card">
        <a href="schermata.php"><img src="immagini/tastologo.png" alt="Debook Logo"></a>

        <h1>Esegui il login</h1>
        
        <form method="post" action="login_controller.php">
            <div class="input-group">
                <label>EMAIL ISTITUZIONALE</label>
                <input type="email" name="email" placeholder="nome.cognome.stud@..." required>
            </div>
            
            <div class="input-group">
                <label>PASSWORD</label>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>
            
            <button type="submit" class="login-btn">LOGIN</button>
        </form>

        <div class="register-section">
            <p>Non hai ancora un account?</p>
            <a href="register.php" class="register-btn">REGISTRATI</a>
        </div>
    </div>

</body>
</html>