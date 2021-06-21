<!-- Header -->
<?php

//Allocates everything to avoid empty variables crash
if (empty($current_page)){
    $current_page = "";
}
if (empty($shrink_nav)){
    $shrink_nav = false;
}

$categories = get_all("categories");
?>


<div id="header-wrapper" <?php if ($shrink_nav) echo "class='shrink'"?>>
    <header id="header" class="container">

        <!-- Logo -->
            <div id="logo">
                <h1><a href="index">Interpost</a></h1>
            </div>

        <!-- Nav -->
            <nav id="nav">
                <ul>
                    <li <?php if ($current_page == "index") echo "class='current'"?>><a href="index">Accueil</a></li>
                    <li <?php if ($current_page == "browse") echo "class='current'"?>><a href="browse">Articles</a>
                        <ul>

                        <?php foreach ($categories as $cate){?>

                            <li><a href="browse?type=<?=$cate->id?>"><?=$cate->name?></a></li>
                            
                        <?php }?>

                        </ul>
                    </li>
                    <li <?php if ($current_page == "about") echo "class='current'"?>><a href="about">La team</a></li>
                    <?php if (check_auth(1)){?>
                        
                    <li <?php if ($current_page == "review") echo "class='current'"?>><a href="review">Articles en attente</a></li>
                    <li <?php if ($current_page == "profile") echo "class='current'"?>><a href="edit">Rédaction</a>
                        <ul>
                            <li><a href="edit">Nouvel article</a></li>
                            <li><a href="profile">Editer mon profil</a></li>
                            <li><a href="logout">Déconnexion</a></li>
                        </ul>
                    </li>
                    <?php }?>
                </ul>
            </nav>

    </header>
</div>
<div id="header-gap" <?php if ($shrink_nav) echo "class='shrink'"?>></div>

<div id="snackbar"><span id = "snackcontent">Some text some message..</span></div>