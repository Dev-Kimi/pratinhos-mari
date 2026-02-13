<?php
header('Content-Type: application/json');
require_once "conexao.php";

// Busca categorias que possuem pelo menos um produto cadastrado
$sql = "SELECT c.id AS cat_id, c.nome AS cat_nome, p.* FROM categorias c
        LEFT JOIN produtos p ON c.id = p.categoria_id
        WHERE p.id IS NOT NULL
        ORDER BY c.nome ASC, p.nome ASC";

$result = $conn->query($sql);
$cardapio = [];

while($row = $result->fetch_assoc()) {
    $catNome = $row['cat_nome'];
    if (!isset($cardapio[$catNome])) {
        $cardapio[$catNome] = [];
    }
    $cardapio[$catNome][] = $row;
}

echo json_encode($cardapio);