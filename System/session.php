<?php
session_start(); // Inicia a sessão

// Verifica se o cliente está logado
if (isset($_SESSION['cliente'])) {
    $cliente = $_SESSION['cliente']; // Informações do cliente
} else {
    $cliente = null; // Nenhum cliente logado
}
?>
