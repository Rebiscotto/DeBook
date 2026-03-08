<?php
// 1. Inizia il buffering dell'uscita (fondamentale per evitare l'errore "headers already sent")
ob_start();

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
    ob_end_clean();
    die("Errore di connessione: " . $e->getMessage());
}

// 3. Ricezione e Pulizia dei dati
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$password_inviata = $_POST['password'] ?? '';

// Validazione Password
if (strlen($password_inviata) < 8) {
    ob_end_clean();
    die("Errore: La password deve essere di almeno 8 caratteri.");
}

// 4. Criptazione della password
$password_hash = password_hash($password_inviata, PASSWORD_DEFAULT);

// 5. Scrittura sul Database
$sql = "INSERT INTO utenti (email, password) VALUES (?, ?)";
$stmt = $pdo->prepare($sql);

try {
    $stmt->execute([$email, $password_hash]);
    
    // --- REGISTRAZIONE RIUSCITA ---
    // Pulisce ogni eventuale spazio o testo generato finora
    ob_end_clean(); 
    
    // Reindirizzamento alla index (assicurati che il percorso sia corretto)
    header("Location: index.php?status=success");
    exit(); 

} catch (\PDOException $e) {
    // In caso di errore, svuota il buffer e mostra l'errore
    ob_end_clean();
    if ($e->getCode() == 23000) {
        die("Errore: Questa email è già registrata.");
    } else {
        die("Errore durante il salvataggio: " . $e->getMessage());
    }
}
?>