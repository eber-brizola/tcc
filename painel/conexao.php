<?php

header('Content-Type: text/html; charset=utf-8');

define('HOST', 'localhost');
define('USUARIO', 'root');
define('SENHA', '');
define('DB', 'docs_ceedo');

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