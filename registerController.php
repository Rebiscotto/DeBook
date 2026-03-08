<?php
// 1. Configurazione del Database
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

// 2. Ricezione e Pulizia dei dati
$email = isset($_POST['email']) ? trim($_POST['email']) : ''; // Corretto il nome variabile
$password_inviata = $_POST['password'] ?? '';

// Validazione Password
if (strlen($password_inviata) < 8) {
    die("Errore: La password deve essere di almeno 8 caratteri.");
}

// 3. Criptazione della password
$password_hash = password_hash($password_inviata, PASSWORD_DEFAULT);

// 4. Scrittura sul Database
$sql = "INSERT INTO utenti (email, password) VALUES (?, ?)";
$stmt = $pdo->prepare($sql);

try {
    $stmt->execute([$email, $password_hash]);
    
    // REGISTRAZIONE RIUSCITA: Reindirizzamento alla index
    // Nota: Non deve esserci alcun "echo" o HTML prima di questa riga
    header("Location: index.php?status=success");
    exit(); 

} catch (\PDOException $e) {
    if ($e->getCode() == 23000) {
        echo "Errore: Questa email è già registrata.";
    } else {
        echo "Errore durante il salvataggio: " . $e->getMessage();
    }
}
?>