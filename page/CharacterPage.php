<?php

	if(isset($_GET['character']) && !do_character_exist($_GET['character']))
	header('Location: /erreur404');

	check_character_ownership($_GET['character']) ? $ownership = true : $ownership = false;

	if( (!is_admin() && !is_employee()) && !$ownership && !is_public_character($_GET['character']))
	header('Location: /erreur403');

	if(isset($_REQUEST['character']))
	{
		//Delete character
		if(isset($_GET['delete']) && isset($_POST['confirm'])){
			$del_return = delete_character($_REQUEST['character']);
			if($del_return == "success"){
				echo"/suppression-succes";
			}
			else{
				echo"/suppression-erreur";
			}
			exit;
		}
		else if(isset($_GET['suspend']) && isset($_POST['confirm'])){
			$del_return = suspend_character($_REQUEST['character']);
			if($del_return == "success"){
				echo"/suspension-character-succes";
			}
			else{
				echo"/suppression-erreur";
			}

			exit;
		}
		else if(isset($_GET['unsuspend']) && isset($_POST['confirm'])){
			$del_return = suspend_character($_REQUEST['character'], true);
			if($del_return == "success"){
				echo"/suspension-character-succes?uns";
			}
			else{
				echo"/suppression-erreur";
			}

			exit;
		}
	}

	if(isset($_POST["user_comment"])){
		$comment = $_POST["user_comment"];
		$note = $_POST["note"];
		if(!empty($comment) && $note <= 5 && $note > 0){
			$was_send = send_comment($comment, $note, $_GET["character"]);
			if($was_send)
				include_once("page/error/comment-success.php");
		}
		else
			include_once("page/error/comment-failure.php");
		
		exit;
	}

?>
<html>
	<head>
		<?php require_once('layout/header/head-data.html'); ?>
		<title>Page de personnage<?php echo TITLE_PAGE ?></title>
	</head>
	<body>
		<?php require_once('layout/header/header.php'); ?>

					<div class="character_page">
						<div class="special_div">
							<?php
							if(is_admin() || is_employee())
							$ownership = true;

							$personnage = get_characters(!$ownership, '', 0,0, $_GET['character']); 
							render_characters($personnage, false); 
							?>
							<div class="buttons-div">
								<?php
									get_character_page_button()
								?>
							</div>
						</div>
						<?php 
							$details = get_details($_GET['character'])
						?>
						<span class='special_span'>
							<H1>Détail</H1>
							<span class="detail-type">
								<div class="detail-row"><h3>Genre : <?php echo htmlspecialchars($details["genre"],ENT_QUOTES,'UTF-8') ?></h3> <h3> Couleur de peau : <?php echo htmlspecialchars($details["couleur_peau"],ENT_QUOTES,'UTF-8') ?></h3></div>
								<div class="detail-row"><h3>Couleur des yeux : <?php echo htmlspecialchars($details["couleur_yeux"],ENT_QUOTES,'UTF-8') ?></h3> <h3> Forme des yeux : <?php echo htmlspecialchars($details["forme_yeux"],ENT_QUOTES,'UTF-8'); ?></h3></div>
								<div class="detail-row"><h3>Forme Bouche : <?php echo htmlspecialchars($details["forme_bouche"],ENT_QUOTES,'UTF-8') ?></h3> <h3> Forme nez : <?php echo htmlspecialchars($details["forme_nez"],ENT_QUOTES,'UTF-8') ?></h3></div>
								<div class="detail-row"><h3>Couleur de cheveux : <?php echo htmlspecialchars($details["couleur_cheveux"],ENT_QUOTES,'UTF-8') ?></h3></div>
							</span>
						</span>
							<?php 
								//get_character_articles_for_page($_GET["character"]);

							if(isset($_GET['delete'])){
									?>										
									<div class='delete_character'>
										<p>Souhaitez-vous vraiment supprimer ce personnage ? Ceci est une action permanente!</p>
										<span class='buttons-div-confirmation'>
											<button class='delete' id='delete_character' onclick='deleteCharacter()'; value=''>Supprimer</button> <button onclick="location.href = '/menu/page-personnage?character=<?php echo $_GET['character'] ?>'";>Annuler</button>
										</span>
									</div>		
									<?php
							}
							if(isset($_GET['suspend']) || isset($_GET["unsuspend"])){
									?>										
									<div class='delete_character'>
										<p>Souhaitez-vous vraiment <?php echo isset($_GET["unsuspend"])? "réactiver" : "suspendre"; ?> ce personnage ?</p>
										<span class='buttons-div-confirmation'>
											<button class='delete' id='suspend_character' onclick='suspendCharacter(<?php echo isset($_GET["unsuspend"])? "true" : "false"; ?>)'; value=''><?php echo isset($_GET["unsuspend"])? "réactiver" : "suspendre"; ?></button> <button onclick="location.href = '/menu/page-personnage?character=<?php echo $_GET['character'] ?>'";>Annuler</button>
										</span>
									</div>		
									<?php
							}
							?>
					</div>
					<div class="articles_section">
						<div class="articles_bar_name"><h1>Article :</h1></div>
						<div class="articles_items">
							<?php render_character_articles($_REQUEST['character']); ?>
						</div>
					</div>
					<div class="comments_tab">
						<div class="comments_bar"><h2>Avis de la communauté :</h2><button onclick="show_comment_bar()">Envoyer un commentaire</button></div>
						<div class="user_comment" id="user_comment_div" style="display:none">
							<form action="" method="POST">
								<div id="textarea_comment">
									<label for="note">Votre commentaire :</label>
									<textarea name="user_comment" id="user_comment" required></textarea>
								</div>
								<label for="note">Votre note :</label>
								<select name="note" class="user_input" id="note">
									<option value="5">5</option>
									<option value="4">4</option>
									<option value="3">3</option>
									<option value="2">2</option>
									<option value="1">1</option>
								</select>
								<input type="submit" value="Envoyer">
							</form>
						</div>
						
						<?php
						get_comments($_GET["character"])
						?>
					</div>
				</div>
			</div>
		</div>
	</body>
	<?php require_once('layout/footer/footer.php'); ?>
</html>
<?php
?>