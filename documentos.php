<?php

require_once __DIR__ . '/painel/conexao.php';

function escaparDocumento(string $texto): string
{
    return htmlspecialchars(
        $texto,
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );
}

function parametroDocumento(string $nome): string
{
    $valor = $_GET[$nome] ?? '';

    if (!is_string($valor)) {
        http_response_code(400);
        exit('Filtro inválido. Volte à página de documentos.');
    }

    return trim($valor);
}

// Recebe e valida os filtros.
$busca = parametroDocumento('busca');
$categoriaRecebida = parametroDocumento('categoria');
$tipo = parametroDocumento('tipo');
$ordem = parametroDocumento('ordem');
$paginaRecebida = parametroDocumento('pagina');

if (
    !mb_check_encoding($busca, 'UTF-8')
    || mb_strlen($busca, 'UTF-8') > 255
) {
    http_response_code(400);
    exit('A pesquisa deve conter até 255 caracteres válidos.');
}

$categoria = null;

if ($categoriaRecebida !== '') {
    $categoria = filter_var(
        $categoriaRecebida,
        FILTER_VALIDATE_INT,
        ['options' => ['min_range' => 1]]
    );

    if ($categoria === false) {
        http_response_code(400);
        exit('Categoria inválida.');
    }
}

$tiposPermitidos = [
    '' => 'Todos os formatos',
    'pdf' => 'PDF',
    'jpg' => 'JPG',
    'png' => 'PNG'
];

if (!array_key_exists($tipo, $tiposPermitidos)) {
    http_response_code(400);
    exit('Formato inválido.');
}

// Apenas estas expressões fixas podem entrar no ORDER BY.
$ordenacoes = [
    'recentes' => 'd.data_upload DESC, d.id_documento DESC',
    'antigos' => 'd.data_upload ASC, d.id_documento ASC',
    'nome' => 'd.nome ASC, d.id_documento ASC'
];

if ($ordem === '') {
    $ordem = 'recentes';
}

if (!isset($ordenacoes[$ordem])) {
    http_response_code(400);
    exit('Ordenação inválida.');
}

$pagina = 1;

if ($paginaRecebida !== '') {
    $pagina = filter_var(
        $paginaRecebida,
        FILTER_VALIDATE_INT,
        ['options' => ['min_range' => 1]]
    );

    if ($pagina === false) {
        http_response_code(400);
        exit('Página inválida.');
    }
}

// Monta somente condições fixas.
// Os valores recebidos são enviados por parâmetros.
$condicoes = [];
$parametros = [];
$tipos = '';

if ($busca !== '') {
    // Trata %, _ e = como caracteres literais na pesquisa.
    $buscaLiteral = strtr($busca, [
        '=' => '==',
        '%' => '=%',
        '_' => '=_'
    ]);

    $condicoes[] = "d.nome LIKE ? ESCAPE '='";
    $parametros[] = '%' . $buscaLiteral . '%';
    $tipos .= 's';
}

if ($categoria !== null) {
    $condicoes[] = 'd.id_categoria = ?';
    $parametros[] = $categoria;
    $tipos .= 'i';
}

// O filtro usa a extensão armazenada.
// A validação real do conteúdo continua nos endpoints de arquivos.
if ($tipo === 'jpg') {
    $condicoes[] =
        "LOWER(SUBSTRING_INDEX(d.caminho, '.', -1)) IN (?, ?)";

    $parametros[] = 'jpg';
    $parametros[] = 'jpeg';
    $tipos .= 'ss';

} elseif ($tipo !== '') {
    $condicoes[] =
        "LOWER(SUBSTRING_INDEX(d.caminho, '.', -1)) = ?";

    $parametros[] = $tipo;
    $tipos .= 's';
}

$where = $condicoes
    ? ' WHERE ' . implode(' AND ', $condicoes)
    : '';

$porPagina = 12;

try {
    $resultadoCategorias = mysqli_query(
        $conexao,
        'SELECT id_categoria, nome_categoria
         FROM categorias
         ORDER BY nome_categoria, id_categoria'
    );

    $categorias = mysqli_fetch_all(
        $resultadoCategorias,
        MYSQLI_ASSOC
    );

    mysqli_free_result($resultadoCategorias);

    // Confere se a categoria recebida ainda existe.
    if ($categoria !== null) {
        $categoriaExiste = false;

        foreach ($categorias as $item) {
            if ((int) $item['id_categoria'] === $categoria) {
                $categoriaExiste = true;
                break;
            }
        }

        if (!$categoriaExiste) {
            http_response_code(404);
            exit('Categoria não encontrada.');
        }
    }

    $baseConsulta = '
        FROM documentos d
        INNER JOIN categorias c
            ON c.id_categoria = d.id_categoria
    ';

    // Conta os resultados dos filtros.
    $stmt = mysqli_prepare(
        $conexao,
        'SELECT COUNT(*) ' . $baseConsulta . $where
    );

    if ($tipos !== '') {
        mysqli_stmt_bind_param($stmt, $tipos, ...$parametros);
    }

    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $total);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    $total = (int) $total;
    $totalPaginas = max(1, (int) ceil($total / $porPagina));
    $pagina = min($pagina, $totalPaginas);
    $offset = ($pagina - 1) * $porPagina;

    // Busca apenas os documentos da página atual.
    $sql = '
        SELECT
            d.id_documento,
            d.nome,
            d.caminho,
            d.data_upload,
            c.nome_categoria
    ' . $baseConsulta . $where
      . ' ORDER BY ' . $ordenacoes[$ordem]
      . ' LIMIT ? OFFSET ?';

    $parametrosPagina = $parametros;
    $parametrosPagina[] = $porPagina;
    $parametrosPagina[] = $offset;

    $tiposPagina = $tipos . 'ii';

    $stmt = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param(
        $stmt,
        $tiposPagina,
        ...$parametrosPagina
    );

    mysqli_stmt_execute($stmt);

    $documentos = mysqli_fetch_all(
        mysqli_stmt_get_result($stmt),
        MYSQLI_ASSOC
    );

    mysqli_stmt_close($stmt);

} catch (Throwable $erro) {
    error_log('Falha na consulta pública de documentos: ' . $erro->getMessage());

    http_response_code(503);
    exit('Não foi possível consultar os documentos. Tente novamente.');
}

// Mantém os filtros ao navegar entre páginas.
$filtrosPagina = [
    'busca' => $busca,
    'categoria' => $categoria === null ? '' : (string) $categoria,
    'tipo' => $tipo,
    'ordem' => $ordem
];

function urlPaginaDocumento(int $numero, array $filtros): string
{
    $filtros['pagina'] = $numero;

    return 'documentos.php?' . http_build_query($filtros);
}

$inicioExibicao = $total > 0 ? $offset + 1 : 0;
$fimExibicao = min($offset + $porPagina, $total);

$temFiltros = (
    $busca !== ''
    || $categoria !== null
    || $tipo !== ''
    || $ordem !== 'recentes'
);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Documentos — CEEDO</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        rel="stylesheet"
    >
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/rodape.css">
</head>

<body class="pagina-documentos">
    <?php include __DIR__ . '/includes/navbar.php'; ?>

    <main class="container py-4 py-lg-5">
        <a href="index.php" class="docs-voltar">
            <i class="bi bi-arrow-left" aria-hidden="true"></i>
            Voltar ao início
        </a>

        <header class="docs-cabecalho">
            <div>
                <p class="docs-sobretitulo">ACERVO PÚBLICO</p>
                <h1>Documentos da instituição</h1>
                <p class="text-muted mb-0">
                    Pesquise, consulte e baixe os arquivos publicados.
                </p>
            </div>

            <span class="docs-contagem">
                <?= $total ?>
                <?= $total === 1 ? 'documento encontrado' : 'documentos encontrados' ?>
            </span>
        </header>

        <form
            action="documentos.php"
            method="GET"
            class="docs-filtros"
            role="search"
            aria-label="Pesquisar documentos"
        >
            <div class="row g-3">
                <div class="col-12 col-lg-5">
                    <label for="busca" class="form-label">
                        Nome do documento
                    </label>

                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-search" aria-hidden="true"></i>
                        </span>

                        <input
                            type="search"
                            id="busca"
                            name="busca"
                            class="form-control"
                            maxlength="255"
                            placeholder="Digite o nome ou parte dele"
                            value="<?= escaparDocumento($busca) ?>"
                        >
                    </div>
                </div>

                <div class="col-12 col-md-6 col-lg-3">
                    <label for="categoria" class="form-label">
                        Categoria
                    </label>

                    <select
                        id="categoria"
                        name="categoria"
                        class="form-select"
                    >
                        <option value="">Todas as categorias</option>

                        <?php foreach ($categorias as $item): ?>
                            <option
                                value="<?= (int) $item['id_categoria'] ?>"
                                <?= $categoria === (int) $item['id_categoria']
                                    ? 'selected'
                                    : '' ?>
                            >
                                <?= escaparDocumento($item['nome_categoria']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-6 col-md-3 col-lg-2">
                    <label for="tipo" class="form-label">
                        Formato
                    </label>

                    <select id="tipo" name="tipo" class="form-select">
                        <?php foreach ($tiposPermitidos as $valor => $rotulo): ?>
                            <option
                                value="<?= escaparDocumento($valor) ?>"
                                <?= $tipo === $valor ? 'selected' : '' ?>
                            >
                                <?= escaparDocumento($rotulo) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-6 col-md-3 col-lg-2">
                    <label for="ordem" class="form-label">
                        Ordenar por
                    </label>

                    <select id="ordem" name="ordem" class="form-select">
                        <option
                            value="recentes"
                            <?= $ordem === 'recentes' ? 'selected' : '' ?>
                        >Mais recentes</option>

                        <option
                            value="antigos"
                            <?= $ordem === 'antigos' ? 'selected' : '' ?>
                        >Mais antigos</option>

                        <option
                            value="nome"
                            <?= $ordem === 'nome' ? 'selected' : '' ?>
                        >Nome: A–Z</option>
                    </select>
                </div>

                <div class="col-12 d-flex flex-wrap gap-2">
                    <button type="submit" class="btn docs-btn-principal">
                        <i class="bi bi-search" aria-hidden="true"></i>
                        Pesquisar
                    </button>

                    <?php if ($temFiltros): ?>
                        <a href="documentos.php" class="btn btn-outline-secondary">
                            <i class="bi bi-x-lg" aria-hidden="true"></i>
                            Limpar filtros
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </form>

        <section class="docs-resultados" aria-labelledby="titulo-resultados">
            <div class="docs-resultados-topo">
                <h2 id="titulo-resultados">Resultados</h2>

                <?php if ($total > 0): ?>
                    <span>
                        Mostrando <?= $inicioExibicao ?>–<?= $fimExibicao ?>
                        de <?= $total ?>
                    </span>
                <?php endif; ?>
            </div>

            <?php if (!$documentos): ?>
                <div class="docs-vazio">
                    <i class="bi bi-search" aria-hidden="true"></i>
                    <h3>Nenhum documento encontrado</h3>
                    <p>
                        <?= $temFiltros
                            ? 'Tente outro nome ou remova os filtros.'
                            : 'Os documentos publicados aparecerão aqui.' ?>
                    </p>

                    <?php if ($temFiltros): ?>
                        <a href="documentos.php" class="btn docs-btn-principal">
                            Ver todos os documentos
                        </a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="row g-3">
                    <?php foreach ($documentos as $documento): ?>
                        <?php
                        $extensao = strtolower(
                            pathinfo($documento['caminho'], PATHINFO_EXTENSION)
                        );

                        if ($extensao === 'jpeg') {
                            $extensao = 'jpg';
                        }

                        $formato = match ($extensao) {
                            'pdf' => 'PDF',
                            'jpg' => 'JPG',
                            'png' => 'PNG',
                            default => 'Arquivo'
                        };

                        $icone = in_array($extensao, ['jpg', 'png'], true)
                            ? 'bi-file-earmark-image'
                            : 'bi-file-earmark-text';

                        $idDocumento = (int) $documento['id_documento'];
                        ?>

                        <div class="col-12 col-md-6 col-xl-4">
                            <article class="docs-item">
                                <div class="docs-item-topo">
                                    <span class="docs-icone">
                                        <i
                                            class="bi <?= $icone ?>"
                                            aria-hidden="true"
                                        ></i>
                                    </span>

                                    <span class="docs-formato">
                                        <?= $formato ?>
                                    </span>
                                </div>

                                <h3>
                                    <?= escaparDocumento($documento['nome']) ?>
                                </h3>

                                <p class="docs-categoria">
                                    <i class="bi bi-folder2" aria-hidden="true"></i>
                                    <?= escaparDocumento($documento['nome_categoria']) ?>
                                </p>

                                <p class="docs-data">
                                    <i class="bi bi-calendar3" aria-hidden="true"></i>
                                    Publicado em
                                    <?= date(
                                        'd/m/Y',
                                        strtotime($documento['data_upload'])
                                    ) ?>
                                </p>

                                <div class="docs-item-acoes">
                                    <a
                                        href="visualizar.php?id=<?= $idDocumento ?>"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="btn docs-btn-principal"
                                        aria-label="<?= escaparDocumento(
                                            'Visualizar ' . $documento['nome']
                                            . ' em nova aba'
                                        ) ?>"
                                    >
                                        <i class="bi bi-eye" aria-hidden="true"></i>
                                        Visualizar
                                    </a>

                                    <a
                                        href="download.php?id=<?= $idDocumento ?>"
                                        class="btn docs-btn-secundario"
                                        aria-label="<?= escaparDocumento(
                                            'Baixar ' . $documento['nome']
                                        ) ?>"
                                    >
                                        <i class="bi bi-download" aria-hidden="true"></i>
                                        Baixar
                                    </a>
                                </div>
                            </article>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if ($totalPaginas > 1): ?>
                <nav class="docs-paginacao" aria-label="Páginas de documentos">
                    <?php if ($pagina > 1): ?>
                        <a
                            class="btn docs-btn-secundario"
                            href="<?= escaparDocumento(
                                urlPaginaDocumento($pagina - 1, $filtrosPagina)
                            ) ?>"
                        >
                            <i class="bi bi-chevron-left" aria-hidden="true"></i>
                            Anterior
                        </a>
                    <?php endif; ?>

                    <span>
                        Página <?= $pagina ?> de <?= $totalPaginas ?>
                    </span>

                    <?php if ($pagina < $totalPaginas): ?>
                        <a
                            class="btn docs-btn-secundario"
                            href="<?= escaparDocumento(
                                urlPaginaDocumento($pagina + 1, $filtrosPagina)
                            ) ?>"
                        >
                            Próxima
                            <i class="bi bi-chevron-right" aria-hidden="true"></i>
                        </a>
                    <?php endif; ?>
                </nav>
            <?php endif; ?>
        </section>
    </main>

    <?php require __DIR__ . '/includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>