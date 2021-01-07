<?php

//Require
require 'include/db.php';


//Variables

$current_page = "index";
$articles = get_first_articles()->data;

$first = array_shift($articles);

?>


<!DOCTYPE HTML>
<!--
	Design : Verti by HTML5UP
	Dev : Atmos4 for INTERPOST
-->
<html>
	<head>
		<title>Interpost</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
		<meta name="description" content="Interpost.fr : un média d'information français tenu par une équipe de passionés - Le meilleur de la course d'orientation française et internationale en continu ! Avant-courses, Débriefings, Analyses, Interviews, News, restez toujours au point sur les news de la CO" />
		<?php include 'include/favicon.php'?>
		<link rel="stylesheet" href="assets/css/main.css" />
	</head>
	<body class="is-preload homepage">
		<div id="fb-root"></div>
			
		<div id="page-wrapper">
			
			<?php 
			include 'include/navbar.php';
			 ?>

			
			<div id="features-wrapper">
				<div class="container">
					<div class="row">
						<div class= "col-8 col-12-medium">
							<div class="row">
								<div class="col-12">
									<section id="banner" class="box feature">
										<a class="flex" href="article?id=<?=$first['id']?>">
											<div class="image wide"><img src=<?=$first['image']?> alt=<?=$first['image']?> /></div>
											<div class="inner">
												<header>
													<h2><?=$first['title']?></h2>
												</header>
												<p><?=$first['subtitle']?></p>
											</div>
										</a>
									</section>
								</div>

							<?php foreach ($articles as $article){
									$id = $article['id'];
									$title = $article['title'];
									$subtitle = $article['subtitle'];
									$image = $article['image'];?>

								<div class="col-6 col-12-medium">
									<section class="box feature">
										<a href="article?id=<?=$id?>">
											<div class="image featured"><img src=<?=$image?> alt=<?=$image?> /></div>
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

								<div class="col-12 align-center">
									<a href="browse">Voir tous les articles</a>
								</div>
							</div>
						</div>
						<div class="col-4 col-12-medium">
							<div class="fb-column">

								<!-- Facebook plugin -->
								<div class="fb-page" data-href="https://www.facebook.com/Interpost-101965001208337" data-tabs="timeline" data-width="500" data-small-header="true" data-adapt-container-width="true" data-show-facepile="false"><blockquote cite="https://www.facebook.com/Interpost-101965001208337" class="fb-xfbml-parse-ignore"><a href="https://www.facebook.com/Interpost-101965001208337">Interpost</a></blockquote></div>

							</div>
						</div>
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
			<script async defer crossorigin="anonymous" src="https://connect.facebook.net/fr_FR/all.js#xfbml=1&version=v4.0"></script>
			
			<script src="assets/js/util.js"></script>
			<script src="assets/js/main.js"></script>

			

	</body>
</html>