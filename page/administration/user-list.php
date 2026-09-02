<?php
$table = "utilisateur";
$column = "id_utilisateur";
$amount = 10;
require("lib/page-library.php");

if(is_admin() && !check_if_user_blocked($_SESSION["id_utilisateur"]) || is_employee() && !check_if_user_blocked($_SESSION["id_utilisateur"])):

	if(is_admin())
		$status = "admin";
	else{
		$status = "employee";
	}

	if(isset($_GET["user"])){
		if(isset($_GET['delete']) && isset($_POST['confirm'])){
			$del_return = delete_user($_REQUEST['user']);
			if($del_return == "success"){
				echo"/suppression-utilisateur-succes";
			}
			else{
				echo"/suppression-erreur";
			}
			exit;
		}
		else if(isset($_GET['suspend']) && isset($_POST['confirm'])){
			$del_return = suspend_user($_REQUEST['user']);
			if($del_return == "success"){
				echo"/suspension-succes";
			}
			else{
				echo"/suspension-error";
			}

			exit;
		}
		else if(isset($_GET['unsuspend']) && isset($_POST['confirm'])){
			$del_return = suspend_user($_REQUEST['user'], true);
			if($del_return == "success"){
				echo"/suspension-succes?uns";
			}
			else{
				echo"/suspension-error";
			}

			exit;
		}
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

				<style>
					div.delete_character {
						top: 50%;
						display: flex;
						flex-direction: column;
						align-items: center;
					}
					.char-appr-div p{
						display: block;
					}
				</style>
				
				<div id="main_screen">
					<div class='user-bar'>
						<h1 class='admin_space'>Liste des utilisateur</h1>
					</div>
					<div class="char-appr-div user-list">

					<?php 
						get_user_list($current_page, $amount);
					

						if(isset($_GET['delete'])){
							?>										
							<div class='delete_character'>
								<p>Souhaitez-vous vraiment supprimer <?php echo get_username_by_id($_GET["user"])?> ? Ceci est une action permanente!</p>
								<span class='buttons-div-confirmation'>
									<button class='delete' id='delete_character' onclick='deleteUser(<?php echo is_admin() ? "true" : "false" ?>)'; value=''>Supprimer</button> <button onclick="location.href = '/<?php echo $status ?>/liste-utilisateur?<?php if(isset($_GET['p'])){echo 'p='.$_GET['p'];} ?>'";>Annuler</button>
								</span>
							</div>		
							<?php
						}
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

