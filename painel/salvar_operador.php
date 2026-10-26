<?php

require_once __DIR__ . '/includes/verifica_root.php';

validarCsrf();

require_once __DIR__ . '/conexao.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

function retornarOperador(
    string $mensagem,
    bool $sucesso = false
): void {
    $_SESSION['mensagem_operador'] = $mensagem;
    $_SESSION['operador_sucesso'] = $sucesso;

    header('Location: novo_operador.php', true, 303);
    exit;
}

$nome = is_string($_POST['nome'] ?? null)
    ? trim($_POST['nome'])
    : '';

$email = is_string($_POST['email'] ?? null)
    ? strtolower(trim($_POST['email']))
    : '';

$senha = is_string($_POST['senha'] ?? null)
    ? $_POST['senha']
    : '';

$confirmacao = is_string($_POST['confirmacao'] ?? null)
    ? $_POST['confirmacao']
    : '';

if (
    $nome === ''
    || !mb_check_encoding($nome, 'UTF-8')
    || mb_strlen($nome, 'UTF-8') > 255
) {
    retornarOperador('Informe um nome válido de até 255 caracteres.');
}

if (
    strlen($email) > 255
    || !filter_var($email, FILTER_VALIDATE_EMAIL)
) {
    retornarOperador('Informe um e-mail válido.');
}

// Bcrypt utiliza no máximo 72 bytes da senha.
// Caracteres acentuados podem ocupar mais de um byte.
if (
    !mb_check_encoding($senha, 'UTF-8')
    || mb_strlen($senha, 'UTF-8') < 15
    || strlen($senha) > 72
    || str_contains($senha, "\0")
) {
    retornarOperador(
        'A senha deve ter pelo menos 15 caracteres e no máximo '
        . '72 bytes. Caracteres acentuados podem ocupar mais espaço.'
    );
}

if ($senha !== $confirmacao) {
    retornarOperador('As senhas não coincidem.');
}

try {
    $hash = password_hash($senha, PASSWORD_BCRYPT);

    // O cargo é definido no servidor.
    // Não aceitamos um cargo enviado pelo formulário.
    $stmt = mysqli_prepare(
        $conexao,
        "INSERT INTO operadores (
            nome_operador,
            email_operador,
            senha_operador,
            nivel,
            ativo
        ) VALUES (?, ?, ?, 'adm', 1)"
    );

    mysqli_stmt_bind_param($stmt, 'sss', $nome, $email, $hash);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

} catch (mysqli_sql_exception $erro) {
    if ((int) $erro->getCode() === 1062) {
        retornarOperador('Já existe uma conta com esse e-mail.');
    }

    error_log($erro->getMessage());

    retornarOperador('Não foi possível cadastrar o administrador.');

} catch (Throwable $erro) {
    error_log($erro->getMessage());

    retornarOperador('Não foi possível cadastrar o administrador.');
}

retornarOperador('Administrador cadastrado com sucesso.', true);