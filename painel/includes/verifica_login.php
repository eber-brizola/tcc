<?php

require_once __DIR__ . '/sessao.php';

require_once __DIR__ . '/csrf.php';

// Verifica se o usuário está autenticado
if (
    !isset(
        $_SESSION['usuario_id'],
        $_SESSION['usuario_nome'],
        $_SESSION['usuario_email'],
        $_SESSION['usuario_nivel']
    )
) {
    $_SESSION['nao_autenticado'] = true;

    header('Location: ../login/');
    exit;
}

// Aceita apenas níveis válidos
if (
    $_SESSION['usuario_nivel'] !== 'adm' &&
    $_SESSION['usuario_nivel'] !== 'root'
) {
    session_unset();
    session_destroy();

    header('Location: ../login/');
    exit;
}