<?php
require_once "painel/conexao.php";

$sqlCategorias = "SELECT id_categoria,
                         nome_categoria,
                         info_categoria
                  FROM categorias
                  ORDER BY nome_categoria ASC";

$categorias = mysqli_query($conexao, $sqlCategorias);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Categorias - CEEDO</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/rodape.css">
<link rel="stylesheet" href="assets/css/visual.css">
</head>

<body>

<?php include "includes/navbar.php"; ?>

<main class="container py-5">

    <section class="text-center mb-5">
        <h1><i class="bi bi-tags" aria-hidden="true"></i> Categorias</h1>
        <p class="text-muted">
            Consulte os documentos organizados por categoria.
        </p>
    </section>

    <div class="row g-4 justify-content-center">

        <?php while ($categoria = mysqli_fetch_assoc($categorias)): ?>

            <?php
                $nomeCategoria = mb_strtolower(trim($categoria["nome_categoria"]), "UTF-8");

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


                [$icone, $cor, $descricao] = $estilos[$nomeCategoria] ?? ["bi-folder", "text-primary", "Documentos disponíveis nesta categoria."];
            ?>

            <div class="col-12 col-sm-6 col-lg-4">

                <a
                    href="categoria.php?id=<?= $categoria["id_categoria"] ?>"
                    class="text-decoration-none text-dark"
                >
                    <div class="card shadow-sm h-100 categoria-card">

                        <div class="card-body text-center py-4">

                            <i class="bi <?= $icone ?> fs-1 <?= $cor ?>"></i>

                            <h4 class="mt-3 text-capitalize">
                                <?= htmlspecialchars($categoria["nome_categoria"]) ?>
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

</main>

<?php include "includes/footer.php"; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>