<?php
require_once "db.php";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Log para debug
file_put_contents("log_debug.txt", date('Y-m-d H:i:s') . " POST: " . print_r($_POST, true) . "\n", FILE_APPEND);

$idPedido = $_POST['id_pedido'] ?? null;
$nota = $_POST['nota'] ?? null;

header('Content-Type: application/json');

// Verifica apenas se o ID do pedido foi enviado
if (!$idPedido) {
  echo json_encode(["success" => false, "message" => "ID do pedido não informado."]);
  exit;
}

try {
  $stmt = $pdo->prepare("UPDATE pedidos SET nota = :nota WHERE id_pedido = :id");

  // Se nota for numérica, faz bind como inteiro, senão, define como null
  if ($nota !== null && is_numeric($nota)) {
    $stmt->bindValue(":nota", intval($nota), PDO::PARAM_INT);
  } else {
    $stmt->bindValue(":nota", null, PDO::PARAM_NULL);
  }

  $stmt->bindValue(":id", intval($idPedido), PDO::PARAM_INT);
  $stmt->execute();

  echo json_encode(["success" => true]);
} catch (PDOException $e) {
  echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>
