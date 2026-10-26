<?php

require_once __DIR__ . '/includes/verifica_root.php';
require_once __DIR__ . '/conexao.php';

$idRecebido = $_GET['id'] ?? null;

$idDestino = is_string($idRecebido)
    ? filter_var(
        $idRecebido,
        FILTER_VALIDATE_INT,
        ['options' => ['min_range' => 1]]
    )
    : false;

if (!$idDestino || $idDestino === (int) $_SESSION['usuario_id']) {
    header('Location: operadores.php', true, 303);
    exit;
}

try {
    $stmt = mysqli_prepare(
        $conexao,
        'SELECT nome_operador, email_operador, nivel, ativo
         FROM operadores
         WHERE id_operador = ?
         LIMIT 1'
    );

    mysqli_stmt_bind_param($stmt, 'i', $idDestino);
    mysqli_stmt_execute($stmt);

    $resultado = mysqli_stmt_get_result($stmt);
    $destino = mysqli_fetch_assoc($resultado);

    mysqli_stmt_close($stmt);

} catch (Throwable $erro) {
    error_log($erro->getMessage());
    http_response_code(503);
    exit('Não foi possível consultar o operador.');
}

if (
    !$destino
    || $destino['nivel'] !== 'adm'
    || (int) $destino['ativo'] !== 1
) {
    $_SESSION['aviso_operadores'] =
        'Selecione um administrador ativo que ainda não seja root.';
    $_SESSION['aviso_operadores_sucesso'] = false;

    header('Location: operadores.php', true, 303);
    exit;
}

function escaparPromocao(string $texto): string
{
    return htmlspecialchars(
        $texto,
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );
}

$inicial = mb_strtoupper(
    mb_substr(trim($destino['nome_operador']), 0, 1, 'UTF-8'),
    'UTF-8'
);

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/sidebar.php';
?>

<style>
    .promocao-pagina {
        max-width: 820px;
        margin: auto;
        color: #18324b;
    }

    .promocao-pagina .pr-voltar {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #52677d;
        text-decoration: none;
        margin-bottom: 24px;
    }

    .promocao-pagina .pr-voltar:hover {
        color: #07306d;
        text-decoration: underline;
    }

    .promocao-pagina .pr-card {
        background: #fff;
        border: 1px solid #e5eaf1;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 12px 32px rgba(16, 42, 67, .05);
    }

    .promocao-pagina .pr-topo {
        padding: 30px;
        border-bottom: 1px solid #e5eaf1;
    }

    .promocao-pagina .pr-icone {
        display: grid;
        place-items: center;
        width: 52px;
        height: 52px;
        border-radius: 16px;
        background: #edf3ff;
        color: #07306d;
        font-size: 24px;
        margin-bottom: 18px;
    }

    .promocao-pagina .pr-corpo {
        padding: 30px;
    }

    .promocao-pagina .pr-conta {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 18px;
        background: #f7f9fc;
        border: 1px solid #e5eaf1;
        border-radius: 14px;
        margin-bottom: 24px;
    }

    .promocao-pagina .pr-avatar {
        display: grid;
        place-items: center;
        width: 46px;
        height: 46px;
        flex-shrink: 0;
        border-radius: 50%;
        background: #e6edfb;
        color: #07306d;
        font-weight: 700;
    }

    .promocao-pagina .pr-texto {
        color: #66788a;
        line-height: 1.6;
    }

    .promocao-pagina .pr-conta-dados {
        min-width: 0;
        overflow-wrap: anywhere;
    }

    .promocao-pagina .pr-aviso {
        border-left: 3px solid #d69b24;
        background: #fff9ed;
        padding: 16px 18px;
        border-radius: 0 10px 10px 0;
        color: #71551d;
        margin-bottom: 26px;
    }

    .promocao-pagina .form-control {
        border-radius: 10px;
        padding: 12px 14px;
    }

    .promocao-pagina .pr-acoes {
        display: flex;
        justify-content: flex-end;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 28px;
    }

    .promocao-pagina .pr-acoes .btn {
        border-radius: 10px;
        padding: 11px 18px;
        font-weight: 600;
    }

    .promocao-pagina .pr-confirmar {
        background: #07306d;
        border-color: #07306d;
    }

    .promocao-pagina .pr-confirmar:hover {
        background: #0b438f;
        border-color: #0b438f;
    }

    @media (max-width: 575px) {
        .promocao-pagina .pr-topo,
        .promocao-pagina .pr-corpo {
            padding: 22px;
        }

        .promocao-pagina .pr-acoes .btn {
            width: 100%;
        }
    }
</style>

<div class="content">
    <?php include __DIR__ . '/includes/navbar.php'; ?>

    <main class="promocao-pagina p-3 p-md-4 p-lg-5">
        <a href="operadores.php" class="pr-voltar">
            <i class="bi bi-arrow-left" aria-hidden="true"></i>
            Voltar para operadores
        </a>

        <section class="pr-card" aria-labelledby="titulo-promocao">
            <div class="pr-topo">
                <div class="pr-icone">
                    <i class="bi bi-shield-lock" aria-hidden="true"></i>
                </div>

                <h1 id="titulo-promocao" class="h3 fw-bold mb-2">
                    Promover a root
                </h1>

                <p class="pr-texto mb-0">
                    Confira a conta selecionada e confirme a alteração
                    com sua senha.
                </p>
            </div>

            <div class="pr-corpo">
                <div class="pr-conta">
                    <div class="pr-avatar" aria-hidden="true">
                        <?= escaparPromocao($inicial) ?>
                    </div>

                    <div class="pr-conta-dados">
                        <div class="fw-semibold">
                            <?= escaparPromocao($destino['nome_operador']) ?>
                        </div>

                        <div class="pr-texto small">
                            <?= escaparPromocao($destino['email_operador']) ?>
                        </div>

                        <div class="small mt-2">
                            Administrador
                            <i
                                class="bi bi-arrow-right mx-2"
                                aria-hidden="true"
                            ></i>
                            <strong>Root</strong>
                        </div>
                    </div>
                </div>

                <div class="pr-aviso">
                    <strong>Esta conta receberá acesso de root.</strong>
                    <p class="mb-0 mt-1 small">
                        Poderá cadastrar administradores e promover outros
                        operadores. Você continuará sendo root.
                    </p>
                </div>

                <form action="promover_operador.php" method="POST">
                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= escaparPromocao(csrfToken()) ?>"
                    >

                    <input
                        type="hidden"
                        name="id_operador"
                        value="<?= (int) $idDestino ?>"
                    >

                    <label for="senha_atual" class="form-label fw-semibold">
                        Sua senha atual
                    </label>

                    <input
                        type="password"
                        id="senha_atual"
                        name="senha_atual"
                        class="form-control"
                        autocomplete="current-password"
                        aria-describedby="ajuda-senha"
                        required
                    >

                    <div id="ajuda-senha" class="form-text">
                        Digite a senha da sua conta, não a do operador
                        que será promovido.
                    </div>

                    <div class="pr-acoes">
                        <a
                            href="operadores.php"
                            class="btn btn-outline-secondary"
                        >
                            Cancelar
                        </a>

                        <button
                            type="submit"
                            class="btn btn-primary pr-confirmar"
                        >
                            <i
                                class="bi bi-shield-check me-2"
                                aria-hidden="true"
                            ></i>
                            Confirmar promoção
                        </button>
                    </div>
                </form>
            </div>
        </section>
    </main>
</div>

<?php
include __DIR__ . '/includes/footer.php';
include __DIR__ . '/includes/scripts.php';