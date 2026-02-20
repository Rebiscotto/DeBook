<form action="register.php" method="POST">
    <h2>Crea un Account</h2>
    <input type="text" name="username" placeholder="Username" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Registrati</button>
</form>
<?php
require_once "db_connection.php"; // 1. Connessione al database

// 2. Recupero e protezione dati
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $_POST['username'];
    $email = $_POST['email'];
    
    // MAI salvare la password in chiaro! Usiamo password_hash.
    $pass = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // 3. Preparazione della query (Prepared Statements per evitare SQL Injection)
    $stmt = $conn->prepare("INSERT INTO utenti (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $user, $email, $pass);

    if ($stmt->execute()) {
        echo "Registrazione effettuata!";
    } else {
        echo "Errore: " . $stmt->error;
    }

    $stmt->close();
}
$conn->close();
?>
