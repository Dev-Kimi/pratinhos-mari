let carrinho = [];
let total = 0;

function adicionar(produto){
    carrinho.push(produto);
    total += parseFloat(produto.preco);
    atualizar();
}

function atualizar(){
    document.getElementById("total").innerText = total.toFixed(2);
}

function finalizar(){
    localStorage.setItem("carrinho", JSON.stringify(carrinho));
    localStorage.setItem("total", total);
    window.location = "checkout.php";
}
