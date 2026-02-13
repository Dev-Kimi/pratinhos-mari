<?php
require_once "../api/conexao.php";

// AÇÃO DO MOTOBOY
if (isset($_POST['acao'])) {
    $id = (int)$_POST['pedido_id'];
    $acao = $_POST['acao'];

    if ($acao == 'entregar') {
        $conn->query("UPDATE pedidos SET status='Entregue', motivo_recusa=NULL WHERE id=$id");
    } elseif ($acao == 'falhar') {
        $motivo = $_POST['motivo'] ?? 'Não informado';
        $stmt = $conn->prepare("UPDATE pedidos SET status='Não Entregue', motivo_recusa=? WHERE id=?");
        $stmt->bind_param("si", $motivo, $id);
        $stmt->execute();
    }
    header("Location: motoboy.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Motoboy</title>
    <style>
        body { font-family: sans-serif; background: #eee; margin: 0; padding: 10px; }
        .card { background: #fff; padding: 20px; margin-bottom: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); border-left: 5px solid #f39c12; }
        .btn { width: 100%; padding: 15px; margin-top: 10px; border: none; border-radius: 5px; font-weight: bold; cursor: pointer; color: white; font-size: 1rem; }
        .btn-ok { background: #27ae60; }
        .btn-fail { background: #c0392b; }
        .area-falha { display: none; margin-top: 10px; }
        input[type="text"] { width: 100%; padding: 10px; box-sizing: border-box; margin-bottom: 5px; border: 1px solid #ccc; }
    </style>
</head>
<body>
    <h2 style="text-align:center">Entregas Pendentes</h2>
    
    <?php
    // CONSULTA REVISADA: Verifique se os nomes das colunas 'usuario_id', 'status' e 'endereco' estão corretos no seu banco
    $sql = "SELECT p.*, u.nome, u.endereco 
            FROM pedidos p 
            INNER JOIN usuarios u ON p.usuario_id = u.id 
            WHERE p.status = 'Saiu para entrega'";
            
    $res = $conn->query($sql);
    
    // Se a consulta falhar, este bloco vai te avisar o motivo real
    if (!$res) {
        die("<div style='color:red; background:white; padding:10px;'>Erro no Banco de Dados: " . $conn->error . "</div>");
    }
    
    if ($res->num_rows > 0):
        while($p = $res->fetch_assoc()):
    ?>
        <div class="card">
            <h3>Pedido #<?= $p['id'] ?> - R$ <?= number_format($p['total'], 2, ',', '.') ?></h3>
            <p><strong>Cliente:</strong> <?= htmlspecialchars($p['nome']) ?></p>
            <p><strong>Endereço:</strong> <?= htmlspecialchars($p['endereco'] ?? 'Não informado') ?></p>
            
            <form method="POST">
                <input type="hidden" name="pedido_id" value="<?= $p['id'] ?>">
                <button type="submit" name="acao" value="entregar" class="btn btn-ok">ENTREGUE</button>
                <button type="button" class="btn btn-fail" onclick="document.getElementById('fail-<?= $p['id'] ?>').style.display='block'">NÃO ENTREGUE</button>
                
                <div id="fail-<?= $p['id'] ?>" class="area-falha">
                    <p>Qual o motivo?</p>
                    <input type="text" name="motivo" placeholder="Ex: Cliente ausente...">
                    <button type="submit" name="acao" value="falhar" class="btn btn-fail">CONFIRMAR FALHA</button>
                </div>
            </form>
        </div>
    <?php 
        endwhile;
    else:
    ?>
        <p style="text-align:center; color:#777">Nenhuma entrega para fazer agora.</p>
    <?php endif; ?>
</body>
</html>