<?php
include "conexao.php";
include "includes/funcoes.php";

$resultado = listarDocumentos($conexao);
$totalDocumentos = mysqli_num_rows($resultado);

include "includes/header.php";
include "includes/sidebar.php";
?>

<div class="content">

    <?php include "includes/navbar.php"; ?>

    <main class="container-fluid p-4 p-lg-5">

        <?php if (isset($_GET['mensagem']) && is_string($_GET['mensagem'])): ?>
            <div class="alert alert-<?= ($_GET['tipo'] ?? '') === 'sucesso' ? 'success' : 'danger' ?>" role="alert">
                <?= htmlspecialchars($_GET['mensagem'], ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <section class="pagina-top">

            <div class="d-flex align-items-center gap-3">

                <div class="pagina-top-icone">
                    <i class="bi bi-files"></i>
                </div>

                <div>

                    <p class="text-primary fw-semibold mb-1">
                        Documentos
                    </p>

                    <h1>Gerenciar documentos</h1>

                    <p class="text-muted mb-0">
                        Consulte os arquivos publicados no Portal de Transparência.
                    </p>

                </div>

            </div>

            <a href="novo_documento.php" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i>
                Novo documento
            </a>

        </section>

        <section class="card painel-bloco">

            <div class="card-body p-4">

                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">

                    <div>

                        <h2 class="h5 fw-bold mb-1">
                            Documentos cadastrados
                        </h2>

                        <p class="text-muted mb-0">
                            <?= $totalDocumentos ?>
                            documento<?= $totalDocumentos != 1 ? "s" : "" ?>
                            encontrado<?= $totalDocumentos != 1 ? "s" : "" ?>.
                        </p>

                    </div>

                    <span class="documento-contador">
                        <i class="bi bi-file-earmark-text"></i>
                        <?= $totalDocumentos ?>
                    </span>

                </div>

                <div class="table-responsive">

                    <table class="table tabela-dashboard align-middle mb-0">

                        <thead>
                            <tr>
                                <th>Documento</th>
                                <th>Categoria</th>
                                <th>Data de envio</th>
                                <th class="text-end">Ações</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php if ($totalDocumentos > 0): ?>

                                <?php while ($documento = mysqli_fetch_assoc($resultado)): ?>

                                    <tr>

                                        <td class="fw-semibold">

                                            <i class="bi bi-file-earmark-text text-primary me-2"></i>

                                            <?= htmlspecialchars($documento["nome"]) ?>

                                        </td>

                                        <td>
                                            <span class="categoria-badge">
                                                <?= htmlspecialchars($documento["nome_categoria"]) ?>
                                            </span>
                                        </td>

                                        <td>
                                            <?= date("d/m/Y", strtotime($documento["data_upload"])) ?>
                                        </td>

                                        <td class="text-end">

                                            <a
                                                href="../visualizar.php?id=<?= $documento["id_documento"] ?>"
                                                target="_blank"
                                                class="btn btn-sm btn-outline-primary"
                                                title="Visualizar documento"
                                            >
                                                <i class="bi bi-eye"></i>
                                                Visualizar
                                            </a>

                                            <a
                                                href="editar_documento.php?id=<?= (int) $documento['id_documento'] ?>"
                                                class="btn btn-sm btn-outline-warning"
                                            >
                                                <i class="bi bi-pencil"></i>
                                                Editar
                                            </a>

                                            <form
                                                action="excluir_documento.php"
                                                method="POST"
                                                class="d-inline"
                                                onsubmit="return confirm('Deseja excluir este documento do portal?');"
                                            >
                                                <input type="hidden" name="id_documento" value="<?= (int) $documento['id_documento'] ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Excluir documento">
                                                    <i class="bi bi-trash"></i>
                                                    Excluir
                                                </button>
                                            </form>

                                        </td>

                                    </tr>

                                <?php endwhile; ?>

                            <?php else: ?>

                                <tr>
                                    <td colspan="4" class="text-center py-5">

                                        <i class="bi bi-folder2-open fs-1 text-muted"></i>

                                        <p class="text-muted mt-3 mb-3">
                                            Nenhum documento foi cadastrado ainda.
                                        </p>

                                        <a href="novo_documento.php" class="btn btn-primary btn-sm">
                                            Adicionar primeiro documento
                                        </a>

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

<?php include "includes/footer.php"; ?>