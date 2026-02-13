<?php
session_start();
require_once "conexao.php";

$erro = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nome = trim($_POST["nome"] ?? "");
    $whatsapp = preg_replace('/[^0-9]/', '', $_POST["whatsapp"] ?? "");

    if ($nome == "" || $whatsapp == "") {
        $erro = "Preencha todos os campos.";
    } else {

        // Verifica se já existe
        $sql = "SELECT * FROM clientes WHERE whatsapp = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $whatsapp);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Já cadastrado → login automático
            $_SESSION["cliente"] = $result->fetch_assoc();
            header("Location: checkout.php");
            exit;
        } else {
            // Novo cadastro
            $sql = "INSERT INTO clientes (nome, whatsapp) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $nome, $whatsapp);

            if ($stmt->execute()) {
                $_SESSION["cliente"] = [
                    "id" => $stmt->insert_id,
                    "nome" => $nome,
                    "whatsapp" => $whatsapp
                ];
                header("Location: checkout.php");
                exit;
            } else {
                $erro = "Erro ao cadastrar.";
            }
        }
    }
}
?>
