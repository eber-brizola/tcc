<?php

require_once __DIR__ . '/includes/verifica_root.php';
require_once __DIR__ . '/conexao.php';

try {
    $resultado = mysqli_query(
        $conexao,
        'SELECT id_operador, nome_operador, email_operador, nivel, ativo
         FROM operadores
         ORDER BY nome_operador, id_operador'
    );

    $operadores = mysqli_fetch_all($resultado, MYSQLI_ASSOC);

} catch (Throwable $erro) {
    error_log($erro->getMessage());
    http_response_code(503);
    exit('Não foi possível carregar os operadores.');
}

function escaparOperador(string $texto): string
{
    return htmlspecialchars(
        $texto,
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );
}

$totalAdms = 0;
$totalRoots = 0;

foreach ($operadores as $operador) {
    if ($operador['nivel'] === 'adm') {
        $totalAdms++;
    }

    if ($operador['nivel'] === 'root') {
        $totalRoots++;
    }
}

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/sidebar.php';

$aviso = $_SESSION['aviso_operadores'] ?? '';
$avisoSucesso = ($_SESSION['aviso_operadores_sucesso'] ?? false) === true;

unset(
    $_SESSION['aviso_operadores'],
    $_SESSION['aviso_operadores_sucesso']
);
?>

<style>
    .operadores-pagina {
        --op-azul: #07306d;
        --op-borda: #e5eaf1;
        color: #18324b;
        max-width: 1400px;
        margin: auto;
    }

    .operadores-pagina .op-subtitulo {
        color: #66788a;
        max-width: 560px;
    }

    .operadores-pagina .op-botao {
        background: var(--op-azul);
        border-color: var(--op-azul);
        border-radius: 12px;
        padding: 12px 18px;
        font-weight: 600;
    }

    .operadores-pagina .op-botao:hover {
        background: #0b438f;
        border-color: #0b438f;
    }

    .operadores-pagina .op-resumo {
        background: #fff;
        border: 1px solid var(--op-borda);
        border-radius: 16px;
        padding: 22px;
        display: flex;
        align-items: center;
        gap: 16px;
        height: 100%;
    }

    .operadores-pagina .op-icone {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: grid;
        place-items: center;
        background: #edf3ff;
        color: var(--op-azul);
        font-size: 22px;
        flex-shrink: 0;
    }

    .operadores-pagina .op-numero {
        font-size: 28px;
        font-weight: 700;
        line-height: 1.2;
    }

    .operadores-pagina .op-legenda {
        color: #66788a;
        font-size: 14px;
        margin-top: 4px;
    }

    .operadores-pagina .op-card {
        background: #fff;
        border: 1px solid var(--op-borda);
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 8px 28px rgba(16, 42, 67, .04);
    }

    .operadores-pagina .op-card-topo {
        padding: 24px;
        border-bottom: 1px solid var(--op-borda);
    }

    .operadores-pagina .op-tabela {
        margin: 0;
        min-width: 650px;
    }

    .operadores-pagina .op-tabela thead th {
        background: #f7f9fc;
        color: #66788a;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .06em;
        padding: 16px 24px;
        border-bottom: 1px solid var(--op-borda);
    }

    .operadores-pagina .op-tabela tbody td {
        padding: 20px 24px;
        border-bottom: 1px solid #f0f3f7;
        vertical-align: middle;
    }

    .operadores-pagina .op-tabela tbody tr:last-child td {
        border-bottom: 0;
    }

    .operadores-pagina .op-avatar {
        width: 44px;
        height: 44px;
        background: #edf3ff;
        color: var(--op-azul);
        border-radius: 50%;
        display: grid;
        place-items: center;
        font-weight: 700;
        flex-shrink: 0;
    }

    .operadores-pagina .op-nome {
        font-weight: 600;
        overflow-wrap: anywhere;
    }

    .operadores-pagina .op-email {
        color: #66788a;
        font-size: 14px;
        overflow-wrap: anywhere;
    }

    .operadores-pagina .op-etiqueta {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 11px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
    }

    .operadores-pagina .op-root {
        color: #6645a6;
        background: #f0ebfb;
    }

    .operadores-pagina .op-adm {
        color: #215ca0;
        background: #edf4ff;
    }

    .operadores-pagina .op-ativo {
        color: #196645;
        background: #eaf7ef;
    }

    .operadores-pagina .op-inativo {
        color: #626d7c;
        background: #f0f2f5;
    }

    .operadores-pagina .op-voce {
        font-size: 11px;
        background: #edf0f5;
        color: #526174;
        border-radius: 5px;
        padding: 3px 6px;
    }

    .operadores-pagina .op-rodape {
        padding: 16px 24px;
        background: #fafbfd;
        border-top: 1px solid var(--op-borda);
        color: #66788a;
        font-size: 13px;
    }

    @media (max-width: 575px) {
        .operadores-pagina .op-botao {
            width: 100%;
        }

        .operadores-pagina .op-card-topo {
            padding: 18px;
        }
    }
</style>

<div class="content">
    <?php include __DIR__ . '/includes/navbar.php'; ?>

    <main class="operadores-pagina p-3 p-md-4 p-lg-5">
        <?php if (is_string($aviso) && $aviso !== ''): ?>
             <div
                class="alert alert-<?= $avisoSucesso ? 'success' : 'danger' ?>"
                role="alert"
            >
                <?= escaparOperador($aviso) ?>
            </div>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-start
                    flex-wrap gap-3 mb-4">
            <div>
                <div class="small fw-semibold text-primary mb-2">
                    ADMINISTRAÇÃO
                </div>

                <h1 class="h2 fw-bold mb-2">Operadores</h1>

                <p class="op-subtitulo mb-0">
                    Consulte as contas que administram o acervo
                    e cadastre novos administradores.
                </p>
            </div>

            <a href="novo_operador.php" class="btn btn-primary op-botao">
                <i class="bi bi-person-plus me-2" aria-hidden="true"></i>
                Novo administrador
            </a>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-12 col-md-4">
                <div class="op-resumo">
                    <div class="op-icone">
                        <i class="bi bi-people" aria-hidden="true"></i>
                    </div>
                    <div>
                        <div class="op-numero"><?= count($operadores) ?></div>
                        <div class="op-legenda">Total de operadores</div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="op-resumo">
                    <div class="op-icone">
                        <i class="bi bi-person-badge" aria-hidden="true"></i>
                    </div>
                    <div>
                        <div class="op-numero"><?= $totalAdms ?></div>
                        <div class="op-legenda">Administradores</div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="op-resumo">
                    <div class="op-icone">
                        <i class="bi bi-shield-lock" aria-hidden="true"></i>
                    </div>
                    <div>
                        <div class="op-numero"><?= $totalRoots ?></div>
                        <div class="op-legenda">Contas root</div>
                    </div>
                </div>
            </div>
        </div>

        <section class="op-card" aria-labelledby="titulo-contas">
            <div class="op-card-topo">
                <h2 id="titulo-contas" class="h5 fw-bold mb-1">
                    Contas cadastradas
                </h2>
                <p class="op-subtitulo small mb-0">
                    Identificação, cargo e situação de cada operador.
                </p>
            </div>

            <div
                class="table-responsive"
                role="region"
                aria-label="Lista de operadores"
                tabindex="0"
            >
                <table class="table op-tabela">
                    <thead>
                        <tr>
                            <th scope="col">Operador</th>
                            <th scope="col">Cargo</th>
                            <th scope="col">Situação</th>
                            <th scope="col">Ações</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($operadores as $operador): ?>
                            <?php
                            $ehRoot = $operador['nivel'] === 'root';
                            $ativo = (int) $operador['ativo'] === 1;
                            $souEu = (int) $operador['id_operador']
                                === (int) $_SESSION['usuario_id'];

                            $inicial = mb_strtoupper(
                                mb_substr(
                                    trim($operador['nome_operador']),
                                    0,
                                    1,
                                    'UTF-8'
                                ),
                                'UTF-8'
                            );
                            ?>

                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="op-avatar" aria-hidden="true">
                                            <?= escaparOperador($inicial) ?>
                                        </div>

                                        <div>
                                            <div class="d-flex align-items-center
                                                        flex-wrap gap-2 mb-1">
                                                <span class="op-nome">
                                                    <?= escaparOperador(
                                                        $operador['nome_operador']
                                                    ) ?>
                                                </span>

                                                <?php if ($souEu): ?>
                                                    <span class="op-voce">Você</span>
                                                <?php endif; ?>
                                            </div>

                                            <div class="op-email">
                                                <?= escaparOperador(
                                                    $operador['email_operador']
                                                ) ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <span class="op-etiqueta
                                        <?= $ehRoot ? 'op-root' : 'op-adm' ?>">
                                        <i
                                            class="bi <?= $ehRoot
                                                ? 'bi-shield-lock'
                                                : 'bi-person-badge' ?>"
                                            aria-hidden="true"
                                        ></i>
                                        <?= $ehRoot ? 'Root' : 'Administrador' ?>
                                    </span>
                                </td>

                                <td>
                                    <span class="op-etiqueta
                                        <?= $ativo ? 'op-ativo' : 'op-inativo' ?>">
                                        <i
                                            class="bi <?= $ativo
                                                ? 'bi-check-circle'
                                                : 'bi-pause-circle' ?>"
                                            aria-hidden="true"
                                        ></i>
                                        <?= $ativo ? 'Ativo' : 'Inativo' ?>
                                    </span>
                                </td>

                                <td>
                                    <?php if (!$ehRoot && !$souEu): ?>
                                        <div class="d-flex flex-wrap gap-2">
                                            <?php if ($ativo): ?>
                                                <a
                                                    href="confirmar_promocao.php?id=<?= (int) $operador['id_operador'] ?>"
                                                    class="btn btn-sm btn-outline-primary text-nowrap"
                                                >
                                                    Promover a root
                                                </a>
                                            <?php endif; ?>

                                            <a
                                                href="confirmar_acao_operador.php?id=<?= (int) $operador['id_operador'] ?>&acao=<?= $ativo ? 'desativar' : 'reativar' ?>"
                                                class="btn btn-sm btn-outline-secondary"
                                            >
                                                <?= $ativo ? 'Desativar' : 'Reativar' ?>
                                            </a>

                                            <a
                                                href="confirmar_acao_operador.php?id=<?= (int) $operador['id_operador'] ?>&acao=excluir"
                                                class="btn btn-sm btn-outline-danger"
                                            >
                                                Excluir
                                            </a>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted small">Conta root protegida</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        <?php if (!$operadores): ?>
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <i
                                        class="bi bi-people fs-1 text-secondary"
                                        aria-hidden="true"
                                    ></i>
                                    <p class="fw-semibold mt-3 mb-1">
                                        Nenhum operador encontrado
                                    </p>
                                    <p class="op-subtitulo mx-auto mb-0">
                                        As contas cadastradas aparecerão aqui.
                                    </p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="op-rodape">
                <i class="bi bi-lock me-1" aria-hidden="true"></i>
                Esta página está disponível somente para usuários root.
            </div>
        </section>

    </main>
</div>

<?php
include __DIR__ . '/includes/footer.php';
include __DIR__ . '/includes/scripts.php';