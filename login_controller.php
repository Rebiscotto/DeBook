
<?php
session_start();

// 1. Attiviamo il report degli errori per il debug
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (isset($_POST["email"]) && isset($_POST["password"])) {
    
    // Recupero dati dal form e pulizia spazi bianchi
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    try {
        // 2. Connessione al database
        $conn = mysqli_connect("localhost", "root", "", "my_fleone");

        // 3. Cerchiamo l'utente SOLO tramite email
        // Non cerchiamo la password nella query perché è hashata!
        $sql = "SELECT * FROM Utenti WHERE email = ?";
        $stmt = mysqli_prepare($conn, $sql);
        
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        // 4. Verifichiamo se l'email esiste
        if ($utente = mysqli_fetch_assoc($result)) {
            
            // 5. Verifichiamo se la password inserita corrisponde all'hash nel DB
            if (password_verify($password, $utente['Password'])) {
                
                // Login successo: salviamo i dati in sessione
                $_SESSION["loggedin"] = true;
                $_SESSION["id"] = $utente["IdUtente"];
                $_SESSION["email"] = $utente["email"];
                $_SESSION["nome"] = $utente["nome"];

                echo "<h4>Benvenuto " . htmlspecialchars($utente['nome']) . "!</h4>";
                echo "<p>Accesso effettuato. <a href='../index.php'>Vai alla Home</a></p>";
                
                // Opzionale: reindirizzamento automatico dopo 2 secondi
                // header("refresh:2;url=../index.php");

            } else {
                // Password errata
                echo "<div style='color:red;'>Password non corretta. <a href='login.php'>Riprova</a></div>";
            }
        } else {
            // Email non trovata
            echo "<div style='color:red;'>Nessun utente trovato con questa email. <a href='login.php'>Riprova</a></div>";
        }

        mysqli_stmt_close($stmt);
        mysqli_close($conn);

    } catch (Exception $e) {
        echo "<div style='color:red;'>Errore di connessione: " . $e->getMessage() . "</div>";
    }

} else {
    // Se si tenta di accedere al file senza passare dal form
    header('Location: login.php');
    exit();
}
?>