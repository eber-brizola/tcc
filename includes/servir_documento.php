<?php

// O modo é definido pelo arquivo que inclui este código.
if (
    !isset($modoDocumento) ||
    !in_array($modoDocumento, ["inline", "attachment"], true)
) {
    http_response_code(404);
    exit;
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

function erroDocumento($mensagem, $status)
{
    http_response_code($status);
    header("Content-Type: text/plain; charset=UTF-8");
    exit($mensagem);
}

// Valida o ID recebido pela URL.
$idDocumento = filter_var(
    $_GET["id"] ?? null,
    FILTER_VALIDATE_INT,
    ["options" => ["min_range" => 1]]
);

if (!$idDocumento) {
    erroDocumento("Documento inválido.", 400);
}

// Busca os dados do documento.
try {
    require_once __DIR__ . "/../painel/conexao.php";

    $stmt = mysqli_prepare(
        $conexao,
        "SELECT nome, caminho
         FROM documentos
         WHERE id_documento = ?"
    );

    mysqli_stmt_bind_param($stmt, "i", $idDocumento);
    mysqli_stmt_execute($stmt);

    $resultado = mysqli_stmt_get_result($stmt);
    $documento = mysqli_fetch_assoc($resultado);

    mysqli_stmt_close($stmt);

} catch (Throwable $erro) {
    error_log($erro->getMessage());
    erroDocumento("Não foi possível consultar o documento.", 500);
}

if (!$documento) {
    erroDocumento("Documento não encontrado.", 404);
}

// Confere o caminho armazenado no banco.
$caminhoRelativo = $documento["caminho"];

if (
    !is_string($caminhoRelativo) ||
    $caminhoRelativo === "" ||
    str_contains($caminhoRelativo, "\0")
) {
    erroDocumento("Caminho de arquivo inválido.", 404);
}

$pastaUploads = realpath(__DIR__ . "/../painel/uploads");

$caminhoArquivo = realpath(
    __DIR__ . "/../painel/" . $caminhoRelativo
);

// O arquivo precisa estar dentro da pasta real de uploads.
if (
    $pastaUploads === false ||
    $caminhoArquivo === false ||
    !str_starts_with(
        $caminhoArquivo,
        $pastaUploads . DIRECTORY_SEPARATOR
    ) ||
    !is_file($caminhoArquivo) ||
    !is_readable($caminhoArquivo)
) {
    erroDocumento("Arquivo não encontrado ou indisponível.", 404);
}

// Identifica o formato pelo conteúdo do arquivo.
try {
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $tipoArquivo = $finfo->file($caminhoArquivo);

} catch (Throwable $erro) {
    error_log($erro->getMessage());
    erroDocumento("Não foi possível identificar o arquivo.", 500);
}

$tiposPermitidos = [
    "application/pdf" => "pdf",
    "image/jpeg" => "jpg",
    "image/png" => "png"
];

if (
    !is_string($tipoArquivo) ||
    !isset($tiposPermitidos[$tipoArquivo])
) {
    erroDocumento("Formato de arquivo não permitido.", 415);
}

$extensao = $tiposPermitidos[$tipoArquivo];

// Usa o título do documento como nome amigável.
$nomeBase = trim($documento["nome"]);

$nomeBase = str_replace(
    ["\\", "/", ":", "*", "?", '"', "<", ">", "|"],
    "_",
    $nomeBase
);

// Remove caracteres de controle e extensão já presente.
$nomeBase = preg_replace('/[\x00-\x1F\x7F]/', "", $nomeBase);
$nomeBase = preg_replace('/\.(pdf|jpe?g|png)$/i', "", $nomeBase);
$nomeBase = trim($nomeBase, " .");

if ($nomeBase === "") {
    $nomeBase = "documento";
}

$nomeBase = mb_strcut($nomeBase, 0, 150, "UTF-8");
$nomeFinal = $nomeBase . "." . $extensao;

$nomeAlternativo = "documento-" . $idDocumento . "." . $extensao;

// Abre antes de enviar a resposta de sucesso.
$arquivo = fopen($caminhoArquivo, "rb");

if ($arquivo === false) {
    erroDocumento("Não foi possível abrir o arquivo.", 500);
}

$informacoes = fstat($arquivo);

if ($informacoes === false) {
    fclose($arquivo);
    erroDocumento("Não foi possível consultar o arquivo.", 500);
}

// Download força o recebimento como arquivo.
// Visualização informa o formato real ao navegador.
$tipoResposta = $modoDocumento === "attachment"
    ? "application/octet-stream"
    : $tipoArquivo;

header("Content-Type: " . $tipoResposta);
header("X-Content-Type-Options: nosniff");

header(
    "Content-Disposition: " . $modoDocumento
    . '; filename="' . $nomeAlternativo . '"; '
    . "filename*=UTF-8''" . rawurlencode($nomeFinal)
);

header("Content-Length: " . $informacoes["size"]);

// Envia o conteúdo do arquivo.
fpassthru($arquivo);
fclose($arquivo);
exit;