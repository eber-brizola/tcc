<?php
require_once __DIR__ . '/../painel/includes/sessao.php';
require_once __DIR__ . '/../painel/includes/csrf.php';

$erroLogin = !empty($_SESSION['nao_autenticado']);
$loginBloqueado = !empty($_SESSION['login_bloqueado']);
unset($_SESSION['nao_autenticado'], $_SESSION['login_bloqueado']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrar — CEEDO | AcervoTech</title>
    <link rel="stylesheet" href="css/acesso.css">
    <link rel="stylesheet" href="../assets/css/rodape.css">
</head>
<body class="pagina-login">
    <header class="login-cabecalho">
        <a class="login-marca" href="../index.php" aria-label="CEEDO, página inicial">
            <span class="marca-simbolo" aria-hidden="true">C</span>
            <span>CEEDO<small>AcervoTech</small></span>
        </a>
        <a class="login-voltar" href="../index.php"><span aria-hidden="true">←</span> Voltar ao portal</a>
    </header>

    <main class="login-principal">
        <section class="login-apresentacao" aria-labelledby="titulo-portal">
            <span class="login-etiqueta">PORTAL DE TRANSPARÊNCIA ESCOLAR</span>
            <h1 id="titulo-portal">Informação organizada.<br><span>Escola conectada.</span></h1>
            <p>Um espaço para organizar e compartilhar os documentos da nossa instituição.</p>
            <div class="login-ilustracao" aria-hidden="true">
                <div class="ilustracao-cabecalho"><span class="ilustracao-quadrado"></span><span>Acervo escolar</span><span class="ilustracao-pontos">···</span></div>
                <div class="ilustracao-documento"><span class="documento-desenho"></span><div><strong>Documentos institucionais</strong><span class="ilustracao-linha"></span></div><span class="ilustracao-check">✓</span></div>
                <div class="ilustracao-documento"><span class="documento-desenho"></span><div><strong>Atas e regimentos</strong><span class="ilustracao-linha curta"></span></div><span class="ilustracao-check">✓</span></div>
                <div class="ilustracao-base">Organização · Acesso · Transparência</div>
            </div>
            <p class="login-publico">Quer consultar um arquivo? <a href="../documentos.php">Explore os documentos públicos <span aria-hidden="true">↗</span></a></p>
        </section>

        <section class="login-cartao" aria-labelledby="titulo-login">
            <span class="login-selo">ÁREA ADMINISTRATIVA</span>
            <h2 id="titulo-login">Bem-vindo de volta</h2>
            <p class="login-instrucao">Entre com sua conta para gerenciar o acervo.</p>

            <?php if ($loginBloqueado): ?>
                <div class="login-aviso login-aviso-alerta" role="alert">
                    <strong>Limite de tentativas atingido.</strong>
                    Aguarde até 15 minutos antes de tentar novamente.
                </div>
            <?php elseif ($erroLogin): ?>
                <div class="login-aviso login-aviso-erro" role="alert">
                    <strong>Não foi possível entrar.</strong>
                    Verifique seu e-mail e sua senha e tente novamente.
                </div>
            <?php endif; ?>

            <form action="../autentica.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8') ?>">
                <div class="login-campo">
                    <label for="email_operador">E-mail</label>
                    <input type="email" name="email_operador" id="email_operador" placeholder="Seu e-mail cadastrado" autocomplete="username" autocapitalize="none" spellcheck="false" maxlength="255" required>
                </div>
                <div class="login-campo">
                    <label for="senha_operador">Senha</label>
                    <div class="login-senha">
                        <input type="password" name="senha_operador" id="senha_operador" autocomplete="current-password" placeholder="Digite sua senha" maxlength="4096" required>
                        <button type="button" id="alternar-senha" aria-controls="senha_operador" aria-label="Mostrar senha" aria-pressed="false" hidden>Mostrar</button>
                    </div>
                </div>
                <button type="submit" class="login-entrar">Entrar no painel <span aria-hidden="true">→</span></button>
            </form>
            <p class="login-ajuda">Precisa de acesso ou esqueceu sua senha?<br>Procure o responsável pelo portal na escola.</p>
        </section>
    </main>

    <?php $prefixoRodape = '../'; require __DIR__ . '/../includes/footer.php'; ?>
    <script src="js/acesso.js" defer></script>
</body>
</html>
