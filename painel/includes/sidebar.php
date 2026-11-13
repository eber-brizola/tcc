<?php
$paginaAtual = basename($_SERVER['SCRIPT_NAME'] ?? '');

$itensMenu = [
    [
        'url' => 'index.php',
        'titulo' => 'Dashboard',
        'icone' => 'bi-speedometer2',
        'paginas' => ['index.php']
    ],
    [
        'url' => 'novo_documento.php',
        'titulo' => 'Adicionar documento',
        'icone' => 'bi-cloud-arrow-up',
        'paginas' => ['novo_documento.php']
    ],
    [
        'url' => 'documentos.php',
        'titulo' => 'Documentos',
        'icone' => 'bi-files',
        'paginas' => ['documentos.php', 'editar_documento.php']
    ],
    [
        'url' => 'categorias.php',
        'titulo' => 'Categorias',
        'icone' => 'bi-tags',
        'paginas' => ['categorias.php', 'editar_categoria.php']
    ],
    [
        'url' => 'ajuda.php',
        'titulo' => 'Ajuda',
        'icone' => 'bi-question-circle',
        'paginas' => ['ajuda.php']
    ]
];

if (($_SESSION['usuario_nivel'] ?? null) === 'root') {
    $itensMenu[] = [
        'url' => 'operadores.php',
        'titulo' => 'Operadores',
        'icone' => 'bi-people',
        'paginas' => [
            'operadores.php',
            'novo_operador.php',
            'confirmar_promocao.php',
            'confirmar_acao_operador.php'
        ]
    ];
}
?>

<aside class="sidebar">
    <div class="sidebar-identidade">
        <a href="index.php" class="sidebar-identidade-link">
            <img
                src="../assets/img/logo-acervotech.png"
                alt="AcervoTech — CEEDO"
                class="sidebar-identidade-logo"
            >
            <span>Painel administrativo</span>
        </a>
    </div>

    <hr>

    <p class="sidebar-titulo">MENU PRINCIPAL</p>

    <nav aria-label="Menu administrativo">
        <?php foreach ($itensMenu as $item): ?>
            <?php
            $ativo = in_array($paginaAtual, $item['paginas'], true);
            ?>

            <a
                href="<?= htmlspecialchars($item['url'], ENT_QUOTES, 'UTF-8') ?>"
                class="menu-item <?= $ativo ? 'active' : '' ?>"
                <?= $ativo ? 'aria-current="page"' : '' ?>
            >
                <i
                    class="bi <?= htmlspecialchars($item['icone'], ENT_QUOTES, 'UTF-8') ?>"
                    aria-hidden="true"
                ></i>

                <span>
                    <?= htmlspecialchars($item['titulo'], ENT_QUOTES, 'UTF-8') ?>
                </span>
            </a>
        <?php endforeach; ?>
    </nav>

    <div class="sidebar-rodape">
        <a
            href="../index.php"
            target="_blank"
            rel="noopener noreferrer"
            class="menu-item"
        >
            <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i>
            <span>Ver portal público</span>
        </a>

        <form action="logout.php" method="POST" class="menu-form">
            <input
                type="hidden"
                name="csrf_token"
                value="<?= htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8') ?>"
            >

            <button type="submit" class="menu-item sair">
                <i class="bi bi-box-arrow-left" aria-hidden="true"></i>
                <span>Sair</span>
            </button>
        </form>
    </div>
</aside>