<?php

require_once __DIR__ . '/includes/verifica_login.php';

validarCsrf();

require_once __DIR__ . '/conexao.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

function retornarDocumentos($mensagem, $tipo = 'erro')
{
    header('Location: documentos.php?' . http_build_query([
        'mensagem' => $mensagem,
        'tipo' => $tipo
    ]), true, 303);

    exit;
}

$idDocumento = filter_var(
    $_POST['id_documento'] ?? null,
    FILTER_VALIDATE_INT,
    ['options' => ['min_range' => 1]]
);

if (!$idDocumento) {
    retornarDocumentos('Documento inválido.');
}

$transacaoAberta = false;

try {
    mysqli_begin_transaction($conexao);
    $transacaoAberta = true;

    // Busca e bloqueia o registro durante a exclusão.
    $stmt = mysqli_prepare(
        $conexao,
        'SELECT caminho
         FROM documentos
         WHERE id_documento = ?
         FOR UPDATE'
    );

    mysqli_stmt_bind_param($stmt, 'i', $idDocumento);
    mysqli_stmt_execute($stmt);

    $resultado = mysqli_stmt_get_result($stmt);
    $documento = mysqli_fetch_assoc($resultado);

    mysqli_stmt_close($stmt);

    if (!$documento) {
        mysqli_rollback($conexao);
        $transacaoAberta = false;

        retornarDocumentos(
            'Documento não encontrado ou já excluído.'
        );
    }

    $caminho = $documento['caminho'];

    // Primeiro exclui o registro do banco.
    $stmt = mysqli_prepare(
        $conexao,
        'DELETE FROM documentos WHERE id_documento = ?'
    );

    mysqli_stmt_bind_param($stmt, 'i', $idDocumento);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    mysqli_commit($conexao);
    $transacaoAberta = false;

} catch (Throwable $erro) {
    if ($transacaoAberta) {
        try {
            mysqli_rollback($conexao);
        } catch (Throwable $erroRollback) {
            error_log($erroRollback->getMessage());
        }
    }

    error_log($erro->getMessage());

    retornarDocumentos(
        'Não foi possível confirmar a exclusão. Atualize a listagem.'
    );
}

// Só tenta remover o arquivo depois de confirmar a exclusão.
try {
    if (
        !is_string($caminho)
        || $caminho === ''
        || str_contains($caminho, "\0")
    ) {
        throw new RuntimeException(
            'Caminho inválido na limpeza do documento.'
        );
    }

    // Preserva o arquivo se outro registro usar o mesmo caminho.
    $stmt = mysqli_prepare(
        $conexao,
        'SELECT COUNT(*) FROM documentos WHERE caminho = ?'
    );

    mysqli_stmt_bind_param($stmt, 's', $caminho);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $referencias);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    if ((int) $referencias > 0) {
        retornarDocumentos(
            'Documento excluído. O arquivo foi mantido porque '
            . 'também é utilizado por outro documento.',
            'sucesso'
        );
    }

    $pastaUploads = realpath(__DIR__ . '/uploads');
    $arquivoReal = realpath(__DIR__ . '/' . $caminho);

    // Nunca apaga um arquivo fora da pasta de uploads.
    if (
        $pastaUploads === false
        || $arquivoReal === false
        || !str_starts_with(
            $arquivoReal,
            $pastaUploads . DIRECTORY_SEPARATOR
        )
        || !is_file($arquivoReal)
    ) {
        throw new RuntimeException(
            'Arquivo ausente ou fora da pasta permitida: '
            . $caminho
        );
    }

    if (!unlink($arquivoReal)) {
        throw new RuntimeException(
            'Falha ao remover o arquivo: ' . $arquivoReal
        );
    }

} catch (Throwable $erro) {
    error_log($erro->getMessage());

    retornarDocumentos(
        'O registro foi excluído, mas não foi possível confirmar '
        . 'a remoção do arquivo. Confira a pasta uploads.'
    );
}

retornarDocumentos(
    'Documento e arquivo excluídos com sucesso.',
    'sucesso'
);