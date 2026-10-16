<?php
$paginaAtual = basename($_SERVER["PHP_SELF"]);

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

?>

<aside class="sidebar">

    <div class="sidebar-logo">
        <div class="logo-icon">
            <i class="bi bi-building"></i>
        </div>

        <div>
            <h3>CEEDO</h3>
            <p>Painel Administrativo</p>
        </div>
    </div>

    <hr>

    <p class="sidebar-titulo">MENU PRINCIPAL</p>

    <a
        href="index.php"
        class="<?= $paginaAtual === "index.php" ? "active" : "" ?>"
    >
        <i class="bi bi-grid-1x2"></i>
        Dashboard
    </a>

    <a
        href="novo_documento.php"
        class="<?= $paginaAtual === "novo_documento.php" ? "active" : "" ?>"
    >
        <i class="bi bi-cloud-arrow-up"></i>
        Adicionar documento
    </a>

    <a
        href="documentos.php"
        class="<?= $paginaAtual === "documentos.php" ? "active" : "" ?>"
    >
        <i class="bi bi-files"></i>
        Documentos
    </a>

    <a
        href="categorias.php"
        class="<?= $paginaAtual === "categorias.php" ? "active" : "" ?>"
    >
        <i class="bi bi-tags"></i>
        Categorias
    </a>

    <div class="sidebar-rodape">

        <a href="../index.php" target="_blank">
            <i class="bi bi-box-arrow-up-right"></i>
            Ver portal público
        </a>

        
        <form action="logout.php" method="POST">
            <input
                type="hidden"
                name="csrf_token"
                value="<?= htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8') ?>"
            >

            <button type="submit" class="sair">
                <i class="bi bi-box-arrow-left"></i>
                Sair
            </button>
        </form>


    </div>

</aside>