<?php
session_start();
if (isset($_POST["email"])) {
    $email = $_POST["email"];
    $password = $_POST["password"]; // La password inserita dall'utente nel form
    
    $conn = mysqli_connect("localhost", "root", "", "my_fleone");
    
    // 1. Cerchiamo l'utente SOLO per email
    $sql = "SELECT * FROM utenti WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($utente = mysqli_fetch_assoc($result)) {
        // 2. Verifichiamo se la password corrisponde all'hash nel database
        // Se non usi password_hash() in registrazione, usa: if ($password == $utente['Password'])
        if (password_verify($password, $utente['Password'])) {
            $_SESSION["loggedin"] = true;
            $_SESSION["email"] = $utente["email"];
            echo "<h4>Benvenuto " . htmlspecialchars($utente['email']) . "</h4>";
        } else {
            echo "Password errata. <a href='login.php'>riprova</a>";
        }
    } else {
        echo "Utente non trovato. <a href='login.php'>riprova</a>";    
    }
    mysqli_close($conn); 
} else {
    header('Location: ../index.php');         
}