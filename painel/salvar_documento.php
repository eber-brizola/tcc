<?php

require_once __DIR__ . '/includes/verifica_login.php';

validarCsrf();

require_once __DIR__ . '/conexao.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

function retornar(string $mensagem, string $tipo = 'erro'): void
{
    header('Location: documentos.php?' . http_build_query([
        'mensagem' => $mensagem,
        'tipo' => $tipo
    ]), true, 303);

    exit;
}

// validarCsrf() já exige o método POST.

$nome = is_string($_POST['nome'] ?? null)
    ? trim($_POST['nome'])
    : '';

if (
    !mb_check_encoding($nome, 'UTF-8')
    || mb_strlen($nome, 'UTF-8') > 255
) {
    retornar(
        'O nome do documento deve ser válido e ter até 255 caracteres.'
    );
}

$categoria = filter_var(
    $_POST['categoria'] ?? null,
    FILTER_VALIDATE_INT,
    ['options' => ['min_range' => 1]]
);

if ($nome === '' || !$categoria) {
    retornar('Informe o nome e uma categoria válida.');
}

// Verifica o upload recebido.
$arquivo = $_FILES['arquivo'] ?? null;

if (
    !is_array($arquivo)
    || !isset($arquivo['error'])
    || !is_int($arquivo['error'])
) {
    retornar('Selecione um arquivo válido.');
}

if ($arquivo['error'] !== UPLOAD_ERR_OK) {
    retornar(
        'O envio falhou. Verifique o arquivo e o limite do servidor.'
    );
}

if (
    !is_string($arquivo['tmp_name'] ?? null)
    || !is_uploaded_file($arquivo['tmp_name'])
) {
    retornar('O arquivo enviado é inválido.');
}

$caminhoCompleto = null;
$arquivoMovido = false;
$insercaoIniciada = false;
$cadastrado = false;

try {
    // Limite de 10 MB.
    $tamanhoMaximo = 10 * 1024 * 1024;
    $tamanho = filesize($arquivo['tmp_name']);

    if (
        $tamanho === false
        || $tamanho === 0
        || $tamanho > $tamanhoMaximo
    ) {
        retornar('O arquivo deve ter conteúdo e no máximo 10 MB.');
    }

    // Identifica o formato pelo conteúdo.
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $tipoArquivo = $finfo->file($arquivo['tmp_name']);

    $tiposPermitidos = [
        'application/pdf' => 'pdf',
        'image/jpeg' => 'jpg',
        'image/png' => 'png'
    ];

    if (
        !is_string($tipoArquivo)
        || !isset($tiposPermitidos[$tipoArquivo])
    ) {
        retornar('Formato não permitido. Envie PDF, JPG ou PNG.');
    }

    // Confirma que a categoria existe.
    $stmt = mysqli_prepare(
        $conexao,
        'SELECT id_categoria
         FROM categorias
         WHERE id_categoria = ?'
    );

    mysqli_stmt_bind_param($stmt, 'i', $categoria);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    $categoriaExiste = mysqli_stmt_num_rows($stmt) > 0;

    mysqli_stmt_close($stmt);

    if (!$categoriaExiste) {
        retornar('A categoria selecionada não existe.');
    }

    // Cria a pasta de uploads se necessário.
    $pasta = __DIR__ . '/uploads/';

    if (
        !is_dir($pasta)
        && !mkdir($pasta, 0755, true)
        && !is_dir($pasta)
    ) {
        throw new RuntimeException(
            'Não foi possível criar a pasta de uploads.'
        );
    }

    // Gera um nome aleatório para o arquivo.
    $extensao = $tiposPermitidos[$tipoArquivo];

    do {
        $nomeArquivo = bin2hex(random_bytes(16)) . '.' . $extensao;
        $caminhoCompleto = $pasta . $nomeArquivo;
    } while (file_exists($caminhoCompleto));

    $caminho = 'uploads/' . $nomeArquivo;

    if (!move_uploaded_file(
        $arquivo['tmp_name'],
        $caminhoCompleto
    )) {
        throw new RuntimeException(
            'Não foi possível guardar o arquivo.'
        );
    }

    $arquivoMovido = true;

    $stmt = mysqli_prepare(
        $conexao,
        'INSERT INTO documentos (nome, id_categoria, caminho)
         VALUES (?, ?, ?)'
    );

    mysqli_stmt_bind_param(
        $stmt,
        'sis',
        $nome,
        $categoria,
        $caminho
    );

    // Marcar ANTES de executar:
    // uma falha de conexão pode ocorrer depois de o banco salvar.
    $insercaoIniciada = true;

    mysqli_stmt_execute($stmt);
    $cadastrado = true;

    mysqli_stmt_close($stmt);

} catch (Throwable $erro) {
    error_log($erro->getMessage());

    // Só apaga automaticamente se o INSERT ainda não foi iniciado.
    if (
        !$insercaoIniciada
        && $arquivoMovido
        && $caminhoCompleto !== null
    ) {
        if (!unlink($caminhoCompleto)) {
            error_log(
                'Não foi possível remover upload: '
                . $caminhoCompleto
            );
        }
    }

    // O INSERT foi confirmado, mas houve uma falha posterior.
    if ($cadastrado) {
        retornar(
            'Documento cadastrado. Houve uma falha ao finalizar '
            . 'a operação; confira a listagem.',
            'sucesso'
        );
    }

    if ($insercaoIniciada) {
        error_log(
            'Cadastro sem confirmação. Arquivo preservado '
            . 'para conferência: ' . $caminhoCompleto
        );

        retornar(
            'Não foi possível confirmar o cadastro. '
            . 'Confira a listagem antes de enviar novamente. '
            . 'O arquivo foi preservado para conferência.'
        );
    }

    retornar('Não foi possível cadastrar o documento. Tente novamente.');
}

retornar('Documento cadastrado com sucesso!', 'sucesso');