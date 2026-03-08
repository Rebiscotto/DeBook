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
?>