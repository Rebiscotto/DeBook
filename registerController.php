<?php
// 1. ABILITA IL BUFFERING (Fondamentale: impedisce invii accidentali al browser)
ob_start();

// 2. INIZIA LA SESSIONE
session_start();

// 3. Configurazione del Database
$host = 'localhost';
$db   = 'my_fleone';
$user = 'fleone';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    // 4. Ricezione dati
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password_inviata = $_POST['password'] ?? '';

    if (empty($email) || strlen($password_inviata) < 8) {
        header("Location: register.php?error=dati_invalidi");
        exit;
    }

    // 5. Hash e Inserimento
    $password_hash = password_hash($password_inviata, PASSWORD_DEFAULT);
    $sql = "INSERT INTO Utenti (email, password) VALUES (?, ?)";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute([$email, $password_hash])) {
        // --- SE L'ESECUZIONE HA SUCCESSO ---
        
        $user_id = $pdo->lastInsertId();

        // Prepariamo la sessione
        $_SESSION["loggedin"] = true;
        $_SESSION["id"] = $user_id;
        $_SESSION["email"] = $email;
        $_SESSION["nome"] = explode('@', $email)[0];

        // Svuota ogni eventuale testo/spazio generato per errore
        ob_end_clean(); 

        // Reindirizzamento
        header("Location: index.php");
        exit;
    }

} catch (\PDOException $e) {
    ob_end_clean();
    if ($e->getCode() == 23000) { 
        header("Location: register.php?error=email_esistente");
    } else {
        // Se c'è un errore tecnico, lo stampiamo per debuggare
        die("Errore DB: " . $e->getMessage());
    }
    exit;
}
?>