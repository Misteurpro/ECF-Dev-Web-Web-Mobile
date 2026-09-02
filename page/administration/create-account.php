<?php
if(is_admin()):
	define("EMPLOYEE_CREATOR", "");
	$email = '';
	$username = '';
	if(isset($_POST["email"]) && isset($_POST["username"]) && isset($_POST["password"]))
	{
		$error_signin = signin_employee($_POST["username"], $_POST["email"], $_POST["password"]);

		$email = isset($_POST["email"])?" value=\"{$_POST["email"]}\"":'';
		$username = isset($_POST["username"])?" value=\"{$_POST["username"]}\"":'';
	}
?>
<html lang="fr">
<head>
	<?php require_once('layout/header/head-data.html'); ?>
	<title>Admin<?php echo TITLE_PAGE ?></title>
</head>
<body>
	<?php require_once('layout/header/header.php'); ?>
	
	<main>
		<div class="row">
			<?php require_once('layout/main/menu-admin.html'); ?>

			<div id="main_column">
				<div id="main_screen">
					<div class='user-bar'>
						<h1 class='admin_space'>Créer un compte employé</h1>
					</div>
					<?php if(!isset($_GET['success'])):?>
					<form action='/admin/creer-compte-employee' method='POST'>
						<label for='username'>Nom d'utilisateur</label>
						<input type='username' name='username' id='username'<?php echo $username ?>>

						<label for='email'>Email</label>
						<input type='email' name='email' id='email'<?php echo $email ?>>

						<label for='password'>Mot de passe</label>
						<input type="password" name="password" id="password"
							minlength="12"
							pattern="(?=.*[a-zà-öø-ÿ])(?=.*[A-ZÀ-ÖØ-Ý])(?=.*\d)(?=.*[^A-ZÀ-ÖØ-Ýa-zà-öø-ÿ\d]).{12,}"
							onkeyup="check_password_strong(this)"
							title="Votre mot de passe doit contenir au moins 12 caractères, 1 chiffre, 1 majuscule, 1 minuscule et 1 caractère spéclial"
							required>

				<?php include_once('layout/main/password-checker.php'); ?>

				<p class="error"><?php echo isset($error_signin)?$error_signin:'' ?></p>

						<p class="error"><?php echo isset($error_signin)?$error_signin:'' ?></p>

						<input type='submit' name='submit' value='Créer le compte'>
					</form>
					<?php else: ?>
					<p>Compte créé avec succès !</p>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</main>

	<?php require_once('layout/footer/footer.php'); ?>
</body>
</html>
<?php
elseif(is_employee()):
	header('Location: /employee');
else:
	header('Location: /login');
endif;
