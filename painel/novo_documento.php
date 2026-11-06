<?php

require_once __DIR__ . '/includes/verifica_login.php';
require_once __DIR__ . '/conexao.php';

$sqlCategorias = "SELECT id_categoria, nome_categoria
                  FROM categorias
                  ORDER BY nome_categoria ASC";

$categorias = mysqli_query($conexao, $sqlCategorias);

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/sidebar.php';
?>

<style>
    .formulario-documento .upload-documento {
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 12px;
        min-height: 230px;
        padding: 28px 20px;
        border: 2px dashed #9ec5fe;
        border-radius: 16px;
        background: #f0f6ff;
        text-align: center;
        cursor: pointer;
    }

    .formulario-documento .upload-documento > i {
        font-size: 2.5rem;
        color: #0d6efd;
    }

    .formulario-documento .upload-documento:hover {
        border-style: solid;
    }

    .formulario-documento .upload-documento.selecionado {
        border-color: #198754;
        background: #f0faf4;
    }

    .formulario-documento .upload-documento.selecionado > i {
        color: #198754;
    }

    .formulario-documento .upload-documento.invalido {
        border-color: #dc3545;
        background: #fff5f5;
    }

    .formulario-documento .upload-documento.invalido > i {
        color: #dc3545;
    }

    .formulario-documento .upload-status {
        width: 100%;
        min-width: 0;
        overflow-wrap: anywhere;
    }

    .formulario-documento .upload-status strong {
        display: block;
        color: #102a43;
    }

    .formulario-documento .upload-status span {
        display: block;
        margin-top: 8px;
        color: #526174;
        font-size: 0.9rem;
    }

    /* Invisível, mas clicável e acessível pelo teclado. */
    .formulario-documento .upload-documento #arquivo {
        position: absolute;
        inset: 0;
        z-index: 1;
        display: block;
        width: 100%;
        height: 100%;
        margin: 0;
        padding: 0;
        opacity: 0;
        cursor: pointer;
    }

    .formulario-documento .upload-documento:focus-within {
        outline: 3px solid #86b7fe;
        outline-offset: 4px;
    }

    .formulario-documento .upload-ajuda {
        color: #526174;
        font-size: 0.8rem;
    }
</style>

<div class="content">

    <?php include __DIR__ . '/includes/navbar.php'; ?>

    <main class="container-fluid p-4 p-lg-5">

        <section class="pagina-top">
            <div class="d-flex align-items-center gap-3">
                <div class="pagina-top-icone">
                    <i class="bi bi-cloud-arrow-up" aria-hidden="true"></i>
                </div>

                <div>
                    <p class="text-primary fw-semibold mb-1">
                        Documentos
                    </p>

                    <h1>Adicionar documento</h1>

                    <p class="text-muted mb-0">
                        Preencha os dados e envie o arquivo para publicação.
                    </p>
                </div>
            </div>

            <a href="documentos.php" class="btn btn-outline-primary">
                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                Voltar
            </a>
        </section>

        <section class="card formulario-documento">
            <div class="card-body p-4 p-lg-5">

                <form
                    action="salvar_documento.php"
                    method="POST"
                    enctype="multipart/form-data"
                >
                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8') ?>"
                    >

                    <div class="row g-4">
                        <div class="col-lg-7">

                            <div class="mb-4">
                                <label for="nome" class="form-label">
                                    Nome do documento
                                </label>

                                <input
                                    type="text"
                                    class="form-control form-control-lg"
                                    name="nome"
                                    id="nome"
                                    placeholder="Ex.: Ata da reunião pedagógica"
                                    maxlength="255"
                                    required
                                >
                            </div>

                            <div>
                                <label for="categoria" class="form-label">
                                    Categoria
                                </label>

                                <select
                                    name="categoria"
                                    id="categoria"
                                    class="form-select form-select-lg"
                                    required
                                >
                                    <option value="">
                                        Selecione uma categoria
                                    </option>

                                    <?php while ($categoria = mysqli_fetch_assoc($categorias)): ?>
                                        <option value="<?= (int) $categoria['id_categoria'] ?>">
                                            <?= htmlspecialchars(
                                                $categoria['nome_categoria'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                        </div>

                        <div class="col-lg-5">
                            <label for="arquivo" class="form-label">
                                Arquivo
                            </label>

                            <div class="upload-documento" id="areaUpload">
                                <i
                                    class="bi bi-file-earmark-arrow-up"
                                    id="iconeArquivo"
                                    aria-hidden="true"
                                ></i>

                                <div
                                    class="upload-status"
                                    role="status"
                                    aria-live="polite"
                                    aria-atomic="true"
                                >
                                    <strong id="nomeArquivo">
                                        Selecionar arquivo
                                    </strong>

                                    <span id="detalhesArquivo">
                                        Clique nesta área para procurar no computador.
                                    </span>
                                </div>

                                <input
                                    type="file"
                                    name="arquivo"
                                    id="arquivo"
                                    accept=".pdf,.jpg,.jpeg,.png"
                                    aria-describedby="ajudaArquivo detalhesArquivo"
                                    required
                                >

                                <small id="ajudaArquivo" class="upload-ajuda">
                                    PDF, JPG ou PNG, com até 10 MB.
                                    Clique nesta área para selecionar ou trocar o arquivo.
                                </small>
                            </div>
                        </div>
                    </div>

                    <hr class="my-5">

                    <div class="d-flex flex-wrap justify-content-end gap-2">
                        <a href="documentos.php" class="btn btn-outline-secondary">
                            <i class="bi bi-x-lg" aria-hidden="true"></i>
                            Cancelar
                        </a>

                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-cloud-arrow-up" aria-hidden="true"></i>
                            Salvar documento
                        </button>
                    </div>
                </form>

            </div>
        </section>

    </main>
</div>

<script>
(() => {
    const campo = document.getElementById('arquivo');
    const area = document.getElementById('areaUpload');
    const nome = document.getElementById('nomeArquivo');
    const detalhes = document.getElementById('detalhesArquivo');
    const icone = document.getElementById('iconeArquivo');

    const limite = 10 * 1024 * 1024;
    const extensoesPermitidas = ['pdf', 'jpg', 'jpeg', 'png'];

    function formatarTamanho(bytes) {
        if (bytes < 1024) {
            return `${bytes} bytes`;
        }

        if (bytes < 1024 * 1024) {
            return `${(bytes / 1024).toLocaleString('pt-BR', {
                maximumFractionDigits: 1
            })} KB`;
        }

        return `${(bytes / (1024 * 1024)).toLocaleString('pt-BR', {
            maximumFractionDigits: 2
        })} MB`;
    }

    function atualizarArquivo() {
        const arquivo = campo.files[0];

        campo.setCustomValidity('');
        campo.removeAttribute('aria-invalid');
        area.classList.remove('selecionado', 'invalido');

        if (!arquivo) {
            nome.textContent = 'Selecionar arquivo';
            detalhes.textContent =
                'Clique nesta área para procurar no computador.';
            icone.className = 'bi bi-file-earmark-arrow-up';
            return;
        }

        nome.textContent = arquivo.name;

        const extensao = arquivo.name.includes('.')
            ? arquivo.name.split('.').pop().toLowerCase()
            : '';

        let erro = '';

        if (!extensoesPermitidas.includes(extensao)) {
            erro = 'Formato não permitido. Selecione PDF, JPG ou PNG.';
        } else if (arquivo.size === 0) {
            erro = 'O arquivo está vazio. Selecione outro arquivo.';
        } else if (arquivo.size > limite) {
            erro = 'O arquivo ultrapassa 10 MB. Selecione um menor.';
        }

        if (erro) {
            area.classList.add('invalido');
            icone.className = 'bi bi-exclamation-circle';

            detalhes.textContent =
                `${formatarTamanho(arquivo.size)} — ${erro}`;

            campo.setCustomValidity(erro);
            campo.setAttribute('aria-invalid', 'true');
            return;
        }

        area.classList.add('selecionado');
        icone.className = 'bi bi-file-earmark-check';

        detalhes.textContent =
            `${formatarTamanho(arquivo.size)} — Arquivo selecionado. ` +
            'Clique em Salvar documento para enviar.';
    }

    campo.addEventListener('change', atualizarArquivo);
    window.addEventListener('pageshow', atualizarArquivo);

    atualizarArquivo();
})();
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>