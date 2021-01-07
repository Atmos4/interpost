<?php
require "../include/db.php";

if (isset($_GET) and is_array($_GET) and isset($_GET['page'])){
    $page = $_GET['page'];
} else $page = 1;

$step = 5;
?>


<div class = "image-column">

<?php 
$images = scan_dir("uploads");
if ($images){
    $i = 1;
    $count = count($images);
    foreach ($images as $id => $img){
        if ($img != "." and $img != ".."){
            if ($i>($page-1)*$step and $i<=$page*$step){ ?>
    <div class = "image-actions">
        <img class = "target-image" src="<?="images/uploads/".$img?>" style = "width : 100%">
        <span class="action-cover">Couverture</span>
        <span class="action-insert">Insérer</span>
    </div>

<?php   }
        $i++;
}}}?>

</div>
