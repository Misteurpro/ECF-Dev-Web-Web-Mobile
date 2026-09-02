<?php
if(!is_connected())
{
	$valide_token = false;
	$email = '';
	$username = '';
	if(isset($_POST["email"])&&!isset($_POST["token"]))
	{
		if(get_username(true, $_POST['email']) === $_POST["username"]){
			$error_forgot = forgot_password($_POST["email"], $_POST["username"]);
			$success_message = ($error_forgot === false)? 'Un mail vous a été envoyé':null;
	
			$email = " value=\"{$_POST["email"]}\"";
			$username = " value=\"{$_POST["username"]}\"";
		}
		else
			$error_forgot = "Erreur : Email ou nom d'utilisateur incorrect";
	}
	elseif(isset($_GET["token"])&&isset($_GET["email"]))
	{
		$email = "value=\"{$_GET["email"]}\" readonly";
		$username = "value=\"{$_GET["username"]}\" readonly";
		if(check_token($_GET["token"],$_GET["email"])){
			$valide_token = true;
		}else{
			$error_forgot = 'Erreur : Token invalide !';
		}
	}
	elseif(isset($_POST["token"])&&isset($_POST["username"])&&isset($_POST["email"])&&isset($_POST["password"]))
	{
		if(get_username(true, $_POST['email']) === $_POST["username"]){
			$email = "value=\"{$_POST["email"]}\" readonly";
			$username = "value=\"{$_POST["username"]}\" readonly";
			if(use_token($_POST["token"],$_POST["email"])){
				$error_forgot = modify_password($_POST["email"],$_POST["password"]);
			}
			else{
				$error_forgot = 'Erreur : Token invalide !';
			}
		}
		else{
			$error_forgot = "Erreur : Email ou nom d'utilisateur incorrect";
		}

	}
?>
	<!DOCTYPE html>
	<html lang="fr">
	<head>
		<?php require_once('layout/header/head-data.html'); ?>
		<title>Reset Mdp<?php echo TITLE_PAGE ?></title>
	</head>
	<body>
		<?php require_once('layout/header/header.php'); ?>

		<main>
			<form action='/forgot-password' method='POST'>
				<label for='email'>Email</label>
				<input type='email' name='email' id='email'<?php echo $email ?> required>
				<label for='username'>Nom d'utilisateur</label>
				<input type='text' name='username' id='username'<?php echo $username ?> required>
			<?php if($valide_token===true):?>
				<p>Vous pouvez maintenant entrer votre nouveau mot de passe</p>
				<label for="password">Mot de passe</label>
				<input type="password" name="password" id="password"
					minlength="12"
					pattern="(?=.*[a-zà-öø-ÿ])(?=.*[A-ZÀ-ÖØ-Ý])(?=.*\d)(?=.*[^A-ZÀ-ÖØ-Ýa-zà-öø-ÿ\d]).{12,}"
					onkeyup="check_password_strong(this)"
					title="Votre mot de passe doit contenir au moins 12 caractères, 1 chiffre, 1 majuscule, 1 minuscule et 1 caractère spécial"
					required>
				<input type="hidden" name="token" value="<?php echo $_REQUEST['token'];?>">

			<?php define('FORGOT_PASSWORD',true);include_once('layout/main/password-checker.php'); ?>

			<?php endif;?>
				<p class="error"><?php echo isset($error_forgot)?$error_forgot:'' ?></p>
				<p class="success"><?php echo isset($success_message)?$success_message:'' ?></p>
			
			<?php if($valide_token!==true):?>
				<input type='submit' name='submit' value='Recevoir le mail'>
			<?php endif;?>
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