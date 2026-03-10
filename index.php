<?php

session_start();

$is_logged = isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true;

$nome_utente = $is_logged ? $_SESSION["nome"] : "";

?>

<!DOCTYPE html>

<html lang="it">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Debook - Home</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>

        :root {

            --dark: #1a1a1a;

            --grey-side: #e0e0e0;

        }



        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Arial Black', 'Arial', sans-serif; }



        body { background-color: #fff; overflow: hidden; height: 100vh; position: relative; }



        /* Header e Navbar */

        .header-nav {

            position: absolute;

            top: 0;

            left: 0;

            width: 100%;

            display: flex;

            justify-content: space-between;

            align-items: center;

            padding: 15px 30px;

            z-index: 1000;

        }



        .logo-link img { height: 70px; cursor: pointer; transition: transform 0.2s; }

        .logo-link img:hover { transform: scale(1.05); }



        .user-menu-container { position: relative; }

        .user-icon { font-size: 2.2rem; color: var(--dark); cursor: pointer; }



        .dropdown-menu {

            position: absolute;

            right: 0;

            top: 50px;

            background: white;

            border: 1px solid #ccc;

            border-radius: 4px;

            box-shadow: 0 4px 10px rgba(0,0,0,0.1);

            display: none;

            flex-direction: column;

            min-width: 180px;

        }

        .dropdown-menu.active { display: flex; }

        .dropdown-menu a { padding: 12px; text-decoration: none; color: var(--dark); font-family: 'Arial', sans-serif; font-size: 0.9rem; border-bottom: 1px solid #eee; }



        /* Layout */

        .wrapper { display: flex; height: 100vh; width: 100%; }



        .side {

            width: 15%;

            background-color: var(--grey-side);

            display: flex;

            justify-content: center;

            align-items: center;

            text-decoration: none;

            transition: 0.3s;

        }

        .side:hover { background-color: #d0d0d0; }

        .side h2 { writing-mode: vertical-rl; transform: rotate(180deg); font-size: 3rem; color: var(--dark); }



        .center-content {

            width: 70%;

            display: flex;

            flex-direction: column;

            align-items: center;

            padding: 110px 40px 20px 40px;

            text-align: center;

            overflow-y: auto; /* Permette lo scroll se il testo è lungo */

        }



        .hero-text h1 { font-size: 3.2rem; color: var(--dark); line-height: 1; margin-bottom: 15px; }

        .hero-text p { font-family: 'Arial', sans-serif; font-size: 0.9rem; color: #444; max-width: 550px; margin-bottom: 20px; }



        .image-container img { max-height: 40vh; width: auto; object-fit: contain; margin-top: 20px; }



        .footer-text {

            font-family: 'Arial', sans-serif;

            font-weight: bold;

            text-decoration: underline;

            margin: 15px 0;

            cursor: pointer;

            color: var(--dark);

        }

    </style>

</head>

<body>



    <header class="header-nav">

        <a href="index.php" class="logo-link">

            <img src="immagini/tastologo.png" alt="Debook Logo">

        </a>

        <div class="user-menu-container">

            <i class="fa-solid fa-circle-user user-icon" id="userBtn"></i>

            <div class="dropdown-menu" id="userDropdown">

                <?php if($is_logged): ?>

                    <a href="#">Profilo di <?php echo htmlspecialchars($nome_utente); ?></a>

                    <a href="#"><i class="fa-solid fa-book"></i> I miei libri</a>

                    <a href="logout.php" style="color: red;">Logout</a>

                <?php else: ?>

                    <a href="login.php">Accedi</a>

                    <a href="register.php">Registrati</a>

                <?php endif; ?>

            </div>

        </div>

    </header>



    <div class="wrapper">

        <a href="Vendi.php" class="side"><h2>VENDI</h2></a>



        <div class="center-content">

            <div class="hero-text">

                <h1>IL SAPERE NON HA PREZZO<br>MA QUI COSTA POCHISSIMO</h1>

                <h2>"Il sapere costa meno se è condiviso." Perché pagare di più? Qui trovi libri usati venduti direttamente da altri studenti.</h2>

            </div>

                    <div class="image-container">

                <img src="immagini/home.jpg" alt="Studenti in biblioteca">

            </div>

            <p id="showBtn" class="footer-text">Scopri di più</p>



            <div id="extraContainer" style="display: none;">

                <div id="extraText">

                    <section style="font-family: 'Segoe UI', Roboto, Arial, sans-serif; line-height: 1.6; color: #333; max-width: 650px; margin: 20px auto; padding: 20px; border-radius: 12px; background-color: #f9f9f9; border: 1px solid #eee;">



    <h2 style="color: #2c3e50; font-size: 1.5rem; text-align: center; margin-bottom: 20px; border-bottom: 2px solid #3498db; pb: 10px; padding-bottom: 10px;">

        Perché nasce <strong>DeBook</strong>?

    </h2>



    <p style="font-size: 1rem; margin-bottom: 20px;">

        L’idea di DeBook nasce da un’osservazione semplice: <strong>il paradosso dei libri scolastici</strong>.

        Mentre molte famiglie affrontano spese ingenti ogni anno, migliaia di volumi in ottime condizioni restano inutilizzati sugli scaffali di chi ha terminato gli studi.

        Un potenziale sprecato, sia per il portafoglio che per l'ambiente.

    </p>



    <div style="background: #fff; padding: 15px; border-left: 4px solid #3498db; margin-bottom: 25px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">

        <h3 style="margin-top: 0; color: #2980b9; font-size: 1.1rem;">Oltre il prestito, verso la comunità</h3>

        <p style="margin-bottom: 0; font-size: 0.95rem;">

            DeBook colma il vuoto tra la necessità di possedere un manuale e il desiderio di farlo in modo <strong>sostenibile</strong>.

            Ci siamo chiesti: <em>perché non portare l'efficienza di piattaforme come Vinted direttamente tra i banchi di scuola?</em>

        </p>

    </div>



    <h3 style="text-align: center; font-size: 1.2rem; color: #2c3e50;">La nostra Visione</h3>

   

    <ul style="list-style: none; padding: 0;">

        <li style="margin-bottom: 15px; display: flex; align-items: flex-start;">

            <span style="background: #e1f5fe; color: #0288d1; padding: 5px 10px; border-radius: 50%; margin-right: 12px; font-weight: bold;">🌱</span>

            <span><strong>Economia Circolare:</strong> Riduciamo lo spreco di carta e l'impatto ambientale incentivando il riuso sistematico.</span>

        </li>

        <li style="margin-bottom: 15px; display: flex; align-items: flex-start;">

            <span style="background: #e8f5e9; color: #388e3c; padding: 5px 10px; border-radius: 50%; margin-right: 12px; font-weight: bold;">💰</span>

            <span><strong>Accessibilità:</strong> Rendiamo il diritto allo studio meno gravoso, permettendo di recuperare parte dell'investimento iniziale.</span>

        </li>

        <li style="margin-bottom: 15px; display: flex; align-items: flex-start;">

            <span style="background: #fff3e0; color: #f57c00; padding: 5px 10px; border-radius: 50%; margin-right: 12px; font-weight: bold;">🤝</span>

            <span><strong>Fiducia e Sicurezza:</strong> Scambi a mano tra compagni di scuola. Niente costi di spedizione, solo trasparenza e volti noti.</span>

        </li>

    </ul>



    <p style="text-align: center; font-style: italic; margin-top: 25px; color: #555; border-top: 1px solid #eee; padding-top: 15px;">

        Scegliere DeBook significa credere in una scuola dove la <strong>collaborazione tra pari</strong> genera valore reale.

    </p>



</section>

                </div>

                <p id="hideBtn" class="footer-text" style="margin-top: 15px;">Vedi meno</p>

            </div>

           

        </div>



        <a href="compra.php" class="side"><h2>COMPRA</h2></a>

    </div>



    <script>

        // Gestione Menu Omino

const btn = document.getElementById('userBtn');

const menu = document.getElementById('userDropdown');

btn.onclick = (e) => {

    menu.classList.toggle('active');

    e.stopPropagation();

};

window.onclick = () => menu.classList.remove('active');



// --- LOGICA ESPANDI/RIDUCI AGGIORNATA ---

const showBtn = document.getElementById('showBtn');     // Tasto "Scopri di più"

const hideBtn = document.getElementById('hideBtn');     // Tasto "Vedi meno"

const extraContainer = document.getElementById('extraContainer'); // Contenitore testo + tasto chiudi



// Quando premo "Scopri di più"

showBtn.onclick = () => {

    showBtn.style.display = 'none';          // Scompare il tasto "Scopri di più"

    extraContainer.style.display = 'block';  // Appare il blocco (testo + tasto "Vedi meno")

};



// Quando premo "Vedi meno"

hideBtn.onclick = () => {

    extraContainer.style.display = 'none';   // Scompare tutto il blocco extra

    showBtn.style.display = 'block';         // Riappare il tasto "Scopri di più"

   

    // Torna su dolcemente all'inizio della sezione

    document.querySelector('.center-content').scrollTo({ top: 0, behavior: 'smooth' });

};

    </script>

</body>

</html>