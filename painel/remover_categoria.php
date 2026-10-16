<?php
require_once __DIR__ . '/includes/verifica_login.php';
require_once __DIR__ . '/conexao.php';

function retornar($mensagem, $tipo = 'erro')
{
    header('Location: categorias.php?' . http_build_query([
        'mensagem' => $mensagem,
        'tipo' => $tipo
    ]));
    exit;
}

// A exclusão só pode ser solicitada pelo formulário.

validarCsrf();

$idCategoria = filter_var(
    $_POST['id_categoria'] ?? null,
    FILTER_VALIDATE_INT,
    ['options' => ['min_range' => 1]]
);

if ($idCategoria === false || $idCategoria === null) {
    retornar('Categoria inválida.');
}

try {
    // Só exclui se nenhum documento estiver vinculado à categoria.
    $sql = 'DELETE FROM categorias
            WHERE id_categoria = ?
              AND NOT EXISTS (
                  SELECT 1 FROM documentos
                  WHERE documentos.id_categoria = categorias.id_categoria
              )';

    $stmt = mysqli_prepare($conexao, $sql);
    if (!$stmt) {
        retornar('Não foi possível preparar a exclusão.');
    }

    mysqli_stmt_bind_param($stmt, 'i', $idCategoria);

    if (!mysqli_stmt_execute($stmt)) {
        retornar('Não foi possível excluir a categoria. Verifique os documentos vinculados.');
    }

    if (mysqli_stmt_affected_rows($stmt) === 0) {
        retornar('Categoria não encontrada ou possui documentos vinculados.');
    }

    mysqli_stmt_close($stmt);
    retornar('Categoria excluída com sucesso.', 'sucesso');
} catch (mysqli_sql_exception $erro) {
    error_log($erro->getMessage());
    retornar('Não foi possível excluir a categoria. Verifique os documentos vinculados.');
}
