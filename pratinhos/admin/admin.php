<?php
// 1. CONEXÃO E CONFIGURAÇÕES
require_once "../api/conexao.php";

// =======================================================
// 2. LÓGICA DE PROCESSAMENTO (BACKEND)
// =======================================================

if (isset($_POST['atualizar_status'])) {
    $status = isset($_POST['aberto']) ? 1 : 0;
    $horario = mysqli_real_escape_string($conn, $_POST['horario_texto']);
    
    $conn->query("UPDATE sistema_status SET esta_aberto = $status, horario_texto = '$horario' WHERE id = 1");
    echo "<script>alert('Status atualizado!');</script>";
}

// Busca o status atual para mostrar no formulário
$status_query = $conn->query("SELECT * FROM sistema_status WHERE id = 1");
$status_data = $status_query->fetch_assoc();


// A. SALVAR OU EDITAR PRODUTO
if (isset($_POST['salvar_produto'])) {
    $id = $_POST['prod_id'] ?? '';
    $nome = mysqli_real_escape_string($conn, $_POST['nome']);
    $descricao = mysqli_real_escape_string($conn, $_POST['descricao']);
    $preco = $_POST['preco'] ?? 0;
    $categoria = $_POST['categoria'] ?? null;
    
    $img_sql = "";
    $img_insert = "padrao.jpg"; 

    if (!empty($_FILES['img']['name'])) {
        $img_name = time() . "_" . $_FILES['img']['name'];
        $diretorio = "../assets/img/"; 
        
        if (!is_dir($diretorio)) { mkdir($diretorio, 0777, true); }

        if (move_uploaded_file($_FILES['img']['tmp_name'], $diretorio . $img_name)) {
            // AJUSTADO: Nome da coluna é imagem_url
            $img_sql = ", imagem_url = '$img_name'"; 
            $img_insert = $img_name;
        }
    }

    if (!empty($id)) {
        // UPDATE - Ajustado para imagem_url
        $id = (int)$id;
        $sql = "UPDATE produtos SET nome='$nome', descricao='$descricao', preco='$preco', categoria_id='$categoria' $img_sql WHERE id=$id";
        $conn->query($sql);
    } else {
        // INSERT - Ajustado para imagem_url
        $sql = "INSERT INTO produtos (nome, descricao, preco, imagem_url, categoria_id) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            die("Erro no SQL: " . $conn->error);
        }

        // s = nome, s = descricao, d = preco, s = imagem_url, i = categoria_id
        $stmt->bind_param("ssdsi", $nome, $descricao, $preco, $img_insert, $categoria);
        $stmt->execute();
    }
    header("Location: admin.php?aba=cadastrar");
    exit;
}

// B. SALVAR OU EDITAR CATEGORIA
if (isset($_POST['salvar_categoria'])) {
    $id = $_POST['cat_id'] ?? '';
    $nome_cat = mysqli_real_escape_string($conn, $_POST['nome_categoria']);
    
    if (!empty($nome_cat)) {
        if (!empty($id)) {
            $id = (int)$id;
            $conn->query("UPDATE categorias SET nome='$nome_cat' WHERE id=$id");
        } else {
            $conn->query("INSERT INTO categorias (nome) VALUES ('$nome_cat')");
        }
        header("Location: admin.php?aba=categorias_gestao");
        exit;
    }
}

// C. ATUALIZAR STATUS DO PEDIDO
if (isset($_POST['status']) && isset($_POST['pedido_id'])) {
    $id_ped = (int)$_POST['pedido_id'];
    $novo_status = mysqli_real_escape_string($conn, $_POST['status']);
    
    if ($novo_status != 'Não Entregue') {
        $sql = "UPDATE pedidos SET status='$novo_status', motivo_recusa=NULL WHERE id=$id_ped";
    } else {
        $sql = "UPDATE pedidos SET status='$novo_status' WHERE id=$id_ped";
    }
    
    if($conn->query($sql)){
        header("Location: admin.php?aba=pedidos");
        exit;
    }
}

// D. EXCLUIR (PRODUTO OU CATEGORIA)
if (isset($_GET['excluir'])) {
    $tipo = $_GET['tipo'];
    $id = (int)$_GET['id'];
    if ($tipo == 'prod') {
        $conn->query("DELETE FROM produtos WHERE id = $id");
        $aba = 'cadastrar';
    }
    if ($tipo == 'cat') {
        // Opcional: Bloquear exclusão se tiver produtos vinculados
        $conn->query("UPDATE produtos SET categoria_id = NULL WHERE categoria_id = $id");
        $conn->query("DELETE FROM categorias WHERE id = $id");
        $aba = 'categorias_gestao';
    }
    
    header("Location: admin.php?aba=$aba");
    exit;
}

// E. DADOS GERAIS
$total_produtos = $conn->query("SELECT COUNT(*) as t FROM produtos")->fetch_assoc()['t'];
$total_pedidos = $conn->query("SELECT COUNT(*) as t FROM pedidos")->fetch_assoc()['t'] ?? 0;
$faturamento_res = $conn->query("SELECT SUM(total) as s FROM pedidos WHERE status = 'Entregue'")->fetch_assoc();
$faturamento = $faturamento_res['s'] ?? 0;

// Busca categorias para o select de produtos
$cats_result = $conn->query("SELECT * FROM categorias ORDER BY nome ASC");
$categorias_list = [];
while($c = $cats_result->fetch_assoc()) {
    $categorias_list[] = $c;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Pratinhos da Mari</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --primary: #f39c12; --dark: #1a1a1a; --bg: #f0f2f5; }
        body { margin: 0; font-family: 'Segoe UI', sans-serif; background: var(--bg); display: flex; }
        
        /* Sidebar */
        .sidebar { width: 250px; height: 100vh; background: var(--dark); color: white; position: fixed; }
        .sidebar-header { padding: 25px; text-align: center; background: rgba(0,0,0,0.2); font-weight: bold; font-size: 1.2rem; }
        .sidebar-menu { list-style: none; padding: 0; margin: 0; }
        .sidebar-menu li { padding: 15px 20px; cursor: pointer; border-bottom: 1px solid rgba(255,255,255,0.05); transition: 0.3s; }
        .sidebar-menu li:hover, .sidebar-menu li.active { background: var(--primary); color: #000; }
        .sidebar-menu li i { margin-right: 10px; width: 20px; text-align: center; }

        /* Main Content */
        .main { margin-left: 250px; width: calc(100% - 250px); padding: 30px; box-sizing: border-box; }
        .tab-content { display: none; animation: fadeIn 0.3s; }
        .tab-content.active { display: block; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

        /* Cards & Forms */
        .card { background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 25px; }
        .grid-dash { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; }
        .card-mini { background: white; padding: 20px; border-radius: 8px; border-left: 5px solid var(--primary); box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .card-mini h3 { margin: 0; font-size: 1.8rem; color: #333; }
        .card-mini p { margin: 5px 0 0; color: #666; }

        /* Tables */
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
        th { background-color: #f8f9fa; color: #333; font-weight: 600; }
        tr:hover { background-color: #f9f9f9; }

        /* Form Elements */
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 500; color: #555; }
        .form-control { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        .select-status { padding: 6px; border-radius: 4px; border: 1px solid #ddd; outline: none; }

        /* Buttons */
        .btn-acao { padding: 8px 12px; text-decoration: none; color: white; border-radius: 4px; font-size: 0.85rem; cursor: pointer; border:none; transition: 0.2s; display: inline-block; margin-right: 5px; }
        .btn-edit { background-color: #3498db; }
        .btn-del { background-color: #e74c3c; }
        .btn-ver { background-color: var(--primary); color: #000; font-weight: bold; }
        .btn-save { background-color: #27ae60; width: 100%; padding: 12px; font-size: 1rem; margin-top: 10px; }
        .btn-acao:hover { opacity: 0.8; }
        
        /* Modal */
        .modal { display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); }
        .modal-content { background: #fff; margin: 5% auto; padding: 25px; border-radius: 12px; width: 500px; position: relative; animation: slideIn 0.3s ease; }
        @keyframes slideIn { from { transform: translateY(-50px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        .close { float: right; font-size: 24px; cursor: pointer; color: #666; }
        .box-endereco { background: #fff9f0; border: 1px dashed var(--primary); padding: 15px; border-radius: 8px; margin: 15px 0; font-size: 0.9rem; color: #555; }
        
        #lista-itens-modal { list-style: none; padding: 0; margin-top: 15px; }
        #lista-itens-modal li { padding: 10px 0; border-bottom: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center; }
        .item-qtd { background: #eee; padding: 2px 8px; border-radius: 12px; font-size: 0.8rem; font-weight: bold; margin-right: 8px; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-header">MARI ADMIN</div>
    <ul class="sidebar-menu">
        <li class="nav-link active" id="btn-dash" onclick="showTab('dash')"><i class="fas fa-chart-line"></i> Dashboard</li>
        <li class="nav-link" id="btn-pedidos" onclick="showTab('pedidos')"><i class="fas fa-shopping-cart"></i> Pedidos</li>
        <li class="nav-link" id="btn-categorias_gestao" onclick="showTab('categorias_gestao')"><i class="fas fa-tags"></i> Categorias</li>
        <li class="nav-link" id="btn-cadastrar" onclick="showTab('cadastrar')"><i class="fas fa-utensils"></i> Produtos</li>
    </ul>
    <div class="status-loja-admin">
    <h4 style="margin-top: 0; color: #f39c12; font-size: 16px;">
        <i class="fas fa-store"></i> Controle da Loja
    </h4>
    
    <form method="POST">
        <div class="switch-container">
            <span>Status:</span>
            <label class="switch">
                <input type="checkbox" name="aberto" onchange="this.form.submit()" <?= $status_data['esta_aberto'] ? 'checked' : '' ?>>
                <span class="slider"></span>
            </label>
            <strong style="color: <?= $status_data['esta_aberto'] ? '#2ecc71' : '#e74c3c' ?>;">
                <?= $status_data['esta_aberto'] ? 'ABERTO' : 'FECHADO' ?>
            </strong>
        </div>

        <div style="margin-top: 15px;">
            <small style="color: #a0a0a0; display: block; margin-bottom: 5px;">Horário de hoje:</small>
            <input type="text" name="horario_texto" value="<?= $status_data['horario_texto'] ?>" 
                   style="width: 100%; background: #252525; color: white; border: 1px solid #333; padding: 8px; border-radius: 5px; font-size: 13px;">
        </div>
        
        <input type="hidden" name="atualizar_status" value="1">
        <button type="submit" class="btn-salvar-status">Atualizar Horário</button>
    </form>
</div>
</div>



<style>
/* Posicionamento do Box */
.status-loja-admin {
    position: fixed;
    bottom: 30px; /* Distância do fundo */
    right: 30px;  /* Distância da direita */
    width: 280px;
    background: #1a1a1a;
    border: 1px solid #2a2a2a;
    border-radius: 15px;
    padding: 20px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.5);
    z-index: 9999;
    border-top: 4px solid #f39c12; /* Detalhe colorido no topo */
}

/* Estilo do Switch (Botão liga/desliga) */
.switch-container {
    display: flex;
    align-items: center;
    gap: 10px;
}

.switch {
    position: relative;
    display: inline-block;
    width: 46px;
    height: 24px;
}

.switch input { opacity: 0; width: 0; height: 0; }

.slider {
    position: absolute;
    cursor: pointer;
    top: 0; left: 0; right: 0; bottom: 0;
    background-color: #333;
    transition: .4s;
    border-radius: 34px;
}

.slider:before {
    position: absolute;
    content: "";
    height: 18px; width: 18px;
    left: 3px; bottom: 3px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
}

input:checked + .slider { background-color: #2ecc71; }
input:checked + .slider:before { transform: translateX(22px); }

.btn-salvar-status {
    width: 100%;
    margin-top: 10px;
    background: #f39c12;
    color: black;
    border: none;
    padding: 8px;
    border-radius: 5px;
    font-weight: bold;
    cursor: pointer;
    font-size: 12px;
}

.btn-salvar-status:hover { background: #d68910; }
</style>

<div class="main">

    <div id="dash" class="tab-content active">
        <h2>Dashboard</h2>
        <div class="grid-dash">
            <div class="card-mini"><h3><?= $total_produtos ?></h3><p>Produtos no Menu</p></div>
            <div class="card-mini"><h3><?= $total_pedidos ?></h3><p>Pedidos Totais</p></div>
            <div class="card-mini"><h3>R$ <?= number_format($faturamento, 2, ',', '.') ?></h3><p>Vendas Concluídas</p></div>
        </div>
    </div>

    <div id="pedidos" class="tab-content">
        <h2>Gerenciamento de Pedidos</h2>
        <div class="card">
            <?php if ($total_pedidos > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th> <th>Cliente</th> <th>Número</th> <th>Total</th> <th>Status</th> <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql_pedidos = "SELECT p.*, u.nome AS cliente_nome FROM pedidos p LEFT JOIN usuarios u ON p.usuario_id = u.id ORDER BY p.id DESC";
                    $pedidos = $conn->query($sql_pedidos);
                    while($p = $pedidos->fetch_assoc()): 
                        $id_atual = (int)$p['id'];
                        // Busca Itens
                        $itens_q = $conn->query("SELECT ip.quantidade, pr.nome, ip.preco_unitario FROM itens_pedido ip LEFT JOIN produtos pr ON ip.produto_id = pr.id WHERE ip.pedido_id = $id_atual");
                        $itens_array = [];
                        while($i = $itens_q->fetch_assoc()) { 
                            $itens_array[] = ['quantidade' => $i['quantidade'], 'nome' => $i['nome'] ?? '(Removido)', 'preco_unitario' => (float)$i['preco_unitario']];
                        }
                        $json_itens = htmlspecialchars(json_encode($itens_array), ENT_QUOTES, 'UTF-8');
                        $endereco_esc_js = htmlspecialchars($p['endereco'] ?? 'Não informado', ENT_QUOTES, 'UTF-8');
                    ?>
                    <tr>
                        <td>#<?= $p['id'] ?></td>
                        <td><?= htmlspecialchars($p['nome_cliente'] ?? 'Cliente') ?></td>

                        <td><?= htmlspecialchars($p['cliente_whatsapp'] ?? 'Cliente') ?></td>
                        
                        <td><strong>R$ <?= number_format($p['total'], 2, ',', '.') ?></strong></td>
                        <td>
                            <form method="POST" style="margin:0;">
                                <input type="hidden" name="pedido_id" value="<?= $p['id'] ?>">
                                <select name="status" class="select-status" onchange="this.form.submit()">
                                    <option value="Pendente" <?= $p['status']=='Pendente'?'selected':'' ?>>Pendente</option>
                                    <option value="Em Produção" <?= $p['status']=='Em Produção'?'selected':'' ?>>Em Produção</option>
                                    <option value="Saiu para entrega" <?= $p['status']=='Saiu para entrega'?'selected':'' ?>>Saiu para entrega</option>
                                    <option value="Entregue" <?= $p['status']=='Entregue'?'selected':'' ?>>Entregue</option>
                                    <option value="Não Entregue" <?= $p['status']=='Não Entregue'?'selected':'' ?>>Não Entregue</option>
                                </select>
                            </form>
                        </td>
                        <td>
                            <button class="btn-acao btn-ver" onclick="abrirModal('<?= $p['id'] ?>', '<?= htmlspecialchars($p['cliente_nome']) ?>', '<?= $json_itens ?>', '<?= $endereco_esc_js ?>')">
                                <i class="fas fa-list"></i> Ver
                            </button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <?php else: ?>
                <p>Nenhum pedido registrado.</p>
            <?php endif; ?>
        </div>
    </div>

    <div id="categorias_gestao" class="tab-content">
        <h2>Gerenciar Categorias</h2>
        
        <div class="card">
            <h3>Cadastrar / Editar Categoria</h3>
            <form method="POST">
                <input type="hidden" name="cat_id" id="cat_id">
                <div class="form-group">
                    <label>Nome da Categoria</label>
                    <input type="text" name="nome_categoria" id="nome_categoria" class="form-control" required placeholder="Ex: Bebidas, Lanches...">
                </div>
                <div style="display:flex; gap:10px;">
                    <button type="submit" name="salvar_categoria" class="btn-acao btn-save">Salvar Categoria</button>
                    <button type="button" onclick="limparFormCat()" class="btn-acao" style="background:#95a5a6; margin-top:10px;">Limpar</button>
                </div>
            </form>
        </div>

        <div class="card">
            <h3>Categorias Existentes</h3>
            <table>
                <thead><tr><th>ID</th><th>Nome</th><th>Ações</th></tr></thead>
                <tbody>
                    <?php 
                    // Reinicia ponteiro das categorias pois já foi usado no PHP lá em cima
                    $cats_listagem = $conn->query("SELECT * FROM categorias ORDER BY id DESC");
                    while($cat = $cats_listagem->fetch_assoc()): 
                    ?>
                    <tr>
                        <td><?= $cat['id'] ?></td>
                        <td><?= htmlspecialchars($cat['nome']) ?></td>
                        <td>
                            <button class="btn-acao btn-edit" onclick="editarCategoria('<?= $cat['id'] ?>', '<?= htmlspecialchars($cat['nome'], ENT_QUOTES) ?>')">
                                <i class="fas fa-edit"></i>
                            </button>
                            <a href="?excluir=1&tipo=cat&id=<?= $cat['id'] ?>" class="btn-acao btn-del" onclick="return confirm('Tem certeza? Isso pode afetar produtos vinculados.')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div id="cadastrar" class="tab-content">
        <h2>Gerenciar Produtos</h2>

        <div class="card">
            <h3 id="titulo-prod">Novo Produto</h3>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="prod_id" id="prod_id">
                
                <div class="grid-dash" style="grid-template-columns: 2fr 1fr;">
                    <div class="form-group">
                        <label>Nome do Produto</label>
                        <input type="text" name="nome" id="prod_nome" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Preço (R$)</label>
                        <input type="number" step="0.01" name="preco" id="prod_preco" class="form-control" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Categoria</label>
                    <select name="categoria" id="prod_categoria" class="form-control" required>
                        <option value="">Selecione...</option>
                        <?php foreach($categorias_list as $c): ?>
                            <option value="<?= $c['id'] ?>"><?= $c['nome'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Descrição</label>
                    <textarea name="descricao" id="prod_descricao" class="form-control" rows="3"></textarea>
                </div>

                <div class="form-group">
                    <label>Imagem do Produto</label>
                    <input type="file" name="img" class="form-control">
                    <small style="color:#666">Deixe em branco para manter a atual (ao editar)</small>
                </div>

                <div style="display:flex; gap:10px;">
                    <button type="submit" name="salvar_produto" class="btn-acao btn-save">Salvar Produto</button>
                    <button type="button" onclick="limparFormProd()" class="btn-acao" style="background:#95a5a6; margin-top:10px;">Limpar / Novo</button>
                </div>
            </form>
        </div>

        <div class="card">
            <h3>Cardápio Atual</h3>
            <table>
                <thead><tr><th>Img</th><th>Nome</th><th>Categoria</th><th>Preço</th><th>Ações</th></tr></thead>
                <tbody>
                    <?php
                    $sql_prod = "SELECT p.*, c.nome as cat_nome FROM produtos p LEFT JOIN categorias c ON p.categoria_id = c.id ORDER BY p.id DESC";
                    $produtos = $conn->query($sql_prod);
                    while($prod = $produtos->fetch_assoc()):
                    ?>
                    <tr>
                        <td>
                            <?php if(!empty($prod['imagem_url'])): ?>
                                <img src="../assets/img/<?= $prod['imagem_url'] ?>" width="40" height="40" style="border-radius:4px; object-fit:cover;">
                            <?php else: ?>
                                <i class="fas fa-image" style="color:#ccc"></i>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($prod['nome']) ?></td>
                        <td><?= htmlspecialchars($prod['cat_nome'] ?? 'Sem Categoria') ?></td>
                        <td>R$ <?= number_format($prod['preco'], 2, ',', '.') ?></td>
                        <td>
                            <button class="btn-acao btn-edit" onclick="editarProduto(
                                '<?= $prod['id'] ?>', 
                                '<?= htmlspecialchars($prod['nome'], ENT_QUOTES) ?>',
                                '<?= htmlspecialchars($prod['descricao'] ?? '', ENT_QUOTES) ?>',
                                '<?= $prod['preco'] ?>',
                                '<?= $prod['categoria_id'] ?>'
                            )"><i class="fas fa-edit"></i></button>
                            
                            <a href="?excluir=1&tipo=prod&id=<?= $prod['id'] ?>" class="btn-acao btn-del" onclick="return confirm('Excluir este produto?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<div id="modalItens" class="modal">
    <div class="modal-content">
        <span class="close" onclick="fecharModal()">&times;</span>
        <h3 style="margin-top:0;">Pedido #<span id="modalIdPedido"></span></h3>
        <p style="color:#666; margin-bottom:5px;"><i class="fas fa-user"></i> <span id="modalCliente"></span></p>
        
        <div class="box-endereco">
            <strong><i class="fas fa-map-marker-alt"></i> Endereço para entrega:</strong><br>
            <span id="modalEndereco"></span>
        </div>

        <hr style="border:0; border-top:1px solid #eee;">
        <ul id="lista-itens-modal"></ul>
    </div>
</div>

<script>
    // SISTEMA DE ABAS
    function showTab(id) {
        document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
        
        const tab = document.getElementById(id);
        const btn = document.getElementById('btn-' + id);
        
        if(tab) tab.classList.add('active');
        if(btn) btn.classList.add('active');
        
        // Salva estado apenas se não for carregamento inicial via PHP
        const urlParams = new URLSearchParams(window.location.search);
        if(!urlParams.has('aba')) {
            localStorage.setItem('activeTab', id);
        }
    }
    
    // Verifica URL (prioridade) ou LocalStorage
    const urlParams = new URLSearchParams(window.location.search);
    const abaUrl = urlParams.get('aba');
    
    if (abaUrl && document.getElementById(abaUrl)) {
        showTab(abaUrl);
        // Limpa a URL para ficar bonita (opcional)
        window.history.replaceState({}, document.title, window.location.pathname);
    } else {
        const storedTab = localStorage.getItem('activeTab');
        if (storedTab && document.getElementById(storedTab)) {
            showTab(storedTab);
        } else {
            showTab('dash');
        }
    }

    // MODAL
    function abrirModal(id, cliente, itensJson, endereco) {
        document.getElementById('modalIdPedido').innerText = id;
        document.getElementById('modalCliente').innerText = cliente || 'Cliente';
        document.getElementById('modalEndereco').innerText = endereco || 'Endereço não informado';
        
        const lista = document.getElementById('lista-itens-modal');
        lista.innerHTML = "";
        
        try {
            const itens = JSON.parse(itensJson);
            itens.forEach(item => {
                lista.innerHTML += `<li>
                    <span class="item-nome"><span class="item-qtd">${item.quantidade}x</span> ${item.nome}</span>
                    <small>R$ ${(item.preco_unitario * item.quantidade).toFixed(2)}</small>
                </li>`;
            });
        } catch (e) {
            lista.innerHTML = "<li>Erro ao carregar itens.</li>";
        }
        document.getElementById('modalItens').style.display = "block";
    }

    function fecharModal() { document.getElementById('modalItens').style.display = "none"; }
    window.onclick = function(event) { if (event.target == document.getElementById('modalItens')) fecharModal(); }

    // EDIÇÃO DE CATEGORIA
    function editarCategoria(id, nome) {
        document.getElementById('cat_id').value = id;
        document.getElementById('nome_categoria').value = nome;
        window.scrollTo(0, 0);
    }

    function limparFormCat() {
        document.getElementById('cat_id').value = '';
        document.getElementById('nome_categoria').value = '';
    }

    // EDIÇÃO DE PRODUTO
    function editarProduto(id, nome, desc, preco, catId) {
        document.getElementById('prod_id').value = id;
        document.getElementById('prod_nome').value = nome;
        document.getElementById('prod_descricao').value = desc;
        document.getElementById('prod_preco').value = preco;
        document.getElementById('prod_categoria').value = catId;
        document.getElementById('titulo-prod').innerText = "Editando Produto #" + id;
        window.scrollTo(0, 0);
    }

    function limparFormProd() {
        document.getElementById('prod_id').value = '';
        document.getElementById('prod_nome').value = '';
        document.getElementById('prod_descricao').value = '';
        document.getElementById('prod_preco').value = '';
        document.getElementById('prod_categoria').value = '';
        document.getElementById('titulo-prod').innerText = "Novo Produto";
    }
</script>

</body>
</html>