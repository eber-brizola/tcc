<?php

require_once __DIR__ . '/painel/includes/sessao.php';
require_once __DIR__ . '/painel/includes/csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login/', true, 303);
    exit;
}

validarCsrf();

function falharLogin(): void
{
    $_SESSION['nao_autenticado'] = true;

    header('Location: login/', true, 303);
    exit;
}

$email = is_string($_POST['email_operador'] ?? null)
    ? trim($_POST['email_operador'])
    : '';

$senha = is_string($_POST['senha_operador'] ?? null)
    ? $_POST['senha_operador']
    : '';

if (
    !filter_var($email, FILTER_VALIDATE_EMAIL)
    || strlen($email) > 255
    || $senha === ''
    || strlen($senha) > 4096
) {
    falharLogin();
}

// Identifica as tentativas para esse e-mail.
$chave = hash('sha256', strtolower($email));

try {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    require_once __DIR__ . '/painel/conexao.php';
    
    // Limpa registros de tentativas com mais de 24 horas.
    // Executa ocasionalmente para evitar consultas desnecessárias.
    if (random_int(1, 100) === 1) {
        mysqli_query(
            $conexao,
            'DELETE FROM login_tentativas
            WHERE inicio_janela < NOW() - INTERVAL 1 DAY'
        );
    }

    // Registra a tentativa antes de conferir a senha.
    // Depois de 15 minutos, começa uma nova janela.
    $sql = '
        INSERT INTO login_tentativas
            (chave, tentativas, inicio_janela)
        VALUES (?, 1, NOW())
        ON DUPLICATE KEY UPDATE
            tentativas = IF(
                inicio_janela <= NOW() - INTERVAL 15 MINUTE,
                1,
                LEAST(tentativas + 1, 6)
            ),
            inicio_janela = IF(
                inicio_janela <= NOW() - INTERVAL 15 MINUTE,
                NOW(),
                inicio_janela
            )
    ';

    $stmt = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param($stmt, 's', $chave);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $stmt = mysqli_prepare(
        $conexao,
        'SELECT tentativas
         FROM login_tentativas
         WHERE chave = ?'
    );

    mysqli_stmt_bind_param($stmt, 's', $chave);
    mysqli_stmt_execute($stmt);

    $resultado = mysqli_stmt_get_result($stmt);
    $controle = mysqli_fetch_assoc($resultado);

    mysqli_stmt_close($stmt);

    if (!$controle) {
        throw new RuntimeException(
            'Não foi possível consultar o controle de login.'
        );
    }

    if ((int) $controle['tentativas'] > 5) {
        $_SESSION['login_bloqueado'] = true;

        header('Location: login/', true, 303);
        exit;
    }

    $stmt = mysqli_prepare(
        $conexao,
        'SELECT
            id_operador,
            nome_operador,
            email_operador,
            senha_operador,
            nivel,
            ativo,
            versao_sessao
         FROM operadores
         WHERE email_operador = ?
         LIMIT 1'
    );

    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);

    $resultado = mysqli_stmt_get_result($stmt);
    $operador = mysqli_fetch_assoc($resultado);

    mysqli_stmt_close($stmt);

    // Mantém a verificação de senha mesmo quando o e-mail não existe.
    $hashReferencia =
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.';

    $hash = $operador
        ? $operador['senha_operador']
        : $hashReferencia;

    $senhaCorreta = password_verify($senha, $hash);

    if (
        !$operador
        || !$senhaCorreta
        || (int) $operador['ativo'] !== 1
        || !in_array($operador['nivel'], ['adm', 'root'], true)
    ) {
        falharLogin();
    }

    // Login correto: limpa o contador desse e-mail.
    $stmt = mysqli_prepare(
        $conexao,
        'DELETE FROM login_tentativas WHERE chave = ?'
    );

    mysqli_stmt_bind_param($stmt, 's', $chave);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

} catch (Throwable $erro) {
    error_log($erro->getMessage());

    http_response_code(503);
    header('Content-Type: text/plain; charset=UTF-8');

    exit('Login temporariamente indisponível. Tente novamente.');
}

// Cria uma nova identificação para a sessão autenticada.
session_regenerate_id(true);

// Descarta dados da sessão anterior, incluindo o token CSRF.
$_SESSION = [];

$_SESSION['usuario_id'] = (int) $operador['id_operador'];
$_SESSION['usuario_nome'] = $operador['nome_operador'];
$_SESSION['usuario_email'] = $operador['email_operador'];
$_SESSION['usuario_nivel'] = $operador['nivel'];
$_SESSION['versao_sessao'] = (int) $operador['versao_sessao'];
$_SESSION['ultima_atividade'] = time();

header('Location: painel/', true, 303);
exit;