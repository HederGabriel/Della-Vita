<?php
include_once 'db.php'; // Inclui o arquivo de conexão com o banco de dados

if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Inicia a sessão se ainda não estiver iniciada
}

// Verifica se o cliente está logado
if (isset($_SESSION['id_cliente'])) {
    $stmt = $pdo->prepare("SELECT nome, avatar FROM clientes WHERE id_cliente = :id_cliente");
    $stmt->execute(['id_cliente' => $_SESSION['id_cliente']]);
    $clienteData = $stmt->fetch();

    $cliente = [
        'id_cliente' => $_SESSION['id_cliente'],
        'nome' => $clienteData['nome'] ?? 'Usuário',
        'avatar' => $clienteData['avatar'] ?? '../IMG/Profile/Default.png' // Avatar padrão
    ];
} else {
    $cliente = null; // Nenhum cliente logado
}
?>
