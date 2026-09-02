<?php
	require_once("credentials.php");
	$host = HOST_NAME;
	$db_name = DB_NAME;

	$dsn = "mysql:host=$host;dbname=$db_name;charset=utf8mb4";
	$options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,];
	$dbh = new PDO($dsn, USER_NAME, USER_PASSWORD, $options);