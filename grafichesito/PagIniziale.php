<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookSchool - Il tuo usato scolastico</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <nav class="navbar">
        <div class="logo">Book<span>School</span></div>
        <ul class="nav-links">
            <li><a href="#">Acquista</a></li>
            <li><a href="#">Vendi</a></li>
            <li><a href="#">Dizionari</a></li>
            <li><a href="#" class="btn-login">Accedi</a></li>
        </ul>
    </nav>
</header>

<section class="hero">
    <div class="hero-content">
        <h1>I tuoi libri scolastici, <br>al miglior prezzo.</h1>
        <p>Cerca per codice ISBN o trova la tua classe in un clic.</p>
        
        <form action="" method="GET" class="search-container">
            <input type="text" name="query" placeholder="Inserisci titolo o codice ISBN..." required>
            <button type="submit">Cerca</button>
        </form>
    </div>
</section>

<main class="results-container">
    <?php
    if (isset($_GET['query'])) {
        $search = htmlspecialchars($_GET['query']);
        echo "<h2>Risultati per: $search</h2>";
        
        // Esempio di array dati (in un caso reale verrebbero dal database)
        $libri = [
            ['titolo' => 'Matematica Blu 2.0', 'isbn' => '9788808', 'prezzo' => '24.50', 'stato' => 'Nuovo'],
            ['titolo' => 'Promessi Sposi - Ed. Critica', 'isbn' => '9788842', 'prezzo' => '12.00', 'stato' => 'Usato Garantito'],
        ];

        echo "<div class='grid-libri'>";
        foreach ($libri as $libro) {
            echo "<div class='card'>
                    <h3>{$libro['titolo']}</h3>
                    <p>ISBN: {$libro['isbn']}</p>
                    <p class='status'>{$libro['stato']}</p>
                    <p class='price'>€ {$libro['prezzo']}</p>
                    <button class='btn-buy'>Aggiungi al carrello</button>
                  </div>";
        }
        echo "</div>";
    }
    ?>
</main>

<footer>
    <p>&copy; 2024 BookSchool - Risparmia sullo studio.</p>
</footer>

</body>
</html>