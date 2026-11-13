<?php
$dadosRodape = [
    'escola' => 'Colégio Estadual Dr. Dorvalino Luciano de Souza',
    'autor' => 'Eber J. Brizola Berckeuer',
    'github' => 'https://github.com/eber-brizola/tcc',
    'endereco' => '',
    'telefone' => '',
    'email' => ''
];

$prefixoRodape = $prefixoRodape ?? '';
$rodapeAdministrativo = $rodapeAdministrativo ?? false;

$textoRodape = static function (string $texto): string {
    return htmlspecialchars(
        $texto,
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );
};

$githubRodape = trim($dadosRodape['github']);

$githubValido = filter_var($githubRodape, FILTER_VALIDATE_URL)
    && strtolower((string) parse_url(
        $githubRodape,
        PHP_URL_SCHEME
    )) === 'https'
    && strtolower((string) parse_url(
        $githubRodape,
        PHP_URL_HOST
    )) === 'github.com';
?>

<footer class="portal-rodape<?= $rodapeAdministrativo ? ' portal-rodape--painel' : '' ?>">
    <div class="rodape-conteudo">

        <div class="rodape-identidade">
            <a
                href="<?= $textoRodape($prefixoRodape) ?>index.php"
                class="rodape-marca"
            >
                <span class="rodape-logo">
                    <i class="bi bi-building" aria-hidden="true"></i>
                </span>

                <span class="rodape-marca-texto">
                    <strong>CEEDO</strong>
                    <span>AcervoTech</span>
                </span>
            </a>

            <p class="rodape-escola">
                <?= $textoRodape($dadosRodape['escola']) ?>
            </p>

            <p class="rodape-descricao">
                Documentos e informações da escola,
                organizados em um só lugar.
            </p>

            <a
                href="https://maps.app.goo.gl/qRH3VRTpiixdo7qn9"
                class="rodape-local"
                target="_blank"
                rel="noopener noreferrer"
            >
                <i class="bi bi-geo-alt" aria-hidden="true"></i>
                Cerro Grande — RS
                <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i>
                <span class="rodape-sr"> — abre o mapa em nova aba</span>
            </a>
        </div>

        <nav class="rodape-coluna" aria-label="Navegação do rodapé">
            <h2>Explore o portal</h2>

            <div class="rodape-links">
                <a href="<?= $textoRodape($prefixoRodape) ?>index.php">
                    <i class="bi bi-house-door" aria-hidden="true"></i>
                    Início
                </a>

                <a href="<?= $textoRodape($prefixoRodape) ?>documentos.php">
                    <i class="bi bi-files" aria-hidden="true"></i>
                    Documentos
                </a>

                <a href="<?= $textoRodape($prefixoRodape) ?>categorias.php">
                    <i class="bi bi-tags" aria-hidden="true"></i>
                    Categorias
                </a>

                <a href="<?= $textoRodape($prefixoRodape) ?>painel/">
                    <i class="bi bi-person-lock" aria-hidden="true"></i>
                    Painel administrativo
                </a>
            </div>
        </nav>

        <div class="rodape-coluna">
            <h2>Sobre o projeto</h2>

            <p class="rodape-descricao">
                Desenvolvido como projeto do curso
                técnico em Informática.
            </p>

            <div class="rodape-autor">
                <i class="bi bi-code-slash" aria-hidden="true"></i>

                <div>
                    <span>Desenvolvido por</span>
                    <strong>
                        <?= $textoRodape($dadosRodape['autor']) ?>
                    </strong>
                </div>
            </div>

            <?php if ($githubValido): ?>
                <a
                    class="rodape-github"
                    href="<?= $textoRodape($githubRodape) ?>"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    <i class="bi bi-github" aria-hidden="true"></i>
                    GitHub do projeto
                    <i class="bi bi-arrow-up-right" aria-hidden="true"></i>
                    <span class="rodape-sr"> — abre em nova aba</span>
                </a>
            <?php endif; ?>
        </div>
    </div>

    <?php if (
        $dadosRodape['endereco'] !== ''
        || $dadosRodape['telefone'] !== ''
        || $dadosRodape['email'] !== ''
    ): ?>
        <address class="rodape-contato">
            <?php if ($dadosRodape['endereco'] !== ''): ?>
                <span>
                    <i class="bi bi-geo-alt" aria-hidden="true"></i>
                    <?= $textoRodape($dadosRodape['endereco']) ?>
                </span>
            <?php endif; ?>

            <?php if ($dadosRodape['telefone'] !== ''): ?>
                <span>
                    <i class="bi bi-telephone" aria-hidden="true"></i>
                    <?= $textoRodape($dadosRodape['telefone']) ?>
                </span>
            <?php endif; ?>

            <?php if ($dadosRodape['email'] !== ''): ?>
                <span>
                    <i class="bi bi-envelope" aria-hidden="true"></i>
                    <?= $textoRodape($dadosRodape['email']) ?>
                </span>
            <?php endif; ?>
        </address>
    <?php endif; ?>

    <div class="rodape-base">
        <span>© <?= date('Y') ?> CEEDO · AcervoTech</span>
        <span>Portal de Transparência Escolar</span>
    </div>
</footer>

<?php
unset(
    $prefixoRodape,
    $rodapeAdministrativo,
    $dadosRodape,
    $textoRodape,
    $githubRodape,
    $githubValido
);
?>