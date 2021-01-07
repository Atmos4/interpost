<?php

require 'include/db.php';

$users_res = get_all_users();
?>

<!DOCTYPE HTML>
<html>
	<head>
		<title>La team Interpost</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
        <link rel="stylesheet" href="assets/css/main.css" />
	</head>
	<body class="is-preload no-sidebar">
		<div id="page-wrapper">
			
			<?php 
            $current_page = 'about';
            $shrink_nav = true;
			include 'include/navbar.php';
			?>

			<!-- Main -->
                <div id="features-wrapper">
                    <div class="container">
                        <div class="row">
                            <div class = "col-12 align-center">
                                <h2>La team Interpost</h2>
                            </div>

                            <?php if ($users_res->success){
                                foreach ($users_res->data as $user){
                                    $name = $user['name'];
                                    $description= $user['description'];
                                    $picture = $user['picture'];
                                    $birthday = $user['birthday'];
                                    ?>

                            <div class="col-3 col-4-medium col-12-small">
                                <div class="card" <?php if (check_auth(3)){?> onclick="window.location.href = 'profile.php?id=<?=$user['id']?>'" style="cursor: pointer"<?php } ?>>
                                        <div class="img-container">
                                            <img src="<?=$picture!=""?$picture:"images/team/default.png"?>" alt="<?=$name?>">
                                        </div>
                                        <div class="card-infos">
                                            <h4><?=$name?></h4>
                                            <p class="title"><?=$description?></p>
                                        </div>
                                    
                                    <?php if ($birthday == date("d-m")){?>
                                    <div class="cake">
                                        <div class="candle" style="left: 15%">
                                            <div class="flame"></div>
                                            <div class="flame"></div>
                                            <div class="flame"></div>
                                            <div class="flame"></div>
                                            <div class="flame"></div>
                                        </div>
                                        <div class="candle" style="left: 35%">
                                            <div class="flame"></div>
                                            <div class="flame"></div>
                                            <div class="flame"></div>
                                            <div class="flame"></div>
                                            <div class="flame"></div>
                                        </div>
                                        <div class="candle" style="left: 60%">
                                            <div class="flame"></div>
                                            <div class="flame"></div>
                                            <div class="flame"></div>
                                            <div class="flame"></div>
                                            <div class="flame"></div>
                                        </div>
                                        <div class="candle" style="left: 80%">
                                            <div class="flame"></div>
                                            <div class="flame"></div>
                                            <div class="flame"></div>
                                            <div class="flame"></div>
                                            <div class="flame"></div>
                                        </div>
                                    </div>
                                    <?php }?>
                                </div>
                            </div>

                                <?php }
                            }
                            if (check_auth(3)){ ?>
                            <div class="col-3 col-4-medium col-12-small">
                                <div class="card" id="newUser" onclick="window.location.href = 'new_user.php'">
                                    <i class="fa fa-plus"></i>
                                    <span>Nouvel utilisateur</span>
                                </div>
                            </div>
                            <?php }?>
                        </div>
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

	</body>
</html>