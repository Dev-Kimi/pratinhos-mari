<?php
$host = "localhost";
$user = "root"; // ou o usuário do seu servidor
$pass = "";     // ou a senha do seu servidor
$db   = "pratinhos_mari"; // o nome EXATO do seu banco

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die(json_encode(["success" => false, "error" => "Falha no banco: " . $conn->connect_error]));
}
?>