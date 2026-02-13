let carrinho = [];

function adicionar(produto) {
    carrinho.push(produto);
    atualizarCarrinho();
}

function atualizarCarrinho() {
    let lista = document.getElementById("listaCarrinho");
    let total = 0;
    lista.innerHTML = "";

    carrinho.forEach((p, i) => {
        total += parseFloat(p.preco);
        lista.innerHTML += `
            <li>
                ${p.nome} - R$ ${p.preco}
                <button onclick="remover(${i})">❌</button>
            </li>`;
    });

    document.getElementById("total").innerText = total.toFixed(2);
}

function remover(i) {
    carrinho.splice(i,1);
    atualizarCarrinho();
}

function finalizarPedido() {
    let texto = "Pedido Pratinhos da Mari:%0A";
    carrinho.forEach(p => {
        texto += `- ${p.nome} (R$ ${p.preco})%0A`;
    });

    window.open(`https://wa.me/55SEUNUMERO?text=${texto}`);
}
