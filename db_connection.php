<?php
  // FILE: connessione.php
  $host = "localhost";
  $username = "fleone"; // <--INSERISCI QUI IL TUO NOME UTENTE ALTERVISTA = nomeprogetto
  $password = "";            // <--SU ALTERVISTA LASCIA VUOTO (o metti la pass del pannello se l'hai cambiata)
  $database = "my_fleone"; // <--SCRIVI my_ SEGUITO DAL TUO NOME UTENTE
  // Creiamo la connessione
  $conn = new mysqli($host, $username, $password, $database);

  // Verifichiamo se ci sono errori
  if ($conn->connect_error) {
      die("Errore di connessione al database: " . $conn->connect_error);
  } 

  // Impostiamo la codifica caratteri corretta
  $conn->set_charset("utf8");
?>
