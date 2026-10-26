<?php

require_once __DIR__ . '/sessao.php';
require_once __DIR__ . '/csrf.php';

function encerrarAcesso(): void
{
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $parametros = session_get_cookie_params();

        setcookie(session_name(), '', [
            'expires' => time() - 3600,
            'path' => $parametros['path'],
            'domain' => $parametros['domain'],
            'secure' => $parametros['secure'],
            'httponly' => $parametros['httponly'],
            'samesite' => $parametros['samesite'] ?? 'Lax'
        ]);
    }

    session_destroy();

    header('Location: ../login/', true, 303);
    exit;
}

// Exige um ID válido na sessão.
$idOperador = filter_var(
    $_SESSION['usuario_id'] ?? null,
    FILTER_VALIDATE_INT,
    ['options' => ['min_range' => 1]]
);

if (!$idOperador) {
    encerrarAcesso();
}

// Encerra o acesso após 30 minutos sem requisições ao painel.
$agora = time();
$limiteInatividade = 30 * 60;
$ultimaAtividade = $_SESSION['ultima_atividade'] ?? $agora;

if (
    !is_int($ultimaAtividade)
    || ($agora - $ultimaAtividade) >= $limiteInatividade
) {
    encerrarAcesso();
}

// Confere a situação atual do operador no banco.
try {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    require_once __DIR__ . '/../conexao.php';

    $stmt = mysqli_prepare(
        $conexao,
        'SELECT nome_operador, email_operador, nivel, ativo
         FROM operadores
         WHERE id_operador = ?
         LIMIT 1'
    );

    mysqli_stmt_bind_param($stmt, 'i', $idOperador);
    mysqli_stmt_execute($stmt);

    $resultado = mysqli_stmt_get_result($stmt);
    $operador = mysqli_fetch_assoc($resultado);

    mysqli_stmt_close($stmt);
} catch (Throwable $erro) {
    error_log($erro->getMessage());

    http_response_code(503);
    exit('Não foi possível verificar seu acesso. Tente novamente.');
}

if (
    !$operador
    || (int) $operador['ativo'] !== 1
    || !in_array($operador['nivel'], ['adm', 'root'], true)
) {
    encerrarAcesso();
}

// Atualiza a sessão usando os dados atuais do banco.
$_SESSION['usuario_nome'] = $operador['nome_operador'];
$_SESSION['usuario_email'] = $operador['email_operador'];
$_SESSION['usuario_nivel'] = $operador['nivel'];
$_SESSION['ultima_atividade'] = $agora;