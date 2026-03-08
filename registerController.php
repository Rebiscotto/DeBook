<?php
// 1. Avvio della sessione (indispensabile per il login automatico)
session_start();

// 2. Configurazione del Database
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
} catch (\PDOException $e) {
    die("Errore di connessione: " . $e->getMessage());
}

// 3. Ricezione e Pulizia dei dati
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$password_inviata = $_POST['password'] ?? '';

// Validazione minima
if (empty($email) || strlen($password_inviata) < 8) {
    die("Errore: Dati non validi o password troppo corta.");
}

// 4. Criptazione della password
$password_hash = password_hash($password_inviata, PASSWORD_DEFAULT);

// 5. Scrittura sul Database
$sql = "INSERT INTO utenti (email, password) VALUES (?, ?)";
$stmt = $pdo->prepare($sql);

try {
    $stmt->execute([$email, $password_hash]);
    
    // --- LOGIN AUTOMATICO DOPO REGISTRAZIONE ---
    
    // Recuperiamo l'ID appena creato dal database
    $user_id = $pdo->lastInsertId();
    
    // Impostiamo le variabili di sessione che la tua index si aspetta
    $_SESSION["loggedin"] = true;
    $_SESSION["id"] = $user_id;
    $_SESSION["email"] = $email;
    
    // Se la tua index usa anche il "nome", lo estraiamo dall'email (es. parte prima della @)
    // o puoi aggiungere un campo nome nel form di registrazione
    $_SESSION["nome"] = explode('@', $email)[0]; 

    // Reindirizzamento immediato alla index
    header("Location: index.php");
    exit; // Importante per bloccare l'esecuzione dello script dopo il redirect

} catch (\PDOException $e) {
    if ($e->getCode() == 23000) { 
        echo "Errore: Questa email è già registrata.";
    } else {
        echo "Errore durante il salvataggio: " . $e->getMessage();
    }
}
?><?php
// 1. Inizio della sessione - Fondamentale che sia la primissima riga
session_start();

// 2. Configurazione del Database
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

    // 3. Ricezione e Pulizia dei dati
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password_inviata = $_POST['password'] ?? '';

    // Validazione Password e Email
    if (empty($email) || strlen($password_inviata) < 8) {
        // Se i dati sono invalidi, torna al form con un avviso
        header("Location: register.php?error=invalid_data");
        exit;
    }

    // 4. Criptazione della password
    $password_hash = password_hash($password_inviata, PASSWORD_DEFAULT);

    // 5. Tentativo di scrittura sul Database
    $sql = "INSERT INTO utenti (email, password) VALUES (?, ?)";
    $stmt = $pdo->prepare($sql);
    
    // Il codice prosegue oltre questa riga SOLO se l'execute non lancia eccezioni
    $stmt->execute([$email, $password_hash]);

    // --- SE SIAMO QUI, LA REGISTRAZIONE È ANDATA A BUON FINE ---

    // Recuperiamo l'ID appena generato
    $user_id = $pdo->lastInsertId();

    // Creiamo la sessione di login
    $_SESSION["loggedin"] = true;
    $_SESSION["id"] = $user_id;
    $_SESSION["email"] = $email;
    $_SESSION["nome"] = explode('@', $email)[0]; // Usa la parte prima della @ come nome

    // Reindirizzamento alla Index
    header("Location: index.php");
    exit;

} catch (\PDOException $e) {
    // --- SE SIAMO QUI, C'È STATO UN ERRORE ---
    
    if ($e->getCode() == 23000) { 
        // Caso: Email già presente nel database
        header("Location: register.php?error=email_exists");
    } else {
        // Altri errori del database
        header("Location: register.php?error=db_fail");
    }
    exit;
}
?>