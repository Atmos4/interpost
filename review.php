<?php
//Require
require 'include/db.php';

# Auth check
if (!check_auth(1)){
    die('Page non autorisée');
}

//Variables
$articles = get_hidden_articles()->data;
?>


<!DOCTYPE HTML>
<!--
	Design : Verti by HTML5UP
	Dev : Atmos4 for INTERPOST
-->
<html>
	<head>
		<title>Interpost - Review</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
		<?php include 'include/favicon.php'?>
		<link rel="stylesheet" href="assets/css/main.css" />
	</head>
	<body class="is-preload homepage">
		<div id="page-wrapper">

			<?php 
			$current_page = "review";
			$shrink_nav = true;
			include 'include/navbar.php';
			 ?>
			
			<div id="features-wrapper">
				<div class="container">
					<div class="row">

					<?php foreach ($articles as $article){
						$id = $article['id'];
						$title = $article['title'];
						$subtitle = $article['subtitle'];
						$image = $article['image'];?>

						<div class="col-4 col-12-medium">
                            <section class="box feature">
								<a href="article?id=<?=$id?>">
									<div class="image featured">
										<img src=<?=$image?> alt=<?=$image?> />
									</div>
									<div class="inner">
										<header>
											<h2><?=$title?></h2>
										</header>
										<p><?=$subtitle?></p>
									</div>
								</a>
                            </section>
						</div>
						
					<?php }?>
					
					</div>
				</div>

			<?php include 'include/footer.php'?>

			</div>

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