<?php
require_once __DIR__ . '/includes/verifica_login.php';

validarCsrf();

require_once __DIR__ . '/conexao.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

function retornar($mensagem, $tipo = "erro")
{
    header("Location: categorias.php?" . http_build_query([
        "mensagem" => $mensagem,
        "tipo" => $tipo
    ]), true, 303);

    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: categorias.php");
    exit;
}

// Recebe os dados.
$idCategoria = filter_var(
    $_POST["id_categoria"] ?? null,
    FILTER_VALIDATE_INT,
    ["options" => ["min_range" => 1]]
);

$nome = is_string($_POST["nome_categoria"] ?? null)
    ? trim($_POST["nome_categoria"])
    : "";

$descricao = is_string($_POST["info_categoria"] ?? null)
    ? trim($_POST["info_categoria"])
    : "";

if (!$idCategoria || $nome === "") {
    retornar("Informe uma categoria válida e seu nome.");
}

// Mantém o mesmo padrão utilizado no cadastro.
$nome = mb_strtoupper(
    mb_substr($nome, 0, 1, "UTF-8"),
    "UTF-8"
) . mb_substr($nome, 1, null, "UTF-8");

if (mb_strlen($nome, "UTF-8") > 100) {
    retornar("O nome deve ter até 100 caracteres.");
}

if (mb_strlen($descricao, "UTF-8") > 255) {
    retornar("A descrição deve ter até 255 caracteres.");
}

try {
    // Confirma que a categoria existe.
    $stmt = mysqli_prepare(
        $conexao,
        "SELECT id_categoria
         FROM categorias
         WHERE id_categoria = ?"
    );

    mysqli_stmt_bind_param($stmt, "i", $idCategoria);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    $existe = mysqli_stmt_num_rows($stmt) > 0;

    mysqli_stmt_close($stmt);

    if (!$existe) {
        retornar("Categoria não encontrada.");
    }

    // Procura outra categoria com o mesmo nome.
    // Exclui da busca a própria categoria que estamos editando.
    $stmt = mysqli_prepare(
        $conexao,
        "SELECT id_categoria
         FROM categorias
         WHERE nome_categoria = ?
           AND id_categoria <> ?
         LIMIT 1"
    );

    mysqli_stmt_bind_param($stmt, "si", $nome, $idCategoria);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    $nomeEmUso = mysqli_stmt_num_rows($stmt) > 0;

    mysqli_stmt_close($stmt);

    if ($nomeEmUso) {
        retornar("Já existe outra categoria com esse nome.");
    }

    // Atualiza sem alterar o ID.
    $stmt = mysqli_prepare(
        $conexao,
        "UPDATE categorias
         SET nome_categoria = ?, info_categoria = ?
         WHERE id_categoria = ?"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "ssi",
        $nome,
        $descricao,
        $idCategoria
    );

    mysqli_stmt_execute($stmt);

    $alterados = mysqli_stmt_affected_rows($stmt);

    mysqli_stmt_close($stmt);

} catch (Throwable $erro) {
    error_log($erro->getMessage());

    retornar("Não foi possível atualizar a categoria.");
}

if ($alterados === 0) {
    retornar(
        "Nenhuma alteração realizada. Os dados podem já estar iguais ou a categoria foi removida.",
        "sucesso"
    );
}

retornar("Categoria atualizada com sucesso!", "sucesso");