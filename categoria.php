<?php
require_once "painel/conexao.php";

if (!isset($_GET["id"]) || !filter_var($_GET["id"], FILTER_VALIDATE_INT)) {
    header("Location: index.php");
    exit;
}

$idCategoria = (int) $_GET["id"];

/* Busca a categoria */
$sqlCategoria = "SELECT id_categoria, nome_categoria
                 FROM categorias
                 WHERE id_categoria = ?
                 LIMIT 1";

$stmtCategoria = mysqli_prepare($conexao, $sqlCategoria);
mysqli_stmt_bind_param($stmtCategoria, "i", $idCategoria);
mysqli_stmt_execute($stmtCategoria);

$resultadoCategoria = mysqli_stmt_get_result($stmtCategoria);
$categoria = mysqli_fetch_assoc($resultadoCategoria);

if (!$categoria) {
    header("Location: index.php");
    exit;
}

/* Busca os documentos ligados à categoria */
$sqlDocumentos = "SELECT id_documento, nome, caminho, data_upload
                  FROM documentos
                  WHERE id_categoria = ?
                  ORDER BY data_upload DESC";

$stmtDocumentos = mysqli_prepare($conexao, $sqlDocumentos);
mysqli_stmt_bind_param($stmtDocumentos, "i", $idCategoria);
mysqli_stmt_execute($stmtDocumentos);

$documentos = mysqli_stmt_get_result($stmtDocumentos);
?>

<?php include "includes/header.php"; ?>
<?php include "includes/navbar.php"; ?>

<main class="container py-5">

    <a href="index.php" class="btn btn-outline-primary mb-4">
        ← Voltar para categorias
    </a>

    <section class="text-center mb-5">
        <h1><?= htmlspecialchars($categoria["nome_categoria"]) ?></h1>
        <p class="text-muted">Documentos disponíveis nesta categoria.</p>
    </section>

    <?php if (mysqli_num_rows($documentos) > 0): ?>

        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-primary">
                    <tr>
                        <th>Documento</th>
                        <th>Data de publicação</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>

                <tbody>
                    <?php while ($documento = mysqli_fetch_assoc($documentos)): ?>
                        <tr>
                            <td><?= htmlspecialchars($documento["nome"]) ?></td>

                            <td>
                                <?= date("d/m/Y", strtotime($documento["data_upload"])) ?>
                            </td>

                            <td class="text-center">
                              <a
                                href="visualizar.php?id=<?= $documento["id_documento"] ?>"
                                target="_blank"
                                class="btn btn-primary btn-sm"
                            >
                                Visualizar
                            </a>

                               <a
                                    href="download.php?id=<?= $documento["id_documento"] ?>"
                                    class="btn btn-success btn-sm"
                                >
                                    Download
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

    <?php else: ?>

        <div class="alert alert-info text-center">
            Ainda não há documentos publicados nesta categoria.
        </div>

    <?php endif; ?>

</main>

<?php include "includes/script.php"; ?>