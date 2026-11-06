<?php
require_once __DIR__ . '/includes/verifica_login.php';

$ehRoot = ($_SESSION['usuario_nivel'] ?? null) === 'root';

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/sidebar.php';
?>

<div class="content">

    <?php include __DIR__ . '/includes/navbar.php'; ?>

    <main class="container-fluid p-4 p-lg-5">

        <section class="pagina-top">
            <div class="d-flex align-items-center gap-3">
                <div class="pagina-top-icone">
                    <i class="bi bi-question-circle" aria-hidden="true"></i>
                </div>

                <div>
                    <p class="text-primary fw-semibold mb-1">
                        Manual do administrador
                    </p>

                    <h1>Ajuda</h1>

                    <p class="text-muted mb-0">
                        Saiba como organizar e publicar os documentos da escola.
                    </p>
                </div>
            </div>
        </section>

        <div class="alert alert-primary">
            <i class="bi bi-info-circle me-2" aria-hidden="true"></i>
            Os documentos publicados ficam disponíveis no portal público.
            Confira o conteúdo antes de enviar e publique apenas arquivos
            autorizados para divulgação.
        </div>

        <div class="row g-4">

            <div class="col-lg-6">
                <section class="card shadow-sm h-100">
                    <div class="card-body p-4">
                        <h2 class="h5 fw-bold mb-3">
                            <i class="bi bi-cloud-arrow-up text-primary me-2"
                               aria-hidden="true"></i>
                            Publicar um documento
                        </h2>

                        <ol class="ps-3">
                            <li class="mb-2">
                                Abra <strong>Adicionar documento</strong> no menu.
                            </li>
                            <li class="mb-2">
                                Preencha um nome claro e selecione a categoria.
                            </li>
                            <li class="mb-2">
                                Clique na área de upload e escolha um arquivo.
                            </li>
                            <li class="mb-2">
                                Confira o nome e o tamanho do arquivo selecionado.
                            </li>
                            <li>
                                Clique em <strong>Salvar documento</strong>
                                e aguarde a mensagem de confirmação.
                            </li>
                        </ol>

                        <p class="text-muted small">
                            Formatos aceitos: PDF, JPG e PNG.
                            Tamanho máximo: 10 MB.
                            A área verde indica a seleção do arquivo;
                            o envio acontece ao salvar.
                        </p>

                        <a href="novo_documento.php"
                           class="btn btn-outline-primary">
                            <i class="bi bi-plus-circle" aria-hidden="true"></i>
                            Adicionar documento
                        </a>
                    </div>
                </section>
            </div>

            <div class="col-lg-6">
                <section class="card shadow-sm h-100">
                    <div class="card-body p-4">
                        <h2 class="h5 fw-bold mb-3">
                            <i class="bi bi-files text-primary me-2"
                               aria-hidden="true"></i>
                            Editar ou excluir documentos
                        </h2>

                        <p>
                            Abra <strong>Documentos</strong> e localize o arquivo.
                            Use <strong>Visualizar</strong> para conferir o conteúdo
                            ou <strong>Editar</strong> para alterar os dados.
                        </p>

                        <p>
                            Na edição, escolha um novo arquivo somente se quiser
                            substituir o atual. Sem selecionar outro arquivo,
                            o documento existente será mantido.
                        </p>

                        <p class="text-danger">
                            A exclusão não possui opção de desfazer no painel.
                            Confirme o documento antes de excluir e mantenha
                            uma cópia dos arquivos importantes.
                        </p>

                        <a href="documentos.php"
                           class="btn btn-outline-primary">
                            <i class="bi bi-files" aria-hidden="true"></i>
                            Gerenciar documentos
                        </a>
                    </div>
                </section>
            </div>

            <div class="col-lg-6">
                <section class="card shadow-sm h-100">
                    <div class="card-body p-4">
                        <h2 class="h5 fw-bold mb-3">
                            <i class="bi bi-tags text-primary me-2"
                               aria-hidden="true"></i>
                            Categorias e ícones
                        </h2>

                        <ol class="ps-3">
                            <li class="mb-2">
                                Abra <strong>Categorias</strong>.
                            </li>
                            <li class="mb-2">
                                Informe o nome e, se desejar, uma descrição.
                            </li>
                            <li class="mb-2">
                                Selecione o ícone que representa a categoria.
                            </li>
                            <li>
                                Clique em <strong>Adicionar categoria</strong>.
                            </li>
                        </ol>

                        <p>
                            Para trocar o nome, a descrição ou o ícone de uma
                            categoria existente, clique em <strong>Editar</strong>
                            e depois em <strong>Salvar alterações</strong>.
                        </p>

                        <p class="text-muted small">
                            O nome aceita até 100 caracteres e a descrição,
                            até 255 caracteres.
                        </p>

                        <a href="categorias.php"
                           class="btn btn-outline-primary">
                            <i class="bi bi-tags" aria-hidden="true"></i>
                            Gerenciar categorias
                        </a>
                    </div>
                </section>
            </div>

            <div class="col-lg-6">
                <section class="card shadow-sm h-100">
                    <div class="card-body p-4">
                        <h2 class="h5 fw-bold mb-3">
                            <i class="bi bi-search text-primary me-2"
                               aria-hidden="true"></i>
                            Consultar o portal público
                        </h2>

                        <p>
                            No portal, abra <strong>Documentos</strong> para
                            pesquisar pelo nome, selecionar filtros e alterar
                            a ordenação dos resultados.
                        </p>

                        <p>
                            Use <strong>Visualizar</strong> para abrir um arquivo
                            ou <strong>Download</strong> para salvá-lo no dispositivo.
                            A consulta pública não exige uma conta administrativa.
                        </p>

                        <p>
                            Depois de publicar ou editar, confira o resultado
                            no portal.
                        </p>

                        <a href="../documentos.php"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="btn btn-outline-primary">
                            <i class="bi bi-box-arrow-up-right"
                               aria-hidden="true"></i>
                            Abrir documentos públicos
                            <span class="visually-hidden">em uma nova aba</span>
                        </a>
                    </div>
                </section>
            </div>

            <?php if ($ehRoot): ?>
                <div class="col-12">
                    <section class="card shadow-sm">
                        <div class="card-body p-4">
                            <h2 class="h5 fw-bold mb-3">
                                <i class="bi bi-people text-primary me-2"
                                   aria-hidden="true"></i>
                                Gerenciar operadores
                            </h2>

                            <p>
                                A seção <strong>Operadores</strong> está disponível
                                para contas root. Nela, você pode cadastrar
                                administradores e acessar as ações permitidas
                                para cada conta.
                            </p>

                            <ul class="ps-3">
                                <li class="mb-2">
                                    <strong>Promover a root:</strong>
                                    concede acesso à gestão de operadores.
                                    Use apenas para responsáveis autorizados.
                                </li>
                                <li class="mb-2">
                                    <strong>Desativar:</strong>
                                    bloqueia o acesso da conta ao painel.
                                </li>
                                <li class="mb-2">
                                    <strong>Reativar:</strong>
                                    permite que a conta volte a acessar o painel.
                                </li>
                                <li>
                                    <strong>Excluir:</strong>
                                    remove definitivamente a conta de administrador.
                                </li>
                            </ul>

                            <p class="text-muted">
                                As ações sensíveis solicitam sua senha atual.
                                Se atingir o limite de confirmações, aguarde
                                o prazo informado antes de tentar novamente.
                            </p>

                            <a href="operadores.php"
                               class="btn btn-outline-primary">
                                <i class="bi bi-people" aria-hidden="true"></i>
                                Gerenciar operadores
                            </a>
                        </div>
                    </section>
                </div>
            <?php endif; ?>

            <div class="col-12">
                <section class="card shadow-sm">
                    <div class="card-body p-4">
                        <h2 class="h5 fw-bold mb-3">
                            <i class="bi bi-life-preserver text-primary me-2"
                               aria-hidden="true"></i>
                            Dúvidas e problemas
                        </h2>

                        <dl class="mb-0">
                            <dt>O arquivo foi recusado.</dt>
                            <dd class="mb-3">
                                Confira se é PDF, JPG ou PNG, se não está vazio
                                e se possui até 10 MB. Se o problema continuar,
                                procure o responsável técnico.
                            </dd>

                            <dt>Não foi possível confirmar o cadastro.</dt>
                            <dd class="mb-3">
                                Confira a listagem antes de enviar novamente
                                para evitar cadastrar o mesmo documento duas vezes.
                            </dd>

                            <dt>A sessão expirou.</dt>
                            <dd class="mb-3">
                                Entre novamente no painel. Alterações de formulário
                                que ainda não foram salvas podem precisar ser refeitas.
                            </dd>

                            <dt>Esqueci minha senha ou perdi o acesso.</dt>
                            <dd class="mb-3">
                                Procure o responsável pelo portal na escola.
                                Não compartilhe sua senha com outras pessoas.
                            </dd>

                            <dt>Terminei de usar o painel.</dt>
                            <dd class="mb-0">
                                Clique em <strong>Sair</strong>, principalmente
                                ao utilizar computadores compartilhados.
                            </dd>
                        </dl>
                    </div>
                </section>
            </div>

        </div>
    </main>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>