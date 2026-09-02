<?php
	//Add all ajax request
	
	//This part is used to call get_characters to search for a character based on gender name and creation date
	if(isset($_REQUEST["search"]) || isset($_REQUEST["gender"]) || isset($_REQUEST["firstdate"]) || isset($_REQUEST["seconddate"]))
	{
		$search_parameter = $_REQUEST["search"];
		$gender_parameter = $_REQUEST["gender"];
		$first_date_parameter = $_REQUEST["firstdate"];
		$second_date_parameter = $_REQUEST["seconddate"];

		if($gender_parameter == "any" || $gender_parameter == "male" || $gender_parameter == "female"){
			$gender = $gender_parameter;
		}
		else{
			$gender = "error";
		}

		$amount = 10;
		
		get_characters(true, $search_parameter, 1, $amount, 0, $first_date_parameter, $second_date_parameter, $gender);
		

		exit();
	}


	//This is used to change the share status of a character, you can make any character public or private!
	if(isset($_REQUEST["share_status"]))
	{
		//Change Character data
		if(is_connected())
		{
			$character_id = $_REQUEST["character_id"];

			if($_REQUEST["share_status"] == "true")
			{
				$share_var = 1;
			}
			else {
				$share_var = 0;
			}
			if(check_character_ownership($character_id))
			{
				$change_sharing_status = $dbh->prepare("UPDATE `personnage` SET `partage` = ? WHERE `personnage`.`id_personnage` = ?");
				$change_sharing_status->bindParam(1, $share_var);
				$change_sharing_status->bindParam(2, $character_id);
				$change_sharing_status->execute();
			}
			else
			{
				echo"<H1>Le personnage que vous avez essayé de modifier ne vous appartient pas</H1>";
			}
		}
		exit();
	}

	//This is supposed to manage the Contact us part, if anyone want to send a message it will automatically fill it up if you are not connected
	if(isset($_POST["email"]) && isset($_POST["username"])){
		$email = $_POST["email"];
		$username = $_POST["username"];
		$message = $_POST["message"];
		$sanitized_email = htmlspecialchars($email, ENT_QUOTES, "UTF-8");
		$sanitized_username = htmlspecialchars($username, ENT_QUOTES, "UTF-8");
		$sanitized_message = htmlspecialchars($message, ENT_QUOTES, "UTF-8");

		if(!compare_username($username)){
			$body = " Email : $sanitized_email<br> Username : $sanitized_username <br> Message : <br> $sanitized_message";
			send_mail(COMPANY_EMAIL, "FantasyRealm : Message de l'utilisateur ".$username, $body);
			header("Location: /contact-succes");
		}
		else{
			$username_error = "Le nom d'utilisateur est inconnu, veuillez utiliser un nom d'utilisateur existant";
		}


	}
	
	$email = "";
	$username = "";

	if(!is_connected() && isset($private_list_character)){
		header("Location: /login");
	}
	else if(is_connected()){
		$email = get_mail_from_user($_SESSION["id_utilisateur"]);
		$username = get_username(false);
	}
?>


<html lang="fr">
<head>
	<?php require_once('layout/header/head-data.html'); ?>
	<title>Menu<?php echo TITLE_PAGE ?></title>
</head>
<body>
	<?php require_once('layout/header/header.php'); ?>

	<main>
		<div class="row">
			<aside>
				<nav>
					<ul class="noDetail">
						<li><button class="noDetail"><img src="/assets/frontend/SVG/Icones/List.svg" alt="" width="40" height="40"></button></li>
						<?php if(is_connected()) : ?>
						<li><a href="/menu/mon-espace" class="button color_secondary_darker"><img class="asd-img-rs" src="/assets/frontend/SVG/Icones/Head.svg" width="40" height="40" alt=""><p class="asd-p-rs">Mon espace</p></a></li>
						<?php endif; ?>
						<li><a href="/menu/galerie-de-personnage" class="button color_secondary_darker"><img class="asd-img-rs" src="/assets/frontend/SVG/Icones/images-user.svg" width="40" height="40" alt=""><p class="asd-p-rs">Galerie de personnages</p></a>
						<li><a href="/menu/contactez-nous" class="button color_secondary_darker"><img class="asd-img-rs" src="/assets/frontend/SVG/Icones/message-circle-lines-alt.svg" width="40" height="40" alt=""><p class="asd-p-rs">Contacter</p></a></li>
					</ul>
				</nav>
			</aside>

			<div id="main_column">
				<?php
				if(!isset($private_list_character) && !isset($welcome_message) && !isset($contact)){
				?> 
				<span class="search_bar">
					<input type="search" name="main_searchbar" id="main_searchbar" value="" placeholder="Search :">
					<label for="gender_select">Genre :</label>
					<select name="gender_select" id="gender_select" onchange="searchCharacter()">
						<option value="any">Tous</option>
						<option value="male">Homme</option>
						<option value="female">Femme</option>
					</select>
					
					<label for="first_date">Première date :</label>
					<input type="date" name="first_date" id="first_date" onchange="searchCharacter()">
								
					<label for="second_date">Seconde date :</label>
					<input type="date" name="second_date" id="second_date" onchange="searchCharacter()">
				</span>
				<?php
				}
				?>
				<div id="main_screen">
					<?php
					if(!empty($private_list_character)){
					?>
						<div class='user-bar'>
							<h1 class='user_my_space vw-150'>Mon espace</h1>
							<div class="ms-b-div">
								<button class="color-secondary-darker vw-075" onclick="location.href='/menu/createur-de-personnage'";><h2>Créer un personnage</h2></button>
								<?php 
								if(!is_admin() && !is_employee()){
									?> <button class="color-red vw-075" onclick="location.href='/menu/mon-espace/supprimer-le-compte'" ><h2>Supprimer mon compte</h2></button> <?php
								}
								?>
							</div>
						</div>
					<?php
					}
					elseif(isset($contact)){
					?>
						<div class='user-bar'>
							<h1 class='user_my_space vw-150'>Contactez-nous</h1>
						</div>
					<?php
					}
					?>
					<div class="char-appr-div">
					<?php
						if(isset($welcome_message))
						{
							if(is_connected())
							{
								$account_pseudo = get_username();
								echo"<H1>Bienvenue $account_pseudo </H1>";
							}
							else
							{
								?>
									<div class='box_standard'>
										<H1>Bienvenue, veuillez vous connecter <a class='selectedlink' href='/login'>ici</a></H1>
										<H2>ou continuer de visiter le site en temps qu'invité</H2>
									</div>
								<?php
							}
						}

						//This part will load all of the public characters
						if(!empty($public_list_character))
						{
							$table = "personnage";
							$column = "id_personnage";
							$amount = 10;
							require_once("lib/page-library.php");
							$tab = @get_characters(true, '', $current_page, $amount);
							render_characters($tab, 'show-only');
						}
						if(!empty($private_list_character))
						{
							$tab = @get_characters(false, '');
							render_characters($tab,true);

						}
						if(isset($contact)){
						?>
							<form action="/menu/contactez-nous" method="POST">
								<label for="email">Email :</label>
								<input type="email" name="email" id="email" value="<?php echo($email) ?>">
								<label for="username">Pseudo :</label>
								<input type="username" name="username" id="username" value="<?php echo($username) ?>">
								<?php echo isset($username_error) ? $username_error : ""; ?>
								<textarea class="contact-us-message" name="message" id="message" placeholder="Votre message..."></textarea>
								<input type="submit" value="Envoyer">
							</form>
						<?php
						}
						
					?>
					</div>
					<?php
					if(!empty($public_list_character))
					include_once("layout/main/page-overlay.php");
					?>
				</div>
			</div>
		</div>
	</main>

	<?php require_once('layout/footer/footer.php'); ?>
</body>
</html>

