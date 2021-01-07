<?php

require 'include/db.php';

# Auth check
if (!check_auth(1)){
    die('Page non autorisée');
}

$edit_mode= false;

if (isset($_GET) and isset($_GET['id'])){
    $result = get_article($_GET['id']);
    if ($result->fail()){
        die('Article non trouvé');
    }
    else{
        $article = $result->data;
        $edit_mode = true;

        $writer_name = $article['name'];
    }
}

$categories = get_all_categories()->data;

$users = get_all_users()->data;



?>

<!DOCTYPE HTML>
<!--
	Verti by HTML5 UP and Atmos4
-->
<html>
	<head>
		<title>Interpost - <?=$edit_mode?"Editer":"Nouveau"?></title>
		<meta charset="utf-8"/>
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
        <link rel="stylesheet" href="assets/css/main.css" />

        <!-- Firebase -->
        <script src="https://www.gstatic.com/firebasejs/7.19.1/firebase.js"></script>

        <!-- CodeMirror -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.17.0/codemirror.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.17.0/codemirror.css" />

        <!-- Firepad -->
        <link rel="stylesheet" href="https://firepad.io/releases/v1.5.9/firepad.css" />
        <script src="https://firepad.io/releases/v1.5.9/firepad.min.js"></script>
        
	</head>
	<body class="is-preload no-sidebar">

		<div id="page-wrapper">

            
            <?php 
            $current_page = "edit";
            $shrink_nav = true;
            include 'include/navbar.php';
             ?>

            <form id="edit_form" method="post" action="" enctype="multipart/form-data" autocomplete="off">
                <input type="hidden" id = "article_id" name="article_id" <?=$edit_mode?"value=".$_GET['id']:""?>>
                <input type="hidden" id = "firebase_ref" name="firebase_ref" <?=($edit_mode and $article['firebase_ref']!="")?"value=".$article['firebase_ref']:""?>>
                <input name="image" id ="image" type="hidden"
                <?php if ($edit_mode){?>value="<?=$article['image']?>"<?php }?>/>

                <div id="article-wrapper">

                    <!-- Content -->
                        <article>
                            <div id = "article-banner" style="background:#888888">

                                                                   
                                <div id="actions-wrapper" class="fixed left">
                                    <a class="action-button left<?=$edit_mode?"":" hidden"?>" id="redirect_link" href="<?=$edit_mode?"article.php?id=".$article['id']:"#"?>">
                                        <span class="tooltip destructive"><i class ="fa fa-arrow-left"></i>Retour</span>
                                    </a>
                                </div>

                                <div id="actions-wrapper" class="fixed">
                                    <a class="action-button" onclick="saveWithJS()">
                                        <span class="tooltip"><i class ="fa fa-save" style="margin-left:2px"></i>Enregistrer</span>
                                    </a>
                                    <a class="action-button" onclick="openImagePanel()">
                                        <span class="tooltip"><i class ="fa fa-image"></i>Images</span>
                                    </a>
                                </div>
                                <div id= "article-title">
                                    <h2>
                                        <input name="title" id="title" type="text" maxlength="50" placeholder="Titre"

                                        <?php if ($edit_mode){?>value="<?=$article['title']?>"<?php }?>/>

                                    </h2>


                                    <h4>
                                        <input name="subtitle" id="subtitle" type="text" maxlength="100" placeholder="Sous-titre"
                                        <?php if ($edit_mode){?>value="<?=$article['subtitle']?>"<?php }?>/>
                                    </h4>
                                </div>
                            </div>
                            
                            <div id="features-wrapper">
                                <div class="container">
                                    <span id = "failedSave" class="error"></span>

                                    <div class="row">
                                        <div class="col-6 col-12-small">

                                            <label for="category">Rubrique</label>

                                            <select name="category">

                                            <?php foreach($categories as $cate){?>
                                                <option value="<?=$cate['id']?>" <?php if($edit_mode and $cate['id']==$article['category_id'])echo "selected"?>><?=$cate['name']?></option>
                                            <?php }?>

                                            </select>

                                        </div>


                                        <div class="col-6 col-12-small">

                                            <label for="writer">Par : </label>

                                            <select name="writer">

                                            <?php foreach($users as $writer){?>
                                                <option value="<?=$writer['id']?>" <?php if ($edit_mode and $writer['id']==$article['writer_id']) echo "selected"?>><?=$writer['name']?></option>
                                            <?php }?>

                                            </select>
                                        </div>
                                    

                                        <div class="col-6 col-12-medium">
                                            <label>Markdown</label>
                                            <div id="firepad-container"></div>
                                            <textarea id ="content" class="hidden"><?php
                                                if ($edit_mode){
                                                    ?><?=stripslashes($article['content'])?><?php 
                                                }?> </textarea>
                                        </div>
                                        <div id="preview" class="col-6 col-12-medium">
                                            <label><a id="previewButton" onclick="renderPreview()">Aperçu</a></label>
                                            <div id="article-content"></div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                        </article>
                </div>

            </form>

            
        <?php include 'include/footer.php'?>

        </div>

        <div id="imagePanel" class="right-sidenav">

            <form method="post" enctype="multipart/form-data" id="upload-form">
                <button type="button" class="primary large icon solid fa-download" id="choose-file">Importer</button>
                <p>Limite de taille : 8 Mo</p>
                <input type="file" name="files[]" id="upload-file" style="display:none;" multiple/>
                <input type="submit" id="submit-form" name="submit" style="display:none"/>
            </form>
            <div id="imagelist-actions">
                <div id="image-previous-page" class="imagelist-btn"><i class="fa fa-caret-left"></i></div>
                <div id="image-current-page">1</div>
                <div id="image-next-page" class="imagelist-btn"><i class="fa fa-caret-right"></i></div>
            </div>

            <div id="image-gallery"></div>
            <a href="javascript:void(0)" class="closebtn" onclick="closeImagePanel()">&times;</a>
        </div>

    <!-- Scripts -->
        <script src="assets/js/jquery.min.js"></script>
        <script src="assets/js/jquery.dropotron.min.js"></script>
        <script src="assets/js/browser.min.js"></script>
        <script src="assets/js/breakpoints.min.js"></script>
        <script src="assets/js/util.js"></script>
        <script src="assets/js/main.js"></script>
        
        <script src="assets/libs/commonmark.js"></script>
        <script src="assets/js/edit.js"></script>

    </body>
</html>