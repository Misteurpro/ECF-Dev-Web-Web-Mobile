<?php
if(is_admin() && !check_if_user_blocked($_SESSION["id_utilisateur"]) || is_employee() && !check_if_user_blocked($_SESSION["id_utilisateur"])):
	if(isset($admin) && $admin === true)
	$status = "admin";
	elseif(isset($employee) && $employee === true)
	$status = "employee";

	$message = "";
	if(isset($_REQUEST["article_name"])){

		$tag = "";
		if(isset($_REQUEST["tag"]))
			$tag = $_REQUEST["tag"];

		create_article($_REQUEST["article_name"], $tag);
		header("location: /article-succes");
	}

	if(isset($_REQUEST["item"]) && $_REQUEST["item"] != null &&!isset($_REQUEST["delete"]) && isset($_REQUEST["confirm"])){

		$disable = true;
		if(isset($_REQUEST["enable"]))
		$disable = false;

		echo disable_article($_REQUEST["item"], $disable);
		exit;
	}
	if(isset($_REQUEST["item"]) && $_REQUEST["item"] != null && isset($_REQUEST["delete"]) && isset($_REQUEST["confirm"])){

		echo delete_article($_REQUEST["item"]);
		exit;
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
			<?php is_admin() ? require_once('layout/main/menu-admin.html') : require_once('layout/main/menu-employee.html');	?>

			<div id="main_column">
				<div id="main_screen">
					<div class='user-bar'>
						<h1 class='admin_space'>Gérer les articles</h1>
					</div>
					<?php if(!isset($create_article) && !isset($article_list)){ ?>
					<div class="pos-center jc-space-around obj-width-fill margin-bottom-7">
						<a href="creer-article" class="button color-secondary-darker bs-5 pos-center margin-1 width-20">Créer un article</a>
						<a href="liste-article" class="button color-secondary-darker bs-5 pos-center margin-1 width-20">Liste des articles créés</a>
					</div>
					<?php
					}
					elseif(isset($create_article) && $create_article === true){?>
						<form class="margin-bottom-7" action="" method="POST">
							<label for="article_name">Nom de l'article</label>
							<input class="margin-bottom-1" type="text" name="article_name" id="article_name" required>

							<label for="tag">Rendre actif une fois créé</label>
							<input class="yesCheck" type="checkbox" name="tag" id="tag" hidden>
							<label class="margin-bottom-1" for="tag"></label>
							<?php if(isset($message)){ echo "<p style='margin:0' class='text-color-green'>$message</p>"; } ?>

							<input type="submit" value="Soumettre">
						</form>

					<?php } 
					elseif(isset($article_list) && $article_list === true){
						get_all_article();
					
					 } ?>
					
				</div>
			</div>
		</div>
	</main>

	<?php require_once('layout/footer/footer.php'); ?>
</body>
</html>
<?php
elseif(check_if_user_blocked($_SESSION["id_utilisateur"])):
	header('Location: /compte-suspendu');
else:
	header('Location: /login');
endif;

