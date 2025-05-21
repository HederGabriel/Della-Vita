<?php
include_once 'session.php'; 
include_once 'db.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['id_cliente'], $_SESSION['nome_cliente'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Usuário não autenticado.']);
    exit;
}

$id_cliente = filter_var($_SESSION['id_cliente'], FILTER_VALIDATE_INT);
$nome_cliente = trim($_SESSION['nome_cliente']);

if (!$id_cliente) {
    http_response_code(401);
    echo json_encode(['error' => 'ID de cliente inválido.']);
    exit;
}

$tipo_pedido = filter_input(INPUT_POST, 'tipo_pedido', FILTER_SANITIZE_STRING);
if (!$tipo_pedido) {
    http_response_code(400);
    echo json_encode(['error' => 'Tipo de pedido não informado.']);
    exit;
}

$tipo_pedido = strtolower(trim($tipo_pedido));
$tipos_validos = ['casa', 'local'];

if (!in_array($tipo_pedido, $tipos_validos, true)) {
    http_response_code(400);
    echo json_encode(['error' => 'Tipo de pedido inválido.']);
    exit;
}

try {
    $pdo->beginTransaction();

    $status_pedido = 'Recebido';

    // Inserir pedido (sem endereço)
    $stmt = $pdo->prepare("
        INSERT INTO pedidos (id_cliente, nome_cliente, data_pedido, tipo_pedido, status_pedido)
        VALUES (:id_cliente, :nome_cliente, NOW(), :tipo_pedido, :status_pedido)
    ");
    $stmt->execute([
        ':id_cliente' => $id_cliente,
        ':nome_cliente' => $nome_cliente,
        ':tipo_pedido' => $tipo_pedido,
        ':status_pedido' => $status_pedido
    ]);

    $id_pedido = $pdo->lastInsertId();

    // Se for pedido para casa, inserir endereço na tabela 'enderecos'
    if ($tipo_pedido === 'casa') {
        $rua = filter_input(INPUT_POST, 'rua', FILTER_SANITIZE_STRING);
        $numero = filter_input(INPUT_POST, 'numero', FILTER_SANITIZE_STRING);
        $bairro = filter_input(INPUT_POST, 'bairro', FILTER_SANITIZE_STRING);
        $setor = filter_input(INPUT_POST, 'setor', FILTER_SANITIZE_STRING);
        $cep = filter_input(INPUT_POST, 'cep', FILTER_SANITIZE_STRING);
        $complemento = filter_input(INPUT_POST, 'complemento', FILTER_SANITIZE_STRING);

        if (!$rua || !$numero || !$bairro || !$cep) {
            $pdo->rollBack();
            http_response_code(400);
            echo json_encode(['error' => 'Campos obrigatórios do endereço faltando (rua, número, bairro, cep).']);
            exit;
        }

        $stmtEndereco = $pdo->prepare("
            INSERT INTO enderecos (rua, numero, bairro, setor, cep, complemento, id_pedido) 
            VALUES (:rua, :numero, :bairro, :setor, :cep, :complemento, :id_pedido)
        ");
        $stmtEndereco->execute([
            ':rua' => $rua,
            ':numero' => $numero,
            ':bairro' => $bairro,
            ':setor' => $setor,
            ':cep' => $cep,
            ':complemento' => $complemento,
            ':id_pedido' => $id_pedido
        ]);
    }

    // Atualiza itens_pedido para associar ao pedido criado
    $stmt = $pdo->prepare("
        UPDATE itens_pedido
        SET id_pedido = :id_pedido
        WHERE id_cliente = :id_cliente AND id_pedido IS NULL
    ");
    $stmt->execute([
        ':id_pedido' => $id_pedido,
        ':id_cliente' => $id_cliente
    ]);

    if ($stmt->rowCount() === 0) {
        $pdo->rollBack();
        http_response_code(400);
        echo json_encode(['error' => 'Nenhum item encontrado para associar ao pedido.']);
        exit;
    }

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Pedido finalizado com sucesso!',
        'id_pedido' => $id_pedido
    ]);
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    error_log('Erro ao finalizar pedido: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao finalizar pedido. Tente novamente mais tarde.']);
    exit;
}
?>
