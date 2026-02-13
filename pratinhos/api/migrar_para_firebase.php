<?php
header('Content-Type: application/json; charset=utf-8');
if (file_exists('conexao.php')) {
    require 'conexao.php';
} elseif (file_exists('../conexao.php')) {
    require '../conexao.php';
} else {
    echo json_encode(['success' => false, 'error' => 'Conexão não encontrada']);
    exit;
}
if (file_exists('firebase.php')) {
    require 'firebase.php';
} elseif (file_exists('../firebase.php')) {
    require '../firebase.php';
} else {
    echo json_encode(['success' => false, 'error' => 'Firebase util não encontrado']);
    exit;
}
$creds = fb_load_credentials();
if (!$creds) {
    echo json_encode(['success' => false, 'error' => 'Credenciais Firebase ausentes']);
    exit;
}
function iso($ts) {
    if (!$ts) return date('c');
    $t = strtotime($ts);
    if ($t === false) return date('c');
    return date('c', $t);
}
$summary = ['categorias' => 0, 'produtos' => 0, 'clientes' => 0, 'configuracoes' => 0, 'usuarios' => 0, 'pedidos' => 0, 'itens' => 0, 'admin' => 0, 'sistema_status' => 0];
if ($res = $conn->query("SELECT * FROM categorias")) {
    while ($c = $res->fetch_assoc()) {
        $fields = [
            'nome' => fb_val_string($c['nome'] ?? ''),
            'icone' => fb_val_string($c['icone'] ?? '')
        ];
        fb_firestore_create('categorias', (string)$c['id'], $fields);
        $summary['categorias']++;
        $pid = (int)$c['id'];
        if ($res2 = $conn->query("SELECT * FROM produtos WHERE categoria_id = " . $pid)) {
            while ($p = $res2->fetch_assoc()) {
                $pfields = [
                    'categoria_id' => fb_val_int($p['categoria_id'] ?? 0),
                    'nome' => fb_val_string($p['nome'] ?? ''),
                    'descricao' => fb_val_string($p['descricao'] ?? ''),
                    'preco' => fb_val_double($p['preco'] ?? 0),
                    'imagem_url' => fb_val_string($p['imagem_url'] ?? ''),
                    'ativo' => fb_val_int($p['ativo'] ?? 1)
                ];
                fb_firestore_create('categorias/' . $pid . '/produtos', (string)$p['id'], $pfields);
                $summary['produtos']++;
            }
        }
    }
}
if ($res = $conn->query("SELECT * FROM clientes")) {
    while ($row = $res->fetch_assoc()) {
        $fields = [
            'nome' => fb_val_string($row['nome'] ?? ''),
            'whatsapp' => fb_val_string($row['whatsapp'] ?? ''),
            'criado_em' => fb_val_timestamp(iso($row['criado_em'] ?? null))
        ];
        fb_firestore_create('clientes', (string)$row['id'], $fields);
        $summary['clientes']++;
    }
}
if ($res = $conn->query("SELECT * FROM configuracoes")) {
    while ($row = $res->fetch_assoc()) {
        $fields = [
            'nome_loja' => fb_val_string($row['nome_loja'] ?? ''),
            'telefone_whatsapp' => fb_val_string($row['telefone_whatsapp'] ?? ''),
            'status_aberto' => fb_val_int($row['status_aberto'] ?? 0)
        ];
        fb_firestore_create('configuracoes', (string)$row['id'], $fields);
        $summary['configuracoes']++;
    }
}
if ($res = $conn->query("SELECT * FROM usuarios")) {
    while ($row = $res->fetch_assoc()) {
        $fields = [
            'nome' => fb_val_string($row['nome'] ?? ''),
            'whatsapp' => fb_val_string($row['whatsapp'] ?? ''),
            'endereco' => fb_val_string($row['endereco'] ?? '')
        ];
        fb_firestore_create('usuarios', (string)$row['id'], $fields);
        $summary['usuarios']++;
    }
}
if ($res = $conn->query("SELECT * FROM admin")) {
    while ($row = $res->fetch_assoc()) {
        $fields = [
            'usuario' => fb_val_string($row['usuario'] ?? ''),
            'senha' => fb_val_string($row['senha'] ?? '')
        ];
        fb_firestore_create('admin', (string)$row['id'], $fields);
        $summary['admin']++;
    }
}
if ($res = $conn->query("SELECT * FROM sistema_status")) {
    while ($row = $res->fetch_assoc()) {
        $fields = [
            'esta_aberto' => fb_val_int($row['esta_aberto'] ?? 0),
            'horario_texto' => fb_val_string($row['horario_texto'] ?? '')
        ];
        fb_firestore_create('sistema_status', (string)$row['id'], $fields);
        $summary['sistema_status']++;
    }
}
if ($res = $conn->query("SELECT * FROM pedidos")) {
    while ($p = $res->fetch_assoc()) {
        $fields = [
            'usuario_id' => fb_val_int($p['usuario_id'] ?? 0),
            'nome_cliente' => fb_val_string($p['nome_cliente'] ?? ''),
            'cliente_whatsapp' => fb_val_string($p['cliente_whatsapp'] ?? ''),
            'endereco' => fb_val_string($p['endereco'] ?? ''),
            'pagamento' => fb_val_string($p['pagamento'] ?? ''),
            'status' => fb_val_string($p['status'] ?? ''),
            'total' => fb_val_double($p['total'] ?? 0),
            'criado_em' => fb_val_timestamp(iso($p['criado_em'] ?? null)),
            'motivo_recusa' => fb_val_string($p['motivo_recusa'] ?? ''),
            'impresso' => fb_val_int($p['impresso'] ?? 0)
        ];
        fb_firestore_create('pedidos', (string)$p['id'], $fields);
        $summary['pedidos']++;
        $pid = (int)$p['id'];
        $added = 0;
        if ($resItems = $conn->query("SELECT * FROM itens_pedido WHERE pedido_id = " . $pid)) {
            while ($it = $resItems->fetch_assoc()) {
                $ifields = [
                    'produto_id' => fb_val_int($it['produto_id'] ?? 0),
                    'quantidade' => fb_val_int($it['quantidade'] ?? 0),
                    'preco_unitario' => fb_val_double($it['preco_unitario'] ?? 0)
                ];
                fb_firestore_create('pedidos/' . $pid . '/itens', (string)$it['id'], $ifields);
                $summary['itens']++;
                $added++;
            }
        }
        if ($added === 0 && $resItems2 = $conn->query("SELECT * FROM pedidos_itens WHERE pedido_id = " . $pid)) {
            while ($it = $resItems2->fetch_assoc()) {
                $ifields = [
                    'produto_nome' => fb_val_string($it['produto_nome'] ?? ''),
                    'quantidade' => fb_val_int($it['quantidade'] ?? 0),
                    'preco_unitario' => fb_val_double($it['preco_unitario'] ?? 0),
                    'subtotal' => fb_val_double($it['subtotal'] ?? 0)
                ];
                fb_firestore_create('pedidos/' . $pid . '/itens', (string)$it['id'], $ifields);
                $summary['itens']++;
                $added++;
            }
        }
        if ($added === 0 && $resItems3 = $conn->query("SELECT * FROM pedido_itens WHERE pedido_id = " . $pid)) {
            while ($it = $resItems3->fetch_assoc()) {
                $ifields = [
                    'produto_id' => fb_val_int($it['produto_id'] ?? 0),
                    'produto_nome' => fb_val_string($it['produto_nome'] ?? ''),
                    'quantidade' => fb_val_int($it['quantidade'] ?? 0),
                    'preco_unitario' => fb_val_double($it['preco_unitario'] ?? 0)
                ];
                fb_firestore_create('pedidos/' . $pid . '/itens', (string)$it['id'], $ifields);
                $summary['itens']++;
            }
        }
    }
}
echo json_encode(['success' => true, 'summary' => $summary]);
