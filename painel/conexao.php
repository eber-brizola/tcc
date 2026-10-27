<?php

// Registra os erros no servidor sem mostrá-los ao visitante.
ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');
ini_set('log_errors', '1');

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

define('HOST', 'localhost');
define('USUARIO', 'u948351601_acervotech');
define('SENHA', 'CEEDOu948351601');
define('DB', 'u948351601_acervotech');

try {
    $conexao = mysqli_connect(
        HOST,
        USUARIO,
        SENHA,
        DB
    );

    mysqli_set_charset($conexao, 'utf8mb4');

} catch (Throwable $erro) {
    error_log('Falha na conexão com o banco: ' . $erro->getMessage());

    http_response_code(503);
    header('Content-Type: text/plain; charset=UTF-8');
    header('Cache-Control: no-store');

    exit(
        'O serviço está temporariamente indisponível. '
        . 'Tente novamente mais tarde.'
    );
}