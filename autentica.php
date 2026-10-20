<?php

session_start();

require_once __DIR__ . '/painel/includes/csrf.php';

// Só aceita acesso através do formulário de login.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login/');
    exit;
}

// Valida antes de consultar o banco ou conferir a senha.
validarCsrf();

require_once __DIR__ . '/painel/conexao.php';

// Recebe e limpa os dados
$email = is_string($_POST['email_operador'] ?? null)
    ? trim($_POST['email_operador'])
    : '';

$senha = is_string($_POST['senha_operador'] ?? null)
    ? $_POST['senha_operador']
    : '';

// Verifica campos vazios
if ($email === '' || $senha === '') {
    $_SESSION['nao_autenticado'] = true;

    header('Location: login/');
    exit;
}

// Procura o operador apenas pelo e-mail
$sql = "SELECT
            id_operador,
            nome_operador,
            email_operador,
            senha_operador,
            nivel,
            ativo
        FROM operadores
        WHERE email_operador = ?
        LIMIT 1";

$stmt = mysqli_prepare($conexao, $sql);

if (!$stmt) {
    error_log(mysqli_error($conexao));

    $_SESSION['nao_autenticado'] = true;

    header('Location: login/');
    exit;
}

mysqli_stmt_bind_param($stmt, 's', $email);
mysqli_stmt_execute($stmt);

$resultado = mysqli_stmt_get_result($stmt);
$operador = mysqli_fetch_assoc($resultado);

mysqli_stmt_close($stmt);

// Usuário não existe
if (!$operador) {
    $_SESSION['nao_autenticado'] = true;

    header('Location: login/');
    exit;
}

// Usuário está desativado
if ((int) $operador['ativo'] !== 1) {
    $_SESSION['nao_autenticado'] = true;

    header('Location: login/');
    exit;
}

// Confere a senha digitada com o hash armazenado no banco
if (!password_verify($senha, $operador['senha_operador'])) {
    $_SESSION['nao_autenticado'] = true;

    header('Location: login/');
    exit;
}

// Login correto: renova a sessão e descarta o token anterior.
session_regenerate_id(true);
unset($_SESSION['csrf_token']);

$_SESSION['usuario_id'] = (int) $operador['id_operador'];
$_SESSION['usuario_nome'] = $operador['nome_operador'];
$_SESSION['usuario_email'] = $operador['email_operador'];
$_SESSION['usuario_nivel'] = $operador['nivel'];

header('Location: painel/');
exit;