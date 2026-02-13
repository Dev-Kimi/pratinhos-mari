<?php
include 'conexao.php';

$nome = $_POST['nome'];
$endereco = $_POST['endereco'];
$pagamento = $_POST['pagamento'];
$total = $_POST['total'];

$conn->query("INSERT INTO pedidos 
(nome_cliente,endereco,pagamento,total)
VALUES ('$nome','$endereco','$pagamento','$total')");

echo "Pedido realizado com sucesso!";
