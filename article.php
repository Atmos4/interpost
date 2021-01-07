<?php

# Require
require 'include/db.php';
require 'assets/libs/Parsedown.php';

$error = "";

if (isset($_GET) and isset($_GET['id'])){
	$result = get_article($_GET['id']);

	if ($result->fail()){
		$error = $result->msg;
	}
	else{
		$article = $result->data;
		$writer = get_user($article['writer_id']);
	}
}
else{
	$error = "Pas d'article ici, désolé.";
}

if ($result->success){
	if ($article['hidden'] and !check_auth(1)){
		$error = "Pas d'article ici, désolé";
	}
}

if ($error == ""){
	
	// $reviews = get_reviews($article['id'])->data;

	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		if (isset($_POST['hideModal']) or isset($_POST['publishModal'])){
			if (check_auth(3)){
				show_article($article['hidden']==0?1:0,$article['id']);
				header('Location: article?id='.$article['id']);
			}
		}
	// 	else if (isset($_POST['approve']) and check_auth(2)){
	// 		add_review($_SESSION['user_id'],$article['id']);
	// 		header('Location: article?id='.$article['id']);
	// 	}
		else if (isset($_POST['deleteModal']) and (check_auth(3) or (check_auth(1) and $_SESSION['user_id']==$article['writer_id']))){
			delete_article($article['id']);
			header('Location: review');
		}
	}
}

$Parsedown = new Parsedown();

?>

<!DOCTYPE HTML>
<html>
	<head>
		<title><?=$error==""?$article['title']:"Pas d'article"?> - Interpost</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
		<?php if ($error==""){
			$img = getimagesize($article['image']);?>
		<meta property="og:url" content="https://www.interpost.fr/article?id=<?=$article['id']?>" />
		<meta property="og:type" content="article" />
		<meta property="og:title" content="<?=$article['title']?>" />
		<meta property="og:description" content="<?=$article['subtitle']?>" />
		<meta property="og:image" content="<?=starts_with($article['image'],'images')?"https://www.interpost.fr/".$article['image']:$article['image']?>" />
		<meta property="og:image:width" content="<?=$img[0]?>" />
		<meta property="og:image:height" content="<?=$img[1]?>" /><?php }?>
		<link rel="stylesheet" href="assets/css/main.css" />
	</head>
	<body class="is-preload no-sidebar">
		
			

		<?php 
		if ($error == "" and (check_auth(3) or (check_auth(1) and $_SESSION['user_id']==$article['writer_id'])) and $article['hidden']){
			$modal_id = "deleteModal";
			$modal_content = "ATTENTION ! Vous êtes sur le point de supprimer cet article ! Êtes-vous certain ?";
			$modal_submit = "Supprimer";
			$modal_destructive = true;
			
			include 'include/modal.php';
		}?>


		
		<?php
		if ($error == "" and (check_auth(3) or (check_auth(1) and $_SESSION['user_id']==$article['writer_id']))){
			if($article['hidden']){
				$modal_id = "publishModal";
				$modal_content = "Voulez-vous vraiment publier cet article ? Il sera alors visible par tout le monde.";
				$modal_submit ="Publier";
			}
			else{
				$modal_id = "hideModal";
				$modal_content = "Voulez-vous masquer cet article ? Il sera alors uniquement visible par les rédacteurs.";
				$modal_submit ="Masquer";
			}
			$modal_destructive = false;
			include 'include/modal.php';
		}?>

		<?php include 'include/image_modal.php'?>

		<div id="page-wrapper">
			
			<?php 
			$current_page = 'article';
			$shrink_nav = true;
			include 'include/navbar.php';
			?>

			<!-- Main -->
				<div id="article-wrapper">
					<div id="content">

						<?php if ($error != ""){?>
							<p class="align-center" style="padding-top: 30px"><?=$error?></p>
						<?php } else {
							$timestamp = strtotime($article['date']);
							$writer_name = $article['name'];
							$text = $Parsedown->text(stripslashes($article['content']))?>

						<!-- Content -->
							<article>
								<div id = "article-banner" style="background-image: url('<?=$article['image']?>');">

									<div id="actions-wrapper" class="fixed left">
										<a class="action-button" href="<?=$article['hidden']?"review.php":"index.php"?>">
											<span class="tooltip"><i class ="fa fa-arrow-left"></i>Retour</span>
										</a>
									</div>

									<?php if(check_auth(2) or (check_auth(1) and ($article['writer_id']==$_SESSION['user_id']))){ ?>

									<div id="actions-wrapper">
										<a class="action-button" href="edit?id=<?=$article['id']?>">
											<span class="tooltip"><i class ="fa fa-pen"></i>Éditer</span>
										</a>
										<?php if(check_auth(3)){?>
										<a class="action-button" onclick="openModal('<?=$modal_id?>')">
											<span class="tooltip">
												<i class ="fa <?=$article['hidden']?'fa-paper-plane':'fa-ban'?>"></i>
												<?=$modal_submit?>
											</span>
										</a>
										<?php
										}
										if ($article['hidden'] and (check_auth(3) or (check_auth(1) and ($_SESSION['user_id']===$article['writer_id'])))){?>
										<a class="action-button" onclick="openModal('deleteModal')">
											<span class="tooltip destructive">
												<i class ="fa fa-trash"></i>
												SUPPRIMER
											</span>
										</a>
										<?php }?>
									</div>

									<?php }?>

									<div id= "article-title">
										<h2><?=$article['title']?></h2>
										<h4><?=$article['subtitle']?></h4>
										<p id="writer-infos">Par <?=$writer_name?>, le <?=date("d-m-Y",$timestamp)?></p>
									</div>
								</div>
								<?php /* if ($article['hidden']){?>
								<form method="post" action="">
									<div id="review-wrapper">
										<?php 
											foreach ($reviews as $review){
											$writer = $review['name'];
											$picture = $review['picture'];
											?>
										<div class="chip approved">
											<img src="<?=$picture!=""?$picture:"images/team/default.png"?>" alt="Person" width="96" height="96">
											<div class="status"><i class="fa fa-check"></i></div>
											<div class="writer-name"><?=$writer?></div>
										</div>
										<?php } ?>
										<?php if (check_auth(2)){ ?>
										<input type="submit" name="approve" id="approve" style="display: none">
										<div class="chip-button approved" style="cursor:pointer" onclick="document.getElementById('approve').click();">
											<i class="fa fa-check"></i>Valider cet article
										</div>
										<?php }?>
									</div>
								</form>
								<?php }*/ ?>
								<div id="article-content">
									<?=$text?>
								</div>

							</article>

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
			<script src="assets/js/dragscroll.js"></script>
			<script src="assets/js/util.js"></script>
			<script src="assets/js/main.js"></script>
			<script src="assets/js/article.js"></script>

	</body>
</html>