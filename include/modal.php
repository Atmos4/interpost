<?php
if (empty($modal_id)){
    $modal_id = "myModal";
}
if (empty($modal_action)){
    $modal_action = "";
}
if (empty($modal_header)){
    $modal_header = "Confirmation";
}
if (empty($modal_content)){
    $modal_content = "Veuillez confirmer";
}
if (empty($modal_submit)){
    $modal_submit = "Valider";
}
if (empty($modal_destructive)){
    $modal_destructive = false;
}?>

<!-- The Modal -->
<div id="<?=$modal_id?>" class="modal">

    <!-- Modal content -->
    <div class="modal-content">
        <form method="post" action="<?=$modal_action?>">
        <div class="modal-header">
            <span class="close" onclick="document.getElementById('<?=$modal_id?>').style.display='none'">&times;</span>
            <h2><?=$modal_header?></h2>
        </div>
        
        <div class="modal-body">
            <p><?=$modal_content?></p>
        </div> 
        
        <div class="modal-footer"> 
            <input type="submit" name="<?=$modal_id?>" value="<?=$modal_submit?>" <?php if ($modal_destructive){?>class="destructive"<?php }?>>
        </div>
    </div>

</div>