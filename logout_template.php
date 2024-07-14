<?php
require("includes/common.inc.php");

//Lifetime der Session setzen: ini_set("session.gc_maxlifetime",1800);
session_start();


ta($_POST);
if(count($_POST)>0 && isset($_POST["btnLogout"])) {
	$_SESSION = []; //löscht sämtlichen Inhalt im Session-Array (für DIESEN Client)
	
	if(ini_get("session.use_cookies")) {
		$params = session_get_cookie_params();
		setcookie(session_name(), '', time() - 86400, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
	}
	
	session_destroy(); //eliminiert die Session-ID
}

if(!(isset($_SESSION["eingeloggt"]) && $_SESSION["eingeloggt"])) {
	header("Location: 09_login01.php");
}
?>
<!doctype html>
<html lang="de">
	<head>
		<title>Eine geschützte Seite</title>
		<meta charset="utf-8">
		<link rel="stylesheet" href="css/common.css">
	</head>
	<body>
		<form method="post">
			<input type="submit" value="ausloggen" name="btnLogout">
		</form>
</html>