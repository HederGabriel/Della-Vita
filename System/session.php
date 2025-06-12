<?php
include_once 'db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['id_cliente'])) {
    $stmt = $pdo->prepare("SELECT nome, avatar FROM clientes WHERE id_cliente = :id_cliente");
    $stmt->execute(['id_cliente' => $_SESSION['id_cliente']]);
    $clienteData = $stmt->fetch(PDO::FETCH_ASSOC);

    $cliente = [
        'id_cliente' => $_SESSION['id_cliente'],
        'nome' => $clienteData['nome'] ?? 'Usuário',
        'avatar' => $clienteData['avatar'] ?? '../IMG/Profile/Default.png'
    ];

    $_SESSION['nome_cliente'] = $cliente['nome'];

} else {
    $cliente = null;
}
?>
