<?php

require_once __DIR__ . '/includes/verifica_login.php';
require_once __DIR__ . '/../includes/icones_categorias.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
require_once __DIR__ . '/conexao.php';

function voltarCategorias(string $mensagem): void
{
    header('Location: categorias.php?' . http_build_query([
        'mensagem' => $mensagem,
        'tipo' => 'erro'
    ]), true, 303);

    exit;
}

$idCategoria = filter_var(
    $_GET['id'] ?? null,
    FILTER_VALIDATE_INT,
    ['options' => ['min_range' => 1]]
);

if (!$idCategoria) {
    voltarCategorias('Categoria inválida.');
}

try {
    $stmt = mysqli_prepare(
        $conexao,
        'SELECT id_categoria,
                nome_categoria,
                info_categoria,
                icone
         FROM categorias
         WHERE id_categoria = ?'
    );

    mysqli_stmt_bind_param($stmt, 'i', $idCategoria);
    mysqli_stmt_execute($stmt);

    $resultado = mysqli_stmt_get_result($stmt);
    $categoria = mysqli_fetch_assoc($resultado);

    mysqli_stmt_close($stmt);

} catch (Throwable $erro) {
    error_log($erro->getMessage());
    voltarCategorias('Não foi possível carregar a categoria.');
}

if (!$categoria) {
    voltarCategorias('Categoria não encontrada.');
}

$iconesPermitidos = iconesCategorias();
$iconeSalvo = $categoria['icone'] ?? 'bi-folder';

// Usa uma pasta caso o valor salvo não pertença à lista.
$iconeAtual = (
    is_string($iconeSalvo)
    && isset($iconesPermitidos[$iconeSalvo])
) ? $iconeSalvo : 'bi-folder';

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/sidebar.php';
?>

<div class="content">

    <?php include __DIR__ . '/includes/navbar.php'; ?>

    <main class="container-fluid p-4 p-lg-5">

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <h1 class="h3 mb-0">
                <span class="titulo-icone">
                    <i class="bi bi-tags" aria-hidden="true"></i>
                </span>
                Editar categoria
            </h1>

            <a href="categorias.php" class="btn btn-outline-primary">
                <i class="bi bi-arrow-left" aria-hidden="true"></i>
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

                    <fieldset class="mb-4">
                        <legend class="fs-6 fw-semibold">
                            Ícone da categoria
                        </legend>

                        <div class="seletor-icones">
                            <?php foreach ($iconesPermitidos as $classe => $rotulo): ?>
                                <input
                                    type="radio"
                                    class="btn-check"
                                    name="icone"
                                    id="icone-<?= htmlspecialchars($classe, ENT_QUOTES, 'UTF-8') ?>"
                                    value="<?= htmlspecialchars($classe, ENT_QUOTES, 'UTF-8') ?>"
                                    <?= $classe === $iconeAtual ? 'checked' : '' ?>
                                    required
                                >

                                <label
                                    class="opcao-icone"
                                    for="icone-<?= htmlspecialchars($classe, ENT_QUOTES, 'UTF-8') ?>"
                                >
                                    <i
                                        class="bi <?= htmlspecialchars($classe, ENT_QUOTES, 'UTF-8') ?>"
                                        aria-hidden="true"
                                    ></i>

                                    <?= htmlspecialchars($rotulo, ENT_QUOTES, 'UTF-8') ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </fieldset>

                    <div class="d-flex justify-content-end flex-wrap gap-2">
                        <a href="categorias.php" class="btn btn-outline-secondary">
                            <i class="bi bi-x-lg" aria-hidden="true"></i>
                            Cancelar
                        </a>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg" aria-hidden="true"></i>
                            Salvar alterações
                        </button>
                    </div>
                </form>

            </div>
        </div>

    </main>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>