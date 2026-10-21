<?php
require_once __DIR__ . '/../painel/includes/sessao.php';

require_once __DIR__ . '/../painel/includes/csrf.php';

$erroLogin = !empty($_SESSION['nao_autenticado']);
unset($_SESSION['nao_autenticado']);
?>


<!doctype html>
<html lang="en">
  <head>
  	<title>Login 01</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<link href="https://fonts.googleapis.com/css?family=Lato:300,400,700&display=swap" rel="stylesheet">

	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
	
	<link rel="stylesheet" href="css/style.css">

	</head>
	<body>
	<section class="ftco-section">
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-md-6 text-center mb-5">
					<h2 class="heading-section">ENTRAR</h2>
				</div>
			</div>
			<form action="../autentica.php" method="POST">
				<input
					type="hidden"
					name="csrf_token"
					value="<?= htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8') ?>"
				>
				<div class="row justify-content-center">
					<div class="col-md-7 col-lg-5">
						<div class="login-wrap p-4 p-md-5">
					<div class="icon d-flex align-items-center justify-content-center">
						<span class="fa fa-user-o"></span>
					</div>
					
						<?php if ($erroLogin): ?>
							<div class="alert alert-danger" role="alert">
								Não foi possível entrar. Verifique seu e-mail e sua senha e tente novamente.
							</div>
						<?php endif; ?>

						<div class="form-group">
							<input type="email" class="form-control rounded-left" placeholder="Email de usuário" name="email_operador" id="email_operador" required>
						</div>
					<div class="form-group d-flex">
					<input type="password" class="form-control rounded-left" placeholder="Senha" name="senha_operador" id="cpf_operador" required>
					</div>
					<div class="form-group">
						<button type="submit" class="form-control btn btn-primary rounded submit px-3">Entrar</button>
					</div>
					<div class="form-group d-md-flex">
						<div class="w-50">
							<label class="checkbox-wrap checkbox-primary">Lembre-se de mim	
										<input type="checkbox" checked>
										<span class="checkmark"></span>
										</label>
									</div>
									<div class="w-50 text-md-right">
										<a href="#">Recuperar senha</a>
									</div>
					</div>
				</div>
					</div>
				</div>
			</div>
			</form>
	</section>

	<script src="js/jquery.min.js"></script>
  <script src="js/popper.js"></script>
  <script src="js/bootstrap.min.js"></script>
  <script src="js/main.js"></script>

	</body>
</html>

