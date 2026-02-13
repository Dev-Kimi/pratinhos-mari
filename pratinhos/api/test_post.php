<?php
$payload = [
    'usuario' => [
        'nome' => 'Teste',
        'whatsapp' => '85999999999'
    ],
    'itens' => [
        ['id' => 1, 'nome' => 'Burger Clássico', 'preco' => 18.90, 'qty' => 1]
    ],
    'total' => 18.90,
    'metodo' => 'entrega',
    'endereco' => 'Rua Exemplo, Bairro Exemplo',
    'pagamento' => 'Pix'
];
$opts = [
    'http' => [
        'method' => 'POST',
        'header' => "Content-Type: application/json\r\n",
        'content' => json_encode($payload)
    ]
];
$ctx = stream_context_create($opts);
$resp = file_get_contents('http://127.0.0.1:8000/api/finalizar_pedido.php', false, $ctx);
header('Content-Type: application/json');
echo $resp;
