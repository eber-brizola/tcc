<?php
require_once __DIR__ . '/includes/verifica_login.php';
require_once __DIR__ . "/conexao.php";

// Valida o ID recebido pela URL.
$idDocumento = filter_var(
    $_GET["id"] ?? null,
    FILTER_VALIDATE_INT,
    ["options" => ["min_range" => 1]]
);

if (!$idDocumento) {
    header("Location: documentos.php");
    exit;
}

// Busca o documento.
$stmt = mysqli_prepare(
    $conexao,
    "SELECT id_documento, nome, id_categoria
     FROM documentos
     WHERE id_documento = ?"
);

mysqli_stmt_bind_param($stmt, "i", $idDocumento);
mysqli_stmt_execute($stmt);

$resultado = mysqli_stmt_get_result($stmt);
$documento = mysqli_fetch_assoc($resultado);

mysqli_stmt_close($stmt);

if (!$documento) {
    header("Location: documentos.php?" . http_build_query([
        "mensagem" => "Documento não encontrado.",
        "tipo" => "erro"
    ]));
    exit;
}

// Busca as opções do campo de categoria.
$categorias = mysqli_query(
    $conexao,
    "SELECT id_categoria, nome_categoria
     FROM categorias
     ORDER BY nome_categoria ASC"
);

include "includes/header.php";
include "includes/sidebar.php";
?>

<div class="content">

    <?php include "includes/navbar.php"; ?>

    <main class="container-fluid p-4 p-lg-5">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Editar documento</h1>

            <a href="documentos.php" class="btn btn-outline-primary">
                Voltar
            </a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-4">

                <form action="atualizar_documento.php" method="POST" enctype="multipart/form-data">

                    <input
                        type="hidden"
                        name="id_documento"
                        value="<?= (int) $documento['id_documento'] ?>"
                    >

                    <div class="mb-3">
                        <label for="nome" class="form-label">
                            Nome do documento
                        </label>

                        <input
                            type="text"
                            name="nome"
                            id="nome"
                            class="form-control"
                            value="<?= htmlspecialchars(
                                $documento['nome'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            required
                        >
                    </div>

                    <div class="mb-3">
                        <label for="categoria" class="form-label">
                            Categoria
                        </label>

                        <select
                            name="categoria"
                            id="categoria"
                            class="form-select"
                            required
                        >
                            <?php while ($categoria = mysqli_fetch_assoc($categorias)): ?>
                                <option
                                    value="<?= (int) $categoria['id_categoria'] ?>"
                                    <?= (int) $categoria['id_categoria'] ===
                                        (int) $documento['id_categoria']
                                        ? 'selected'
                                        : '' ?>
                                >
                                    <?= htmlspecialchars(
                                        $categoria['nome_categoria'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="arquivo" class="form-label">
                            Substituir arquivo (opcional)
                        </label>

                        <input
                            type="file"
                            name="arquivo"
                            id="arquivo"
                            class="form-control"
                            accept=".pdf,.jpg,.jpeg,.png"
                        >

                        <div class="form-text">
                            Deixe vazio para manter o arquivo atual.
                            Formatos permitidos: PDF, JPG e PNG. Máximo de 10 MB.
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="documentos.php" class="btn btn-light">
                            Cancelar
                        </a>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg"></i>
                            Salvar alterações
                        </button>
                    </div>

                </form>

            </div>
        </div>

    </main>

</div>

<?php include "includes/footer.php"; ?>