<?php
session_start();

include('painel/conexao.php');

if(empty($_POST['email_operador']) || empty($_POST['senha_operador'])){
    $_SESSION['nao_autenticado'] = true;

    header('Location: login/');
    exit;
}

$email_operador = mysqli_real_escape_string($conexao, $_POST['email_operador']);
$senha_operador = mysqli_real_escape_string($conexao, $_POST['senha_operador']);

$query = "SELECT * FROM operadores 
WHERE email_operador = '$email_operador' 
AND senha_operador = '$senha_operador' LIMIT 1";

$result = mysqli_query($conexao, $query);

if($result && mysqli_num_rows($result) == 1){

    $_SESSION['usuario'] = $email_operador;

    session_regenerate_id(true);

    header('Location: painel/');
    exit;
}

$_SESSION['nao_autenticado'] = true;

header('Location: login/index.php');
exit;
?>