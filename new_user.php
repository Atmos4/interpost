<?php
require 'include/db.php';

if (!check_auth(3)){
    die('Page non autorisée');
}

$login  = $name = $description = "";
$loginErr = $nameErr = $descriptionErr = "";
$level = 1;
$loginRes = new Response();
$error = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (empty($_POST["login"])) {
        $error = true;
        $loginErr = "Pas de login";
    }else{
        $login = $_POST["login"];
    }

    if (empty($_POST["name"])) {
        $error = true;
        $nameErr = "Pas de nom";
    } else{
        $name = $_POST["name"];
    }

    if (empty($_POST["description"])) {
        $error = true;
        $descriptionErr = "Pas de description";
    } else{
        $description = $_POST["description"];
    }
    if (!empty($_POST["level"])) {
        $level = $_POST["level"];
    }

    if (!$error){
        $loginRes = create_new_user($level,$name,$description,$login);
        if ($loginRes->success){
            header('Location: about.php');
        }
        else{
            $loginErr = $loginRes->msg;
        }
    }
}
?>

<!DOCTYPE HTML>
<html>
	<head>
		<title>Nouvel utilisateur - Interpost</title>
        <meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
		<link rel="stylesheet" href="assets/css/main.css" />
	</head>
	<body class="is-preload no-sidebar">
		<div id="page-wrapper">

            <?php
            $shrink_nav = true;
            include 'include/navbar.php';?>

            <div id="main-wrapper">
                <div class = "container">
                    <form id="profile-form" autocomplete="off" method="POST" action="" enctype="multipart/form-data">
                        <h2 class = "align-center">Nouvel utilisateur</h2>
                        <div class = "row aln-middle">
                            <div class = "col-6 col-12-small">
                                <h3 class = "align-center">Informations</h3>
                                <label for="name">Nom</label>
                                <input name= "name" type="text" value="<?=$name?>">

                                <label for="description">Description</label>
                                <input name= "description" type="text" value="<?=$description?>">
                            </div>
                            <div class="col-6 col-12-small">
                                <h3 class = "align-center">Sécurité</h3>

                                <label for="login">Login</label>
                                <input name = "login" type="text">
                                <span class="<?=$loginRes->success?"info":"error"?>"><?=$loginRes->msg?></span>

                                
                                <label for="level">Niveau</label>
                                <select name = "level">
                                    <option value = "1" selected>1</option>
                                    <option value = "2">2</option>
                                </select>

                            </div>                            
                            <div class= "col-12" style="text-align : center;">
                                    <input type="submit" value="Enregistrer" style="margin : 20px auto;">
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <?php include 'include/footer.php'?>
        </div>

		<!-- Scripts -->

            <script src="assets/js/jquery.min.js"></script>
			<script src="assets/js/jquery.dropotron.min.js"></script>
			<script src="assets/js/browser.min.js"></script>
			<script src="assets/js/breakpoints.min.js"></script>
			<script src="assets/js/util.js"></script>
			<script src="assets/js/main.js"></script>

            <script src="assets/js/profile.js"></script>
	</body>
</html>