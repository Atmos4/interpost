<?php

require "Parsedown.php";

$Parsedown = new Parsedown();

if (!empty($_POST) and is_array($_POST) and !empty($_POST['text'])){
    echo $Parsedown->text($_POST['text']);
}