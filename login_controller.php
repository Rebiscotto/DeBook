<?php
session_start();
if (isset($_POST["username"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];
    $conn =mysqli_connect("localhost","root","","my_fleone");
    $sql="select * from utenti where username = ? and password= ?";
    $stmt=mysqli_prepare($conn,$sql);
    mysqli_stmt_bind_param($stmt,"ss", $username, $password);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if (mysqli_num_rows($result)> 0) {
            $utente= mysqli_fetch_assoc($result);
            $_SESSION["loggedin"] = true;
            $_SESSION["username"] = $utente["username"];
            $_SESSION["ruolo"] = $utente["ruolo"];
            echo "<h4>Benvenuto ". $utente['username'];

    } else 
    {
        echo "utente o password non corretti. <a href='login.php'>riprova</a>";    
    }
    mysqli_close(($conn)); 

}
else {

 header('Location: ../index.php');         
}