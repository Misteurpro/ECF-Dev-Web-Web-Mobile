<?php
/**
 * This will return true or false if this character is own by the user
 * @param int $character_id is the id of a specific character you are looking for
 * @return bool
 */
function check_character_ownership(int $character_id) :bool{

	global $dbh;
	if(is_connected()){
		$user_id = $_SESSION['id_utilisateur'];
		
		$get_character_user = $dbh->prepare("SELECT * FROM `personnage` WHERE `id_personnage` = ? AND `id_utilisateur` = ?");
		$get_character_user->bindParam(1, $character_id);
		$get_character_user->bindParam(2, $user_id);
		$get_character_user->execute();
		$user_confirmation = $get_character_user->fetch(PDO::FETCH_ASSOC);
		
		if($user_confirmation != null)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	return false;
}


/**
 * This will return the owner of a character
 * @param int $character_id is the id of a specific character owner you are looking for
 * @return int this is the id of the character that will be returned
 */
function get_character_owner(int $character_id) :int{
	global $dbh;

	$get_owner = $dbh->prepare("SELECT `id_utilisateur` FROM `personnage` WHERE `id_personnage` = ?");
	$get_owner->bindParam(1, $character_id);
	$get_owner->execute();
	$owner = $get_owner->fetch(PDO::FETCH_ASSOC);

	return $owner['id_utilisateur'];
}

/**
 * This will return the username with the id of a user account
 * @param int $user_id is the id of the account you are looking for.
 * @return string the name of the user
 */
function get_username_by_id(int $user_id) {
	global $dbh;

	$get_user = $dbh->prepare("SELECT `pseudo` FROM `utilisateur` WHERE `id_utilisateur` = ?");
	$get_user->bindParam(1, $user_id);
	$get_user->execute();
	$user = $get_user->fetch(PDO::FETCH_ASSOC);

	return htmlspecialchars($user['pseudo'], ENT_QUOTES, 'UTF-8');
}

/**
 * This will return all/or selected character based on if it's public or not
 * @param bool $public bool is used to select public character or private one
 * @param string $search is the search parameter based on the character name, if left empty it won't try to research anything
 * @param int $page is the current page you are in
 * @param int $limit is the maximum ammount of article you get returned
 * @param int $character_id is the id of a specific character you are looking for, if set to 0 it won't look for anything
 * @param mixed $first_date is the start date it will look for characters if null won't look for character based on date
 * @param mixed $second_date is the last date it will look for characters if null won't look for character based on date
 * @param mixed $genre if the gender is null it will look for every character without caring for the gender
 * @return mixed
 */
function get_characters(bool $public, string $search, int $page = 0, int $limit = 0, int $character_id = 0, mixed $first_date = null, mixed $second_date = null, mixed $genre = null) :mixed{
	global $dbh;
	$final_search = '%'.$search.'%';
	
	$range = ($page > 0) ? ($page-1) * $limit : 0;

	if($character_id !== 0){

		$get_character = null;
		if($public){
			$get_character = $dbh->prepare("SELECT * FROM `personnage` WHERE `id_personnage` = ? AND `partage` = 1 AND `autorise` = 1");
		}
		else if(!$public){
			if(check_character_ownership($character_id) || is_admin() || is_employee()){
				$get_character = $dbh->prepare("SELECT * FROM `personnage` WHERE `id_personnage` = ?");
			}
		}
		
		$get_character->bindParam(1, $character_id);
		$get_character->execute();
		
		$characters = $get_character->fetchAll(PDO::FETCH_ASSOC);
		if($get_character != null){
			return $characters;
		}
		else{
			return "Erreur : Le personnage que vous voulez voir n'est pas public ou n'est pas vérifié";
		}
	}
	else if(!$public){
		if(is_connected()){
			$get_characters = $dbh->prepare("SELECT * FROM `personnage` WHERE `id_utilisateur` = ?");
			$get_characters->bindParam(1, $_SESSION['id_utilisateur'], PDO::PARAM_STR);
			$get_characters->execute();
			
			$characters = $get_characters->fetchall(PDO::FETCH_ASSOC);


			if($characters != null){
				return $characters;
			}
			else{
				return "Oups : Il n'y a aucun personnage... Essayer de créer votre tout premier personnage";
			}
		}else{
			return 'Veuillez vous connecter pour accéder à vos personnages';
		}
	}
	else if($public){

		if($genre === "male" || $genre === "female"){
			$gender_type = "AND `genre` = '$genre' ";
		}
		else{
			$gender_type = "";
		}

		if($first_date != '' && $second_date != '' || $first_date != null && $second_date != null){
			$get_characters = $dbh->prepare("SELECT * FROM `personnage` WHERE `partage` = 1 AND `autorise` = 1 $gender_type AND `nom` LIKE ? AND `date` BETWEEN ? AND ? ");
			$get_characters->bindValue(2, $first_date);
			$get_characters->bindValue(3, $second_date);
		}
		else{
			$get_characters = $dbh->prepare("SELECT * FROM `personnage` WHERE `partage` = 1 AND `autorise` = 1 $gender_type AND `nom` LIKE ? LIMIT $range,$limit");
		}
		$get_characters->bindValue(1, "%$search%", PDO::PARAM_STR);

		$get_characters->execute();

		$characters = $get_characters->fetchall(PDO::FETCH_ASSOC);
		if($characters != null){
			render_characters($characters, 'show-only');
			return "";
		}
	}

	return "Oups : Il n'y a aucun personnage... Soyer le premier à en créer un!!";
}

/**
 * This will return any and all character
 * @param int $page is the specific page you are at.
 * @param int $limit is the maximum ammount of article you are getting.
 * @return mixed
 */
function get_all_characters(int $page, int $limit) :mixed{
	global $dbh;

	$range = ($page > 0) ? ($page-1) * $limit : 0;
	if(is_admin() || is_employee()){

		$get_characters = $dbh->prepare("SELECT * FROM `personnage` LIMIT $range,$limit");
		$get_characters->execute();

		$characters = $get_characters->fetchall(PDO::FETCH_ASSOC);
		if($characters != null){
			return $characters;
		}
	}

	return "Oups : Il n'y a aucun personnage...";
}

/**
 * This will return all character that need to be approved
 * @param int $page is the specific page you are at.
 * @param int $limit is the maximum ammount of article you are getting.
 * @return mixed
 */
function get_characters_to_approve(int $page, int $limit){
	global $dbh;

	$range = ($page > 0) ? ($page-1) * $limit : 0;
	if(is_admin() && !check_if_user_blocked($_SESSION["id_utilisateur"])||is_employee() && !check_if_user_blocked($_SESSION["id_utilisateur"])){
		$get_characters = $dbh->prepare("SELECT * FROM `personnage` WHERE `autorise` = 0 LIMIT $range,$limit ");
		$get_characters ->execute();
		$characters = $get_characters->fetchAll(PDO::FETCH_ASSOC);
		if($characters != null)
			return $characters;
		else
			return "Beau travail! Il n'y a plus aucun personnage à approuver. ";
	}
}

/**
 * This will return the character name
 * @param int $character_id is the id of a specific character you are looking for
 * @return string
 */
function get_character_name(int $character_id){
	global $dbh;

	$get_character_name = $dbh->prepare("SELECT `nom` FROM `personnage` WHERE id_personnage = ? ");
	$get_character_name->bindParam(1, $character_id);
	$get_character_name->execute();
	$character = $get_character_name->fetch(PDO::FETCH_ASSOC);
	$sanitized_name = htmlspecialchars($character["nom"], ENT_QUOTES, "UTF-8");
	return $sanitized_name;
}

/**
 * This will approve a character and send a mail to the user
 * @param int $character_id is the id of a specific character you are looking for
 * @return void
 */
function approve_character(int $character_id){
	global $dbh;

	if(is_admin() && !check_if_user_blocked($_SESSION["id_utilisateur"]) || is_employee() && !check_if_user_blocked($_SESSION["id_utilisateur"])){
		$set_character = $dbh->prepare("UPDATE `personnage` SET `autorise` = 1 WHERE `personnage`.`id_personnage` = ?");
		$set_character->bindParam(1, $character_id);
		$set_character ->execute();

		$subject = "Votre personnage a été accepté";
		$body="<p>Votre dernier personnage a été accepté<br>
		Vous pouvez maintenant modifier les accessoires de votre personnage</p>";


		send_mail(get_mail_from_character($character_id), $subject, $body);
	}
}

/**
 * This will return true or false based on if the character is approved or not
 * @param int $character_id is the id of a specific character you are looking for
 * @return bool
 */
function is_approved(int $character_id){
	global $dbh;

	$get_approvement = $dbh->prepare("SELECT * FROM `personnage` WHERE `autorise` = 1 AND `id_personnage` = ?");
	$get_approvement->bindValue(1, $character_id);
	$get_approvement->execute();
	$approvement = $get_approvement->fetch(PDO::FETCH_ASSOC);
	if($approvement != null){
		return true;
	}
	return false;
}

/**
 * This will refuse the character given and send a mail to the creator
 * @param int $character_id is the id of a specific character you are looking for
 * @param string $reason is the reason of refusal
 * @return void
 */
function refuse_character(int $character_id, string $reason){
	global $dbh;

	if(is_admin() && !check_if_user_blocked($_SESSION["id_utilisateur"]) || is_employee() && !check_if_user_blocked($_SESSION["id_utilisateur"])){
		$character_name = get_character_name($character_id);

		$subject = "Votre personnage a été refusé";
		$body="<p>Votre personnage $character_name a été refusé pour raison : $reason.<br>
		Votre personnage a donc été supprimé définitivement.</p>";

		send_mail(get_mail_from_character($character_id), $subject, $body);

		$remove_character = $dbh->prepare("DELETE FROM `personnage` WHERE `personnage`.`id_personnage` = ?");
		$remove_character->bindParam(1, $character_id);
		$remove_character->execute();
	}
}

/**
 * Used by get_characters function, this will return an array of all characters images
 * @param array $characters list of character array
 * @return array
 */
function get_characters_image(array $characters) :array{
	$list = [];
	foreach ($characters as $character) {
		$list[] = base64_decode($character["image"]);
	}
	return $list;
}


/**
 * This will return the button's for the character page based on is ownership is user role and is status
 * @return void
 */
function get_character_page_button(){
	if(check_character_ownership($_GET["character"])){
		$character_id = htmlspecialchars($_GET["character"], ENT_QUOTES, "UTF-8");
		?>
			<a href='/menu/page-personnage?character=<?php echo $character_id ?>&delete' class='button color-red bs-5'>Supprimer</a>
			<span class="margin-1"></span>
			<a href='<?php if(check_if_character_blocked($_GET["character"]) == 1){echo "/menu/page-personnage/modifier?character=$character_id";} ?>' class='button <?php echo check_if_character_blocked($_GET["character"]) != 1? "inactive" : "" ?> color-secondary-darker bs-5'>Modifier</a>
		<?php
	}
	elseif(is_admin() && !is_approved($_GET["character"]) && !check_if_character_blocked($_GET["character"]) && !check_if_user_blocked($_SESSION["id_utilisateur"]) || is_employee() &&!check_if_user_blocked($_SESSION["id_utilisateur"]) && !is_approved($_GET["character"]) && check_if_character_blocked($_GET["character"])){
		?>
			<a href='/employee/approuver-personnage?character=<?php echo $_GET["character"] ?>&refuse' class='button color-red bs-5'>Refuser</a>
			<span class="margin-1"></span>
			<a href='/employee/approuver-personnage?character=<?php echo $_GET["character"] ?>&approve' class='button color-secondary-darker bs-5'>Approuver</a>
		<?php
	}elseif(is_admin() && !check_if_user_blocked($_SESSION["id_utilisateur"]) || is_employee() && !check_if_user_blocked($_SESSION["id_utilisateur"])){
		?>
			<a href='/employee/approuver-personnage?character=<?php echo $_GET["character"] ?>&delete' class='button color-red bs-5'>Supprimer</a>
			<span class="margin-1"></span>
			<?php if(check_if_character_blocked($_GET["character"]) !== 2 ){ ?><a href='/menu/page-personnage?character=<?php echo $_GET["character"] ?>&suspend' class='button color-secondary-darker'>Suspendre</a><?php }else{ ?>
							<a href='/menu/page-personnage?character=<?php echo $_GET["character"] ?>&unsuspend' class='button color-secondary-darker'>Reactivation</a>
							<?php } ?>
		<?php
	}
	else{
		?>
			<a href='/menu/contactez-nous' class='button color-red'>Signaler</a>
		<?php
	}
}

/**
 * This will return true or false if is public and authorized
 * @param int $character_id is the id of a specific character you are looking for
 * @return bool
 */
function is_public_character(int $character_id) :bool{
	global $dbh;

	$get_characters = $dbh->prepare("SELECT * FROM `personnage` WHERE `id_personnage` = ? AND `partage` = 1 AND `autorise` = 1");
	$get_characters->bindParam(1, $character_id);
	$get_characters->execute();

	$character = $get_characters->fetchAll(PDO::FETCH_ASSOC);
	if($character != null)
		return true;
	else 
		return false;
}

/**
 * This will return the value to check if a character is blocked or not
 * @param int $character_id is the id of a specific character you are looking for
 * @return mixed
 */
function check_if_character_blocked($character_id){
	global $dbh;
	$check_character = $dbh->prepare("SELECT `autorise` FROM `personnage` WHERE `id_personnage` = ?");
	$check_character->bindParam(1, $character_id);
	$check_character->execute();
	$is_blocked = $check_character->fetch(PDO::FETCH_ASSOC);

	return $is_blocked['autorise'];
}

/**
 * This will return the details of a character
 * @param int $character_id is the id of a specific character you are looking for
 * @return mixed
 */
function get_details(int $character_id){
	global $dbh;

	$get_details = $dbh->prepare("SELECT * FROM `personnage` WHERE `id_personnage` = ?");
	$get_details->bindParam(1, $character_id);
	$get_details->execute();
	$details = $get_details->fetch(PDO::FETCH_ASSOC);
	return $details;
}

/**
 * This will render the character articles by returning the html bits with the information of your character articles
 * @param int $character_id is the id of a specific character you are looking for
 * @return void
 */
function render_character_articles(int $character_id){
	$obj_list = get_character_articles($character_id);

	foreach($obj_list as $obj){
		?>
		<div class="article_obj">
			<div class="article_name"><h2><?php echo $obj["nom"] ?></h2></div>
			<div class="article_image_box"><img class="article_image" src="/assets/frontend/SVG/Icones/box_grey.svg" alt="Box" width="55"></div>
		</div>
		<?php
	}
}

/**
 * This will return the active and existing article a character is equiped with
 * @param int $character_id is the id of a specific character you are looking for
 * @return mixed
 */
function get_character_articles(int $character_id){
	global $dbh;

	$get_articles = $dbh->prepare("SELECT `article` FROM `personnage` WHERE `id_personnage` = ?");
	$get_articles->bindParam(1, $character_id);
	$get_articles->execute();
	$articles = $get_articles->fetch(PDO::FETCH_ASSOC);

	$obj_list = [];

	if($articles["article"] != null){
		$json_array = json_decode($articles["article"], true);
		foreach($json_array as $article){
			$get_obj = $dbh->prepare("SELECT * FROM `article` WHERE `id_article` = ? AND `actif` = 1");
			$get_obj->bindParam(1, $article);
			$get_obj->execute();
			$obj = $get_obj->fetch(PDO::FETCH_ASSOC);
			if($obj !== false){
				$obj_list[] = $obj;
			}
		}
	}
	return $obj_list;
}

/**
 * This will render any given characters
 * @param mixed $characters list of character array or error given
 * @param mixed $enable_overlay this enable the overlay (button modify)
 * @return void
 */
function render_characters(mixed $characters, mixed $enable_overlay, bool $is_admin = false) :void{

	if(is_array($characters)){
		foreach($characters as $character){
			?>
				<form class='character_sheet' action='Menu.php?link=mycharacter' method='POST'>
					<span>
						<img src="data:image/png;base64,<?php echo base64_encode($character['image']);?>" width="200">
					</span>
					<table>
						<tr>
							<td>Nom :</td>
							<td><?php echo htmlspecialchars($character['nom'], ENT_QUOTES, 'UTF-8');?></td>
						</tr>
						<tr>
							<td>Créateur :</td>
							<td><?php echo get_username_by_id($character['id_utilisateur']);?></td>
						</tr>
						<tr>
						<td><?php echo htmlspecialchars($character['date'], ENT_QUOTES, 'UTF-8') ?></td>
						<td>
							<?php
							if($enable_overlay === true && !$is_admin){
							?>
									<?php if($character['autorise'] === 1){ ?>
									<input
										class='sharedCheck'
										type='checkbox'
										name='tag_<?php echo htmlspecialchars($character["id_personnage"], ENT_QUOTES, 'UTF-8') ?>'
										id='tag_<?php echo htmlspecialchars($character["id_personnage"], ENT_QUOTES, 'UTF-8'); ?>'
										onchange='loadDoc(<?php echo htmlspecialchars($character["id_personnage"], ENT_QUOTES, "UTF-8") ?>);'
										<?php echo $character['partage']===1?'checked':null;?>
										hidden
									>
									<label for='tag_<?php echo $character["id_personnage"] ?>'></label>
									<input type='hidden' name='character_id' value='<?php echo htmlspecialchars($character["id_personnage"], ENT_QUOTES, 'UTF-8') ?>'>
									<?php 
									}
									?>
									<?php if($character['autorise'] !==1 ){?> <span class='Check <?php if($character['autorise'] === 2){ echo "suspend"; } ?>'><?php if($character['autorise'] === 2){ echo "Suspendu"; }else{ echo "En attente"; } ?></span> <?php } ?>
									<?php }
							else ?>		
							</td>
						</tr>
					</table>
					<!--<div>

						<h2><a class="title_link" href="/menu/page-personnage?character=<?php echo $character["id_personnage"] ?>"> <?php echo $character['nom'] ?> </a></h2>
					</div>-->
					<?php
					if($enable_overlay === true && !$is_admin){
					?>
						<span class='overlay'>
						<span>
							<a href='/menu/page-personnage?character=<?php echo htmlspecialchars($character["id_personnage"], ENT_QUOTES, 'UTF-8') ?>' class='button'>Modifier</a>
							<a href='/menu/page-personnage?character=<?php echo htmlspecialchars($character["id_personnage"], ENT_QUOTES, 'UTF-8') ?>&delete' class='button color-red'>Supprimer</a>
						</span>
						<span>
							<a href='/menu/page-personnage?character=<?php echo htmlspecialchars($character["id_personnage"], ENT_QUOTES, 'UTF-8') ?>' class='button color-green'>Ouvrir</a>
						</span>
					</span>
					<?php
					}
					elseif($is_admin && $enable_overlay === true){
					?>
						<span class='overlay'>
							<span>
								<a href='/employee/approuver-personnage?character=<?php echo htmlspecialchars($character["id_personnage"], ENT_QUOTES, 'UTF-8') ?>&approve' class='button'>Approuver</a>
								<a href='/employee/approuver-personnage?character=<?php echo htmlspecialchars($character["id_personnage"], ENT_QUOTES, 'UTF-8') ?>&refuse' class='button color-red'>Refuser</a>
							</span>
							<span>
								<a href='/menu/page-personnage?character=<?php echo htmlspecialchars($character["id_personnage"], ENT_QUOTES, 'UTF-8') ?>' class='button color-green'>Ouvrir</a>
							</span>
						</span>
					<?php
					}
					elseif($enable_overlay === 'show-only'){
					?>
					<span class='overlay'>
						<span>
							<a href='/menu/page-personnage?character=<?php echo htmlspecialchars($character["id_personnage"], ENT_QUOTES, 'UTF-8') ?>' class='button color-green'>Ouvrir</a>
						</span>
					</span>
					<?php
					}
					?>
				</form>
			
			<?php
		}
	}
	else{
		$final_string = is_string($characters)?$characters:"Erreur inconnue : lors de l'affichage du personnage";
		echo htmlspecialchars($final_string, ENT_QUOTES, 'UTF-8');
	}
}


/**
 * This will render all character for the admin page to check upon all existing character.
 * @param mixed $characters is the information of all the characters plan to be rendered
 * @return void
 */
function render_all_character(mixed $characters){
	
	if(is_array($characters)){
		foreach($characters as $character){
			?>
				<form class='character_sheet' action='Menu.php?link=mycharacter' method='POST'>
					<span>
						<img src="data:image/png;base64,<?php echo base64_encode($character['image']);?>" width="200">
					</span>
					<table>
						<tr>
							<td>Nom :</td>
							<td><?php echo htmlspecialchars($character['nom'], ENT_QUOTES, 'UTF-8');?></td>
						</tr>
						<tr>
							<td>Créateur :</td>
							<td><?php echo get_username_by_id($character['id_utilisateur']);?></td>
						</tr>
						<?php
						?>
							<tr>
								<td>
									<?php if($character['autorise'] === 1){ ?>
									<input
										class='sharedCheck'
										type='checkbox'
										<?php echo $character['partage']===1?'checked':null;?>
										disabled
										hidden
									>
									<label for='tag_<?php echo htmlspecialchars($character["id_personnage"], ENT_QUOTES, 'UTF-8') ?>' class="sharedlabel"></label>
									<input type='hidden' name='character_id' value='<?php echo htmlspecialchars($character["id_personnage"], ENT_QUOTES, 'UTF-8') ?>'>
									<?php }?>
								</td>
								<td>
									<span class='Check <?php if($character['autorise'] === 1){ echo "approved"; }else if($character['autorise'] === 2){ echo "suspend"; } ?>'><?php if($character['autorise'] === 1){echo "Approuver";}else if($character['autorise'] === 2){ echo "Suspendu"; }else{ echo "En attente"; } ?></span>
								</td>
							</tr>
						
					</table>
					<!--<div>

						<h2><a class="title_link" href="/menu/page-personnage?character=<?php echo $character["id_personnage"] ?>"> <?php echo $character['nom'] ?> </a></h2>
					</div>-->
					<span class='overlay'>
						<span>
							<?php if(check_if_character_blocked($character["id_personnage"]) !== 2 ){ ?><a href='/menu/page-personnage?character=<?php echo $character["id_personnage"] ?>&suspend' class='button'>Suspendre</a><?php }else{ ?>
							<a href='/menu/page-personnage?character=<?php echo htmlspecialchars($character["id_personnage"], ENT_QUOTES, 'UTF-8'); ?>&unsuspend' class='button'>Reactivation</a>
							<?php } ?>
							<a href='/menu/page-personnage?character=<?php echo htmlspecialchars($character["id_personnage"], ENT_QUOTES, 'UTF-8') ?>&delete' class='button color-red'>Supprimer</a>
						</span>
						<span>
							<a href='/menu/page-personnage?character=<?php echo htmlspecialchars($character["id_personnage"], ENT_QUOTES, 'UTF-8') ?>' class='button color-green'>Ouvrir</a>
						</span>
					</span>
				</form>
			<?php
		}
	}
	else{
		$final_string = is_string($characters)?$characters:"Erreur inconnue : lors de l'affichage du personnage";
		echo htmlspecialchars($final_string, ENT_QUOTES, 'UTF-8');
	}
}

/**
 * This will return true or false if character is on database
 * @param int $character_id is the id of a specific character you are looking for
 * @return bool
 */
function do_character_exist(int $character_id) :bool{
	global $dbh;

	$get_character = $dbh->prepare("SELECT * FROM `personnage` WHERE `id_personnage` = ?");
	$get_character->bindParam(1, $character_id);
	$get_character->execute();

	$character = $get_character->fetchAll(PDO::FETCH_ASSOC);
	if($character != null)
		return true;
	else 
		return false;
}

/**
 * This will delete and return success, notowned or notconnected if character is on database
 * @param int $character_id is the id of a specific character you are looking for
 * @return string
 */
function delete_character(int $character_id = 0) :string{
	global $dbh;

	if(is_connected()){
		if($character_id === 0)
			$character_id = $_REQUEST['character'];
	
		if(check_character_ownership($character_id) || is_admin() || is_employee())
		{
			$delete_character = $dbh->prepare("DELETE FROM `personnage` WHERE `personnage`.`id_personnage` = ?");
			$delete_character->bindParam(1, $character_id);
			$delete_character->execute();
			return "success";
		}
		else
		{
			return "notowned";
		}
	}
	return "notconnected";
}

/**
 * This will return success notadmin or notconnected if you decide to suspend a character for what ever reason
 * @param int $character_id is the id of a specific character you are looking for
 * @param bool $unsuspend is to decide if you want to unsuspend or suspend the character
 * @return bool
 */
function suspend_character(int $character_id = 0, $unsuspend = false) :string{
	global $dbh;

	if(is_connected()){
			if($character_id === 0)
			$character_id = $_REQUEST['character'];
		
			if(is_admin() && !check_if_user_blocked($_SESSION["id_utilisateur"]) || is_employee() && !check_if_user_blocked($_SESSION["id_utilisateur"]))
			{
				if(!$unsuspend){

					$suspend_character = $dbh->prepare("UPDATE `personnage` SET `autorise` = '2' WHERE `personnage`.`id_personnage` = ?");
					$suspend_character->bindParam(1, $character_id);
					$suspend_character->execute();
					return "success";
				}
				else{
					$suspend_character = $dbh->prepare("UPDATE `personnage` SET `autorise` = '1' WHERE `personnage`.`id_personnage` = ?");
					$suspend_character->bindParam(1, $character_id);
					$suspend_character->execute();
					return "success";
				}
			}
			else
			{
				return "notadmin";
			}
	}
	return "notconnected";
}

/**
 * This is used to check the character name to see if it fit the requirement
 * @param string $name is the name you want to verify
 * @return mixed
 */
function check_character_name(string $name){
	global $dbh;

	$check_name = $dbh->prepare("SELECT `nom` FROM `personnage` WHERE `nom` = ?");
	$check_name->bindParam(1, $name);
	$check_name->execute();
	$char_name = $check_name->fetch(PDO::FETCH_ASSOC);
	if($char_name == null){
		if(mb_strlen($name) <= 14){
			return true;
		}
		else if(mb_strlen($name) > 14){
			return "toolong";
		}
		elseif($name == null){
			return "empty";
		}
	}
	else{
		return false;
	}
}

/**
 * This will get all articles and show it
 * @param int $character_id is the id of a specific character you are looking for
 * @return void
 */

function get_character_articles_for_page(int $character_id) :void{
	global $dbh;

	$get_character = $dbh->prepare("SELECT `genre`, `worn_article` FROM `personnage` WHERE `id_personnage` = ?");
	$get_character->bindParam(1, $character_id);
	$get_character->execute();
	$character = $get_character->fetch(PDO::FETCH_ASSOC);

	$gender = $character["genre"];
	$json_array = json_decode($character["worn_article"], true);
	$character_articles = $json_array["articles"];

	foreach($character_articles as $item)
	{
		$get_articles = $dbh->prepare("SELECT * FROM `article` WHERE `image` = ?");
		$get_articles->bindParam(1, $item);
		$get_articles->execute();
		$article = $get_articles->fetch(PDO::FETCH_ASSOC);

		$image = null;
		if($article != null){
			$type = $article["type"];
	
			$image = $article["image"];
			if($type != "cheveux")
				$article_url = "/assets/frontend/Articles/$gender/$type/$image.png";
			else
				$article_url = "/assets/frontend/Articles/$type/$image.png";
	
			$article_name_sanitized = htmlspecialchars($article_url, ENT_QUOTES, "UTF-8");
		}

		if($image !== null){
			echo "
			<div>
				<img class='article-img-box' src='$article_name_sanitized' alt='' width='100' height='100'>
			</div>            
			";
		}
	}
}

/**
 * This will get different articles for your character (eg: hair, shoes, etc.)
 * @return void
 */
function get_articles() :void{
	global $dbh;

	$get_articles_asset = $dbh->prepare("SELECT * FROM `article` WHERE `actif` = 1");
	$get_articles_asset->execute();
	$articles = $get_articles_asset->fetchAll(PDO::FETCH_ASSOC);

	foreach($articles as $item)
	{
		?>

		<div class="article_obj" data-id="<?php echo htmlspecialchars($item['id_article'],ENT_QUOTES,'UTF-8') ?>" onclick="set_article_active(this)">
			<div class="article_name"><h2><?php echo htmlspecialchars($item["nom"],ENT_QUOTES,'UTF-8') ?></h2></div>
			<div class="article_image_box"><img class="article_image" src="/assets/frontend/SVG/Icones/box_grey.svg" alt="Box" width="55"></div>
		</div>

		<?php
	}
}

/**
 * This will add all the selected articles for the character you are modifying
 * @param array $ articles is an array of all the articles you are adding
 * @param int $character_id is the id of a specific character you are looking for
 * @return string
 */
function add_articles_to_character(array $articles, int $character_id) :string{
	global $dbh;

	$json = json_encode($articles);

	if(!empty($articles) && check_character_ownership($character_id)){

		$add_articles = $dbh->prepare("UPDATE `personnage` SET `article` = ? WHERE `personnage`.`id_personnage` =?");
		$add_articles->bindParam(1, $json);
		$add_articles->bindParam(2, $character_id);
		$add_articles->execute();

		insert_logs(true, $character_id);

		return "/personnage-modifier-avec-succes";
	}
	else if(empty($articles)){
		return "/personnage-modifier-avec-succes";
	}
	return "/erreur403";
}