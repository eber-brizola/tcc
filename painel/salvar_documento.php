<?php
require_once __DIR__ . '/includes/verifica_login.php';

validarCsrf();

require_once __DIR__ . '/conexao.php';
// Redireciona para a listagem com uma mensagem.
function retornar($mensagem, $tipo = "erro")
{
    header("Location: documentos.php?" . http_build_query([
        "mensagem" => $mensagem,
        "tipo" => $tipo
    ]), true, 303);

    exit;
}

// Só aceita requisições POST.
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: novo_documento.php");
    exit;
}

// Recebe os campos do formulário.
$nome = is_string($_POST["nome"] ?? null)
    ? trim($_POST["nome"])
    : "";

$categoria = filter_var(
    $_POST["categoria"] ?? null,
    FILTER_VALIDATE_INT,
    ["options" => ["min_range" => 1]]
);

if ($nome === "" || !$categoria) {
    retornar(
        "Informe o nome e uma categoria válida. "
        . "Se o envio for grande, confira o limite do servidor."
    );
}

// Verifica o upload recebido.
$arquivo = $_FILES["arquivo"] ?? null;

if (
    !is_array($arquivo) ||
    !isset($arquivo["error"]) ||
    !is_int($arquivo["error"])
) {
    retornar("Selecione um arquivo válido.");
}

if ($arquivo["error"] !== UPLOAD_ERR_OK) {
    retornar("O envio falhou. Verifique o arquivo e o limite do servidor.");
}

if (
    !is_string($arquivo["tmp_name"] ?? null) ||
    !is_uploaded_file($arquivo["tmp_name"])
) {
    retornar("O arquivo enviado é inválido.");
}

// Controlam a limpeza caso o cadastro falhe.
$caminhoCompleto = null;
$arquivoMovido = false;
$cadastrado = false;

try {
    // Limite de 10 MB.
    $tamanhoMaximo = 10 * 1024 * 1024;
    $tamanho = filesize($arquivo["tmp_name"]);

    if (
        $tamanho === false ||
        $tamanho === 0 ||
        $tamanho > $tamanhoMaximo
    ) {
        retornar("O arquivo deve ter conteúdo e no máximo 10 MB.");
    }

    // Identifica o formato pelo conteúdo.
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $tipoArquivo = $finfo->file($arquivo["tmp_name"]);

    $tiposPermitidos = [
        "application/pdf" => "pdf",
        "image/jpeg" => "jpg",
        "image/png" => "png"
    ];

    if (!isset($tiposPermitidos[$tipoArquivo])) {
        retornar("Formato não permitido. Envie PDF, JPG ou PNG.");
    }

    // Confirma que a categoria existe no banco.
    $stmt = mysqli_prepare(
        $conexao,
        "SELECT id_categoria
         FROM categorias
         WHERE id_categoria = ?"
    );

    if (!$stmt) {
        throw new RuntimeException("Falha ao preparar consulta.");
    }

    mysqli_stmt_bind_param($stmt, "i", $categoria);

    if (
        !mysqli_stmt_execute($stmt) ||
        !mysqli_stmt_store_result($stmt)
    ) {
        throw new RuntimeException("Falha ao consultar categoria.");
    }

    $categoriaExiste = mysqli_stmt_num_rows($stmt) > 0;

    mysqli_stmt_close($stmt);

    if (!$categoriaExiste) {
        retornar("A categoria selecionada não existe.");
    }

    // Cria a pasta de uploads se necessário.
    $pasta = __DIR__ . "/uploads/";

    if (
        !is_dir($pasta) &&
        !mkdir($pasta, 0755, true) &&
        !is_dir($pasta)
    ) {
        throw new RuntimeException(
            "Não foi possível criar a pasta de uploads."
        );
    }

    // Gera um nome aleatório para não reutilizar o nome original.
    $extensao = $tiposPermitidos[$tipoArquivo];

    do {
        $nomeArquivo = bin2hex(random_bytes(16)) . "." . $extensao;
        $caminhoCompleto = $pasta . $nomeArquivo;
    } while (file_exists($caminhoCompleto));

    $caminho = "uploads/" . $nomeArquivo;

    // Move o arquivo temporário para a pasta definitiva.
    if (!move_uploaded_file($arquivo["tmp_name"], $caminhoCompleto)) {
        throw new RuntimeException("Não foi possível mover o arquivo.");
    }

    $arquivoMovido = true;

    // Cadastra usando parâmetros.
    $stmt = mysqli_prepare(
        $conexao,
        "INSERT INTO documentos (nome, id_categoria, caminho)
         VALUES (?, ?, ?)"
    );

    if (!$stmt) {
        throw new RuntimeException("Falha ao preparar cadastro.");
    }

    mysqli_stmt_bind_param(
        $stmt,
        "sis",
        $nome,
        $categoria,
        $caminho
    );

    if (!mysqli_stmt_execute($stmt)) {
        throw new RuntimeException("Falha ao cadastrar documento.");
    }

    $cadastrado = true;

    mysqli_stmt_close($stmt);

} catch (Throwable $erro) {
    // Se o cadastro falhou, tenta remover o arquivo recém-enviado.
    if ($arquivoMovido && !$cadastrado && $caminhoCompleto !== null) {
        if (!unlink($caminhoCompleto)) {
            error_log(
                "Não foi possível remover upload: " . $caminhoCompleto
            );
        }
    }

    error_log($erro->getMessage());

    retornar("Não foi possível cadastrar o documento. Tente novamente.");
}

retornar("Documento cadastrado com sucesso!", "sucesso");