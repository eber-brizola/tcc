<?php
require_once "conexao.php";

$sqlTotalDocumentos = "SELECT COUNT(*) AS total FROM documentos";
$totalDocumentos = mysqli_fetch_assoc(
    mysqli_query($conexao, $sqlTotalDocumentos)
)["total"];

$sqlTotalCategorias = "SELECT COUNT(*) AS total FROM categorias";
$totalCategorias = mysqli_fetch_assoc(
    mysqli_query($conexao, $sqlTotalCategorias)
)["total"];

$sqlUltimoDocumento = "SELECT nome, data_upload
                       FROM documentos
                       ORDER BY data_upload DESC
                       LIMIT 1";

$ultimoDocumento = mysqli_fetch_assoc(
    mysqli_query($conexao, $sqlUltimoDocumento)
);

$sqlRecentes = "SELECT documentos.id_documento,
                       documentos.nome,
                       documentos.data_upload,
                       categorias.nome_categoria
                FROM documentos
                INNER JOIN categorias
                    ON documentos.id_categoria = categorias.id_categoria
                ORDER BY documentos.data_upload DESC
                LIMIT 5";

$documentosRecentes = mysqli_query($conexao, $sqlRecentes);

include "includes/header.php";
include "includes/sidebar.php";
?>

<div class="content">

    <?php include "includes/navbar.php"; ?>

    <main class="container-fluid p-4 p-lg-5">

        <section class="dashboard-top">

            <div>

                <p class="text-primary fw-semibold mb-1">
                    <i class="bi bi-speedometer2"></i>
                    Visão geral
                </p>

                <h1>Dashboard</h1>

                <p class="text-muted mb-0">
                    Gerencie os documentos e as categorias do Portal de Transparência CEEDO.
                </p>

                

            </div>

            <div class="dashboard-top-acoes">

                <a href="../index.php" target="_blank" class="btn btn-outline-primary">
                    <i class="bi bi-box-arrow-up-right"></i>
                    Ver portal
                </a>

                <a href="novo_documento.php" class="btn btn-primary">
                    <i class="bi bi-cloud-arrow-up"></i>
                    Adicionar documento
                </a>

            </div>

        </section>

        <section class="row g-4 mb-5">

            <div class="col-md-6 col-xl-4">

                <div class="card dashboard-card dashboard-card-primary h-100">

                    <div class="card-body d-flex align-items-center justify-content-between">

                        <div>

                            <p class="card-label">Documentos cadastrados</p>

                            <h2 class="card-number">
                                <?= $totalDocumentos ?>
                            </h2>

                            <a href="documentos.php" class="dashboard-link">
                                Gerenciar documentos
                                <i class="bi bi-arrow-right"></i>
                            </a>

                        </div>

                        <div class="dashboard-icon icon-primary">
                            <i class="bi bi-file-earmark-text"></i>
                        </div>

                    </div>

                </div>

            </div>

            <div class="col-md-6 col-xl-4">

                <div class="card dashboard-card dashboard-card-success h-100">

                    <div class="card-body d-flex align-items-center justify-content-between">

                        <div>

                            <p class="card-label">Categorias ativas</p>

                            <h2 class="card-number">
                                <?= $totalCategorias ?>
                            </h2>

                            <a href="categorias.php" class="dashboard-link">
                                Gerenciar categorias
                                <i class="bi bi-arrow-right"></i>
                            </a>

                        </div>

                        <div class="dashboard-icon icon-success">
                            <i class="bi bi-folder2-open"></i>
                        </div>

                    </div>

                </div>

            </div>

            <div class="col-md-6 col-xl-4">

                <div class="card dashboard-card dashboard-card-warning h-100">

                    <div class="card-body d-flex align-items-center justify-content-between">

                        <div>

                            <p class="card-label">Último documento enviado</p>

                            <h2 class="card-date">
                                <?= $ultimoDocumento
                                    ? date("d/m/Y", strtotime($ultimoDocumento["data_upload"]))
                                    : "Sem uploads" ?>
                            </h2>

                            <?php if ($ultimoDocumento): ?>

                                <p class="card-detail">
                                    <?= htmlspecialchars($ultimoDocumento["nome"]) ?>
                                </p>

                            <?php endif; ?>

                        </div>

                        <div class="dashboard-icon icon-warning">
                            <i class="bi bi-clock-history"></i>
                        </div>

                    </div>

                </div>

            </div>

        </section>

        <section class="card painel-bloco mb-5">

    <div class="card-body p-4">

        <div class="d-flex align-items-center justify-content-between mb-4">

            <h2 class="h5 fw-bold mb-0">
                Ações rápidas
            </h2>

        </div>

        <div class="row g-3">

            <div class="col-md-4">

                <a href="novo_documento.php" class="acao-rapida acao-primaria">

                    <i class="bi bi-cloud-arrow-up"></i>

                    <span class="texto-acao">
                        <strong>Adicionar documento</strong>
                    </span>

                    <i class="bi bi-arrow-right seta-acao"></i>

                </a>

            </div>

            <div class="col-md-4">

                <a href="documentos.php" class="acao-rapida acao-sucesso">

                    <i class="bi bi-files"></i>

                    <span class="texto-acao">
                        <strong>Gerenciar documentos</strong>
                    </span>

                    <i class="bi bi-arrow-right seta-acao"></i>

                </a>

            </div>

            <div class="col-md-4">

                <a href="categorias.php" class="acao-rapida acao-aviso">

                    <i class="bi bi-tags"></i>

                    <span class="texto-acao">
                        <strong>Gerenciar categorias</strong>
                    </span>

                    <i class="bi bi-arrow-right seta-acao"></i>

                </a>

            </div>

        </div>

    </div>

</section>

        <section class="card painel-bloco">

            <div class="card-body p-4">

                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">

                    <div>

                        <h2 class="h5 fw-bold mb-1">
                            Documentos recentes
                        </h2>

                        <p class="text-muted mb-0">
                            Últimos arquivos adicionados ao portal.
                        </p>

                    </div>

                    <a href="documentos.php" class="btn btn-outline-primary btn-sm">
                        Ver todos
                        <i class="bi bi-arrow-right"></i>
                    </a>

                </div>

                <div class="table-responsive">

                    <table class="table tabela-dashboard align-middle mb-0">

                        <thead>
                            <tr>
                                <th>Documento</th>
                                <th>Categoria</th>
                                <th>Data de envio</th>
                                <th>Status</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php if (mysqli_num_rows($documentosRecentes) > 0): ?>

                                <?php while ($documento = mysqli_fetch_assoc($documentosRecentes)): ?>

                                    <tr>

                                        <td class="fw-semibold">
                                            <i class="bi bi-file-earmark-text text-primary me-2"></i>
                                            <?= htmlspecialchars($documento["nome"]) ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($documento["nome_categoria"]) ?>
                                        </td>

                                        <td>
                                            <?= date("d/m/Y", strtotime($documento["data_upload"])) ?>
                                        </td>

                                        <td>
                                            <span class="badge-status">
                                                <i class="bi bi-check-circle-fill"></i>
                                                Publicado
                                            </span>
                                        </td>

                                    </tr>

                                <?php endwhile; ?>

                            <?php else: ?>

                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        Nenhum documento cadastrado ainda.
                                    </td>
                                </tr>

                            <?php endif; ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </section>

    </main>

</div>

<script>
    const arquivo = document.getElementById("arquivo");
    const nomeArquivo = document.getElementById("nomeArquivo");

    arquivo.addEventListener("change", function () {
        if (arquivo.files.length > 0) {
            nomeArquivo.textContent = arquivo.files[0].name;
        }
    });
</script>

<?php
include "includes/footer.php";
include "includes/scripts.php";
?>

<script>
    setInterval(function () {
        window.location.reload();
    }, 30000);
</script>