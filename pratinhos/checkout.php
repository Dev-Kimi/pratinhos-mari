<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Finalizar Pedido</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<header><h1>Finalizar Pedido</h1></header>

<form method="POST" action="api/salvar_pedido.php" style="padding:20px">
<input name="nome" placeholder="Seu nome" required><br><br>
<textarea name="endereco" placeholder="Endereço" required></textarea><br><br>

<select name="pagamento">
<option>Dinheiro</option>
<option>Pix</option>
<option>Cartão</option>
</select><br><br>

<input type="hidden" name="total" id="total">
<button class="btn">Confirmar Pedido</button>
</form>

<script>
document.getElementById("total").value =
localStorage.getItem("total");
</script>

</body>
</html>
