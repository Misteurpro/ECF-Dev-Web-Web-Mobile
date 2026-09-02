<?php
$table = "commentaire";
$column = "id_commentaire";
$amount = 10;
require_once("lib/page-library.php");

if(is_admin() && !check_if_user_blocked($_SESSION["id_utilisateur"]) || is_employee() && !check_if_user_blocked($_SESSION["id_utilisateur"])):

	if(!empty($_GET['commentaire'])){
		$comment_id = $_GET['commentaire'];
		if(is_connected() && isset($_GET['approve'])){
			approve_comment($comment_id);
		}
		elseif(is_connected() && isset($_GET['refuse'])){
			refuse_comment($comment_id);
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
				<div id="main_screen">
					<div class='user-bar'>
						<h1 class='admin_space'>Approuver des commentaires</h1>
					</div>
					<div class="com-appr-div">
						<?php
						echo get_comments_to_approve($current_page, $amount);
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

