<?php
if(!is_connected())
{
	$email = '';
	$username = '';
	if(isset($_POST["email"]) && isset($_POST["username"]) && isset($_POST["password"]))
	{
		$error_signin = signin($_POST["username"], $_POST["email"], $_POST["password"]);

		$email = isset($_POST["email"])?" value=\"{$_POST["email"]}\"":'';
		$username = isset($_POST["username"])?" value=\"{$_POST["username"]}\"":'';
	}
?>
	<!DOCTYPE html>
	<html lang="fr">
	<head>
		<?php require_once('layout/header/head-data.html'); ?>
		<title>Créer un compte<?php echo TITLE_PAGE ?></title>
	</head>
	<body>
		<?php require_once('layout/header/header.php'); ?>

		<main>
			<form action='/signin' method='POST'>
				<label for='username'>Nom d'utilisateur</label>
				<input type='username' name='username' id='username'<?php echo $username ?> required>

				<label for='email'>Email</label>
				<input type='email' name='email' id='email'<?php echo $email ?> required>

				<label for='password'>Mot de passe</label>
				<input type="password" name="password" id="password"
					minlength="12"
					pattern="(?=.*[a-zà-öø-ÿ])(?=.*[A-ZÀ-ÖØ-Ý])(?=.*\d)(?=.*[^A-ZÀ-ÖØ-Ýa-zà-öø-ÿ\d]).{12,}"
					onkeyup="check_password_strong(this)"
					title="Votre mot de passe doit contenir au moins 12 caractères, 1 chiffre, 1 majuscule, 1 minuscule et 1 caractère spéclial"
					required>

				<?php include_once('layout/main/password-checker.php'); ?>

				<p class="error"><?php echo isset($error_signin)?$error_signin:'' ?></p>

				<input type='submit' name='submit' value='Créer le compte'>
			</form>
		</main>

		<?php require_once('layout/footer/footer.php'); ?>
	</body>
	</html>
<?php
}
else 
{
	header("Location: /menu");

}