<?php
require_once "../api/conexao.php";
$id = (int)$_GET['id'];
$p = $conn->query("SELECT p.*, u.nome FROM pedidos p LEFT JOIN usuarios u ON p.usuario_id = u.id WHERE p.id = $id")->fetch_assoc();
$itens = $conn->query("SELECT ip.*, pr.nome FROM itens_pedido ip LEFT JOIN produtos pr ON ip.produto_id = pr.id WHERE ip.pedido_id = $id");
?>
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Courier New', monospace; width: 58mm; font-size: 12px; margin: 0; padding: 5px; }
        .center { text-align: center; }
        .line { border-top: 1px dashed #000; margin: 5px 0; }
        @media print { .btn-fechar { display: none; } }
    </style>
</head>
<body onload="window.print();">
    <div class="center">
        <strong>PRATINHOS DA MARI</strong><br>
        PEDIDO #<?= $p['id'] ?><br>
        <?= date('d/m/Y H:i') ?>
    </div>
    <div class="line"></div>
    <strong>CLIENTE:</strong> <?= $p['nome'] ?><br>
    <strong>ENDEREÇO:</strong> <?= $p['endereco'] ?>
    <div class="line"></div>
    <strong>ITENS:</strong><br>
    <?php while($i = $itens->fetch_assoc()): ?>
        <?= $i['quantidade'] ?>x <?= $i['nome'] ?> - R$ <?= number_format($i['preco_unitario'], 2, ',', '.') ?><br>
    <?php endwhile; ?>
    <div class="line"></div>
    <strong>TOTAL: R$ <?= number_format($p['total'], 2, ',', '.') ?></strong>
    <div class="line"></div>
    <div class="center"><br>Cozinha: Bom apetite!</div>
</body>
</html>