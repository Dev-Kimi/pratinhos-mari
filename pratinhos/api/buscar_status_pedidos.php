<?php
// api/buscar_status_pedidos.php
header("Content-Type: application/json; charset=utf-8");
require 'conexao.php';

$ids_str = $_GET['ids'] ?? '';

if (empty($ids_str)) {
    echo json_encode([]);
    exit;
}

// Limpeza básica para segurança (aceita apenas números e vírgulas)
$ids_str = preg_replace('/[^0-9,]/', '', $ids_str);
$ids_array = explode(',', $ids_str);

if (count($ids_array) === 0) {
    echo json_encode([]);
    exit;
}

// Cria os placeholders (?,?,?) para a query
$placeholders = implode(',', array_fill(0, count($ids_array), '?'));

try {
    $sql = "SELECT id, status FROM pedidos WHERE id IN ($placeholders)";
    $stmt = $conn->prepare($sql);
    
    // Vincula os IDs dinamicamente
    $types = str_repeat('i', count($ids_array));
    $stmt->bind_param($types, ...$ids_array);
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $status_list = [];
    while ($row = $result->fetch_assoc()) {
        $status_list[] = $row;
    }

    echo json_encode($status_list);

} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}