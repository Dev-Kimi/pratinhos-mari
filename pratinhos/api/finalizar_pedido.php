<?php
// api/finalizar_pedido.php
ini_set('display_errors', 1);
error_reporting(E_ALL);
header("Content-Type: application/json; charset=utf-8");

// Conexão
if (file_exists('conexao.php')) {
    require 'conexao.php';
} elseif (file_exists('../conexao.php')) {
    require '../conexao.php';
} else {
    die(json_encode(["success" => false, "error" => "Arquivo conexao.php não encontrado!"]));
}
$hasFirebase = false;
if (file_exists(__DIR__ . '/firebase.php')) {
    require __DIR__ . '/firebase.php';
    $hasFirebase = fb_load_credentials() ? true : false;
}

try {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if (!$data) throw new Exception('JSON inválido ou vazio.');

    $conn->begin_transaction();

    $nome      = $data['usuario']['nome'] ?? 'Cliente';
    $whatsapp  = $data['usuario']['whatsapp'] ?? '';
    $endereco  = $data['endereco'] ?? ''; 
    $pagamento = $data['pagamento'] ?? '';
    $total     = $data['total'] ?? 0;

    // --- CORREÇÃO AQUI ---
    // Colunas ajustadas para: nome_cliente, cliente_whatsapp, endereco, pagamento, total, status
    // Note que mudei 'forma_pagamento' para 'pagamento'
    $sql_pedido = "INSERT INTO pedidos (nome_cliente, cliente_whatsapp, endereco, pagamento, total, status) VALUES (?, ?, ?, ?, ?, 'Aguardando')";
    
    $stmt = $conn->prepare($sql_pedido);
    if (!$stmt) throw new Exception("Erro no Prepare Pedido: " . $conn->error);

    $stmt->bind_param("ssssd", $nome, $whatsapp, $endereco, $pagamento, $total);
    
    if (!$stmt->execute()) throw new Exception("Erro ao salvar Pedido: " . $stmt->error);

    $pedido_id = $conn->insert_id;

    // Itens do Pedido
    $stmtItem = $conn->prepare("INSERT INTO itens_pedido (pedido_id, produto_id, quantidade, preco_unitario) VALUES (?, ?, ?, ?)");
    if (!$stmtItem) throw new Exception("Erro no Prepare Itens: " . $conn->error);

    foreach ($data['itens'] as $item) {
        $p_id  = $item['id'];
        $qty   = $item['qty'];   
        $preco = $item['preco']; 

        $stmtItem->bind_param("iiid", $pedido_id, $p_id, $qty, $preco);
        if (!$stmtItem->execute()) throw new Exception("Erro ao salvar Item: " . $stmtItem->error);
    }

    $conn->commit();
    if ($hasFirebase) {
        $fields = [
            'nome_cliente' => fb_val_string($nome),
            'cliente_whatsapp' => fb_val_string($whatsapp),
            'endereco' => fb_val_string($endereco),
            'pagamento' => fb_val_string($pagamento),
            'total' => fb_val_double($total),
            'status' => fb_val_string('Aguardando'),
            'data_hora' => fb_val_timestamp(date('c'))
        ];
        $doc = fb_firestore_create('pedidos', (string)$pedido_id, $fields);
        if ($doc && isset($data['itens'])) {
            $docName = $doc['name'];
            $path = substr($docName, strpos($docName, 'documents/') + 10);
            foreach ($data['itens'] as $item) {
                $ifields = [
                    'produto_id' => fb_val_int($item['id']),
                    'quantidade' => fb_val_int($item['qty']),
                    'preco_unitario' => fb_val_double($item['preco']),
                    'nome' => fb_val_string($item['nome'] ?? '')
                ];
                fb_firestore_create_sub($path, 'itens', $ifields);
            }
        }
    }
    echo json_encode(["success" => true, "id_pedido" => $pedido_id]);

} catch (Exception $e) {
    if (isset($conn)) $conn->rollback();
    // Log de erro
    file_put_contents('erro_log.txt', date('d/m/Y H:i:s') . " - " . $e->getMessage() . "\n", FILE_APPEND);
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}
?>
