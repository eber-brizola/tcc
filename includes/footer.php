<?php
/* PREENCHA SEUS DADOS AQUI. Campos vazios de contato não aparecem.
   GitHub: cole o endereço completo, começando por https://github.com/ */
$dadosRodape = [
    'escola' => 'COlÉGIO ESTADUAL DE EDUCAÇÃO DOM ORIONE',
    'autor' => 'Eber J. brizola Berckeuer',
    'descricao' => 'Documentos e informações da escola em um só lugar.',
    'endereco' => '',
    'telefone' => '',
    'email' => '',
    'github' => 'https://github.com/eber-brizola/tcc.git',
];

$prefixoRodape = $prefixoRodape ?? '';
$rodapeAdministrativo = $rodapeAdministrativo ?? false;
$textoRodape = static function (string $texto): string {
    return htmlspecialchars($texto, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
};
// Só transforma o endereço em link se for uma URL HTTPS do GitHub.
$githubRodape = trim($dadosRodape['github']);
$githubValido = filter_var($githubRodape, FILTER_VALIDATE_URL)
    && strtolower((string) parse_url($githubRodape, PHP_URL_SCHEME)) === 'https'
    && strtolower((string) parse_url($githubRodape, PHP_URL_HOST)) === 'github.com';
?>
<footer class="portal-rodape<?= $rodapeAdministrativo ? ' portal-rodape--painel' : '' ?>">
    <div class="rodape-conteudo">
        <div class="rodape-identidade">
            <a href="<?= $textoRodape($prefixoRodape) ?>index.php" class="rodape-marca">CEEDO <span>AcervoTech</span></a>
            <p class="rodape-escola"><?= $textoRodape($dadosRodape['escola']) ?></p>
            <p><?= $textoRodape($dadosRodape['descricao']) ?></p>
            <?php if ($rodapeAdministrativo): ?><span class="rodape-etiqueta">Painel administrativo</span><?php endif; ?>
        </div>
        <nav class="rodape-coluna" aria-label="Navegação do rodapé">
            <h2>Explore o portal</h2>
            <div class="rodape-links">
                <a href="<?= $textoRodape($prefixoRodape) ?>index.php">Início</a>
                <a href="<?= $textoRodape($prefixoRodape) ?>documentos.php">Documentos</a>
                <a href="<?= $textoRodape($prefixoRodape) ?>categorias.php">Categorias</a>
            </div>
        </nav>
        <div class="rodape-coluna">
            <h2>Sobre o projeto</h2>
            <p>Desenvolvido por<br><strong><?= $textoRodape($dadosRodape['autor']) ?></strong></p>
            <p class="rodape-projeto">Projeto do curso técnico em Informática.</p>
            <?php if ($githubValido): ?>
                <a class="rodape-github" href="<?= $textoRodape($githubRodape) ?>" target="_blank" rel="noopener noreferrer">GitHub do projeto <span aria-hidden="true">↗</span><span class="rodape-sr"> (abre em nova aba)</span></a>
            <?php else: ?>
                <span class="rodape-github rodape-github-pendente">GitHub do projeto</span>
            <?php endif; ?>
        </div>
    </div>
    <?php if ($dadosRodape['endereco'] !== '' || $dadosRodape['telefone'] !== '' || $dadosRodape['email'] !== ''): ?>
        <address class="rodape-contato" aria-label="Contato da escola">
            <?php if ($dadosRodape['endereco'] !== ''): ?><span><strong>Endereço</strong> <?= $textoRodape($dadosRodape['endereco']) ?></span><?php endif; ?>
            <?php if ($dadosRodape['telefone'] !== ''): ?><span><strong>Telefone</strong> <?= $textoRodape($dadosRodape['telefone']) ?></span><?php endif; ?>
            <?php if ($dadosRodape['email'] !== ''): ?><span><strong>E-mail</strong> <?= $textoRodape($dadosRodape['email']) ?></span><?php endif; ?>
        </address>
    <?php endif; ?>
    <div class="rodape-base"><span>© <?= date('Y') ?> <?= $textoRodape($dadosRodape['escola']) ?></span><span>Portal de Transparência Escolar</span></div>
</footer>
<?php unset($prefixoRodape, $rodapeAdministrativo, $dadosRodape, $textoRodape, $githubRodape, $githubValido); ?>
