<?php
// api/status_pedidos.php
header("Content-Type: application/json; charset=utf-8");

// Tenta localizar a conexão
if (file_exists('conexao.php')) {
    require 'conexao.php';
} elseif (file_exists('../conexao.php')) {
    require '../conexao.php';
} else {
    die(json_encode(["error" => "Conexão não encontrada"]));
}

// Pega o whatsapp via GET (ex: status_pedidos.php?whatsapp=8599...)
$whatsapp = $_GET['whatsapp'] ?? '';

if (empty($whatsapp)) {
    echo json_encode([]);
    exit;
}

try {
    // Buscamos os últimos 10 pedidos desse cliente
    // Importante: usei 'nome_cliente' e 'pagamento' que são os nomes que confirmamos antes
    $sql = "SELECT id, total, status, pagamento, DATE_FORMAT(data_hora, '%d/%m %H:%i') as data 
            FROM pedidos 
            WHERE cliente_whatsapp = ? 
            ORDER BY id DESC 
            LIMIT 10";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $whatsapp);
    $stmt->execute();
    $result = $stmt->get_result();

    $pedidos = [];
    while ($row = $result->fetch_assoc()) {
        $pedidos[] = $row;
    }

    echo json_encode($pedidos);

} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}