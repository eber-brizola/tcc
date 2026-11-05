<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.use_trans_sid', '0');

    $https = (
        !empty($_SERVER['HTTPS'])
        && strtolower((string) $_SERVER['HTTPS']) !== 'off'
    );

    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => $https,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);

    if (!session_start()) {
        error_log('Não foi possível iniciar a sessão.');
        http_response_code(503);
        header('Content-Type: text/plain; charset=UTF-8');
        exit('O serviço está temporariamente indisponível. Tente novamente.');
    }
}

// Páginas autenticadas e formulários de login não devem ficar no cache.
header('Cache-Control: no-store');
