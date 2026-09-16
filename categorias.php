<?php
require_once "painel/conexao.php";

$sqlCategorias = "SELECT id_categoria, nome_categoria
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
</head>

<body>

<?php include "includes/navbar.php"; ?>

<main class="container py-5">

    <section class="text-center mb-5">
        <h1>Categorias</h1>
        <p class="text-muted">
            Consulte os documentos organizados por categoria.
        </p>
    </section>

    <div class="row g-4 justify-content-center">

        <?php while ($categoria = mysqli_fetch_assoc($categorias)): ?>

            <?php
                $nomeCategoria = mb_strtolower($categoria["nome_categoria"], "UTF-8");

                $icone = "bi-folder";
                $cor = "text-warning";
                $descricao = "Documentos disponíveis nesta categoria.";

                if ($nomeCategoria === "atas") {
                    $icone = "bi-file-earmark-text";
                    $cor = "text-success";
                    $descricao = "Atas de reuniões e conselhos.";
                } elseif ($nomeCategoria === "leis") {
                    $icone = "bi-bank";
                    $cor = "text-danger";
                    $descricao = "Leis, normas e resoluções.";
                } elseif ($nomeCategoria === "regimentos") {
                    $icone = "bi-folder-check";
                    $cor = "text-warning";
                    $descricao = "Regulamentos internos.";
                } elseif ($nomeCategoria === "trabalhos") {
                    $icone = "bi-journal-text";
                    $cor = "text-primary";
                    $descricao = "Trabalhos e projetos escolares.";
                }
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