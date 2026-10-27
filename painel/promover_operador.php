<?php

require_once __DIR__ . '/includes/verifica_root.php';

validarCsrf();

require_once __DIR__ . '/conexao.php';
require_once __DIR__ . '/includes/limite_root.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

function voltarOperadores(
    string $mensagem,
    bool $sucesso = false
): void {
    $_SESSION['aviso_operadores'] = $mensagem;
    $_SESSION['aviso_operadores_sucesso'] = $sucesso;

    header('Location: operadores.php', true, 303);
    exit;
}

$idDestino = is_string($_POST['id_operador'] ?? null)
    ? filter_var(
        $_POST['id_operador'],
        FILTER_VALIDATE_INT,
        ['options' => ['min_range' => 1]]
    )
    : false;

$idAutor = (int) $_SESSION['usuario_id'];

$senha = is_string($_POST['senha_atual'] ?? null)
    ? $_POST['senha_atual']
    : '';

if (
    !$idDestino
    || $idDestino === $idAutor
    || $senha === ''
    || strlen($senha) > 4096
) {
    voltarOperadores('Informe um operador válido e sua senha atual.');
}

$transacaoAberta = false;
$confirmacaoIniciada = false;

try {
    // Usa o mesmo contador de desativação, reativação e exclusão.
    if (!reservarConfirmacaoRoot($conexao, $idAutor)) {
        voltarOperadores(
            'Limite de 5 confirmações atingido. '
            . 'Aguarde o fim da janela de 15 minutos.'
        );
    }

    mysqli_begin_transaction($conexao);
    $transacaoAberta = true;

    $stmt = mysqli_prepare(
        $conexao,
        'SELECT id_operador, senha_operador, nivel, ativo, versao_sessao
         FROM operadores
         WHERE id_operador IN (?, ?)
         ORDER BY id_operador
         FOR UPDATE'
    );

    mysqli_stmt_bind_param($stmt, 'ii', $idAutor, $idDestino);
    mysqli_stmt_execute($stmt);

    $resultado = mysqli_stmt_get_result($stmt);
    $contas = [];

    while ($conta = mysqli_fetch_assoc($resultado)) {
        $contas[(int) $conta['id_operador']] = $conta;
    }

    mysqli_stmt_close($stmt);

    $autor = $contas[$idAutor] ?? null;
    $destino = $contas[$idDestino] ?? null;

    $autorValido = (
        $autor
        && (int) $autor['ativo'] === 1
        && $autor['nivel'] === 'root'
        && ($_SESSION['versao_sessao'] ?? null)
            === (int) $autor['versao_sessao']
        && password_verify($senha, $autor['senha_operador'])
    );

    if (!$autorValido) {
        mysqli_rollback($conexao);
        $transacaoAberta = false;

        voltarOperadores(
            'Não foi possível autorizar a ação. Confira sua senha e seu acesso.'
        );
    }

    if (
        !$destino
        || (int) $destino['ativo'] !== 1
        || $destino['nivel'] !== 'adm'
    ) {
        mysqli_rollback($conexao);
        $transacaoAberta = false;

        voltarOperadores(
            'Selecione um administrador ativo que ainda não seja root.'
        );
    }

    $stmt = mysqli_prepare(
        $conexao,
        "UPDATE operadores
         SET nivel = 'root',
             versao_sessao = versao_sessao + 1
         WHERE id_operador = ? AND nivel = 'adm' AND ativo = 1"
    );

    mysqli_stmt_bind_param($stmt, 'i', $idDestino);
    mysqli_stmt_execute($stmt);

    if (mysqli_stmt_affected_rows($stmt) !== 1) {
        throw new RuntimeException('A promoção não alterou uma conta.');
    }

    mysqli_stmt_close($stmt);

    $confirmacaoIniciada = true;
    mysqli_commit($conexao);
    $transacaoAberta = false;

} catch (Throwable $erro) {
    if ($transacaoAberta) {
        try {
            mysqli_rollback($conexao);
        } catch (Throwable $erroRollback) {
            error_log($erroRollback->getMessage());
        }
    }

    error_log($erro->getMessage());

    if ($confirmacaoIniciada) {
        voltarOperadores(
            'Não foi possível confirmar o resultado. '
            . 'Confira o cargo na listagem.'
        );
    }

    voltarOperadores('Não foi possível promover o operador.');
}

voltarOperadores('Operador promovido a root com sucesso.', true);