<?php

	if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
	http_response_code(403);
	header('Location: /erreur403');
	exit;
	};

	//Database related stuff
	define("HOST_NAME", "localhost"); //Just add the IP of the the hosting database.
	define("DB_NAME","fantasyrealm online"); //This shouldn't have to be messed with but this is the database name, if you downloaded the provided database and put it in the hosting database everything will work well!
	define("USER_NAME", "username_here"); //This is the user name of a user you made to access the database.
	define("USER_PASSWORD", "userpassword_here"); //This is the password of the user. 

	//Company related stuff here
	define("COMPANY_EMAIL", "companymail_here"); //This is the company mail that will get every message from the contact form.

	define('MAIL_USER', 'mailsender_here');
	define('MAIL_PWD', 'mailpassword_here');
	define('MAIL_HOST', 'mailhost_here');
