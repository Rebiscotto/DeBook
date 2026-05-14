<?php
session_start();
require_once 'db_connection.php';

if(!isset($_GET['id'])) { header("Location: compra.php"); exit; }
$id_libro = intval($_GET['id']);

$query = "SELECT L.*, A.titolo, A.autore, A.materia, A.codISBN, U.nome as nome_venditore, U.IdUtente 
          FROM Libri L 
          JOIN AnagraficaLibri A ON L.IdAnag = A.IdAnag 
          JOIN Utenti U ON L.IdVenditore = U.IdUtente 
          WHERE L.IdLibro = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_libro);
$stmt->execute();
$libro = $stmt->get_result()->fetch_assoc();

if(!$libro) { die("Libro non trovato."); }

// Prepariamo l'array delle immagini
$imgs = explode(",", $libro['immagine']);
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($libro['titolo']); ?> - Debook</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f7f6; margin: 0; padding: 0; font-family: Arial, sans-serif; }
        .container-detail { 
            max-width: 1000px; margin: 40px auto; padding: 30px; 
            display: flex; gap: 40px; background: white; 
            border-radius: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); 
        }

        /* --- STILE SLIDER --- */
        .image-section { flex: 1; position: relative; max-width: 400px; }
        .slider-container { 
            position: relative; overflow: hidden; border-radius: 20px; 
            box-shadow: 0 5px 15px rgba(0,0,0,0.1); background: #eee;
        }
        .slider-wrapper { display: flex; transition: transform 0.4s ease-in-out; }
        .slide { min-width: 100%; display: flex; align-items: center; justify-content: center; background: white; }
        .slide img { width: 100%; height: 450px; object-fit: contain; }

        /* Frecce */
        .nav-btn {
            position: absolute; top: 50%; transform: translateY(-50%);
            background: rgba(255,255,255,0.8); border: none; width: 40px; height: 40px;
            border-radius: 50%; cursor: pointer; z-index: 10; transition: 0.3s;
            display: flex; align-items: center; justify-content: center; color: #333;
        }
        .nav-btn:hover { background: white; box-shadow: 0 2px 10px rgba(0,0,0,0.2); }
        .prev { left: 10px; }
        .next { right: 10px; }

        /* Puntini (Dots) */
        .dots-container { text-align: center; margin-top: 15px; }
        .dot { 
            display: inline-block; width: 10px; height: 10px; background: #ccc; 
            border-radius: 50%; margin: 0 5px; cursor: pointer; transition: 0.3s;
        }
        .dot.active { background: #27ae60; transform: scale(1.2); }
        /* -------------------- */
        
        .info-section { flex: 1.2; }
        .btn-back { border: none; background: none; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; margin-bottom: 20px; text-decoration: none; color: #7f8c8d; font-weight: bold; transition: 0.3s; font-size: 1rem; }
        .btn-back:hover { color: #333; }
        
        .price { font-size: 2.8rem; font-family: 'Arial Black'; color: #2ecc71; margin: 15px 0; }
        
        .specs { width: 100%; margin: 20px 0; border-collapse: collapse; }
        .specs td { padding: 12px; border-bottom: 1px solid #eee; }
        .label { font-weight: bold; color: #95a5a6; width: 35%; }

        .btn-buy { display: block; background: #27ae60; color: white; padding: 18px; text-align: center; border-radius: 50px; text-decoration: none; font-weight: bold; margin-bottom: 12px; font-size: 1.1rem; transition: 0.3s; }
        .btn-buy:hover { background: #219150; transform: translateY(-2px); }
        
        .btn-chat { display: block; background: #34495e; color: white; padding: 18px; text-align: center; border-radius: 50px; text-decoration: none; font-weight: bold; transition: 0.3s; }
        .btn-chat:hover { background: #2c3e50; transform: translateY(-2px); }
        
        @media (max-width: 768px) { 
            .container-detail { flex-direction: column; margin: 10px; padding: 20px; } 
            .image-section { max-width: 100%; }
            .slide img { height: 350px; }
        }
    </style>
</head>
<body>
    <div class="container-detail">
        <div class="image-section">
            <div class="slider-container">
                <?php if(count($imgs) > 1): ?>
                    <button class="nav-btn prev" onclick="moveSlide(-1)"><i class="fa-solid fa-chevron-left"></i></button>
                    <button class="nav-btn next" onclick="moveSlide(1)"><i class="fa-solid fa-chevron-right"></i></button>
                <?php endif; ?>

                <div class="slider-wrapper" id="sliderWrapper">
                    <?php foreach($imgs as $img): ?>
                        <div class="slide">
                            <img src="<?php echo htmlspecialchars(trim($img)); ?>" alt="Foto libro">
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <?php if(count($imgs) > 1): ?>
                <div class="dots-container" id="dotsContainer">
                    <?php foreach($imgs as $index => $img): ?>
                        <span class="dot <?php echo $index === 0 ? 'active' : ''; ?>" onclick="currentSlide(<?php echo $index; ?>)"></span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="info-section">
            <button onclick="history.back()" class="btn-back">
                <i class="fa-solid fa-arrow-left"></i> Indietro
            </button>
            
            <h1 style="margin: 0; color: #2c3e50;"><?php echo htmlspecialchars($libro['titolo']); ?></h1>
            <p style="font-size: 1.2rem; color: #7f8c8d; margin-top: 5px;">di <?php echo htmlspecialchars($libro['autore']); ?></p>
            
            <div class="price"><?php echo number_format($libro['prezzo'], 2); ?> €</div>

            <table class="specs">
                <tr><td class="label">Materia</td><td><?php echo htmlspecialchars($libro['materia']); ?></td></tr>
                <tr><td class="label">ISBN</td><td><?php echo htmlspecialchars($libro['codISBN'] ?? 'N/D'); ?></td></tr>
                <tr><td class="label">Condizione</td><td><strong><?php echo htmlspecialchars($libro['condizione'] ?? 'Usato'); ?></strong></td></tr>
                <tr><td class="label">Venditore</td><td><i class="fa-solid fa-user-circle"></i> <?php echo htmlspecialchars($libro['nome_venditore']); ?></td></tr>
                <tr>
                    <td class="label">Libro Digitale</td>
                    <td>
                        <?php if($libro['digitale_usato'] == 0): ?>
                            <span style="color: #27ae60; font-weight: bold;"><i class="fa-solid fa-check-circle"></i> Codice Disponibile</span>
                        <?php else: ?>
                            <span style="color: #e74c3c; font-weight: bold;"><i class="fa-solid fa-circle-xmark"></i> Codice Già Usato</span>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>

            <div class="actions" style="margin-top: 30px;">
                <?php if(isset($_SESSION['id'])): ?>
                    <?php if($_SESSION['id'] != $libro['IdUtente']): ?>
                        <a href="checkout.php?id_libro=<?php echo $id_libro; ?>" class="btn-buy">
                            <i class="fa-solid fa-credit-card"></i> COMPRA ORA
                        </a>
                        <a href="chat.php?with=<?php echo $libro['IdUtente']; ?>&id_libro=<?php echo $id_libro; ?>" class="btn-chat">
                            <i class="fa-solid fa-comments"></i> CONTATTA IL VENDITORE
                        </a>
                    <?php else: ?>
                        <div style="background: #fff3cd; color: #856404; padding: 15px; border-radius: 15px; text-align: center; border: 1px solid #ffeeba;">
                            <i class="fa-solid fa-info-circle"></i> Questo annuncio è tuo.
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="login.php" class="btn-buy">ACCEDI PER ACQUISTARE</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        let currentIndex = 0;
        const wrapper = document.getElementById('sliderWrapper');
        const slides = document.querySelectorAll('.slide');
        const dots = document.querySelectorAll('.dot');
        const totalSlides = slides.length;

        function updateSlider() {
            wrapper.style.transform = `translateX(-${currentIndex * 100}%)`;
            // Aggiorna dots
            dots.forEach((dot, index) => {
                dot.classList.toggle('active', index === currentIndex);
            });
        }

        function moveSlide(direction) {
            currentIndex += direction;
            if (currentIndex >= totalSlides) currentIndex = 0;
            if (currentIndex < 0) currentIndex = totalSlides - 1;
            updateSlider();
        }

        function currentSlide(index) {
            currentIndex = index;
            updateSlider();
        }

        // Supporto swipe per mobile
        let touchStartX = 0;
        wrapper.addEventListener('touchstart', e => touchStartX = e.touches[0].clientX);
        wrapper.addEventListener('touchend', e => {
            let touchEndX = e.changedTouches[0].clientX;
            if (touchStartX - touchEndX > 50) moveSlide(1);
            if (touchStartX - touchEndX < -50) moveSlide(-1);
        });
    </script>
</body>
</html>