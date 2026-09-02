<?php 
$status = is_admin() ? "admin" : "employee" ;
$is_unsuspend = isset($_GET["uns"]) ? "rétablie" : "suspendu";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <?php require_once('layout/header/head-data.html'); ?>
	<title>Success<?php echo TITLE_PAGE ?></title>
</head>
<body>
	<?php require_once('layout/header/header.php'); ?>
    <div class="margin-12">
        <H1>Le compte utilisateur a été <?php echo $is_unsuspend ?></H1>
        <p>L'utilisateur a été <?php echo $is_unsuspend ?> avec succès, veuillez revenir sur la page administrateur, <a href="/admin/liste-utilisateur">cliquez-ici</a></p>
    </div>
    <?php require_once('layout/footer/footer.php'); ?>
</body>
</html>