<?php

require_once __DIR__ . '/includes/verifica_root.php';
require_once __DIR__ . '/conexao.php';

$acoes = [
    'desativar' => [
        'titulo' => 'Desativar administrador',
        'descricao' => 'A conta perderá acesso ao painel. Você poderá reativá-la depois.',
        'botao' => 'Confirmar desativação'
    ],
    'reativar' => [
        'titulo' => 'Reativar administrador',
        'descricao' => 'A conta voltará a acessar o painel como administrador.',
        'botao' => 'Confirmar reativação'
    ],
    'excluir' => [
        'titulo' => 'Excluir administrador',
        'descricao' => 'A conta será removida definitivamente. Os documentos do portal serão mantidos.',
        'botao' => 'Excluir definitivamente'
    ]
];

$id = is_string($_GET['id'] ?? null)
    ? filter_var(
        $_GET['id'],
        FILTER_VALIDATE_INT,
        ['options' => ['min_range' => 1]]
    )
    : false;

$acao = is_string($_GET['acao'] ?? null)
    ? $_GET['acao']
    : '';

if (
    !$id
    || !isset($acoes[$acao])
    || $id === (int) $_SESSION['usuario_id']
) {
    header('Location: operadores.php', true, 303);
    exit;
}

try {
    $stmt = mysqli_prepare(
        $conexao,
        'SELECT nome_operador, email_operador, nivel, ativo
         FROM operadores WHERE id_operador = ?'
    );

    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);

    $destino = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);

} catch (Throwable $erro) {
    error_log($erro->getMessage());
    http_response_code(503);
    exit('Não foi possível consultar o administrador.');
}

if (!$destino || $destino['nivel'] !== 'adm') {
    header('Location: operadores.php', true, 303);
    exit;
}

function escaparAcao(string $texto): string
{
    return htmlspecialchars(
        $texto,
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );
}

$dadosAcao = $acoes[$acao];

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/sidebar.php';
?>

<div class="content">
    <?php include __DIR__ . '/includes/navbar.php'; ?>

    <main class="container-fluid p-3 p-md-5">
        <div class="mx-auto" style="max-width: 700px;">
            <a
                href="operadores.php"
                class="text-decoration-none d-inline-block mb-4"
            >
                <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>
                Voltar para operadores
            </a>

            <section class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4 p-md-5">
                    <h1 class="h3 fw-bold mb-3">
                        <?= escaparAcao($dadosAcao['titulo']) ?>
                    </h1>

                    <p class="text-muted">
                        <?= escaparAcao($dadosAcao['descricao']) ?>
                    </p>

                    <div class="bg-light border rounded-3 p-3 my-4">
                        <div class="fw-semibold text-break">
                            <?= escaparAcao($destino['nome_operador']) ?>
                        </div>
                        <div class="text-muted small text-break">
                            <?= escaparAcao($destino['email_operador']) ?>
                        </div>
                    </div>

                    <form action="alterar_operador.php" method="POST">
                        <input
                            type="hidden"
                            name="csrf_token"
                            value="<?= escaparAcao(csrfToken()) ?>"
                        >
                        <input
                            type="hidden"
                            name="id_operador"
                            value="<?= (int) $id ?>"
                        >
                        <input
                            type="hidden"
                            name="acao"
                            value="<?= escaparAcao($acao) ?>"
                        >

                        <label for="senha_atual" class="form-label fw-semibold">
                            Sua senha atual
                        </label>

                        <input
                            type="password"
                            id="senha_atual"
                            name="senha_atual"
                            class="form-control form-control-lg"
                            autocomplete="current-password"
                            required
                        >

                        <div class="form-text">
                            Informe a senha da sua conta root.
                        </div>

                        <div class="d-flex flex-wrap justify-content-end gap-2 mt-4">
                            <a
                                href="operadores.php"
                                class="btn btn-outline-secondary"
                            >
                                Cancelar
                            </a>

                            <button
                                type="submit"
                                class="btn <?= $acao === 'excluir'
                                    ? 'btn-danger'
                                    : 'btn-primary' ?>"
                            >
                                <?= escaparAcao($dadosAcao['botao']) ?>
                            </button>
                        </div>
                    </form>
                </div>
            </section>
        </div>
    </main>
</div>

<?php
include __DIR__ . '/includes/footer.php';
include __DIR__ . '/includes/scripts.php';