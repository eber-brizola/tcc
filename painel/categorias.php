<?php
require_once "conexao.php";

$mensagem = $_GET["mensagem"] ?? "";
$tipo = $_GET["tipo"] ?? "";

$sqlCategorias = "SELECT id_categoria, nome_categoria
                  FROM categorias
                  ORDER BY nome_categoria ASC";

$categorias = mysqli_query($conexao, $sqlCategorias);

include "includes/header.php";
include "includes/sidebar.php";
?>

<div class="content">

    <?php include "includes/navbar.php"; ?>

    <div class="container p-4">

        <h2>Categorias</h2>

        <?php if ($mensagem): ?>
            <div class="alert alert-<?= $tipo === "erro" ? "danger" : "success" ?>">
                <?= htmlspecialchars($mensagem) ?>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm mb-4">

            <div class="card-body">

                <h5 class="card-title mb-3">Nova categoria</h5>

                <form action="salvar_categoria.php" method="POST">

                    <div class="row g-3 align-items-end">

                        <div class="col-md-8">

                            <label for="nome_categoria" class="form-label">
                                Nome da categoria
                            </label>

                            <input
                                type="text"
                                name="nome_categoria"
                                id="nome_categoria"
                                class="form-control"
                                placeholder="Ex.: Trabalhos"
                                required
                                maxlength="100"
                            >

                        </div>

                        <div class="col-md-4">

                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-plus-circle"></i>
                                Adicionar categoria
                            </button>

                        </div>

                    </div>

                </form>

            </div>

        </div>

        <div class="card shadow-sm">

            <div class="card-body">

                <h5 class="card-title mb-3">Categorias cadastradas</h5>

                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0">

                        <thead class="table-primary">

                            <tr>
                                <th>Categoria</th>
                                <th class="text-end">Ação</th>
                            </tr>

                        </thead>

                        <tbody>

                            <?php if (mysqli_num_rows($categorias) > 0): ?>

                                <?php while ($categoria = mysqli_fetch_assoc($categorias)): ?>

                                    <tr>

                                        <td>
                                            <?= htmlspecialchars($categoria["nome_categoria"]) ?>
                                        </td>

                                        <td class="text-end">

                                            <form
                                                action="remover_categoria.php"
                                                method="POST"
                                                class="d-inline"
                                                onsubmit="return confirm('Deseja realmente excluir esta categoria?');"
                                            >

                                                <input
                                                    type="hidden"
                                                    name="id_categoria"
                                                    value="<?= $categoria["id_categoria"] ?>"
                                                >

                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="bi bi-trash"></i>
                                                    Excluir
                                                </button>

                                            </form>

                                        </td>

                                    </tr>

                                <?php endwhile; ?>

                            <?php else: ?>

                                <tr>
                                    <td colspan="2" class="text-center text-muted">
                                        Nenhuma categoria cadastrada.
                                    </td>
                                </tr>

                            <?php endif; ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

</div>

<?php
include "includes/footer.php";
include "includes/scripts.php";
?>