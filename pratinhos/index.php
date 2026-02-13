<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Pratinhos da Mari | App Premium</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap');

        :root {
            --primary: #f39c12;
            --primary-hover: #d68910;
            --bg: #0f0f0f;
            --card: #1a1a1a;
            --input-bg: #252525;
            --text: #ffffff;
            --text-muted: #a0a0a0;
            --border: #2a2a2a;
            --success: #2ecc71;
            --whatsapp: #25D366;
        }


        /* Remove para Chrome, Safari, Edge e Opera */
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
        }

        /* Remove para Firefox */
        input[type=number] {
        -moz-appearance: textfield;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Plus Jakarta Sans', sans-serif; -webkit-tap-highlight-color: transparent; outline: none; }
        
        body { background: var(--bg); color: var(--text); overflow-x: hidden; padding-bottom: 100px; }

        /* --- HEADER --- */
        header { 
            position: sticky; top: 0; background: rgba(15, 15, 15, 0.95); 
            backdrop-filter: blur(10px); z-index: 1000; padding: 15px 0;
            border-bottom: 1px solid var(--border);
        }
        .logo { text-align: center; font-weight: 800; font-size: 1.2rem; margin-bottom: 15px; letter-spacing: -1px; }
        .logo span { color: var(--primary); }

        .cat-scroll { 
            display: flex; overflow-x: auto; gap: 12px; padding: 0 15px; 
            scrollbar-width: none;
        }
        .cat-scroll::-webkit-scrollbar { display: none; }
        .cat-tab { 
            padding: 8px 16px; border-radius: 12px; font-size: 0.85rem; 
            font-weight: 600; white-space: nowrap; color: var(--text-muted);
            background: var(--card); border: 1px solid var(--border); transition: 0.3s;
        }
        .cat-tab.active { background: var(--primary); color: #000; border-color: var(--primary); transform: scale(1.05); }

        /* --- GRID DE PRODUTOS --- */
        .content { padding: 15px; max-width: 600px; margin: 0 auto; }
        .section-title { margin: 25px 0 15px; font-size: 1.1rem; font-weight: 800; display: flex; align-items: center; gap: 10px; }
        .section-title::after { content: ''; flex: 1; height: 1px; background: var(--border); }

        .product-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .p-card { 
            background: var(--card); border-radius: 20px; overflow: hidden; 
            border: 1px solid var(--border); position: relative;
            animation: fadeIn 0.5s ease forwards; cursor: pointer;
        }
        .p-img { width: 100%; height: 120px; object-fit: cover; }
        .p-info { padding: 12px; }
        .p-name { font-size: 0.85rem; font-weight: 600; margin-bottom: 5px; height: 34px; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; }
        .p-price { color: var(--primary); font-weight: 800; font-size: 1rem; }
        
        .btn-add-small {
            position: absolute; bottom: 10px; right: 10px;
            background: var(--primary); color: #000; border: none;
            width: 30px; height: 30px; border-radius: 10px; font-size: 1.2rem;
            display: flex; align-items: center; justify-content: center; font-weight: 800;
        }

        /* --- BARRA DE CARRINHO --- */
        .cart-bar {
            position: fixed; bottom: 85px; left: 15px; right: 15px;
            background: var(--primary); color: #000; padding: 16px 20px;
            border-radius: 18px; display: flex; justify-content: space-between;
            align-items: center; box-shadow: 0 10px 30px rgba(243, 156, 18, 0.4);
            z-index: 1100; transform: translateY(200px); transition: 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            max-width: 570px; margin: 0 auto; cursor: pointer;
        }
        .cart-bar.show { transform: translateY(0); }
        .cart-bar strong { font-size: 1.1rem; }

        /* --- MODAIS (DRAWER) --- */
        .drawer { 
            position: fixed; top: 0; left: 0; width: 100%; height: 100%; 
            background: rgba(0,0,0,0.8); z-index: 2000; display: none; align-items: flex-end; 
            justify-content: center;
        }
        .drawer-content { 
            background: #1a1a1a; width: 100%; max-width: 600px; border-radius: 30px 30px 0 0; 
            padding: 30px 20px; position: relative;
            transform: translateY(100%); transition: 0.4s;
            max-height: 90vh; overflow-y: auto;
        }
        .drawer.active { display: flex; }
        .drawer.active .drawer-content { transform: translateY(0); }

        .qty-selector { display: flex; align-items: center; justify-content: center; gap: 20px; margin: 25px 0; }
        .qty-btn { 
            width: 45px; height: 45px; border-radius: 15px; border: 1px solid var(--border);
            background: var(--input-bg); color: white; font-size: 1.5rem; cursor: pointer;
        }

        /* --- FORMULÁRIOS --- */
        .input-group { margin-bottom: 15px; }
        .input-group label { display: block; color: var(--text-muted); font-size: 0.8rem; margin-bottom: 5px; margin-left: 5px; }
        input, select, textarea { 
            width: 100%; padding: 15px; background: var(--input-bg); border: 1px solid var(--border);
            color: #fff; border-radius: 12px; font-size: 1rem; transition: 0.3s;
        }
        input:focus, select:focus, textarea:focus { border-color: var(--primary); }
        
        /* Toggle Switch para Entrega/Retirada */
        .delivery-toggle {
            display: flex; background: var(--input-bg); padding: 5px; border-radius: 15px;
            margin-bottom: 20px; border: 1px solid var(--border);
        }
        .dt-option {
            flex: 1; padding: 12px; text-align: center; border-radius: 10px;
            font-weight: 600; cursor: pointer; transition: 0.3s; color: var(--text-muted);
        }
        .dt-option.active { background: var(--primary); color: #000; shadow: 0 2px 10px rgba(0,0,0,0.2); }

        /* Botões */
        .btn-main { 
            width: 100%; padding: 18px; border-radius: 15px; border: none; 
            background: var(--primary); color: #000; font-weight: 800; font-size: 1rem;
            cursor: pointer; transition: 0.2s; display: flex; align-items: center; justify-content: center; gap: 10px;
        }
        .btn-main:active { transform: scale(0.98); }
        .btn-main.whatsapp { background: var(--whatsapp); color: #fff; }
        .btn-sec { width: 100%; background: none; border: none; color: var(--text-muted); margin-top: 15px; padding: 10px; cursor: pointer; }

        /* --- STATUS DOS PEDIDOS --- */
        .order-card {
            background: var(--input-bg); border-radius: 15px; padding: 15px;
            margin-bottom: 15px; border: 1px solid var(--border);
        }
        .order-header { display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 0.9rem; color: var(--text-muted); }
        .order-items { font-size: 0.95rem; margin-bottom: 10px; line-height: 1.4; }
        .status-badge {
            display: inline-block; padding: 5px 12px; border-radius: 20px;
            font-size: 0.75rem; font-weight: 800; text-transform: uppercase;
        }
        .st-aguardando { background: #e67e22; color: #fff; }
        .st-finalizado { background: #2ecc71; color: #fff; }

        /* --- LOADING --- */
        .spinner {
            width: 40px; height: 40px; border: 4px solid rgba(243, 156, 18, 0.3);
            border-top: 4px solid var(--primary); border-radius: 50%;
            animation: spin 1s linear infinite; margin: 30px auto;
        }

        /* --- SUCCESS MODAL --- */
        .success-icon {
            width: 80px; height: 80px; background: var(--success); color: white;
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            font-size: 2.5rem; margin: 0 auto 20px; animation: bounceIn 0.8s;
        }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>
</head>
<body>

    <header>
        <div class="logo">Pratinhos da <span>Mari</span></div>
        <div class="cat-scroll" id="cat-tabs"></div>
    </header>

    <main class="content" id="main-content">
        <div class="spinner"></div>
    </main>

    <div id="aba-pedidos" class="content" style="display:none;">
        <h2 style="margin-bottom: 20px;">Meus Pedidos</h2>
        <div id="pedidos-lista">
            </div>
    </div>

    <div class="cart-bar" id="cart-bar" onclick="abrirCheckout()">
        <div><span id="cart-count">0</span> itens</div>
        <strong>R$ <span id="cart-total">0,00</span> <i class="fas fa-chevron-right" style="margin-left: 5px;"></i></strong>
    </div>

    <div class="drawer" id="modalQty">
        <div class="drawer-content">
            <h2 id="modalProdNome">Nome do Produto</h2>
            <p id="modalProdDesc" style="color: var(--text-muted); margin: 10px 0; font-size: 0.9rem;"></p>
            
            <div class="qty-selector">
                <button class="qty-btn" onclick="updateQty(-1)"><i class="fas fa-minus"></i></button>
                <span id="displayQty" style="font-size: 1.5rem; font-weight: 800; min-width: 30px; text-align: center;">1</span>
                <button class="qty-btn" onclick="updateQty(1)"><i class="fas fa-plus"></i></button>
            </div>

            <button class="btn-main" onclick="confirmAdd()">
                <span>Adicionar</span>
                <span id="modalProdSubtotal">R$ 0,00</span>
            </button>
            <button class="btn-sec" onclick="closeModal('modalQty')">Cancelar</button>
        </div>
    </div>

    

    <div class="drawer" id="modalCheckout">
        <div class="drawer-content">
            <h2 style="margin-bottom: 20px; text-align: center;">Finalizar Pedido</h2>
            
            <div id="step-auth">
                <div class="input-group">
                    <label>Seu Nome</label>
                    <input type="text" id="userName" placeholder="Ex: João Silva">
                </div>
                <div class="input-group">
                    <label>Seu WhatsApp (com DDD)</label>
                    <input type="tel" id="userWhats" placeholder="Ex: 85999999999" maxlength="15" oninput="maskPhone(this)">
                </div>
                <button class="btn-main" onclick="authAndNext()">Continuar <i class="fas fa-arrow-right"></i></button>
            </div>

            <div id="step-delivery" style="display:none;">
                
                <div class="delivery-toggle">
                    <div class="dt-option active" id="opt-entrega" onclick="setDeliveryMode('entrega')">
                        <i class="fas fa-motorcycle"></i> Entrega
                    </div>
                    <div class="dt-option" id="opt-retirada" onclick="setDeliveryMode('retirada')">
                        <i class="fas fa-shopping-bag"></i> Retirada
                    </div>
                </div>

                <div id="address-fields">
                    <div class="input-group">
                        <input type="text" id="endRua" placeholder="Rua e Número">
                    </div>

                    

                    <div class="input-group">
                        <input type="text" id="endBairro" placeholder="Bairro">
                    </div>
                    <div class="input-group">
                        <textarea id="endRef" rows="2" placeholder="Ponto de Referência (Opcional)"></textarea>
                    </div>
                </div>

                <div class="input-group">
                    <label>Forma de Pagamento</label>
                    <select id="pagamento" onchange="checkTroco()">
                        <option value="Pix">Pix</option>
                        <option value="Cartão">Cartão (Maquininha)</option>
                        <option value="Dinheiro">Dinheiro</option>
                    </select>
                </div>

                <div class="input-group" id="div-troco" style="display: none; animation: fadeIn 0.3s;">
                    <label style="color: var(--primary);">Troco para quanto?</label>
                    <input type="tel" id="valTroco" placeholder="Ex: 50,00" oninput="maskMoney(this)">
                </div>

                <div style="margin: 20px 0; border-top: 1px dashed var(--border); padding-top: 15px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 5px; color: var(--text-muted);">
                        <span>Subtotal</span>
                        <span id="checkout-subtotal">R$ 0,00</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 1.2rem; font-weight: 800;">
                        <span>Total</span>
                        <span id="checkout-total" style="color: var(--primary);">R$ 0,00</span>
                    </div>
                </div>

                <button class="btn-main" id="btnFinalizar" onclick="finalizarPedidoFinal()">CONFIRMAR PEDIDO</button>
            </div>
            
            <button class="btn-sec" onclick="closeModal('modalCheckout')">Voltar</button>
        </div>
    </div>

    <div class="drawer" id="modalSuccess" style="z-index: 2100;">
        <div class="drawer-content" style="text-align: center;">
            <div class="success-icon"><i class="fas fa-check"></i></div>
            <h2>Pedido Confirmado!</h2>
            <p style="color: var(--text-muted); margin: 10px 0 20px;">Seu pedido foi registrado com sucesso.</p>
            
            <p style="margin-bottom: 20px; font-size: 0.9rem;">Para agilizar o preparo, envie os detalhes no nosso WhatsApp agora:</p>
            
            <button class="btn-main whatsapp" id="btnWhatsApp" onclick="enviarWhatsApp()">
                <i class="fab fa-whatsapp"></i> Enviar no WhatsApp
            </button>
            <button class="btn-sec" onclick="fecharTudo()">Fechar</button>
        </div>
    </div>

    <div style="position: fixed; bottom: 0; width: 100%; height: 70px; background: rgba(17, 17, 17, 0.95); backdrop-filter: blur(10px); display: flex; justify-content: space-around; align-items: center; border-top: 1px solid var(--border); z-index: 1000;">
        <div onclick="changeTab('cardapio')" id="tab-btn-cardapio" style="text-align: center; color: var(--primary); cursor: pointer; transition: 0.3s;">
            <i class="fas fa-hamburger" style="font-size: 1.3rem;"></i><p style="font-size: 0.6rem; margin-top: 4px;">CARDÁPIO</p>
        </div>
        <div onclick="changeTab('pedidos')" id="tab-btn-pedidos" style="text-align: center; color: var(--text-muted); cursor: pointer; transition: 0.3s;">
            <i class="fas fa-clipboard-list" style="font-size: 1.3rem;"></i><p style="font-size: 0.6rem; margin-top: 4px;">PEDIDOS</p>
        </div>
    </div>

    <script>
        // --- ESTADO DA APLICAÇÃO ---
        let sacola = [];
        let usuario = JSON.parse(localStorage.getItem('mari_user')) || null;
        // Armazena apenas o resumo localmente, mas atualizaremos o status via API
        let historicoPedidos = JSON.parse(localStorage.getItem('mari_pedidos')) || [];
        
        let currentItem = null;
        let currentQty = 1;
        let modoEntrega = 'entrega'; 
        let lastOrderData = null;

        // --- INICIALIZAÇÃO ---
        async function init() {
            try {
                // 1. Busca o cardápio real do PHP
                const response = await fetch('api/buscar_cardapio.php');
                const data = await response.json();
                
                if (data.error) {
                    alert("Erro ao carregar cardápio: " + data.error);
                } else {
                    renderCardapio(data);
                }
            } catch (error) {
                console.error(error);
                document.getElementById('main-content').innerHTML = '<p style="text-align:center; margin-top:50px">Erro de conexão com o servidor.</p>';
            }

            // 2. Preenche dados do usuário se existir
            if (usuario) {
                document.getElementById('userName').value = usuario.nome;
                document.getElementById('userWhats').value = usuario.whatsapp;
                if (usuario.endereco) {
                    document.getElementById('endRua').value = usuario.endereco.rua || '';
                    document.getElementById('endBairro').value = usuario.endereco.bairro || '';
                }
            }
            
            // 3. Atualiza status dos pedidos antigos
            atualizarStatusPedidos();
        }

        // --- RENDERIZAÇÃO ---
        function renderCardapio(data) {
            let menuHtml = ''; let tabsHtml = '';
            let isFirst = true;

            // O PHP retorna um Objeto {"Burgers": [...], "Bebidas": [...]}
            // Se o array estiver vazio, exibe mensagem
            if (Object.keys(data).length === 0) {
                 document.getElementById('main-content').innerHTML = '<p style="text-align:center; margin-top:50px">Nenhum produto cadastrado no momento.</p>';
                 return;
            }

            for (const [cat, prods] of Object.entries(data)) {
                const activeClass = isFirst ? 'active' : '';
                tabsHtml += `<div class="cat-tab ${activeClass}" onclick="scrollToSection('${cat}', this)">${cat}</div>`;
                
                menuHtml += `
                    <h3 class="section-title" id="sec-${cat}">
                        <i class="fas fa-utensils" style="font-size:0.8rem; color:var(--primary)"></i> ${cat}
                    </h3>
                    <div class="product-grid">`;
                
                prods.forEach(p => {
    // AJUSTADO: Agora pegamos p.imagem_url que vem do banco
                let nomeImagem = p.imagem_url ? p.imagem_url.trim() : "";
                let imgUrl = "";

                if (nomeImagem) {
                    // Remove caminhos duplicados se houver
                    nomeImagem = nomeImagem.replace("assets/img/", "");
                    imgUrl = `assets/img/${nomeImagem}`;
                } else {
                    imgUrl = `https://via.placeholder.com/300x200/333/f39c12?text=${encodeURIComponent(p.nome)}`;
                }

                menuHtml += `
                    <div class="p-card" onclick='openAdd(${JSON.stringify(p)})'>
                        <img src="${imgUrl}" class="p-img" onerror="this.src='https://via.placeholder.com/300x200/333/f39c12?text=Erro+na+Foto'">
                        <div class="p-info">
                            <h4 class="p-name">${p.nome}</h4>
                            <p class="p-price">R$ ${parseFloat(p.preco).toFixed(2).replace('.',',')}</p>
                        </div>
                        <div class="btn-add-small"><i class="fas fa-plus"></i></div>
                    </div>`;
            });
                menuHtml += `</div>`;
                isFirst = false;
            }
            document.getElementById('cat-tabs').innerHTML = tabsHtml;
            document.getElementById('main-content').innerHTML = menuHtml;
        }

        async function atualizarStatusPedidos() {
            if (historicoPedidos.length === 0) return;

            // Pega os IDs dos pedidos locais
            const ids = historicoPedidos.map(p => p.id).join(',');
            
            try {
                const res = await fetch(`api/buscar_status_pedidos.php?ids=${ids}`);
                const statusReais = await res.json();
                
                // Atualiza o localStorage com o status novo do banco
                statusReais.forEach(real => {
                    const local = historicoPedidos.find(p => p.id == real.id);
                    if (local) {
                        local.status = real.status;
                    }
                });
                localStorage.setItem('mari_pedidos', JSON.stringify(historicoPedidos));
            } catch (e) {
                console.log("Não foi possível atualizar status agora.");
            }
        }

        function renderPedidos() {
            // Atualiza antes de mostrar
            atualizarStatusPedidos();

            const container = document.getElementById('pedidos-lista');
            // Recarrega do localStorage
            historicoPedidos = JSON.parse(localStorage.getItem('mari_pedidos')) || []; 
            
            if (historicoPedidos.length === 0) {
                container.innerHTML = `
                    <div style="text-align:center; margin-top:50px; color:gray;">
                        <i class="fas fa-ghost fa-3x" style="margin-bottom:15px; opacity:0.3;"></i>
                        <p>Você ainda não fez nenhum pedido.</p>
                        <button class="btn-main" style="width:auto; margin: 20px auto;" onclick="changeTab('cardapio')">Fazer Pedido</button>
                    </div>`;
                return;
            }

            const listaOrdenada = historicoPedidos.slice().reverse();

            let html = '';
            listaOrdenada.forEach(p => {
                const data = new Date(p.data);
                const dataFormatada = data.toLocaleDateString('pt-BR') + ' às ' + data.toLocaleTimeString('pt-BR', {hour: '2-digit', minute:'2-digit'});
                
                const resumoItens = p.itens.map(i => `${i.qty}x ${i.nome}`).join(', ');

                // Define cor do badge baseado no status
                let badgeColor = '#e67e22'; // Laranja (Padrão/Aguardando)
                if(p.status === 'Finalizado' || p.status === 'Entregue') badgeColor = '#2ecc71'; // Verde
                if(p.status === 'Saiu para Entrega') badgeColor = '#3498db'; // Azul
                if(p.status === 'Cancelado') badgeColor = '#e74c3c'; // Vermelho

                html += `
                    <div class="order-card animate__animated animate__fadeInUp">
                        <div class="order-header">
                            <span>Pedido #${p.id}</span>
                            <span>${dataFormatada}</span>
                        </div>
                        <div class="order-items">
                            ${resumoItens}
                        </div>
                        <div style="display:flex; justify-content:space-between; align-items:center;">
                            <span class="status-badge" style="background:${badgeColor}">${p.status}</span>
                            <strong style="color:var(--primary);">R$ ${parseFloat(p.total).toFixed(2).replace('.',',')}</strong>
                        </div>
                    </div>
                `;
            });
            container.innerHTML = html;
        }

        // --- LÓGICA DO CARRINHO ---
        function openAdd(p) {
            currentItem = p; currentQty = 1;
            document.getElementById('modalProdNome').innerText = p.nome;
            document.getElementById('modalProdDesc').innerText = p.descricao || "";
            updateQty(0);
            document.getElementById('modalQty').classList.add('active');
        }

        function updateQty(v) {
            currentQty += v; if (currentQty < 1) currentQty = 1;
            document.getElementById('displayQty').innerText = currentQty;
            const sub = (currentQty * parseFloat(currentItem.preco)).toFixed(2).replace('.',',');
            document.getElementById('modalProdSubtotal').innerText = `R$ ${sub}`;
        }

        function confirmAdd() {
            const existing = sacola.find(i => i.id === currentItem.id);
            if (existing) {
                existing.qty += currentQty;
            } else {
                sacola.push({ ...currentItem, qty: currentQty });
            }
            atualizarCarrinho();
            closeModal('modalQty');
        }

        function atualizarCarrinho() {
            const bar = document.getElementById('cart-bar');
            if (sacola.length > 0) {
                const total = sacola.reduce((acc, i) => acc + (parseFloat(i.preco) * i.qty), 0);
                document.getElementById('cart-count').innerText = sacola.reduce((acc, i) => acc + i.qty, 0);
                document.getElementById('cart-total').innerText = total.toFixed(2).replace('.',',');
                bar.classList.add('show');
                
                document.getElementById('checkout-subtotal').innerText = `R$ ${total.toFixed(2).replace('.',',')}`;
                document.getElementById('checkout-total').innerText = `R$ ${total.toFixed(2).replace('.',',')}`;
            } else {
                bar.classList.remove('show');
            }
        }

        // --- CHECKOUT FLOW ---
        function abrirCheckout() {
            document.getElementById('modalCheckout').classList.add('active');
            if (usuario) {
                document.getElementById('step-auth').style.display = 'none';
                document.getElementById('step-delivery').style.display = 'block';
            } else {
                document.getElementById('step-auth').style.display = 'block';
                document.getElementById('step-delivery').style.display = 'none';
            }
        }

        function authAndNext() {
            const nome = document.getElementById('userName').value.trim();
            const whats = document.getElementById('userWhats').value.replace(/\D/g, '');
            
            if (nome.length < 3) return alert("Por favor, digite seu nome completo.");
            if (whats.length < 10) return alert("Por favor, digite um WhatsApp válido.");

            usuario = { nome, whatsapp: whats, endereco: {} };
            localStorage.setItem('mari_user', JSON.stringify(usuario));

            document.getElementById('step-auth').classList.add('animate__animated', 'animate__fadeOutLeft');
            setTimeout(() => {
                document.getElementById('step-auth').style.display = 'none';
                document.getElementById('step-delivery').style.display = 'block';
                document.getElementById('step-delivery').classList.add('animate__animated', 'animate__fadeInRight');
            }, 300);
        }

        function setDeliveryMode(mode) {
            modoEntrega = mode;
            document.getElementById('opt-entrega').className = `dt-option ${mode === 'entrega' ? 'active' : ''}`;
            document.getElementById('opt-retirada').className = `dt-option ${mode === 'retirada' ? 'active' : ''}`;
            
            const addrFields = document.getElementById('address-fields');
            if (mode === 'retirada') {
                addrFields.style.display = 'none';
            } else {
                addrFields.style.display = 'block';
                addrFields.classList.add('animate__animated', 'animate__fadeIn');
            }
        }

        function checkTroco() {
            const method = document.getElementById('pagamento').value;
            const divTroco = document.getElementById('div-troco');
            divTroco.style.display = (method === 'Dinheiro') ? 'block' : 'none';
        }

        // --- FINALIZAÇÃO REAL (CONECTADA AO PHP) ---
        async function finalizarPedidoFinal() {
            const btn = document.getElementById('btnFinalizar');
            const total = sacola.reduce((acc, i) => acc + (parseFloat(i.preco) * i.qty), 0);
            const formaPagamento = document.getElementById('pagamento').value;
            
            let enderecoFinal = "Retirada no Local";
            let trocoMsg = "";

            if (modoEntrega === 'entrega') {
                const rua = document.getElementById('endRua').value;
                const bairro = document.getElementById('endBairro').value;
                if (!rua || !bairro) return alert("Para entrega, informe Rua e Bairro!");
                
                usuario.endereco = { rua, bairro };
                localStorage.setItem('mari_user', JSON.stringify(usuario));
                
                const ref = document.getElementById('endRef').value;
                enderecoFinal = `${rua}, ${bairro}` + (ref ? ` (${ref})` : '');
            }

            if (formaPagamento === 'Dinheiro') {
                const valTroco = document.getElementById('valTroco').value;
                if (!valTroco) return alert("Informe para quanto é o troco!");
                trocoMsg = `(Troco para R$ ${valTroco})`;
            }

            const pagamentoCompleto = `${formaPagamento} ${trocoMsg}`.trim();

            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';

            // Dados para enviar ao PHP
            const payload = {
                usuario: usuario,
                itens: sacola,
                total: total,
                metodo: modoEntrega,
                endereco: enderecoFinal,
                pagamento: pagamentoCompleto
            };

            try {
                const response = await fetch('api/finalizar_pedido.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                
                const result = await response.json();

                if (result.success) {
                    // Pedido Salvo no Banco!
                    
                    // Salva histórico local com o ID REAL do banco
                    const novoPedidoLocal = {
                        id: result.id_pedido, // ID gerado pelo MySQL
                        data: new Date().toISOString(),
                        itens: sacola,
                        total: total,
                        status: 'Aguardando', // Status inicial
                        // Dados para o botão do WhatsApp
                        whatsappData: {
                            metodo: modoEntrega,
                            endereco: enderecoFinal,
                            pagamento: pagamentoCompleto,
                            usuario: usuario
                        }
                    };

                    historicoPedidos.push(novoPedidoLocal);
                    localStorage.setItem('mari_pedidos', JSON.stringify(historicoPedidos));
                    
                    lastOrderData = novoPedidoLocal; // Para o modal de sucesso usar

                    sacola = [];
                    atualizarCarrinho();
                    closeModal('modalCheckout');
                    document.getElementById('modalSuccess').classList.add('active');

                } else {
                    alert('Erro ao salvar pedido: ' + result.error);
                }
            } catch (error) {
                console.error(error);
                alert('Erro de conexão ao tentar finalizar o pedido.');
            } finally {
                btn.disabled = false;
                btn.innerText = "CONFIRMAR PEDIDO";
            }
        }

        function enviarWhatsApp() {
            if (!lastOrderData) return;
            const p = lastOrderData;
            const dados = p.whatsappData;
            
            let texto = `*Olá! Acabei de fazer um pedido no Site.*\n\n`;
            texto += `*Pedido #${p.id}*\n`;
            texto += `------------------------------\n`;
            
            p.itens.forEach(item => {
                texto += `${item.qty}x ${item.nome} \n`;
            });
            
            texto += `------------------------------\n`;
            texto += `*Total: R$ ${parseFloat(p.total).toFixed(2).replace('.',',')}*\n`;
            texto += `Pagamento: ${dados.pagamento}\n`;
            texto += `Tipo: *${dados.metodo.toUpperCase()}*\n`;
            if (dados.metodo === 'entrega') {
                texto += `Endereço: ${dados.endereco}\n`;
            }
            texto += `\nCliente: ${dados.usuario.nome}`;

            const numeroLoja = "5585999999999"; // <--- SEU NÚMERO AQUI
            const url = `https://wa.me/${numeroLoja}?text=${encodeURIComponent(texto)}`;
            window.open(url, '_blank');
        }

        // --- NAVEGAÇÃO E UTILITÁRIOS ---
        function changeTab(aba) {
            document.getElementById('main-content').style.display = (aba === 'cardapio' ? 'block' : 'none');
            document.getElementById('aba-pedidos').style.display = (aba === 'pedidos' ? 'block' : 'none');
            
            const corAtiva = 'var(--primary)';
            const corInativa = 'var(--text-muted)';
            
            document.getElementById('tab-btn-cardapio').style.color = (aba === 'cardapio' ? corAtiva : corInativa);
            document.getElementById('tab-btn-pedidos').style.color = (aba === 'pedidos' ? corAtiva : corInativa);

            if (aba === 'pedidos') renderPedidos();
        }

        function closeModal(id) { document.getElementById(id).classList.remove('active'); }
        
        function fecharTudo() {
            closeModal('modalSuccess');
            changeTab('pedidos'); 
        }

        function scrollToSection(cat, element) {
            document.querySelectorAll('.cat-tab').forEach(el => el.classList.remove('active'));
            element.classList.add('active');
            const el = document.getElementById('sec-' + cat);
            window.scrollTo({ top: el.offsetTop - 110, behavior: 'smooth' });
        }

        function maskPhone(input) {
            let v = input.value.replace(/\D/g,'');
            v = v.replace(/^(\d{2})(\d)/g,"($1) $2");
            v = v.replace(/(\d)(\d{4})$/,"$1-$2");
            input.value = v;
        }
        function maskMoney(input) {
            let v = input.value.replace(/\D/g,'');
            v = (v/100).toFixed(2) + '';
            v = v.replace(".", ",");
            input.value = v;
        }

        // Atualiza os status a cada 45 segundos automaticamente
        setInterval(() => {
            atualizarStatusPedidos();
            // Se o usuário estiver na aba de pedidos, renderiza novamente para mostrar a mudança
            if (document.getElementById('aba-pedidos').style.display === 'block') {
                renderPedidos();
            }
        }, 45000);

        init();
    </script>
</body>
</html>