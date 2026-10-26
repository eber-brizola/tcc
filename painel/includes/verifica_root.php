<?php

// Confere login, expiração, usuário ativo e cargo atual no banco.
require_once __DIR__ . '/verifica_login.php';

// Somente root pode continuar.
if (($_SESSION['usuario_nivel'] ?? null) !== 'root') {
    http_response_code(403);
    header('Content-Type: text/plain; charset=UTF-8');
    header('Cache-Control: no-store');

    exit('Você não tem permissão para acessar esta função.');
}