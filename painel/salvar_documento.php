<?php 
require "conexao.php";

$nome =$_POST['nome'];
$categoria = $_POST['categoria'];
$arquivo = $_FILES['arquivo'];

$pasta = "uploads/";
$nomeArquivo = basename($arquivo["name"]);

$caminho = $pasta . $nomeArquivo;

if (move_uploaded_file($arquivo["tmp_name"], $caminho)){
    $sql = "INSERT INTO  documentos (nome, id_categoria, caminho) VALUES ('$nome', '$categoria', '$caminho')";
    mysqli_query($conexao, $sql);
    echo "Arquivo movido com sucesso!";
} else {
    echo " erro". mysqli_error($conexao);
}
?>