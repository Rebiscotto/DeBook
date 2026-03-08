<?php
session_start();
if (isset($_POST["email"])) {
    $email = $_POST["email"];
    $password = $_POST["password"];
    $conn =mysqli_connect("localhost","root","","my_fleone");
    $sql="select * from utenti where email = ? and password= ?";
    $stmt=mysqli_prepare($conn,$sql);
    mysqli_stmt_bind_param($stmt,"ss", $email, $password);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if (mysqli_num_rows($result)> 0) {
            $utente= mysqli_fetch_assoc($result);
            $_SESSION["loggedin"] = true;
            $_SESSION["email"] = $utente["email"];
            echo "<h4>Benvenuto ". $utente['email'];

    } else 
    {
        echo "utente o password non corretti. <a href='login.php'>riprova</a>";    
    }
    mysqli_close(($conn)); 

}
else {

 header('Location: ../index.php');         
}