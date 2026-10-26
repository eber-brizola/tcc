<?php

require_once __DIR__ . '/includes/verifica_root.php';

validarCsrf();

require_once __DIR__ . '/conexao.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

function retornarAcaoOperador(
    string $mensagem,
    bool $sucesso = false
): void {
    $_SESSION['aviso_operadores'] = $mensagem;
    $_SESSION['aviso_operadores_sucesso'] = $sucesso;

    header('Location: operadores.php', true, 303);
    exit;
}

$idAutor = (int) $_SESSION['usuario_id'];

$idDestino = is_string($_POST['id_operador'] ?? null)
    ? filter_var(
        $_POST['id_operador'],
        FILTER_VALIDATE_INT,
        ['options' => ['min_range' => 1]]
    )
    : false;

$acao = is_string($_POST['acao'] ?? null)
    ? $_POST['acao']
    : '';

$senha = is_string($_POST['senha_atual'] ?? null)
    ? $_POST['senha_atual']
    : '';

if (
    !$idDestino
    || $idDestino === $idAutor
    || !in_array($acao, ['desativar', 'reativar', 'excluir'], true)
    || $senha === ''
    || strlen($senha) > 4096
) {
    retornarAcaoOperador('Solicitação inválida. Confira os dados.');
}

// Compartilha o limite de confirmações com as outras ações de root.
$agora = time();
$controle = $_SESSION['confirmacoes_root'] ?? null;

if (
    !is_array($controle)
    || ($agora - ($controle['inicio'] ?? 0)) >= 900
) {
    $controle = ['inicio' => $agora, 'tentativas' => 0];
}

if ($controle['tentativas'] >= 5) {
    retornarAcaoOperador(
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

    // Confere autor e destinatário com os registros bloqueados.
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

    if (
        !$autor
        || (int) $autor['ativo'] !== 1
        || $autor['nivel'] !== 'root'
        || !password_verify($senha, $autor['senha_operador'])
    ) {
        mysqli_rollback($conexao);
        $transacaoAberta = false;

        retornarAcaoOperador(
            'Não foi possível autorizar a ação. Confira sua senha e seu acesso.'
        );
    }

    if (!$destino || $destino['nivel'] !== 'adm') {
        mysqli_rollback($conexao);
        $transacaoAberta = false;

        retornarAcaoOperador(
            'Somente contas de administradores podem receber essa alteração.'
        );
    }

    if (
        ($acao === 'desativar' && (int) $destino['ativo'] !== 1)
        || ($acao === 'reativar' && (int) $destino['ativo'] === 1)
    ) {
        mysqli_rollback($conexao);
        $transacaoAberta = false;

        retornarAcaoOperador(
            'A conta já está nessa situação. Atualize a listagem.'
        );
    }

    if ($acao === 'excluir') {
        $stmt = mysqli_prepare(
            $conexao,
            "DELETE FROM operadores
             WHERE id_operador = ? AND nivel = 'adm'"
        );

        mysqli_stmt_bind_param($stmt, 'i', $idDestino);

    } else {
        $ativo = $acao === 'reativar' ? 1 : 0;

        $stmt = mysqli_prepare(
            $conexao,
            "UPDATE operadores
            SET ativo = ?,
                    versao_sessao = versao_sessao + 1
            WHERE id_operador = ? AND nivel = 'adm'"
        );

        mysqli_stmt_bind_param($stmt, 'ii', $ativo, $idDestino);
    }

    mysqli_stmt_execute($stmt);

    if (mysqli_stmt_affected_rows($stmt) !== 1) {
        throw new RuntimeException('A operação não alterou uma conta.');
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
        retornarAcaoOperador(
            'Não foi possível confirmar o resultado. Confira a listagem antes de repetir.'
        );
    }

    retornarAcaoOperador('Não foi possível concluir a operação.');
}

unset($_SESSION['confirmacoes_root']);

$mensagens = [
    'desativar' => 'Administrador desativado.',
    'reativar' => 'Administrador reativado.',
    'excluir' => 'Administrador excluído definitivamente.'
];

retornarAcaoOperador($mensagens[$acao], true); 