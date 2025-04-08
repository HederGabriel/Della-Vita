<?php
$host = '127.0.0.1';   
$user = 'root';        
$password = '';       
$dbname = 'pizzaria';   

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}
?>
