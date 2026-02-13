<?php
include "conexao.php";
header('Content-Type: application/json');
$d = json_decode(file_get_contents('php://input'), true);

$stmt = $conn->prepare("SELECT id, nome, whatsapp FROM usuarios WHERE whatsapp = ?");
$stmt->bind_param("s", $d['whatsapp']);
$stmt->execute();
$res = $stmt->get_result();

if ($u = $res->fetch_assoc()) {
    echo json_encode($u);
} else {
    $ins = $conn->prepare("INSERT INTO usuarios (nome, whatsapp) VALUES (?, ?)");
    $ins->bind_param("ss", $d['nome'], $d['whatsapp']);
    $ins->execute();
    echo json_encode(["id" => $ins->insert_id, "nome" => $d['nome'], "whatsapp" => $d['whatsapp']]);
}