<?php

function listarDocumentos($conexao)
{
    $sql = "SELECT documentos.*, categorias.nome_categoria
            FROM documentos
            INNER JOIN categorias
            ON documentos.id_categoria = categorias.id_categoria
            ORDER BY documentos.id_documento DESC";

    $resultado = mysqli_query($conexao, $sql);

    if (!$resultado) {
        die("Erro na consulta: " . mysqli_error($conexao));
    }

    return $resultado;
}