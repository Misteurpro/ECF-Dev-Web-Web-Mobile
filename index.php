<?php
	session_start();
	require_once('extension/vendor/autoload.php');
	require_once("lib/function.php");
	require_once("lib/var.php");
	require_once('lib/database.php');
	require_once('lib/Character.php');
	require_once("credentials.php");
	debug_off();

	$raw_link = $_SERVER['REQUEST_URI'];
	$link = explode('?', $raw_link)[0];

	$page_dir = 'page/';
	switch ($link) {
		case '/':
			define('PAGE_IS_HOME', true);
			$page_dir .= 'home.php';
			break;
		case '/menu':
			$welcome_message = true;
			$page_dir .= 'menu.php';
			break;
		case '/menu/galerie-de-personnage':
			$public_list_character = [''];
			$page_dir .= 'menu.php';
			break;
		case '/menu/mon-espace':
			$private_list_character = [''];
			$page_dir .= 'menu.php';
			break;
		case '/menu/mon-espace/supprimer-le-compte':
			$delete_account;
			$page_dir .= '/error/delete-account-confirmation.php';
			break;
		case '/menu/contactez-nous';
			$contact = 1;
			$page_dir .= 'menu.php';
			break;
		case '/contact-succes':
			$page_dir .= '/error/contact-success.php';
			break;
		case '/contact-erreur':
			$page_dir .= '/error/contact-error.php';
			break;
		case '/mention-legales':
			$page_dir .= '/legal-mention.php';
			break;
		case '/cgu':
			$page_dir .= '/condition-general-utilisation.php';
			break;
		case '/politique-de-confidentialite':
			$page_dir .= '/confidentiality.php';
			break;
		case '/menu/page-personnage':
			$page_dir .='CharacterPage.php';
			break;
		case '/menu/createur-de-personnage':
			$page_dir .='CharacterCreator.php';
			$edit = false;
			break;
		case '/menu/page-personnage/modifier':
			$page_dir .='CharacterCreator.php';
			$edit = true;
			break;
		case '/menu/page-personnage/modifier/selection-des-articles':
			$page_dir .='CharacterArticleSelector.php';
			break;
		case '/login':
			$page_dir .= 'login.php';
			break;
		case '/logout':
			$page_dir .= 'logout.php';
			break;
		case '/signin':
			$page_dir .= 'signin.php';
			break;
		case '/forgot-password':
			$page_dir .= 'forgot-password.php';
			break;
		case '/admin':
			$page_dir .= 'administration/administration.php';
			break;
		case '/admin/approuver-commentaire':
			$page_dir .= '/administration/approve-comment.php';
			break;
		case '/admin/approuver-personnage':
			$page_dir .= '/administration/approve-character.php';
			break;
		case '/admin/creer-compte-employee':
			$page_dir .= '/administration/create-account.php';
			break;
		case '/admin/liste-personnage':
			$page_dir .= '/administration/character-list.php';
			break;
		case '/admin/liste-utilisateur':
			$page_dir .= '/administration/user-list.php';
			break;
		case '/admin/manage-article':
			$admin = true;
			$page_dir .= 'administration/manage-item.php';
			break;
		case '/admin/creer-article':
			$admin = true;
			$create_article = true;
			$page_dir .= 'administration/manage-item.php';
			break;
		case '/admin/liste-article':
			$admin = true;
			$article_list = true;
			$page_dir .= 'administration/manage-item.php';
			break;
		case '/admin/logs':
			$admin = true;
			$article_list = true;
			$page_dir .= 'administration/logs.php';
			break;
		case '/employee':
			$welcome_message = true;
			$page_dir .= '/administration/administration.php';
			break;
		case '/employee/approuver-commentaire':
			$page_dir .= '/administration/approve-comment.php';
			break;
		case '/employee/approuver-personnage':
			$page_dir .= '/administration/approve-character.php';
			break;
		case '/employee/liste-personnage':
			$page_dir .= '/administration/character-list.php';
			break;
		case '/employee/liste-utilisateur':
			$page_dir .= '/administration/user-list.php';
			break;
		case '/employee/manage-article':
			$admin = true;
			$page_dir .= 'administration/manage-item.php';
			break;
		case '/employee/creer-article':
			$employee = true;
			$create_article = true;
			$page_dir .= 'administration/manage-item.php';
			break;
		case '/employee/liste-article':
			$employee = true;
			$article_list = true;
			$page_dir .= 'administration/manage-item.php';
			break;
		case '/erreur403':
			$page_dir .= 'error/403.php';
			break;
		case '/suppression-succes':
			$page_dir .= 'error/delete-success.php';
			break;
		case '/suppression-erreur':
			$page_dir .= 'error/delete-error.php';
			break;
		case '/suspension-succes':
			$page_dir .= 'error/suspension-success.php';
			break;
		case '/suspension-character-succes':
			$page_dir .= 'error/suspension-character-success.php';
			break;
		case '/suspension-error':
			$page_dir .= 'error/suspension-success.php';
			break;
		case '/compte-suspendu':
			$page_dir .= 'error/account-suspended.php';
			break;
		case '/suppression-utilisateur-succes':
			$page_dir .= 'error/delete-user-success.php';
			break;
		case '/personnage-creer-erreur':
			$page_dir .= 'error/character-failure.php';
			break;
		case '/personnage-creer-avec-succes':
			$page_dir .= 'error/character-success.php';
			break;
		case '/personnage-modifier-avec-succes':
			$page_dir .= 'error/character-success.php';
			break;
		case '/article-succes':
			$page_dir .= 'error/article-success.php';
			break;
		default:
			http_response_code(404);
			$page_dir .= 'error/404.php';
			break;
	}

	require_once($page_dir);
?>