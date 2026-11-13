<?php
require_once __DIR__ . '/painel/conexao.php';
require_once __DIR__ . '/includes/icones_categorias.php';

$categorias = mysqli_query(
    $conexao,
    'SELECT id_categoria, nome_categoria, info_categoria, icone
     FROM categorias
     ORDER BY nome_categoria ASC'
);

$iconesPermitidos = iconesCategorias();

$escaparCategorias = static function (string $texto): string {
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

    <title>Categorias — AcervoTech</title>

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
                <i class="bi bi-tags" aria-hidden="true"></i>
            </span>

            <div>
                <p class="categoria-sobretitulo">Explore o acervo</p>
                <h1>Categorias</h1>
                <p class="categoria-resumo">
                    Escolha uma categoria para consultar os documentos da escola.
                </p>
            </div>
        </div>

        <a href="documentos.php" class="btn btn-outline-primary">
            <i class="bi bi-search" aria-hidden="true"></i>
            Todos os documentos
        </a>
    </header>

    <?php if (mysqli_num_rows($categorias) > 0): ?>
        <div class="row g-4">
            <?php while ($categoria = mysqli_fetch_assoc($categorias)): ?>
                <?php
                $iconeSalvo = $categoria['icone'] ?? '';

                $icone = (
                    is_string($iconeSalvo)
                    && isset($iconesPermitidos[$iconeSalvo])
                ) ? $iconeSalvo : 'bi-folder';

                $descricao = trim($categoria['info_categoria'] ?? '');

                if ($descricao === '') {
                    $descricao = 'Consulte os documentos disponíveis nesta categoria.';
                }
                ?>

                <div class="col-12 col-md-6 col-lg-4">
                    <a
                        href="categoria.php?id=<?= (int) $categoria['id_categoria'] ?>"
                        class="categoria-atalho"
                    >
                        <article class="card categoria-card h-100">
                            <div class="card-body">
                                <i
                                    class="bi <?= $escaparCategorias($icone) ?>"
                                    aria-hidden="true"
                                ></i>

                                <h2 class="categoria-card-titulo">
                                    <?= $escaparCategorias($categoria['nome_categoria']) ?>
                                </h2>

                                <p>
                                    <?= $escaparCategorias($descricao) ?>
                                </p>

                                <span class="categoria-card-link">
                                    Ver documentos
                                    <i class="bi bi-arrow-right" aria-hidden="true"></i>
                                </span>
                            </div>
                        </article>
                    </a>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="categoria-vazia">
            <i class="bi bi-folder2-open" aria-hidden="true"></i>
            <h2>Nenhuma categoria disponível</h2>
            <p>As categorias aparecerão aqui quando forem cadastradas.</p>
        </div>
    <?php endif; ?>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>