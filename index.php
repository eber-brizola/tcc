<?php
require_once "painel/conexao.php";

$sqlCategorias = "SELECT id_categoria, nome_categoria
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




?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>CEEDO - Portal de Transparência Escolar</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <!-- CSS próprio -->
    <link rel="stylesheet" href="assets/css/style.css">

</head>

<body>


<!-- Navbar -->

<?php include('includes/navbar.php')?>



<div class="container">


    <!-- Banner -->

    <div class="hero">


        <h1>
            Portal de Transparência Escolar
        </h1>


        <p class="lead mt-3">

            Consulte documentos, atas, leis, regimentos e trabalhos
            disponibilizados pela instituição.

        </p>


        <a href="#documentos" class="btn btn-light btn-lg mt-3">

            Ver Documentos

        </a>


    </div>




    <!-- Categorias -->

    <section class="mt-5">


        <h2 class="text-center mb-4">

            Categorias

        </h2>


        <div class="row g-4 justify-content-center">



          <?php while ($categoria = mysqli_fetch_assoc($categorias)): ?>

    <?php
    $nomeOriginal = trim($categoria["nome_categoria"]);

    // Deixa somente a primeira letra maiúscula, preservando o restante.
    $nomeExibicao =
        mb_strtoupper(mb_substr($nomeOriginal, 0, 1, "UTF-8"), "UTF-8")
        . mb_substr($nomeOriginal, 1, null, "UTF-8");

    $nomeCategoria = mb_strtolower($nomeOriginal, "UTF-8");

    // Ícone, cor e descrição de cada categoria.
    $estilos = [
        "atas" => [
            "bi-file-earmark-text",
            "text-success",
            "Atas de reuniões e conselhos."
        ],
        "leis" => [
            "bi-bank",
            "text-danger",
            "Leis, normas e resoluções."
        ],
        "regimentos" => [
            "bi-folder-check",
            "text-warning",
            "Regulamentos internos."
        ],
        "trabalhos" => [
            "bi-journal-text",
            "text-primary",
            "Trabalhos e atividades escolares."
        ],
        "projetos" => [
            "bi-lightbulb",
            "text-warning",
            "Projetos desenvolvidos pela instituição."
        ],
        "editais" => [
            "bi-megaphone",
            "text-danger",
            "Editais e chamadas públicas."
        ],
        "calendários" => [
            "bi-calendar-event",
            "text-primary",
            "Datas e eventos do calendário escolar."
        ],
        "relatórios" => [
            "bi-bar-chart",
            "text-success",
            "Relatórios e resultados da instituição."
        ],
        "comunicados" => [
            "bi-chat-left-text",
            "text-primary",
            "Avisos e comunicados da escola."
        ],
        "contratos" => [
            "bi-pen",
            "text-secondary",
            "Contratos e acordos institucionais."
        ],
        "prestações de contas" => [
            "bi-cash-coin",
            "text-success",
            "Demonstrativos e prestação de contas."
        ],
        "cardápios" => [
            "bi-cup-hot",
            "text-danger",
            "Cardápios da alimentação escolar."
        ],
        "horários" => [
            "bi-clock",
            "text-primary",
            "Horários de aulas e atividades."
        ],
        "eventos" => [
            "bi-calendar-check",
            "text-success",
            "Eventos e atividades escolares."
        ],
        "manuais" => [
            "bi-book",
            "text-secondary",
            "Manuais e orientações."
        ]
    ];

    [$icone, $cor, $descricao] = $estilos[$nomeCategoria] ?? [
        "bi-folder",
        "text-warning",
        "Documentos disponíveis nesta categoria."
    ];
    ?>

    <div class="col-md-6 col-lg-3">

        <a
            href="categoria.php?id=<?= $categoria["id_categoria"] ?>"
            class="text-decoration-none text-dark"
        >
            <div class="card shadow-sm h-100 categoria-card">

                <div class="card-body text-center">

                    <i class="bi <?= $icone ?> fs-1 <?= $cor ?>"></i>

                    <h4 class="mt-3">
                        <?= htmlspecialchars($nomeExibicao, ENT_QUOTES, "UTF-8") ?>
                    </h4>

                    <p class="text-muted">
                        <?php
                        $descricaoExibida = trim($categoria['info_categoria'] ?? '');

                        if ($descricaoExibida === '') {
                            $descricaoExibida = $descricao;
                        }
                        ?>

                        <?= htmlspecialchars($descricaoExibida, ENT_QUOTES, 'UTF-8') ?>
                    </p>

                </div>

            </div>
        </a>

    </div>

<?php endwhile; ?>
        </div>


    </section>





    <!-- Documentos -->


    <section id="documentos" class="mt-5 mb-5">


        <h2 class="text-center mb-4">

            Últimos Documentos Publicados

        </h2>

        <div class="table-responsive">


            <table class="table table-striped table-hover">


                <thead class="table-primary">


                    <tr>

                        <th>Documento</th>

                        <th>Categoria</th>

                        <th>Data</th>

                        <th>Ações</th>

                    </tr>


                </thead>


<tbody>

    <?php if (mysqli_num_rows($ultimosDocumentos) > 0): ?>

        <?php while ($documento = mysqli_fetch_assoc($ultimosDocumentos)): ?>

            <tr>

                <td>
                    <?= htmlspecialchars($documento["nome"]) ?>
                </td>

                <td>
                    <?= htmlspecialchars($documento["nome_categoria"]) ?>
                </td>

                <td>
                    <?= date("d/m/Y", strtotime($documento["data_upload"])) ?>
                </td>

                <td>

                    <a
                        href="visualizar.php?id=<?= $documento["id_documento"] ?>"
                        target="_blank"
                        class="btn btn-primary btn-sm"
                    >
                        <i class="bi bi-eye"></i>
                        Visualizar
                    </a>

                    <a
                        href="download.php?id=<?= $documento["id_documento"] ?>"
                        class="btn btn-success btn-sm"
                    >
                        <i class="bi bi-download"></i>
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



</div>





<footer class="bg-dark text-white text-center py-4">


    <p class="mb-0">

        © 2026 CEEDO - Portal de Transparência Escolar

    </p>


</footer>





<!-- Bootstrap JS -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


</body>

</html>