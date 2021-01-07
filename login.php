<?php
require 'include/db.php';

$login = $password = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (empty($_POST["password"])) {
        $error = "Mot de passe requis";
    } else {
        $password = check_varchar($_POST["password"]);
    }

    if (empty($_POST["login"])) {
        $error = "Login requis";
    } else {
        $login = check_varchar($_POST["login"]);
    }

    if ($error == ""){
        $attempt = sign_in($login,$password);
        if($attempt->success){
            header("Location: index");
        }
        else{
            $error = $attempt->msg;
        }
    }
}
?>

<!DOCTYPE HTML>
<html>
	<head>
		<title>Login - Interpost</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
		<link rel="stylesheet" href="assets/css/main.css" />
	</head>
	<body class="is-preload no-sidebar">
		<div id="page-wrapper">
            <div id="login-wrapper">
                    <form id="login-form" autocomplete="off" method="POST" action="">
                        <h2>Espace rédaction</h2>
                        <label for="login">Login</label>
                        <input name = "login" type="text" value="<?=$login?>">
                        <label for="password">Mot de passe</label>
                        <input name="password" type="password">
                        <span class="error"><?=$error?></span>
                        <input type="submit" value="Connexion" style="margin-top : 20px;">
                        <a href="index.php" style="display:block;margin: 20px;">Retour à l'accueil</a>
                    </form>
            </div>
        </div>
	</body>
</html>