<?php
$table = "personnage";
$column = "id_personnage";
$amount = 9;
require("lib/page-library.php");

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
			<?php 
			is_admin() ? require_once('layout/main/menu-admin.html') : require_once('layout/main/menu-employee.html');
			?>

			<div id="main_column">
				<div id="main_screen" class="flex-start">
					<div class='user-bar'>
						<h1 class='admin_space'>Logs</h1>
					</div>
					<div class="logs-div">
						<table>
							<tr><td>id_log</td><td>raison</td><td>id_personnage</td><td>nom_personnage</td><td>articles</td><td>trait_visage</td><td>id_utilisateur</td><td>date</td></tr>
							<?php get_logs() ?>
						</table>
					</div>
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

