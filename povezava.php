<?php
// Poskusi različne nastavitve za MAMP
$host = "localhost";
$username = "root";
$password = "root";
$dbname = "moja_stran";
$port = 8889;

// Poskusi povezavo z portom
$conn = new mysqli($host, $username, $password, $dbname, $port);

// Če ne dela, poskusi brez porta
if ($conn->connect_error) {
    $conn = new mysqli($host, $username, $password, $dbname);
}

// Če še vedno ne dela, poskusi z IP
if ($conn->connect_error) {
    $conn = new mysqli("127.0.0.1", $username, $password, $dbname, $port);
}

// Če še vedno ne dela, prikaži napako
if ($conn->connect_error) {
    die("Napaka pri povezavi z bazo: " . $conn->connect_error . 
        "<br>Preveri če je MAMP zagnan in MySQL teče na portu " . $port);
}
?>