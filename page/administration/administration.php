<?php
if(is_admin() || is_employee()):
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
			<?php is_admin() ? require_once('layout/main/menu-admin.html') : require_once('layout/main/menu-employee.html'); ?>

			<div id="main_column">
				<div id="main_screen">
					<?php
						$account_pseudo = get_username();
						echo"<H1>Bienvenue $account_pseudo, dans votre bureau </H1>";
					?>
				</div>
			</div>
		</div>
	</main>

	<?php require_once('layout/footer/footer.php'); ?>
</body>
</html>
<?php
else:
	header('Location: /login');
endif;
