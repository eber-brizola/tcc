<?php
require_once __DIR__ . '/painel/conexao.php';
require_once __DIR__ . '/includes/icones_categorias.php';

$idRecebido = $_GET['id'] ?? null;

$idCategoria = is_string($idRecebido)
    ? filter_var(
        $idRecebido,
        FILTER_VALIDATE_INT,
        ['options' => ['min_range' => 1]]
    )
    : false;

if ($idCategoria === false) {
    header('Location: categorias.php', true, 303);
    exit;
}

$stmtCategoria = mysqli_prepare(
    $conexao,
    'SELECT id_categoria, nome_categoria, info_categoria, icone
     FROM categorias
     WHERE id_categoria = ?
     LIMIT 1'
);

mysqli_stmt_bind_param($stmtCategoria, 'i', $idCategoria);
mysqli_stmt_execute($stmtCategoria);

$resultadoCategoria = mysqli_stmt_get_result($stmtCategoria);
$categoria = mysqli_fetch_assoc($resultadoCategoria);

mysqli_stmt_close($stmtCategoria);

if (!$categoria) {
    header('Location: categorias.php', true, 303);
    exit;
}

$stmtDocumentos = mysqli_prepare(
    $conexao,
    'SELECT id_documento, nome, data_upload
     FROM documentos
     WHERE id_categoria = ?
     ORDER BY data_upload DESC, id_documento DESC'
);

mysqli_stmt_bind_param($stmtDocumentos, 'i', $idCategoria);
mysqli_stmt_execute($stmtDocumentos);

$documentos = mysqli_stmt_get_result($stmtDocumentos);
$totalDocumentos = mysqli_num_rows($documentos);

mysqli_stmt_close($stmtDocumentos);

$iconesPermitidos = iconesCategorias();
$iconeSalvo = $categoria['icone'] ?? '';

$iconeCategoria = (
    is_string($iconeSalvo)
    && isset($iconesPermitidos[$iconeSalvo])
) ? $iconeSalvo : 'bi-folder';

$descricaoCategoria = trim($categoria['info_categoria'] ?? '');

if ($descricaoCategoria === '') {
    $descricaoCategoria = 'Consulte os documentos disponíveis nesta categoria.';
}

$escaparCategoria = static function (string $texto): string {
    return htmlspecialchars(
        $texto,
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );
};
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= $escaparCategoria($categoria['nome_categoria']) ?> — AcervoTech</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/rodape.css">
    <link rel="stylesheet" href="assets/css/visual.css">
</head>

<body class="pagina-categorias-portal">

<?php include __DIR__ . '/includes/navbar.php'; ?>

<main class="container py-5">
    <header class="categoria-cabecalho">
        <div class="categoria-apresentacao">
            <span class="categoria-emblema">
                <i
                    class="bi <?= $escaparCategoria($iconeCategoria) ?>"
                    aria-hidden="true"
                ></i>
            </span>

            <div>
                <p class="categoria-sobretitulo">Acervo por categoria</p>

                <h1>
                    <?= $escaparCategoria($categoria['nome_categoria']) ?>
                </h1>

                <p class="categoria-resumo">
                    <?= $escaparCategoria($descricaoCategoria) ?>
                </p>
            </div>
        </div>

        <div class="categoria-cabecalho-acoes">
            <a href="categorias.php" class="btn btn-outline-primary">
                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                Categorias
            </a>

            <a
                href="documentos.php?categoria=<?= (int) $idCategoria ?>"
                class="btn btn-primary"
            >
                <i class="bi bi-search" aria-hidden="true"></i>
                Pesquisar nesta categoria
            </a>
        </div>
    </header>

    <section
        class="documentos-recentes categoria-documentos"
        aria-labelledby="titulo-documentos"
    >
        <div class="documentos-cabecalho">
            <div>
                <h2 id="titulo-documentos">
                    <i class="bi bi-files" aria-hidden="true"></i>
                    Documentos publicados
                </h2>

                <p>
                    <?= $totalDocumentos ?>
                    <?= $totalDocumentos === 1
                        ? 'documento disponível.'
                        : 'documentos disponíveis.' ?>
                </p>
            </div>
        </div>

        <div class="documentos-bloco">
            <?php if ($totalDocumentos > 0): ?>
                <div
                    class="table-responsive"
                    role="region"
                    aria-label="Documentos da categoria"
                    tabindex="0"
                >
                    <table class="table tabela-recentes">
                        <caption class="visually-hidden">
                            Documentos de
                            <?= $escaparCategoria($categoria['nome_categoria']) ?>
                        </caption>

                        <thead>
                            <tr>
                                <th scope="col">Documento</th>
                                <th scope="col">Publicação</th>
                                <th scope="col" class="coluna-acoes">Ações</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php while ($documento = mysqli_fetch_assoc($documentos)): ?>
                                <?php
                                $dataDocumento = strtotime(
                                    $documento['data_upload'] ?? ''
                                );
                                ?>

                                <tr>
                                    <td>
                                        <div class="documento-identificacao">
                                            <span class="documento-simbolo">
                                                <i
                                                    class="bi bi-file-earmark-text"
                                                    aria-hidden="true"
                                                ></i>
                                            </span>

                                            <span class="documento-nome">
                                                <?= $escaparCategoria($documento['nome']) ?>
                                            </span>
                                        </div>
                                    </td>

                                    <td class="documento-data">
                                        <?= $dataDocumento !== false
                                            ? date('d/m/Y', $dataDocumento)
                                            : 'Não informada' ?>
                                    </td>

                                    <td>
                                        <div class="documento-acoes">
                                            <a
                                                href="visualizar.php?id=<?= (int) $documento['id_documento'] ?>"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                class="btn btn-primary btn-sm"
                                            >
                                                <i class="bi bi-eye" aria-hidden="true"></i>
                                                Visualizar
                                                <span class="visually-hidden">
                                                    <?= $escaparCategoria($documento['nome']) ?>
                                                    — abre em nova aba
                                                </span>
                                            </a>

                                            <a
                                                href="download.php?id=<?= (int) $documento['id_documento'] ?>"
                                                class="btn btn-outline-primary btn-sm"
                                            >
                                                <i class="bi bi-download" aria-hidden="true"></i>
                                                Download
                                                <span class="visually-hidden">
                                                    <?= $escaparCategoria($documento['nome']) ?>
                                                </span>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="categoria-vazia">
                    <i class="bi bi-folder2-open" aria-hidden="true"></i>
                    <h2>Nenhum documento publicado</h2>
                    <p>
                        Os documentos desta categoria aparecerão aqui
                        quando forem disponibilizados.
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>