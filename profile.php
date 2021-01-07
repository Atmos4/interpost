<?php
require 'include/db.php';

if (!check_auth(1)){
    die('Page non autorisée');
}

$login  = $name = $description = $password = "";
$new_login = $new_password_repeat = $new_password =  "";
$passwordRes = $loginRes = $pictureRes= new Response();

$admin = false;

if (check_auth(3) and isset($_GET['id'])){
    $result = get_user($_GET['id']);
    $user_id = $_GET['id'];
    $admin = true;
}
else {
    $result = get_user($_SESSION['user_id']);
    $user_id = $_SESSION['user_id'];
}

if($result->fail()){
    die($result->err);
}

$user = $result->data;

$login = $new_login = $user['login'];
$name = $user['name'];
$description = $user['description'];
$picture = $user['picture'];
$refresh_cache = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (!empty($_POST["password"])) {
        if ($admin){
            reset_password($user_id);
            $passwordRes = Response::Message("Mot de passe réinitialisé");
        }
        else{
            $new_password = $_POST["password"];

            if (!empty($_POST["old_password"])) {
                $old_password = $_POST["old_password"];
            }
            if (!empty($_POST["password2"])) {
                $new_password_repeat = $_POST["password2"];
            }
            $passwordRes = change_password($user_id,$old_password,$new_password,$new_password_repeat);
        }
        
    }

    if (empty($_POST["login"])) {
        $new_login = $login;
    }else{
        $new_login = $_POST["login"];
        $loginRes = change_login($user_id,$login, $new_login);
    }

    if (!empty($_POST["name"])) {
        $name = $_POST["name"];
    }

    if (!empty($_POST["description"])) {
        $description = $_POST["description"];
    }

    update_infos($user_id,$name,$description);

    if (isset($_FILES["profilePicture"]) and is_array($_FILES["profilePicture"]) and $_FILES["profilePicture"]['name']!=""){
        
        $pictureRes =upload_profile_picture($user_id,$picture);

        if ($pictureRes->success){
            $picture = $pictureRes->data;
            $refresh_cache = true;
        }
    }
}
?>

<!DOCTYPE HTML>
<html>
	<head>
		<title>Profil - Interpost</title>
        <meta charset="utf-8" />
        <?php if($refresh_cache) echo "<META HTTP-EQUIV=\"CACHE-CONTROL\" CONTENT=\"NO-CACHE\">"?>
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
		<link rel="stylesheet" href="assets/css/main.css" />
	</head>
	<body class="is-preload no-sidebar">
		<div id="page-wrapper">

            <?php $current_page = "profile";
            $shrink_nav = true;
            include 'include/navbar.php';?>

            <div id="main-wrapper">
                <div class = "container">
                    <form id="profile-form" autocomplete="off" method="POST" action="" enctype="multipart/form-data">
                        <div class = "row aln-middle">
                            <div class = "col-6 col-12-small">
                                <h3 class = "align-center">Informations</h3>
                                <label for="name">Nom</label>
                                <input name= "name" type="text" value="<?=$name?>">

                                <label for="description">Description</label>
                                <input name= "description" type="text" value="<?=$description?>">
                            </div>
                            <div class = "col-6 col-12-small">
                                <div style="max-width : 250px; margin : auto;">
                                    <div class="card">
                                        <div class="img-container" onclick="clickImage()" >
                                            <img src="<?=$picture!=""?$picture:"images/team/default.png"?>" id="profile-picture" alt="<?=$name?>">
                                            <div class="overlay"><i class = "fa fa-plus"></i></div>
                                        </div>
                                        <input type ="file" name="profilePicture" id="profile-picture-upload" onchange="changeImage(this)" style="display : none;">
                                        
                                        <div class="card-infos">
                                            <h4><?=$name?></h4>
                                            <p class="title"><?=$description?></p>
                                        </div>
                                    </div>
                                    <span class="<?=$pictureRes->success?"info":"error"?>"><?=$pictureRes->msg?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <hr/>
                        <div class="row">
                            <div class="col-6 col-12-medium">
                                <h3 class = "align-center">Login</h3>

                                <label for="login">Nouveau login</label>
                                <input name = "login" type="text">

                                <span class="<?=$loginRes->success?"info":"error"?>"><?=$loginRes->msg?></span>
                            </div>
                            <div class = "col-6 col-12-medium">
                                    <h3 class = "align-center">Mot de passe</h3>

                                <?php if ($admin){?>
                                <div class= "col-12" style="text-align : center;">
                                    <input type = "submit" class = "alt" name ="password" value = "Réinitialiser">
                                </div>


                                <?php } else {?>
                                
                                        <label for="password">Ancien mot de passe</label>
                                        <input name="old_password" type="password">

                                        <label for="password">Nouveau mot de passe</label>
                                        <input name="password" type="password">
                                        
                                        <label for="password2">Confirmer le mot de passe</label>
                                        <input name="password2" type="password">

                                <?php }?>

                                        <span class="<?=$passwordRes->success?"info":"error"?>"><?=$passwordRes->msg?></span>
                                    </div>
                                </div>
                            </div>

                            
                            <div class= "col-12" style="text-align : center;">
                                    <input type="submit" value="Enregistrer" class = "primary" style="margin : 20px auto;">
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