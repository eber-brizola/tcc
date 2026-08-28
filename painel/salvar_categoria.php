<?php
require_once "conexao.php";

$nomeCategoria = trim($_POST["nome_categoria"] ?? "");

if ($nomeCategoria === "") {
    header("Location: categorias.php?tipo=erro&mensagem=Informe o nome da categoria.");
    exit;
}

$sqlVerificar = "SELECT id_categoria
                 FROM categorias
                 WHERE nome_categoria = ?
                 LIMIT 1";

$stmtVerificar = mysqli_prepare($conexao, $sqlVerificar);
mysqli_stmt_bind_param($stmtVerificar, "s", $nomeCategoria);
mysqli_stmt_execute($stmtVerificar);

$resultado = mysqli_stmt_get_result($stmtVerificar);

if (mysqli_num_rows($resultado) > 0) {
    header("Location: categorias.php?tipo=erro&mensagem=Essa categoria já existe.");
    exit;
}

$sql = "INSERT INTO categorias (nome_categoria) VALUES (?)";

$stmt = mysqli_prepare($conexao, $sql);
mysqli_stmt_bind_param($stmt, "s", $nomeCategoria);

if (mysqli_stmt_execute($stmt)) {
    header("Location: categorias.php?tipo=sucesso&mensagem=Categoria adicionada com sucesso.");
    exit;
}

header("Location: categorias.php?tipo=erro&mensagem=Não foi possível adicionar a categoria.");
exit;