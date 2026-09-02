<?php
$table = "personnage";
$column = "id_personnage";
$amount = 9;
require_once("lib/page-library.php");

if(is_admin() && !check_if_user_blocked($_SESSION["id_utilisateur"]) || is_employee() && !check_if_user_blocked($_SESSION["id_utilisateur"])):

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
						<h1 class='admin_space'>Liste des personnages</h1>
					</div>
					<div class="char-appr-div">
						<?php
						$character = get_all_characters($current_page, $amount);
						render_all_character($character);
						?>
					</div>
					<?php include_once("layout/main/page-overlay.php");?>
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

