<?php
require_once __DIR__ . '/includes/verifica_login.php';
require_once __DIR__ . '/conexao.php';

function retornarDocumentos($mensagem, $tipo = 'erro')
{
    header('Location: documentos.php?' . http_build_query([
        'mensagem' => $mensagem,
        'tipo' => $tipo
    ]), true, 303);
    exit;
}

// Não exclui documentos ao abrir um link diretamente.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: documentos.php');
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

try {
    // Remove o registro. O arquivo físico é mantido em uploads.
    $stmt = mysqli_prepare($conexao, 'DELETE FROM documentos WHERE id_documento = ?');
    if (!$stmt) {
        retornarDocumentos('Não foi possível preparar a exclusão.');
    }
    mysqli_stmt_bind_param($stmt, 'i', $idDocumento);

    if (!mysqli_stmt_execute($stmt)) {
        retornarDocumentos('Não foi possível excluir o documento.');
    }
    if (mysqli_stmt_affected_rows($stmt) === 0) {
        retornarDocumentos('Documento não encontrado ou já excluído.');
    }
    mysqli_stmt_close($stmt);
    retornarDocumentos('Documento excluído do portal. O arquivo permanece na pasta uploads.', 'sucesso');
} catch (mysqli_sql_exception $erro) {
    error_log($erro->getMessage());
    retornarDocumentos('Não foi possível excluir o documento.');
}
