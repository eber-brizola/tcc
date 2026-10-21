
<?php
require_once __DIR__ . '/includes/sessao.php';
// A saída só pode ser solicitada por POST.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Confere o token de segurança.
$token = $_POST['csrf_token'] ?? '';

if (
    !is_string($token) ||
    empty($_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $token)
) {
    http_response_code(403);
    exit('Solicitação inválida. Atualize a página e tente novamente.');
}

// Limpa os dados da sessão.
$_SESSION = [];

// Invalida o cookie da sessão, quando utilizado.
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();

    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

session_destroy();

header('Location: ../login/');
exit;
