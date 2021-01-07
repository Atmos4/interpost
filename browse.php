<?php

//Require
require 'include/db.php';


//Variables

$current_page = "browse";
$grouped = false;
$filtered = false;
if (isset($_GET) and isset($_GET['type'])){
	$type = check_num($_GET['type']);
	$filter = array("category" => $type);
	$articles = get_articles_filtered($filter)->data;
	$filtered = true;
}
else if (isset($_GET) and isset($_GET['group'])){
	$articles = get_grouped_articles()->data;
	$grouped = true;
}
else{
	$articles = get_articles()->data;
}


?>


<!DOCTYPE HTML>
<!--
	Design : Verti by HTML5UP
	Dev : Atmos4 for INTERPOST
-->
<html>
	<head>
		<title>Interpost - Accueil</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
		<?php include 'include/favicon.php'?>
		<link rel="stylesheet" href="assets/css/main.css" />
	</head>
	<body class="is-preload homepage">
		<div id="page-wrapper">

            <?php 
            $current_page = 'browse';
            $shrink_nav = true;
			include 'include/navbar.php';
             ?>
             
			
			<div id="features-wrapper">
                
             
            <?php if (!$filtered){?>
                <div id = "browse-nav">
                    <a title="Trier par date" class="icon solid fa-clock <?php if(!$grouped) echo "current"?>" href="browse">
                    </a>
                    <a title="Grouper par rubrique" class="icon solid fa-layer-group <?php if($grouped) echo "current"?>" href="browse?group">
                    </a>
                </div>
            <?php }?>

				<div class="container">
					<div class="row">
                    <?php if ($grouped){
                        foreach ($articles as $id=>$group){?>

                        <div class="col-12">
                            <div class="group-title"<?php if($id){?> onclick="window.location = ('browse?type=<?=$id?>')"<?php }?>>
                                <?=$id?$categories[$id]['name']:"Autre"?>
                            </div>
                        </div>

                        <?php foreach ($group as $article){
                            $id = $article['id'];
                            $title = $article['title'];
                            $subtitle = $article['subtitle'];
                            $image = $article['image'];?>

                            <div class="col-4 col-12-medium">
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
                        
                    <?php }}} else {
                        //If some stupid guy tries to type random sh*t in the address bar
                        if ($filtered and empty($categories[$type])){?>

                                <div class="col-12 align-center">Cette catégorie n'existe pas</div>

                        <?php } else {
                            if ($filtered){?>

                            <div class="col-12"><div class="group-title"><?=$type?$categories[$type]['name']:"Autre"?></div></div>
    
                        <?php }

                        //If there isn't any article in this category
                        if ($filtered and count($articles)==0){?>
                            <div class = "col-12 align-center">Cette rubrique n'a pas encore d'article</div>
                        <?php } else {

                        foreach ($articles as $article){
                            $id = $article['id'];
                            $title = $article['title'];
                            $subtitle = $article['subtitle'];
                            $image = $article['image'];?>

                        <div class="col-4 col-12-medium">
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
                        
                    <?php }}}}?>
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