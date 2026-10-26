<?php

require_once __DIR__ . '/includes/verifica_root.php';

validarCsrf();

require_once __DIR__ . '/conexao.php';

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

$idDestino = filter_var(
    $_POST['id_operador'] ?? null,
    FILTER_VALIDATE_INT,
    ['options' => ['min_range' => 1]]
);

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

// Limita confirmações de senha nesta sessão.
// Essa proteção complementa o limite existente no login.
$agora = time();
$controle = $_SESSION['confirmacoes_root'] ?? null;

if (
    !is_array($controle)
    || ($agora - ($controle['inicio'] ?? 0)) >= 900
) {
    $controle = ['inicio' => $agora, 'tentativas' => 0];
}

if ($controle['tentativas'] >= 5) {
    voltarOperadores(
        'Limite de confirmações atingido. Aguarde 15 minutos.'
    );
}

$controle['tentativas']++;
$_SESSION['confirmacoes_root'] = $controle;

$transacaoAberta = false;
$confirmacaoIniciada = false;

try {
    mysqli_begin_transaction($conexao);
    $transacaoAberta = true;

    // Bloqueia as duas contas em ordem de ID.
    // A autorização é conferida novamente dentro da transação.
    $stmt = mysqli_prepare(
        $conexao,
        'SELECT id_operador, senha_operador, nivel, ativo
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
            'Não foi possível confirmar o resultado. Confira o cargo na listagem.'
        );
    }

    voltarOperadores('Não foi possível promover o operador.');
}

unset($_SESSION['confirmacoes_root']);

voltarOperadores('Operador promovido a root com sucesso.', true);