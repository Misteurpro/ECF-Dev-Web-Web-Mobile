<?php
//SECURE FILE
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
	http_response_code(403);
	header('Location: /erreur403');
	exit;
};

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * This will return true or false if you are connected or not
 * @return bool
 */
function is_connected(){
	if(session_status() === PHP_SESSION_ACTIVE && !empty($_SESSION['id_utilisateur'])){
		return true;
	}
	return false;
}

/**
 * This will return true or false if you are an employee or not
 * @return bool
 */
function is_employee(){
	global $dbh;

	if(is_connected()){
		$user_role = $dbh->prepare("SELECT `id_role` FROM `utilisateur` WHERE `id_utilisateur` = ? AND `id_role`=2");
		$user_role->bindParam(1, $_SESSION['id_utilisateur']);
		$user_role->execute();
		$is_employee = $user_role->fetch(PDO::FETCH_ASSOC);
		return $is_employee != null;
	}
	return false;
}

/**
 * This will return true or false if you are an admin or not
 * @return bool
 */
function is_admin(){
	global $dbh;

	if(is_connected()){
		$user_role = $dbh->prepare("SELECT `id_role` FROM `utilisateur` WHERE `id_utilisateur` = ? AND `id_role`=3");
		$user_role->bindParam(1, $_SESSION['id_utilisateur']);
		$user_role->execute();
		$is_employee = $user_role->fetch(PDO::FETCH_ASSOC);
		return $is_employee != null;
	}
	return false;
}

/**
 * This will enable debugging on the website
 * @return void
 */
function debug_on() : void {
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
}
/**
 * This will disable debugging on the website
 * @return void
 */
function debug_off() : void {
	ini_set('display_errors', 0);
	ini_set('display_startup_errors', 0);
	error_reporting(0);
}

function check_password_strong($password){
	if(preg_match("/^(?=.*[a-zà-öø-ÿ])(?=.*[A-ZÀ-ÖØ-Ý])(?=.*\d)(?=.*[^A-ZÀ-ÖØ-Ýa-zà-öø-ÿ\d]).{12,}$/", $password)){
		return true;
	}
	return false;
}

function login(string $email, string $password) : string {
	global $dbh;

	$check_account = $dbh->prepare("SELECT * FROM `utilisateur` WHERE mail = ?");
	$check_account->bindParam(1, $email);
	$check_account->execute();
	$value_account = $check_account->fetch(PDO::FETCH_ASSOC);

	if($value_account === false){
		return "Erreur : l'email n'existe pas";
	}else{
		$chain = $value_account["password"];
		$verified_password = password_verify($password, $chain);
		if($verified_password)
		{
			$_SESSION['id_utilisateur'] = $value_account["id_utilisateur"];
			header('Location: /menu');
			exit;
		}
		else{
			return "Erreur : le mot de passe est incorrect";
		}
	}
}
function forgot_password(string $email, string $username) : string|bool {
	global $dbh;
	$sanitized_email = htmlspecialchars($email, ENT_QUOTES,'UTF-8');
	$sanitized_username = htmlspecialchars($username, ENT_QUOTES, 'UTF-8');

	$check_account = $dbh->prepare("SELECT * FROM `utilisateur` WHERE mail = ?");
	$check_account->bindParam(1, $email);
	$check_account->execute();
	$value_account = $check_account->fetch(PDO::FETCH_ASSOC);

	if($value_account === false){
		return "Erreur : l'email n'existe pas";
	}else{
		$link = 'http://fantasyrealm/forgot-password?email={{EMAIL}}&username={{USERNAME}}&token={{TOKEN}}';
		$token = generate_token(32);
		$link = str_replace(["{{EMAIL}}","{{USERNAME}}","{{TOKEN}}"],[$sanitized_email,$sanitized_username,$token],$link);
		set_token_to_user($token, $email);
		$check = send_mail_forgot_password($email, $link);
		if($check === true){
			return false;
		}else{
			return "Erreur : l'email n'a pas pu être envoyé";
		}
	}
}

function create_article(string $article_name, bool $start_active){
	global $dbh;

	if(is_connected()){
		if(is_admin() && !check_if_user_blocked($_SESSION["id_utilisateur"]) || is_employee() && !check_if_user_blocked($_SESSION["id_utilisateur"])){
			$active = 0;
			if($start_active){$active = 1;}
			else{$active = 0;}

			$create_article = $dbh->prepare("INSERT INTO `article` (`id_article`, `nom`, `actif`) VALUES (NULL, ?, ?)");
			$create_article->bindParam(1, $article_name);
			$create_article->bindParam(2, $active);
			$create_article->execute();
		}
	}
}

function disable_article(int $article_id, bool $disable){
	global $dbh;

	if(is_connected() ){
		if(is_admin() && !check_if_user_blocked($_SESSION["id_utilisateur"]) || is_employee() && !check_if_user_blocked($_SESSION["id_utilisateur"])){

			if(!$disable){
				$suspend_item = $dbh->prepare("UPDATE `article` SET `actif` = '1' WHERE `article`.`id_article` = ?");
				$suspend_item->bindParam(1, $article_id);
				$suspend_item->execute();
				return "success";
			}
			else if($disable){
				$suspend_item = $dbh->prepare("UPDATE `article` SET `actif` = '0' WHERE `article`.`id_article` = ?");
				$suspend_item->bindParam(1, $article_id);
				$suspend_item->execute();
				return "success";
			}
			else{
				return "error";
			}
		}
		else
			return "error";
	}
	else
		return "error";
}

function delete_article(int $article_id){
	global $dbh;

	if(is_connected() ){
		if(is_admin() && !check_if_user_blocked($_SESSION["id_utilisateur"]) || is_employee() && !check_if_user_blocked($_SESSION["id_utilisateur"])){

			$suspend_item = $dbh->prepare("DELETE FROM article WHERE `article`.`id_article` = ?");
			$suspend_item->bindParam(1, $article_id);
			$suspend_item->execute();
			return "success";
		}
		else
			return "error";
	}
	else 
		return "error";
}

function get_all_article(){
	global $dbh;

	if(is_connected() ){
		if(is_admin() && !check_if_user_blocked($_SESSION["id_utilisateur"]) || is_employee() && !check_if_user_blocked($_SESSION["id_utilisateur"])){
			$get_items = $dbh->prepare("SELECT * FROM `article` WHERE 1");
			$get_items->execute();
			$items = $get_items->fetchAll(PDO::FETCH_ASSOC);

			foreach($items as $item){
				$sanitized_name = htmlspecialchars($item["nom"], ENT_QUOTES, "UTF-8");
				$sanitized_actif = htmlspecialchars($item["actif"], ENT_QUOTES, "UTF-8");
				?>
					<div class="item-card" id="card-<?php echo $item['id_article'] ?>">
						<h3>Article : <?php echo $sanitized_name ?></h3>


						<div class="buttons-div">
							<button class="button suspend" data-isactive="<?php echo $sanitized_actif == true ? 'active' : 'disabled'; ?>" onclick="manageItem(<?php echo $item['id_article'] ?>, <?php echo $sanitized_actif == true ? 'true' : 'false'; ?>, this)"><?php echo $sanitized_actif == true ? "Desactiver" : "Activer" ; ?></button> <button onclick="deleteItem(<?php echo $item['id_article'] ?>, 'card-<?php echo $item['id_article'] ?>')" class="button color-red">Supprimer</button>
						</div>
					</div>
				<?php
			}
		}
	}
}

function suspend_user(int $user_id, bool $unsuspend = false) :string{
	global $dbh;

	if(is_connected()){
			if(is_admin() && !check_if_user_blocked($_SESSION["id_utilisateur"]) || is_employee() && !check_if_user_blocked($_SESSION["id_utilisateur"]))
			{
				if(!$unsuspend){
					$suspend_user = $dbh->prepare("UPDATE `utilisateur` SET `suspendu` = '1' WHERE `utilisateur`.`id_utilisateur` = ?");
					$suspend_user->bindParam(1, $user_id);
					$suspend_user->execute();
					return "success";
				}
				else{
					$suspend_user = $dbh->prepare("UPDATE `utilisateur` SET `suspendu` = '0' WHERE `utilisateur`.`id_utilisateur` = ?");
					$suspend_user->bindParam(1, $user_id);
					$suspend_user->execute();
					return "success";
				}
			}
			else
			{
				return "notadmin";
			}
	}
	return "notconnected";
}

function delete_user(int $user_id){
	global $dbh;

	if(is_connected()){
		if(is_admin() && !check_if_user_blocked($_SESSION["id_utilisateur"]) || is_employee() && !check_if_user_blocked($_SESSION["id_utilisateur"]))
		{
			$name = get_username_by_id($user_id);
			$subject = "Suppression de compte permanente";
			$body = "Bonjour $name, nous sommes désolés de vous annoncer que votre compte a été supprimé de façon permanente par notre équipe de modération.<br>
			Tout personnage que vous avez créé est donc perdu à jamais, si vous souhaitez contacter notre équipe veuillez <a href='http://fantasyrealm/menu/contactez-nous'>cliquez-ici</a>" ;

				send_mail(get_mail_from_user($user_id), $subject, $body);
				$suspend_user = $dbh->prepare("DELETE FROM utilisateur WHERE `utilisateur`.`id_utilisateur` = ?");
				$suspend_user->bindParam(1, $user_id);
				$suspend_user->execute();
				return "success";
		}
		else
		{
			return "notadmin";
		}
	}
	return "notconnected";
}

function delete_self_account(){
	global $dbh;

	if(is_connected()){
		if(!is_admin() && !is_employee()){
			$user_id = $_SESSION["id_utilisateur"];

			session_destroy();
			
			$suspend_user = $dbh->prepare("DELETE FROM utilisateur WHERE `utilisateur`.`id_utilisateur` = ?");
			$suspend_user->bindParam(1, $user_id);
			$suspend_user->execute();

			echo"deleted";
			exit();
		}
		else{
			echo"can't delete your account if admin or employee";
			exit();
		}
	}
	else{
		echo"you are not connected";
		exit();
	}
}

function signin(string $username, string $email, string $password) : string {
	global $dbh;

	$check_account = $dbh->prepare("SELECT * FROM `utilisateur` WHERE mail = ? OR pseudo = ?");
	$check_account->bindParam(1, $email);
	$check_account->bindParam(2, $username);
	$check_account->execute();
	$account_exist = $check_account->fetch(PDO::FETCH_ASSOC);

	if(!$account_exist){
		if(check_password_strong($password)){
			$password_hashed = password_hash($password, PASSWORD_DEFAULT);
	
			$create_account = $dbh->prepare("INSERT INTO `utilisateur` (`id_utilisateur`, `id_role`, `pseudo`, `mail`, `password`, `suspendu`, `motdepasse_a_modifier`) VALUES (NULL, '1', :pseudo, :mail, :password, '0', '0')");
			$create_account->execute([
				'pseudo' => $username,
				'mail' => $email,
				'password' => $password_hashed,
			]);
	
			login($email, $password);
			header('Location: /login');
			exit;
		}else{
			return 'Erreur : le mot de passe ne respecte pas les consignes de sécurité';
		}
	}else{
		if($account_exist['mail'] === $email){
			return "Erreur : l'email existe déjà";
		}
		elseif($account_exist['pseudo'] === $username){
			return "Erreur : le nom d'utilisateur existe déjà";
		}
		return 'Erreur : inconnu';
	}
}

function modify_password(string $email, string $password) : string {
	global $dbh;

	$check_account = $dbh->prepare("SELECT * FROM `utilisateur` WHERE mail = ?");
	$check_account->bindParam(1, $email);
	$check_account->execute();
	$value_account = $check_account->fetch(PDO::FETCH_ASSOC);

	if($value_account === false){
		return "Erreur : l'email n'existe pas";
	}else{
		if(check_password_strong($password)){
			$password_hashed = password_hash($password, PASSWORD_DEFAULT);

			$create_account = $dbh->prepare("UPDATE `utilisateur` SET `password` = ? WHERE mail = ?");
			$create_account->bindParam(1,$password_hashed);
			$create_account->bindParam(2,$email);
			$create_account->execute();

			login($email, $password);
			header('Location: /login');
			exit;
		}else{
			return 'Erreur : le mot de passe ne respecte pas les consignes de sécurité';
		}
	}
}

function signin_employee(string $username, string $email, string $password) : string {
	global $dbh;

	if(!is_admin()){
		return "Erreur : vous n'avez pas le droit de créer de compte employé";
	}
	$check_account = $dbh->prepare("SELECT * FROM `utilisateur` WHERE mail = ? OR pseudo = ?");
	$check_account->bindParam(1, $email);
	$check_account->bindParam(2, $username);
	$check_account->execute();
	$account_exist = $check_account->fetch(PDO::FETCH_ASSOC);

	if(!$account_exist)
	{
		if(check_password_strong($password)){
			$password_hashed = password_hash($password, PASSWORD_DEFAULT);
	
			$create_account = $dbh->prepare("INSERT INTO `utilisateur` (`id_utilisateur`, `id_role`, `pseudo`, `mail`, `password`, `suspendu`, `motdepasse_a_modifier`) VALUES (NULL, '2', :pseudo, :mail, :password, '0', '0')");
			$create_account->execute([
				'pseudo' => $username,
				'mail' => $email,
				'password' => $password_hashed,
			]);
	
			header('Location: /admin/creer-compte-employee?success');
			exit;
		}
		else{
			return 'Erreur : le mot de passe ne respecte pas les consignes de sécurité';
		}
	}
	else
	{
		if($account_exist['mail'] === $email){
			return "Erreur : l'email existe déjà";
		}
		elseif($account_exist['pseudo'] === $username){
			return "Erreur : le nom d'utilisateur existe déjà";
		}
		return 'Erreur : inconnu';
	}
	return '';
}

function get_token(string $token_hash, string $mail_user){
	global $dbh;

	$get_token = $dbh->prepare("SELECT * FROM `token` WHERE `token_hash` = ? and mail = ?");
	$get_token->bindParam(1, $token_hash);
	$get_token->bindParam(2, $mail_user);
	$get_token->execute();
	$token_value = $get_token->fetch(PDO::FETCH_ASSOC);

	if($token_value == null)
		return false;
	else
		return $token_value;
}

function get_tokens_by_mail(string $mail_user){
	global $dbh;

	$get_token = $dbh->prepare("SELECT * FROM `token` WHERE mail = ?");
	$get_token->bindParam(1, $mail_user);
	$get_token->execute();
	$token_list = $get_token->fetchAll(PDO::FETCH_ASSOC);

	if($token_list == null)
		return false;
	else
		return $token_list;
}

function set_token_to_user(string $token_hash, string $email){
	global $dbh;

	$check_account = $dbh->prepare("SELECT * FROM `utilisateur` WHERE mail = ?");
	$check_account->bindParam(1, $email);
	$check_account->execute();
	$value_account = $check_account->fetch(PDO::FETCH_ASSOC);

	if($value_account !== false){
		$tokens = get_tokens_by_mail($email);

		if($tokens){
			foreach ($tokens as $token){
				delete_token($token['token_hash'], $token['mail']);
			}
		}

		$timeout = time()+ 60*10;

		$set_token = $dbh->prepare("INSERT INTO `token` (`token_hash`, `token_expiration_date`, `mail`) VALUES (?, ?, ?)");
		$set_token->bindParam(1, $token_hash);
		$set_token->bindParam(2, $timeout );
		$set_token->bindParam(3, $email);
		$set_token->execute();
	}
}

function delete_token(string $token_hash, string $mail_user){
	global $dbh;

	$delete_character = $dbh->prepare("DELETE FROM token WHERE `token_hash` = ? and mail = ?");
	$delete_character->bindParam(1, $token_hash);
	$delete_character->bindParam(2, $mail_user);
	$delete_character->execute();

	return "success";
}

function use_token(string $token_hash, string $mail_user){

	$token = get_token($token_hash, $mail_user);

	if($token !== false)
	{
		delete_token($token_hash, $mail_user);
		if(time() < $token["token_expiration_date"])
		{
			return true;
		}
	}

	return false;
}

function check_token(string $token_hash, string $mail_user){

	$token = get_token($token_hash, $mail_user);


	if($token !== false)
	{
		if(time() < $token["token_expiration_date"])
		{
			return true;
		}
	}
	return false;
}

function generate_token(int $length){
	return bin2hex(random_bytes($length));
}

function add_link_menu(string $link, string $name){
	$is_active_page = ($link === $_SERVER['REQUEST_URI'])?' class="selectedLink"':'';
	echo "<a href='{$link}'{$is_active_page}>{$name}</a>";
}

function get_username(bool $with_email = false, string $email = ''){
	global $dbh;

	if(!$with_email){
		$user_id = $_SESSION['id_utilisateur'];
		$get_username = $dbh->prepare("SELECT `pseudo` FROM `utilisateur` WHERE `id_utilisateur` = ?");
		$get_username->bindParam(1, $user_id);
		$get_username->execute();
		$username = $get_username->fetch(PDO::FETCH_ASSOC);
	}
	else{
		$get_username = $dbh->prepare("SELECT `pseudo` FROM `utilisateur` WHERE `mail` = ?");
		$get_username->bindParam(1, $email);
		$get_username->execute();
		$username = $get_username->fetch(PDO::FETCH_ASSOC);
	}

	if($username !=null)
	return htmlspecialchars($username["pseudo"], ENT_QUOTES,"UTF-8");
	else
		return "noaccount";
}

function compare_username(string $username){
	global $dbh;

	$get_username = $dbh->prepare("SELECT `pseudo` FROM `utilisateur` WHERE `pseudo` = ?");
	$get_username->bindParam(1, $username);
	$get_username->execute();
	$checked_username = $get_username->fetch(PDO::FETCH_ASSOC);

	if($checked_username === false){
		return true;
	}
	else{
		return false;
	}
}

function send_mail(string $email, string $subject, string $body, string $alt_body='Veuillez utiliser une messagerie en HTML'){
	$debug = false;

	$html_base = '<!DOCTYPE html>
		<html lang="fr">
		<head>
			<meta charset="UTF-8">
			<meta name="viewport" content="width=device-width, initial-scale=1.0">
			<title>{{SUBJECT}}</title>
		</head>
		<body>
			{{BODY}}
		</body>
		</html>';

	$PHPMailer = new PHPMailer($debug?:null);
	$PHPMailer->IsSMTP();
	$PHPMailer->SMTPDebug = 0; // 1 erreur et message ; 2 = message
	$PHPMailer->SMTPAuth = true;
	$PHPMailer->SMTPSecure = 'ssl';

	$PHPMailer->Host = MAIL_HOST;
	$PHPMailer->Port = 465;
	$PHPMailer->Username = MAIL_USER;
	$PHPMailer->Password = MAIL_PWD;

	$PHPMailer->setLanguage('fr');
	$PHPMailer->CharSet = 'UTF-8';

	$PHPMailer->isHTML(true);
	$PHPMailer->AltBody = $alt_body;
	$PHPMailer->setFrom(MAIL_USER, 'Fantasyrealm');

	$PHPMailer->addAddress($email);
	$PHPMailer->Subject = $subject;
	$PHPMailer->msgHTML( str_replace(['{{SUBJECT}}','{{BODY}}'], [$subject,$body], $html_base) );

	try{
		$PHPMailer->send();
		return true;
	}catch(Exception $e){
		if($debug)
			return "Le message n'a pas pu être envoyé. Erreur: {$PHPMailer->ErrorInfo}";
	}
}

function send_mail_forgot_password(string $email, string $link){
	$body = "<p>Bonjour,<br>
		Veuillez cliquer sur ce lien pour modifier votre mot de passe : <a href='{$link}'>{$link}</a> <br> 
		Si vous n'êtes pas à l'origine de la demande vous n'avez rien à faire et vous pouvez ignorer ce message</p>";
	return send_mail($email, 'Mot de passe oublié - Fantasyrealm', $body);
}

function get_mail_from_character(int $character_id){
	global $dbh;

	$get_user_id = $dbh->prepare("SELECT `id_utilisateur` FROM `personnage` WHERE `id_personnage` = ?");
	$get_user_id->bindParam(1, $character_id);
	$get_user_id->execute();
	$user_id = $get_user_id->fetch(PDO::FETCH_ASSOC)["id_utilisateur"];

	$get_mail = $dbh->prepare("SELECT `mail` FROM `utilisateur` WHERE `id_utilisateur` = ?");
	$get_mail->bindParam(1, $user_id);
	$get_mail->execute();
	$mail = $get_mail->fetch(PDO::FETCH_ASSOC)["mail"];

	return $mail;
}

function get_mail_from_user(int $user_id){
	global $dbh;
	
	$get_mail = $dbh->prepare("SELECT `mail` FROM `utilisateur` WHERE `id_utilisateur` = ?");
	$get_mail->bindParam(1, $user_id);
	$get_mail->execute();
	$mail = $get_mail->fetch(PDO::FETCH_ASSOC)["mail"];

	return $mail;
}

function get_comments(int $character_id) : void{
	global $dbh;

	$get_comments = $dbh->prepare("SELECT * FROM `commentaire` WHERE `id_personnage` = ? AND `statut` = 1");
	$get_comments->bindParam(1, $character_id);
	$get_comments->execute();
	$comments = $get_comments->fetchAll(PDO::FETCH_ASSOC);
	
	foreach($comments as $comment){

		$comment_sanitized = htmlspecialchars($comment['commentaire'], ENT_QUOTES, "UTF-8");

		$get_user_name = $dbh->prepare("SELECT `pseudo` FROM `utilisateur` WHERE `id_utilisateur` = ?");
		$get_user_name->bindParam(1, $comment['id_utilisateur']);
		$get_user_name->execute();
		$user_name = $get_user_name->fetch(PDO::FETCH_ASSOC);

		$user_name_sanitized = htmlspecialchars($user_name['pseudo'], ENT_QUOTES, "UTF-8");

		$note = "";

		for($i = 0; $i < $comment['note']; $i++){
			$note = $note."⭐";
		}

		?>
			<div class="comment">
				<div class="title-comment"><h3><?php echo $user_name_sanitized ?></h3> <h3><?php echo $note ?></h3></div>
				<p><?php echo $comment_sanitized ?></p>
				<p class="comment-date"><img width="20" height="20" src="/assets/frontend/SVG/Icones/calendar-lines-pen.svg" alt=""><?php echo $comment['date_commentaire'] ?></p>
			</div>
		<?php
	}
}

function get_comments_to_approve(int $page, int $limit) : mixed{
	global $dbh;

	$range = ($page > 0) ? ($page-1) * $limit : 0;

	if(is_admin() && !check_if_user_blocked($_SESSION["id_utilisateur"]) ||is_employee() && !check_if_user_blocked($_SESSION["id_utilisateur"])){
		$get_comments = $dbh->prepare("SELECT * FROM `commentaire` WHERE `statut` = 0 LIMIT $range, $limit");
		$get_comments->execute();
		$comments = $get_comments->fetchAll(PDO::FETCH_ASSOC);
		
		if($comments != null){
			foreach($comments as $comment){
			
				$comment_sanitized = htmlspecialchars($comment['commentaire'], ENT_QUOTES, "UTF-8");
		
				$get_user_name = $dbh->prepare("SELECT `pseudo` FROM `utilisateur` WHERE `id_utilisateur` = ?");
				$get_user_name->bindParam(1, $comment['id_utilisateur']);
				$get_user_name->execute();
				$user_name = $get_user_name->fetch(PDO::FETCH_ASSOC);
		
				$user_name_sanitized = htmlspecialchars($user_name['pseudo'], ENT_QUOTES, "UTF-8");
		
				$note = "";
		
				for($i = 0; $i < $comment['note']; $i++){
					$note = $note."⭐";
				}
		
				if(is_admin())
					$url = "admin";
				else
					$url = "employee";
		
				?>
				<div>
					<p>Personnage : <a href="/menu/page-personnage?character=<?php echo $comment['id_personnage'] ?>"><?php echo get_character_name($comment['id_personnage']) ?></a></p>
					<div class="comment">
						<div class="title-comment">
							<h3><?php echo $user_name_sanitized ?></h3>
							<h3><?php echo $note ?></h3>
						</div>
						<p><?php echo $comment_sanitized ?></p>
						<div class="date-approval">
							<p class="comment-date"><img width="20" height="20" src="/assets/frontend/SVG/Icones/calendar-lines-pen.svg" alt=""><?php echo $comment['date_commentaire'] ?></p>
							<div class="buttons-div">
								<a href='/<?php echo $url ?>/approuver-commentaire?commentaire=<?php echo $comment["id_commentaire"] ?>&approve' class='button color-secondary-darker'>Approuver</a>
								<a href='/<?php echo $url ?>/approuver-commentaire?commentaire=<?php echo $comment["id_commentaire"] ?>&refuse' class='button color-red'>Refuser</a>
							</div>
						</div>
					</div>
				</div>
				<?php
			}
		}
		else{
			return "<p>Beau travail! Il n'y a plus aucun commentaire à approuver.</p>";
		}
	}
	return null;
}

function approve_comment(int $comment){
	global $dbh;

	if(is_admin() && !check_if_user_blocked($_SESSION["id_utilisateur"]) || is_employee() && !check_if_user_blocked($_SESSION["id_utilisateur"])){
		$set_character = $dbh->prepare("UPDATE `commentaire` SET `statut` = 1 WHERE `id_commentaire` = ?");
		$set_character->bindParam(1, $comment);
		$set_character ->execute();
		$get_comment = $dbh->prepare("SELECT * FROM `commentaire` WHERE `id_commentaire` = ?");
		$get_comment->bindParam(1, $comment);
		$get_comment->execute();
		$comment = $get_comment->fetch(PDO::FETCH_ASSOC);
	
		$comment_character_name = get_character_name($comment["id_personnage"]);
		$link = "http://fantasyrealm/menu/page-personnage?character=".$comment["id_personnage"];
	
		$subject ="Un commentaire a été envoyer sur votre personnage";
		$body ="<p> Un commentaire a été envoyer sur votre personnage <a href='$link'>$comment_character_name</a></p>";
	
		send_mail(get_mail_from_character($comment["id_personnage"]), $subject, $body);
	}
}

function refuse_comment(int $comment){
	global $dbh;

	if(is_admin() && !check_if_user_blocked($_SESSION["id_utilisateur"]) || is_employee() && !check_if_user_blocked($_SESSION["id_utilisateur"])){
		$delete_comment = $dbh->prepare("DELETE FROM commentaire WHERE `commentaire`.`id_commentaire` = ?");
		$delete_comment->bindParam(1,$comment);
		$delete_comment->execute();
	}

}

function send_comment(string $comment, int $note, int $character_id){
	global $dbh;
	
	if(is_connected() && do_character_exist($character_id) && !check_if_user_blocked($_SESSION["id_utilisateur"])){
		$id_utilisateur = $_SESSION["id_utilisateur"];
		$date = date("Y-m-d");;
		$send_comment = $dbh->prepare("INSERT INTO `commentaire` (`id_commentaire`, `note`, `commentaire`, `date_commentaire`, `statut`, `id_personnage`, `id_utilisateur`) VALUES (NULL, ?, ?, ?, '0', ?, ?)");
		$send_comment->bindParam(1, $note);
		$send_comment->bindParam(2, $comment);
		$send_comment->bindParam(3, $date);
		$send_comment->bindParam(4, $character_id);
		$send_comment->bindParam(5, $id_utilisateur);
		$send_comment->execute();

		return true;
	}
	else{
		return false;
	}
}

function get_user_list(int $page, int $limit){

	$range = ($page > 0) ? ($page-1) * $limit : 0;
	global $dbh;

	if(is_admin()){
		$get_users = $dbh->prepare("SELECT * FROM `utilisateur` WHERE 1 AND `id_role` != 3  LIMIT $range, $limit");
	}
	else{
		$get_users = $dbh->prepare("SELECT * FROM `utilisateur` WHERE 1 AND `id_role` = 1  LIMIT $range, $limit");
	}
	
	$get_users->execute();
	$users = $get_users->fetchAll(PDO::FETCH_ASSOC);
	if(is_admin()){
	$status = "admin";
	$status_bool = true;
	}
	else{
		$status = "employee";
		$status_bool = false;
	}

	foreach($users as $user){

		$sanitized_pseudo = htmlspecialchars($user["pseudo"], ENT_QUOTES, 'UTF-8');

		?>
			<div class="user-card">
				<div class="name-bar">
					<span class="profileCase"><img class="profilePic" src="/assets/frontend/SVG/Icones/Head.svg" alt="" width="30" height="30"></span>
					<h3><?php echo $sanitized_pseudo ?></h3>
					<h3><?php echo $user["id_role"] == 2? "(Employée)" : "(Utilisateur)"; ?></h3>
				</div>
				<div class="buttons-div">
					<?php if(check_if_user_blocked($user["id_utilisateur"]) !== 1 ){ ?><button class='button suspend' onclick="suspendUser(<?php echo $user['id_utilisateur'] ?>, false,<?php echo $status_bool ?>)">Suspendre</button><?php }else{ ?>
					<button class='button border-color-orange' onclick="suspendUser(<?php echo $user['id_utilisateur'] ?>, true,<?php echo $status_bool ?>)">Reactivation</button>
					<?php } ?>
					<a href='/<?php echo $status ?>/liste-utilisateur?user=<?php echo $user["id_utilisateur"] ?>&delete' class='button color-red'>Supprimer</a>
					
				</div>
			</div>
		<?php
	}
}

function get_logs(){
	global $dbh;

	if(is_admin()){
		$get_logs = $dbh->prepare("SELECT * FROM `logs` WHERE 1");
		$get_logs->execute();
		$logs = $get_logs->fetchAll(PDO::FETCH_ASSOC);
		foreach ($logs as $log) {
			$log_id = $log["log_id"];
			$reason = htmlspecialchars($log["reason"], ENT_QUOTES, 'UTF-8');
			$character_id = $log["character_id"];
			$character_name = htmlspecialchars($log["character_name"], ENT_QUOTES, 'UTF-8');
			$articles = $log["articles"] != null? htmlspecialchars($log["articles"], ENT_QUOTES, 'UTF-8') : "";
			$trait_visage = $log["trait_visage"] != null? htmlspecialchars($log["trait_visage"], ENT_QUOTES, 'UTF-8') : "";
			$id_utilisateur = $log["id_utilisateur"];
			$date = $log["date"];
			echo"<tr><td>$log_id</td><td>$reason</td><td>$character_id</td><td>$character_name</td><td>$articles</td><td>$trait_visage</td><td>$id_utilisateur</td><td>$date</td></tr>";
		}
	}
}

function insert_logs(bool $is_modify ,int $character_id){
	global $dbh;

	$character = get_characters(false, "", 0, 0, $character_id);
	$name = $character[0]["nom"];
	$articles = $character[0]["article"];
	$skin_color = $character[0]["couleur_peau"];
	$hair_color = $character[0]["couleur_cheveux"];
	$eye_color = $character[0]["couleur_yeux"];
	$eye_shape = $character[0]["forme_yeux"];
	$nose_shape = $character[0]["forme_nez"];
	$mouth_shape = $character[0]["forme_bouche"];
	$face_details = [];
	$face_details[] = $skin_color;
	$face_details[] = $hair_color;
	$face_details[] = $eye_color;
	$face_details[] = $eye_shape;
	$face_details[] = $nose_shape;
	$face_details[] = $mouth_shape;

	$face = json_encode($face_details);

	$modify = "créer";
	if($is_modify){
		$modify = "modifier";
	}

	$insert_log = $dbh->prepare("INSERT INTO `logs` (`log_id`, `reason`, `character_id`, `character_name`, `articles`, `trait_visage`, `id_utilisateur`, `date`) VALUES (NULL, ?, ?, ?, ?, ?, ?, NOW())");
	$insert_log->bindParam(1, $modify);
	$insert_log->bindParam(2, $character_id);
	$insert_log->bindParam(3, $name);
	$insert_log->bindParam(4, $articles);
	$insert_log->bindParam(5, $face);
	$insert_log->bindParam(6, $_SESSION["id_utilisateur"]);
	$insert_log->execute();
}

function check_if_user_blocked(int $user_id){
	global $dbh;
	$check_character = $dbh->prepare("SELECT `suspendu` FROM `utilisateur` WHERE `id_utilisateur` = ?");
	$check_character->bindParam(1, $user_id);
	$check_character->execute();
	$is_blocked = $check_character->fetch(PDO::FETCH_ASSOC);

	return $is_blocked['suspendu'];
}

function get_page_amount(string $table, string $column, int $item_per_page){
	global $dbh;
	$get_row_amount = $dbh->prepare("SELECT COUNT($column) FROM $table");
	$get_row_amount->execute();
	$row_amount = $get_row_amount->fetch(PDO::FETCH_ASSOC)["COUNT($column)"];
	$page = ceil($row_amount / $item_per_page);
	return $page;
}