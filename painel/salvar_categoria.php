<?php

require_once __DIR__ . '/includes/verifica_login.php';
require_once __DIR__ . '/conexao.php';

function retornarCategoria($mensagem, $tipo = 'erro')
{
    header('Location: categorias.php?' . http_build_query([
        'mensagem' => $mensagem,
        'tipo' => $tipo
    ]), true, 303);

    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: categorias.php');
    exit;
}

$nomeCategoria = is_string($_POST['nome_categoria'] ?? null)
    ? trim($_POST['nome_categoria'])
    : '';

$descricao = is_string($_POST['info_categoria'] ?? null)
    ? trim($_POST['info_categoria'])
    : '';

if ($nomeCategoria === '') {
    retornarCategoria('Informe o nome da categoria.');
}

// Primeira letra maiúscula, preservando o restante.
$nomeCategoria =
    mb_strtoupper(mb_substr($nomeCategoria, 0, 1, 'UTF-8'), 'UTF-8')
    . mb_substr($nomeCategoria, 1, null, 'UTF-8');

if (mb_strlen($nomeCategoria, 'UTF-8') > 100) {
    retornarCategoria('O nome deve ter até 100 caracteres.');
}

if (mb_strlen($descricao, 'UTF-8') > 255) {
    retornarCategoria('A descrição deve ter até 255 caracteres.');
}

try {
    $sql = 'SELECT id_categoria
            FROM categorias
            WHERE nome_categoria = ?
            LIMIT 1';

    $stmt = mysqli_prepare($conexao, $sql);

    if (!$stmt) {
        retornarCategoria('Não foi possível verificar a categoria.');
    }

    mysqli_stmt_bind_param($stmt, 's', $nomeCategoria);

    if (!mysqli_stmt_execute($stmt)) {
        retornarCategoria('Não foi possível verificar a categoria.');
    }

    $resultado = mysqli_stmt_get_result($stmt);
    $existe = mysqli_num_rows($resultado) > 0;

    mysqli_stmt_close($stmt);

    if ($existe) {
        retornarCategoria('Essa categoria já existe.');
    }

    $sql = 'INSERT INTO categorias (nome_categoria, info_categoria)
            VALUES (?, ?)';

    $stmt = mysqli_prepare($conexao, $sql);

    if (!$stmt) {
        retornarCategoria('Não foi possível preparar o cadastro.');
    }

    mysqli_stmt_bind_param(
        $stmt,
        'ss',
        $nomeCategoria,
        $descricao
    );

    if (!mysqli_stmt_execute($stmt)) {
        retornarCategoria('Não foi possível cadastrar a categoria.');
    }

    mysqli_stmt_close($stmt);

    retornarCategoria('Categoria adicionada com sucesso.', 'sucesso');
} catch (mysqli_sql_exception $erro) {
    error_log($erro->getMessage());
    retornarCategoria('Não foi possível cadastrar a categoria.');
}