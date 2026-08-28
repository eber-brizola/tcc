<?php
require_once "painel/conexao.php";

if (!isset($_GET["id"]) || !filter_var($_GET["id"], FILTER_VALIDATE_INT)) {
    header("Location: index.php");
    exit;
}

$idDocumento = (int) $_GET["id"];

$sql = "SELECT nome, caminho
        FROM documentos
        WHERE id_documento = ?
        LIMIT 1";

$stmt = mysqli_prepare($conexao, $sql);
mysqli_stmt_bind_param($stmt, "i", $idDocumento);
mysqli_stmt_execute($stmt);

$resultado = mysqli_stmt_get_result($stmt);
$documento = mysqli_fetch_assoc($resultado);

if (!$documento) {
    header("Location: index.php");
    exit;
}

$caminhoRelativo = $documento["caminho"];

if (!str_starts_with($caminhoRelativo, "uploads/")) {
    exit("Arquivo inválido.");
}

$caminhoArquivo = __DIR__ . "/painel/" . $caminhoRelativo;

if (!file_exists($caminhoArquivo)) {
    exit("Arquivo não encontrado.");
}

$tipoArquivo = mime_content_type($caminhoArquivo);

header("Content-Type: " . $tipoArquivo);
header('Content-Disposition: inline; filename="' . basename($documento["nome"]) . '"');
header("Content-Length: " . filesize($caminhoArquivo));

readfile($caminhoArquivo);
exit;