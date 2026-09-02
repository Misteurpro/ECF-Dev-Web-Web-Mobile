<?php 
	if(isset($_POST["confirm"])){
		delete_self_account();
	}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <?php require_once('layout/header/head-data.html'); ?>
	<title>Suppression de compte<?php echo TITLE_PAGE ?></title>
</head>
<body>
	<?php require_once('layout/header/header.php'); ?>
    <div class="margin-12">
        <H1>Vous êtes sur le point de supprimer votre compte et toutes vos données de façon permanente et ceci ne peut être annulé une fois cliqué sur le bouton de suppression!</H1><br>
		<h2>Souhaitez-vous vraiment procéder à la suppression?</h2>
     	<button class="color-red vw-075" onclick="deleteSelfAccount()"><h2>Confirmer la suppression permanente de mon compte</h2></button> <button class="color-secondary-darker vw-075" onclick="location.href='/menu'";><h2>Annuler la suppression de mon compte</h2></button>
    </div>
    <?php require_once('layout/footer/footer.php'); ?>
</body>
</html>