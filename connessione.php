<?php
$connessione = mysqli_connect("localhost", "root", "", "autoadvisor");
if (!$connessione) {
    die("Connessione fallita: " . mysqli_connect_error());
}
mysqli_set_charset($connessione, "utf8mb4");
?>