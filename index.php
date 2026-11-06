<?php

require_once __DIR__ . '/painel/conexao.php';
require_once __DIR__ . '/includes/icones_categorias.php';

$iconesPermitidos = iconesCategorias();

$sqlCategorias = "SELECT id_categoria,
                         nome_categoria,
                         info_categoria,
                         icone
                  FROM categorias
                  ORDER BY nome_categoria ASC";

$categorias = mysqli_query($conexao, $sqlCategorias);

$sqlDocumentos = "SELECT documentos.id_documento,
                         documentos.nome,
                         documentos.caminho,
                         documentos.data_upload,
                         categorias.nome_categoria
                  FROM documentos
                  INNER JOIN categorias
                      ON documentos.id_categoria = categorias.id_categoria
                  ORDER BY documentos.data_upload DESC
                  LIMIT 10";

$ultimosDocumentos = mysqli_query($conexao, $sqlDocumentos);

// Cores e descrições padrão.
// O ícone agora vem da escolha salva no banco.
$estilos = [
    'atas' => [
        'text-success',
        'Atas de reuniões e conselhos.'
    ],
    'leis' => [
        'text-danger',
        'Leis, normas e resoluções.'
    ],
    'regimentos' => [
        'text-warning',
        'Regulamentos internos.'
    ],
    'trabalhos' => [
        'text-primary',
        'Trabalhos e atividades escolares.'
    ],
    'projetos' => [
        'text-warning',
        'Projetos desenvolvidos pela instituição.'
    ],
    'editais' => [
        'text-danger',
        'Editais e chamadas públicas.'
    ],
    'calendários' => [
        'text-primary',
        'Datas e eventos do calendário escolar.'
    ],
    'relatórios' => [
        'text-success',
        'Relatórios e resultados da instituição.'
    ],
    'comunicados' => [
        'text-primary',
        'Avisos e comunicados da escola.'
    ],
    'contratos' => [
        'text-secondary',
        'Contratos e acordos institucionais.'
    ],
    'prestações de contas' => [
        'text-success',
        'Demonstrativos e prestação de contas.'
    ],
    'cardápios' => [
        'text-danger',
        'Cardápios da alimentação escolar.'
    ],
    'horários' => [
        'text-primary',
        'Horários de aulas e atividades.'
    ],
    'eventos' => [
        'text-success',
        'Eventos e atividades escolares.'
    ],
    'manuais' => [
        'text-secondary',
        'Manuais e orientações.'
    ]
];
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>CEEDO - Portal de Transparência Escolar</title>

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
    <link rel="stylesheet" href="assets/css/visual.css">
</head>

<body>

<?php include __DIR__ . '/includes/navbar.php'; ?>

<main class="container">

    <!-- Banner -->
    <div class="hero">
        <h1>Portal de Transparência Escolar</h1>

        <p class="lead mt-3">
            Consulte documentos, atas, leis, regimentos e trabalhos
            disponibilizados pela instituição.
        </p>

        <a href="documentos.php" class="btn btn-light btn-lg mt-3">
            <i class="bi bi-search" aria-hidden="true"></i>
            Ver documentos
        </a>
    </div>

    <!-- Categorias -->
    <section class="mt-5">
        <h2 class="text-center mb-4">Categorias</h2>

        <div class="row g-4 justify-content-center">

            <?php if (mysqli_num_rows($categorias) > 0): ?>

                <?php while ($categoria = mysqli_fetch_assoc($categorias)): ?>
                    <?php
                    $nomeOriginal = trim($categoria['nome_categoria']);

                    $nomeExibicao =
                        mb_strtoupper(
                            mb_substr($nomeOriginal, 0, 1, 'UTF-8'),
                            'UTF-8'
                        )
                        . mb_substr($nomeOriginal, 1, null, 'UTF-8');

                    $nomeCategoria = mb_strtolower(
                        $nomeOriginal,
                        'UTF-8'
                    );

                    [$cor, $descricaoPadrao] = $estilos[$nomeCategoria] ?? [
                        'text-primary',
                        'Documentos disponíveis nesta categoria.'
                    ];

                    // Só exibe ícones presentes na lista permitida.
                    $iconeSalvo = $categoria['icone'] ?? 'bi-folder';

                    $icone = (
                        is_string($iconeSalvo)
                        && isset($iconesPermitidos[$iconeSalvo])
                    ) ? $iconeSalvo : 'bi-folder';

                    $descricaoExibida = trim(
                        $categoria['info_categoria'] ?? ''
                    );

                    if ($descricaoExibida === '') {
                        $descricaoExibida = $descricaoPadrao;
                    }
                    ?>

                    <div class="col-md-6 col-lg-3">
                        <a
                            href="categoria.php?id=<?= (int) $categoria['id_categoria'] ?>"
                            class="text-decoration-none text-dark"
                        >
                            <div class="card shadow-sm h-100 categoria-card">
                                <div class="card-body text-center">

                                    <i
                                        class="bi <?= htmlspecialchars($icone, ENT_QUOTES, 'UTF-8') ?> fs-1 <?= htmlspecialchars($cor, ENT_QUOTES, 'UTF-8') ?>"
                                        aria-hidden="true"
                                    ></i>

                                    <h4 class="mt-3">
                                        <?= htmlspecialchars($nomeExibicao, ENT_QUOTES, 'UTF-8') ?>
                                    </h4>

                                    <p class="text-muted">
                                        <?= htmlspecialchars($descricaoExibida, ENT_QUOTES, 'UTF-8') ?>
                                    </p>

                                </div>
                            </div>
                        </a>
                    </div>

                <?php endwhile; ?>

            <?php else: ?>

                <div class="col-12">
                    <p class="text-center text-muted">
                        Nenhuma categoria cadastrada ainda.
                    </p>
                </div>

            <?php endif; ?>

        </div>
    </section>

    <!-- Últimos documentos -->
    <section id="documentos" class="mt-5 mb-5">
        <h2 class="text-center mb-4">
            Últimos Documentos Publicados
        </h2>

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-primary">
                    <tr>
                        <th scope="col">Documento</th>
                        <th scope="col">Categoria</th>
                        <th scope="col">Data</th>
                        <th scope="col">Ações</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (mysqli_num_rows($ultimosDocumentos) > 0): ?>

                        <?php while ($documento = mysqli_fetch_assoc($ultimosDocumentos)): ?>
                            <tr>
                                <td>
                                    <?= htmlspecialchars($documento['nome'], ENT_QUOTES, 'UTF-8') ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($documento['nome_categoria'], ENT_QUOTES, 'UTF-8') ?>
                                </td>

                                <td>
                                    <?= date('d/m/Y', strtotime($documento['data_upload'])) ?>
                                </td>

                                <td>
                                    <a
                                        href="visualizar.php?id=<?= (int) $documento['id_documento'] ?>"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="btn btn-primary btn-sm"
                                    >
                                        <i class="bi bi-eye" aria-hidden="true"></i>
                                        Visualizar
                                    </a>

                                    <a
                                        href="download.php?id=<?= (int) $documento['id_documento'] ?>"
                                        class="btn btn-outline-primary btn-sm"
                                    >
                                        <i class="bi bi-download" aria-hidden="true"></i>
                                        Download
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>

                    <?php else: ?>

                        <tr>
                            <td colspan="4" class="text-center text-muted">
                                Nenhum documento publicado ainda.
                            </td>
                        </tr>

                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

</main>

<?php require __DIR__ . '/includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>