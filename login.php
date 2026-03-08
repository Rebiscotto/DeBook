<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debook - Login</title>
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

        /* Contenitore principale della pagina di login */
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

        /* Stile per il tuo H1 "Esegui il login" */
        h1 {
            font-size: 1.1rem;
            color: #666;
            margin-bottom: 20px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Stile dei gruppi di input (Username e Password) */
        .input-group {
            margin-bottom: 15px;
            text-align: left;
            font-size: 0.9rem;
            font-weight: 600;
            color: #333;
        }

        .input-group input[type="text"],
        .input-group input[type="password"] {
            width: 100%;
            padding: 12px;
            margin-top: 6px; /* Spazio tra l'etichetta e il campo */
            border: 1px solid #d1d1d1;
            border-radius: 8px;
            font-size: 1rem;
            outline: none;
            transition: border-color 0.3s ease;
        }

        .input-group input:focus {
            border-color: #1a1a1a;
        }

        /* Stile del bottone di submit (forma ovale) */
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
            letter-spacing: 1px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .login-btn:hover {
            background-color: #1a1a1a;
            color: #ffffff;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <img src="immagini/tastologo.jpg" alt="Debook Logo">

        <h1>Esegui il login</h1>
        
        <form method="post" action="login_controller.php">
            <div class="input-group">
                Email<br>
                <input type="text" name="email" required>
            </div>
            
            <div class="input-group">
                Password<br>
                <input type="password" name="password" required>
            </div>
            
            <input type="submit" value="LOGIN" class="login-btn">
        </form>
    </div>

</body>
</html>


