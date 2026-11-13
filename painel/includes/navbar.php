<nav class="navbar bg-white shadow-sm">

<div class="container-fluid">

<h5 class="mb-0"><i class="bi bi-building" aria-hidden="true"></i> Painel CEEDO</h5>


<?php
$nomeUsuarioTopo = is_string($_SESSION['usuario_nome'] ?? null)
    ? trim($_SESSION['usuario_nome'])
    : '';

$nomeUsuarioTopo = $nomeUsuarioTopo !== ''
    ? $nomeUsuarioTopo
    : 'Usuário';

$cargoUsuarioTopo = match ($_SESSION['usuario_nivel'] ?? null) {
    'root' => 'Root',
    'adm' => 'Administrador',
    default => 'Usuário'
};
?>

<div class="usuario-topo">
    <span class="usuario-topo-avatar">
        <i class="bi bi-person" aria-hidden="true"></i>
    </span>

    <div class="usuario-topo-dados">
        <span class="usuario-topo-nome">
            <?= htmlspecialchars(
                $nomeUsuarioTopo,
                ENT_QUOTES | ENT_SUBSTITUTE,
                'UTF-8'
            ) ?>
        </span>

        <span class="usuario-topo-cargo">
            <?= htmlspecialchars($cargoUsuarioTopo, ENT_QUOTES, 'UTF-8') ?>
        </span>
    </div>
</div>

<link rel="stylesheet" href="assets/css/style.css">
</div>

</nav>