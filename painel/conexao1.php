<?php
// Registra os erros no servidor sem mostrá-los ao visitante.
ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');
ini_set('log_errors', '1');

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
header('Content-Type: text/html; charset=utf-8');

define('HOST', 'localhost');
define('USUARIO', 'u948351601_acervotech');
define('SENHA', 'CEEDOu948351601');
define('DB', 'u948351601_acervotech');

$conexao = mysqli_connect(
    HOST,
    USUARIO,
    SENHA,
    DB
);

if (!$conexao) {
    die("ERRO na conexão: " . mysqli_connect_error());
}

mysqli_set_charset($conexao, "utf8mb4");