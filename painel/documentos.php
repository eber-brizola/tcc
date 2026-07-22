<?php
include "conexao.php";
include "includes/funcoes.php";

$resultado = listarDocumentos($conexao);

include "includes/header.php";
include "includes/sidebar.php";

?>

<div class="content">

    <?php include "includes/navbar.php"; ?>

    <div class="container-fluid p-4">

        <div class="d-flex justify-content-between align-items-center">

            <h2>
                Documentos
            </h2>

            <a href="novo_documento.php" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i>
                Novo Documento
            </a>

        </div>

        <div class="card shadow-sm mt-4">

            <div class="card-body">

                <div class="row mb-4">

                    <div class="col-md-8">

                        <input 
                            type="text"
                            class="form-control"
                            placeholder="Pesquisar documento..."
                        >

                    </div>

                    <div class="col-md-4">

                        <select class="form-select">

                            <option>
                                Todas categorias
                            </option>

                            <option>
                                Atas
                            </option>

                            <option>
                                Leis
                            </option>

                            <option>
                                Regimentos
                            </option>

                        </select>

                    </div>

                </div>

                <table class="table table-hover align-middle">

                    <thead class="table-dark">

                        <tr>

                            <th>
                                Documento
                            </th>

                            <th>
                                Categoria
                            </th>

                            <th>
                                Data
                            </th>

                            <th>
                                Ações
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php while ($documento = mysqli_fetch_assoc($resultado)) { ?>

                            <tr>

                                <td>
                                    <?= htmlspecialchars($documento['nome']) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($documento['nome_categoria']) ?>
                                </td>

                                <td>
                                    <?= date('d/m/Y', strtotime($documento['data_upload'])) ?>
                                </td>

                                <td>

                                    <a 
                                        href="<?= htmlspecialchars($documento['caminho']) ?>"
                                        target="_blank"
                                        class="btn btn-sm btn-primary"
                                    >
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    <button class="btn btn-sm btn-warning">
                                        <i class="bi bi-pencil"></i>
                                    </button>

                                    <button class="btn btn-sm btn-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>

                                </td>

                            </tr>

                        <?php } ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

<?php

include "includes/footer.php";

?>