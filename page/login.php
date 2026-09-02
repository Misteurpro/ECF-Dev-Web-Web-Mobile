<?php
//This check if somebody isn't already connected to not risk error with dual connection
if(!is_connected())
{
	$email = '';
	if(isset($_POST["email"]) && isset($_POST["password"]))
	{
		$error_login = login($_POST["email"], $_POST["password"]);

		$email = isset($_POST["email"])?" value=\"{$_POST["email"]}\"":'';
	}
?>
	<!DOCTYPE html>
	<html lang="fr">
	<head>
		<?php require_once('layout/header/head-data.html'); ?>
		<title>Se connecter<?php echo TITLE_PAGE ?></title>
	</head>
	<body>
		<?php require_once('layout/header/header.php'); ?>

		<main>
			<form action='/login' method='POST'>
				<label for='email'>Email</label>
				<input type='email' name='email' id='email'<?php echo $email ?>>
			
				<label for='password'>Mot de passe</label>
				<input type='password' name='password' id='password'>

				<p class="error"><?php echo isset($error_login)?$error_login:'' ?></p>

				<div>
					<a href='/forgot-password'>Mot de passe oublié</a>
					<a href='/signin'>Créer un compte</a>
				</div>
			
				<input type='submit' name='submit' value='Se connecter'>
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