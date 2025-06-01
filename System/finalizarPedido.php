<?php
session_start();
require_once '../System/db.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['id_cliente'], $_SESSION['nome_cliente'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Usuário não autenticado.']);
    exit;
}

$id_cliente = filter_var($_SESSION['id_cliente'], FILTER_VALIDATE_INT);
$nome_cliente = trim($_SESSION['nome_cliente']);

if (!$id_cliente) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'ID de cliente inválido.']);
    exit;
}

// Recebe e valida tipo_pedido
$tipo_pedido = filter_input(INPUT_POST, 'tipo_pedido', FILTER_SANITIZE_STRING);
if (!$tipo_pedido) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Tipo de pedido não informado.']);
    exit;
}

$tipo_pedido = strtolower(trim($tipo_pedido));
$tipos_validos = ['casa', 'local'];
if (!in_array($tipo_pedido, $tipos_validos, true)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Tipo de pedido inválido.']);
    exit;
}

// Recebe comentário do cliente
$comentario = filter_input(INPUT_POST, 'comentario', FILTER_SANITIZE_STRING);
$comentario = $comentario ? trim($comentario) : null;

// Recebe e valida IDs dos itens
$ids_itens_str = filter_input(INPUT_POST, 'ids_itens', FILTER_SANITIZE_STRING);
if (!$ids_itens_str) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'IDs dos itens não fornecidos.']);
    exit;
}

$ids_itens_raw = array_filter(array_map('intval', explode(',', $ids_itens_str)));
if (empty($ids_itens_raw)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Nenhum ID de item válido fornecido.']);
    exit;
}

try {
    $pdo->beginTransaction();

    // Valida itens pertencem ao cliente, ainda sem pedido e com entrega correta
    $placeholders = implode(',', array_fill(0, count($ids_itens_raw), '?'));
    $paramsCheck = array_merge([$id_cliente, $tipo_pedido], $ids_itens_raw);

    $stmtCheck = $pdo->prepare("
        SELECT id_item_pedido, id_produto, quantidade 
        FROM itens_pedido 
        WHERE id_cliente = ? 
          AND id_pedido IS NULL 
          AND entrega = ? 
          AND id_item_pedido IN ($placeholders)
    ");
    $stmtCheck->execute($paramsCheck);
    $itens_validos = $stmtCheck->fetchAll(PDO::FETCH_ASSOC);

    if (empty($itens_validos)) {
        $pdo->rollBack();
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Nenhum item válido encontrado para processar o pedido.']);
        exit;
    }

    // Pega os ids dos itens válidos para atualizar depois
    $ids_itens_validos = array_column($itens_validos, 'id_item_pedido');

    // Consulta preços dos produtos para calcular total
    $ids_produtos = array_column($itens_validos, 'id_produto');
    $ids_produtos_placeholders = implode(',', array_fill(0, count($ids_produtos), '?'));

    $stmtProdutos = $pdo->prepare("
        SELECT id_produto, preco 
        FROM produtos 
        WHERE id_produto IN ($ids_produtos_placeholders)
    ");
    $stmtProdutos->execute($ids_produtos);
    $produtos = $stmtProdutos->fetchAll(PDO::FETCH_KEY_PAIR);

    $valor_total = 0.0;
    foreach ($itens_validos as $item) {
        $id_produto = $item['id_produto'];
        $quantidade = (int)$item['quantidade'];
        if (!isset($produtos[$id_produto])) {
            $pdo->rollBack();
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Produto não encontrado.']);
            exit;
        }
        $valor_total += $produtos[$id_produto] * $quantidade;
    }

    if ($valor_total <= 0) {
        $pdo->rollBack();
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Valor total inválido.']);
        exit;
    }

    // Insere pedido
    $status_pedido = 'Recebido';
    $stmtInsertPedido = $pdo->prepare("
        INSERT INTO pedidos (id_cliente, nome_cliente, data_pedido, tipo_pedido, status_pedido, valor_total, comentario)
        VALUES (:id_cliente, :nome_cliente, NOW(), :tipo_pedido, :status_pedido, :valor_total, :comentario)
    ");
    $stmtInsertPedido->execute([
        ':id_cliente' => $id_cliente,
        ':nome_cliente' => $nome_cliente,
        ':tipo_pedido' => $tipo_pedido,
        ':status_pedido' => $status_pedido,
        ':valor_total' => $valor_total,
        ':comentario' => $comentario
    ]);
    $id_pedido = $pdo->lastInsertId();

    // Se entrega em casa, valida e insere endereço
    if ($tipo_pedido === 'casa') {
        $rua = filter_input(INPUT_POST, 'rua', FILTER_SANITIZE_STRING);
        $numero = filter_input(INPUT_POST, 'numero', FILTER_SANITIZE_STRING);
        $bairro = filter_input(INPUT_POST, 'setor', FILTER_SANITIZE_STRING);
        $cep = filter_input(INPUT_POST, 'cep', FILTER_SANITIZE_STRING);
        $complemento = filter_input(INPUT_POST, 'complemento', FILTER_SANITIZE_STRING);
        $cidade = filter_input(INPUT_POST, 'cidade', FILTER_SANITIZE_STRING) ?: 'Posse';

        if (!$rua || !$numero || !$bairro || !$cep) {
            $pdo->rollBack();
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Campos obrigatórios do endereço faltando (rua, número, bairro, cep).']);
            exit;
        }

        $stmtEndereco = $pdo->prepare("
            INSERT INTO enderecos (rua, numero, setor, cep, complemento, id_pedido, cidade) 
            VALUES (:rua, :numero, :bairro, :cep, :complemento, :id_pedido, :cidade)
        ");
        $stmtEndereco->execute([
            ':rua' => $rua,
            ':numero' => $numero,
            ':bairro' => $bairro,
            ':cep' => $cep,
            ':complemento' => $complemento,
            ':id_pedido' => $id_pedido,
            ':cidade' => $cidade
        ]);
    }

    // Atualiza os itens vinculando ao pedido
    $placeholdersUpdate = implode(',', array_fill(0, count($ids_itens_validos), '?'));
    $paramsUpdate = array_merge([$id_pedido, $id_cliente, $tipo_pedido], $ids_itens_validos);

    $stmtUpdate = $pdo->prepare("
        UPDATE itens_pedido
        SET id_pedido = ?
        WHERE id_cliente = ? 
          AND id_pedido IS NULL 
          AND entrega = ? 
          AND id_item_pedido IN ($placeholdersUpdate)
    ");
    $stmtUpdate->execute($paramsUpdate);

    if ($stmtUpdate->rowCount() === 0) {
        $pdo->rollBack();
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Nenhum item encontrado para associar ao pedido.']);
        exit;
    }

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Pedido finalizado com sucesso!',
        'redirect' => '../Pages/Pedidos.php'
    ]);
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Erro ao finalizar pedido. Tente novamente mais tarde.']);
    exit;
}
