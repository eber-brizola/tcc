<?php
require_once __DIR__ . '/includes/verifica_login.php';
require_once "conexao.php";

$sqlCategorias = "SELECT id_categoria, nome_categoria
                  FROM categorias
                  ORDER BY nome_categoria ASC";

$categorias = mysqli_query($conexao, $sqlCategorias);

include "includes/header.php";
include "includes/sidebar.php";
?>

<div class="content">

    <?php include "includes/navbar.php"; ?>

    <main class="container-fluid p-4 p-lg-5">

        <section class="pagina-top">

            <div class="d-flex align-items-center gap-3">

                <div class="pagina-top-icone">
                    <i class="bi bi-cloud-arrow-up"></i>
                </div>

                <div>
                    <p class="text-primary fw-semibold mb-1">
                        Documentos
                    </p>

                    <h1>Adicionar documento</h1>

                    <p class="text-muted mb-0">
                        Preencha os dados e envie o arquivo para publicação.
                    </p>
                </div>

            </div>

            <a href="documentos.php" class="btn btn-outline-primary">
                <i class="bi bi-arrow-left"></i>
                Voltar
            </a>

        </section>

        <section class="card formulario-documento">

            <div class="card-body p-4 p-lg-5">

                <form
                    action="salvar_documento.php"
                    method="POST"
                    enctype="multipart/form-data"
                >
                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8') ?>"
                    >
                    <div class="row g-4">

                        <div class="col-lg-7">

                            <div class="mb-4">

                                <label for="nome" class="form-label">
                                    Nome do documento
                                </label>

                                <input
                                    type="text"
                                    class="form-control form-control-lg"
                                    name="nome"
                                    id="nome"
                                    placeholder="Ex.: Ata da reunião pedagógica"
                                    required
                                >

                            </div>

                            <div>

                                <label for="categoria" class="form-label">
                                    Categoria
                                </label>

                                <select
                                    name="categoria"
                                    id="categoria"
                                    class="form-select form-select-lg"
                                    required
                                >

                                    <option value="">
                                        Selecione uma categoria
                                    </option>

                                    <?php while ($categoria = mysqli_fetch_assoc($categorias)): ?>

                                        <option value="<?= $categoria["id_categoria"] ?>">
                                            <?= htmlspecialchars($categoria["nome_categoria"]) ?>
                                        </option>

                                    <?php endwhile; ?>

                                </select>

                            </div>

                        </div>

                        <div class="col-lg-5">

                            <label for="arquivo" class="form-label">
                                Arquivo
                            </label>

                            <label for="arquivo" class="area-upload">

                                <i class="bi bi-file-earmark-arrow-up"></i>

                                <strong>Selecionar arquivo</strong>

                                <span id="nomeArquivo">
                                    Clique nesta área para procurar no computador.
                                </span>

                                <input
                                    type="file"
                                    name="arquivo"
                                    id="arquivo"
                                    accept=".pdf,.jpg,.jpeg,.png"
                                    required
                                >

                            </label>

                            </div>

                        </div>

                    </div>

                    <hr class="my-5">

                    <div class="d-flex flex-wrap justify-content-end gap-2">

                        <a href="documentos.php" class="btn btn-light">
                            Cancelar
                        </a>

                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-cloud-arrow-up"></i>
                            Salvar documento
                        </button>

                    </div>

                </form>

            </div>

        </section>

    </main>

</div>

<?php include "includes/footer.php"; ?>