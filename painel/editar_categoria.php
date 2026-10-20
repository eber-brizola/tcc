<?php
require_once __DIR__ . '/includes/verifica_login.php';
require_once __DIR__ . "/conexao.php";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

function voltarCategorias($mensagem)
{
    header("Location: categorias.php?" . http_build_query([
        "mensagem" => $mensagem,
        "tipo" => "erro"
    ]));

    exit;
}

$idCategoria = filter_var(
    $_GET["id"] ?? null,
    FILTER_VALIDATE_INT,
    ["options" => ["min_range" => 1]]
);

if (!$idCategoria) {
    voltarCategorias("Categoria inválida.");
}

try {
    $stmt = mysqli_prepare(
        $conexao,
        "SELECT id_categoria, nome_categoria, info_categoria
         FROM categorias
         WHERE id_categoria = ?"
    );

    mysqli_stmt_bind_param($stmt, "i", $idCategoria);
    mysqli_stmt_execute($stmt);

    $resultado = mysqli_stmt_get_result($stmt);
    $categoria = mysqli_fetch_assoc($resultado);

    mysqli_stmt_close($stmt);

} catch (Throwable $erro) {
    error_log($erro->getMessage());
    voltarCategorias("Não foi possível carregar a categoria.");
}

if (!$categoria) {
    voltarCategorias("Categoria não encontrada.");
}

include "includes/header.php";
include "includes/sidebar.php";
?>

<div class="content">

    <?php include "includes/navbar.php"; ?>

    <main class="container-fluid p-4 p-lg-5">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Editar categoria</h1>

            <a href="categorias.php" class="btn btn-outline-primary">
                Voltar
            </a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-4">

                <form action="atualizar_categoria.php" method="POST">
                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8') ?>"
                    >

                    <input
                        type="hidden"
                        name="id_categoria"
                        value="<?= (int) $categoria['id_categoria'] ?>"
                    >

                    <div class="mb-3">
                        <label for="nome_categoria" class="form-label">
                            Nome da categoria
                        </label>

                        <input
                            type="text"
                            name="nome_categoria"
                            id="nome_categoria"
                            class="form-control"
                            maxlength="100"
                            value="<?= htmlspecialchars(
                                $categoria['nome_categoria'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            required
                        >
                    </div>

                    <div class="mb-4">
                        <label for="info_categoria" class="form-label">
                            Descrição
                        </label>

                        <textarea
                            name="info_categoria"
                            id="info_categoria"
                            class="form-control"
                            rows="3"
                            maxlength="255"
                        ><?= htmlspecialchars(
                            $categoria['info_categoria'] ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?></textarea>

                        <div class="form-text">
                            Opcional. Aparece no card da categoria no portal.
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="categorias.php" class="btn btn-light">
                            Cancelar
                        </a>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg"></i>
                            Salvar alterações
                        </button>
                    </div>

                </form>

            </div>
        </div>

    </main>

</div>

<?php include "includes/footer.php"; ?>