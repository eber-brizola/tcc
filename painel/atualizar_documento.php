<?php
require_once __DIR__ . '/includes/verifica_login.php';

validarCsrf();

require_once __DIR__ . '/conexao.php';

// Faz os erros do MySQLi gerarem exceções para o catch.
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

function retornar($mensagem, $tipo = "erro")
{
    header("Location: documentos.php?" . http_build_query([
        "mensagem" => $mensagem,
        "tipo" => $tipo
    ]), true, 303);

    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: documentos.php");
    exit;
}

// Validação dos campos.
$idDocumento = filter_var(
    $_POST["id_documento"] ?? null,
    FILTER_VALIDATE_INT,
    ["options" => ["min_range" => 1]]
);

$categoria = filter_var(
    $_POST["categoria"] ?? null,
    FILTER_VALIDATE_INT,
    ["options" => ["min_range" => 1]]
);

$nome = is_string($_POST["nome"] ?? null)
    ? trim($_POST["nome"])
    : "";

if (
    !mb_check_encoding($nome, 'UTF-8')
    || mb_strlen($nome, 'UTF-8') > 255
) {
    retornar('O nome do documento deve ser válido e ter até 255 caracteres.');
}

if (!$idDocumento || !$categoria || $nome === "") {
    retornar(
        "Dados inválidos. Confira os campos e o limite de upload do servidor."
    );
}

// O arquivo é opcional.
$arquivo = $_FILES["arquivo"] ?? null;
$substituirArquivo = false;


if ($arquivo !== null) {
    if (
        !is_array($arquivo) ||
        !isset($arquivo["error"]) ||
        !is_int($arquivo["error"])
    ) {
        retornar("Arquivo inválido.");
    }

    if ($arquivo["error"] === UPLOAD_ERR_OK) {
        $substituirArquivo = true;
    } elseif ($arquivo["error"] !== UPLOAD_ERR_NO_FILE) {
        retornar("O envio falhou. Confira o tamanho e tente novamente.");
    }
}

$pasta = __DIR__ . "/uploads/";
$caminhoNovoCompleto = null;
$arquivoNovoMovido = false;
$transacaoAberta = false;
$caminhoAntigo = null;
$confirmacaoIniciada = false;

try {
    // Valida o arquivo novo antes de alterar o banco.
    if ($substituirArquivo) {
        if (
            !is_string($arquivo["tmp_name"] ?? null) ||
            !is_uploaded_file($arquivo["tmp_name"])
        ) {
            retornar("O arquivo enviado é inválido.");
        }

        $tamanho = filesize($arquivo["tmp_name"]);
        $limite = 10 * 1024 * 1024;

        if (
            $tamanho === false ||
            $tamanho === 0 ||
            $tamanho > $limite
        ) {
            retornar("O arquivo deve ter conteúdo e no máximo 10 MB.");
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $tipo = $finfo->file($arquivo["tmp_name"]);

        $permitidos = [
            "application/pdf" => "pdf",
            "image/jpeg" => "jpg",
            "image/png" => "png"
        ];

        if (!isset($permitidos[$tipo])) {
            retornar("Envie somente PDF, JPG ou PNG.");
        }

        $extensao = $permitidos[$tipo];
    }

    // Confirma a categoria.
    $stmt = mysqli_prepare(
        $conexao,
        "SELECT id_categoria
         FROM categorias
         WHERE id_categoria = ?"
    );

    mysqli_stmt_bind_param($stmt, "i", $categoria);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    $categoriaExiste = mysqli_stmt_num_rows($stmt) > 0;
    mysqli_stmt_close($stmt);

    if (!$categoriaExiste) {
        retornar("Categoria não encontrada.");
    }

    // Inicia uma transação.
    mysqli_begin_transaction($conexao);
    $transacaoAberta = true;

    // Bloqueia o registro durante esta atualização.
    $stmt = mysqli_prepare(
        $conexao,
        "SELECT caminho
         FROM documentos
         WHERE id_documento = ?
         FOR UPDATE"
    );

    mysqli_stmt_bind_param($stmt, "i", $idDocumento);
    mysqli_stmt_execute($stmt);

    $resultado = mysqli_stmt_get_result($stmt);
    $documento = mysqli_fetch_assoc($resultado);

    mysqli_stmt_close($stmt);

    if (!$documento) {
        mysqli_rollback($conexao);
        $transacaoAberta = false;

        retornar("Documento não encontrado.");
    }

    $caminhoAntigo = $documento["caminho"];

    // Por padrão, mantém o arquivo existente.
    $caminho = $caminhoAntigo;

    if ($substituirArquivo) {
        if (
            !is_dir($pasta) &&
            !mkdir($pasta, 0755, true) &&
            !is_dir($pasta)
        ) {
            throw new RuntimeException(
                "Não foi possível criar a pasta de uploads."
            );
        }

        // Gera um nome diferente para o arquivo novo.
        do {
            $nomeArquivo = bin2hex(random_bytes(16)) . "." . $extensao;
            $caminhoNovoCompleto = $pasta . $nomeArquivo;
        } while (file_exists($caminhoNovoCompleto));

        $caminho = "uploads/" . $nomeArquivo;

        if (!move_uploaded_file(
            $arquivo["tmp_name"],
            $caminhoNovoCompleto
        )) {
            throw new RuntimeException("Falha ao guardar o arquivo novo.");
        }

        $arquivoNovoMovido = true;
    }

    // Atualiza os dados, mantendo o mesmo ID e a data de publicação.
    $stmt = mysqli_prepare(
        $conexao,
        "UPDATE documentos
         SET nome = ?, id_categoria = ?, caminho = ?
         WHERE id_documento = ?"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "sisi",
        $nome,
        $categoria,
        $caminho,
        $idDocumento
    );

    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // A partir daqui, uma falha de conexão pode deixar
    // incerto se o banco confirmou a atualização.
    $confirmacaoIniciada = true;

    mysqli_commit($conexao);
    $transacaoAberta = false;

} catch (Throwable $erro) {
    if ($transacaoAberta) {
        try {
            mysqli_rollback($conexao);
        } catch (Throwable $erroRollback) {
            error_log($erroRollback->getMessage());
        }
    }

    // Só remove o arquivo novo se a falha ocorreu
    // antes de tentar confirmar a transação.
    if (
        !$confirmacaoIniciada
        && $arquivoNovoMovido
        && $caminhoNovoCompleto !== null
    ) {
        if (!unlink($caminhoNovoCompleto)) {
            error_log(
                "Falha ao remover upload novo: "
                . $caminhoNovoCompleto
            );
        }
    }

    error_log($erro->getMessage());

    if ($confirmacaoIniciada) {
        retornar(
            "Não foi possível confirmar o resultado da atualização. "
            . "Confira o documento na listagem antes de tentar novamente."
        );
    }

    retornar("Não foi possível atualizar o documento.");
}

// A atualização já foi confirmada.
// Só agora tentamos remover o arquivo antigo.
if ($substituirArquivo && $caminhoAntigo !== $caminho) {
    try {
        // Arquivos antigos podem estar associados a mais de um registro.
        $stmt = mysqli_prepare(
            $conexao,
            "SELECT COUNT(*)
             FROM documentos
             WHERE caminho = ?"
        );

        mysqli_stmt_bind_param($stmt, "s", $caminhoAntigo);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $referencias);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);

        if ((int) $referencias === 0) {
            $pastaReal = realpath($pasta);
            $arquivoAntigoReal = realpath(
                __DIR__ . "/" . $caminhoAntigo
            );

            // Só remove arquivos realmente localizados dentro de uploads.
            if (
                $pastaReal !== false &&
                $arquivoAntigoReal !== false &&
                str_starts_with(
                    $arquivoAntigoReal,
                    $pastaReal . DIRECTORY_SEPARATOR
                ) &&
                is_file($arquivoAntigoReal)
            ) {
                if (!unlink($arquivoAntigoReal)) {
                    throw new RuntimeException(
                        "Não foi possível remover o arquivo antigo."
                    );
                }
            }
        }

    } catch (Throwable $erro) {
        // Uma falha de limpeza não desfaz uma atualização concluída.
        error_log($erro->getMessage());

        retornar(
            "Documento atualizado. O arquivo antigo foi mantido "
            . "porque não foi possível concluir a limpeza.",
            "sucesso"
        );
    }
}

retornar("Documento atualizado com sucesso!", "sucesso");