<?php
require_once __DIR__ . '/includes/verifica_root.php';

$mensagem = is_string($_SESSION['mensagem_operador'] ?? null)
    ? $_SESSION['mensagem_operador']
    : '';

$sucesso = ($_SESSION['operador_sucesso'] ?? false) === true;

unset(
    $_SESSION['mensagem_operador'],
    $_SESSION['operador_sucesso']
);

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/sidebar.php';
?>

<div class="content">
    <?php include __DIR__ . '/includes/navbar.php'; ?>

    <main class="container-fluid p-4">
        <h1 class="h3 mb-4">Cadastrar administrador</h1>

        <?php if ($mensagem !== ''): ?>
            <div
                class="alert alert-<?= $sucesso ? 'success' : 'danger' ?>"
                role="alert"
            >
                <?= htmlspecialchars(
                    $mensagem,
                    ENT_QUOTES | ENT_SUBSTITUTE,
                    'UTF-8'
                ) ?>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="card-body p-4">
                <form action="salvar_operador.php" method="POST">
                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= htmlspecialchars(
                            csrfToken(),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                    >

                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome</label>
                        <input
                            type="text"
                            id="nome"
                            name="nome"
                            class="form-control"
                            maxlength="255"
                            autocomplete="off"
                            required
                        >
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">E-mail</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-control"
                            maxlength="255"
                            autocomplete="off"
                            required
                        >
                    </div>

                    <div class="mb-3">
                        <label for="senha" class="form-label">
                            Senha do novo administrador
                        </label>
                        <input
                            type="password"
                            id="senha"
                            name="senha"
                            class="form-control"
                            minlength="15"
                            autocomplete="new-password"
                            required
                        >
                        <div class="form-text">
                            Use pelo menos 15 caracteres.
                            Prefira uma frase longa e exclusiva.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="confirmacao" class="form-label">
                            Repita a senha
                        </label>
                        <input
                            type="password"
                            id="confirmacao"
                            name="confirmacao"
                            class="form-control"
                            minlength="15"
                            autocomplete="new-password"
                            required
                        >
                    </div>

                    <p class="text-muted">
                        A conta será criada ativa, com cargo de administrador.
                    </p>

                    <button type="submit" class="btn btn-primary">
                        Cadastrar administrador
                    </button>

                    <a href="index.php" class="btn btn-outline-secondary">
                        Voltar
                    </a>
                </form>
            </div>
        </div>
    </main>
</div>

<?php
include __DIR__ . '/includes/footer.php';
include __DIR__ . '/includes/scripts.php';