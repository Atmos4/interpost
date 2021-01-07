<?php


require '../include/db.php';

# Auth check
if (!check_auth(1)){
    die('Page non autorisée');
}

$edit_mode= false;

$response = array();
$messages = array();
$title = $subtitle = $category = $image = $content = $writer = $firebase_ref = "";
$error = false;


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['article_id']) and $_POST['article_id']!=""){
        $result = get_article($_POST['article_id']);
        if (!$result->fail()){
            $article = $result->data;
            $edit_mode = true;

            $writer_name = $article['name'];
        }
    }

    if (isset($_POST['firebase_ref'])){
        $firebase_ref = $_POST['firebase_ref'];
    }

    if (empty($_POST["title"])) {
        $error = true;
        $messages['title'] = "Pas de titre";
    } else {
        $title = check_varchar($_POST["title"]);
    }

    if (empty($_POST["subtitle"])) {
        $error = true;
        $messages['subtitle'] = "Pas de description";
    } else {
        $subtitle = check_varchar($_POST["subtitle"]);
    }

    if (empty($_POST["image"])) {
        $error = true;
        $messages['image'] = "Pas d'image de fond";
    } else {
        $image = check_varchar($_POST["image"]);
    }

    if (empty($_POST['category'])){
        $error = true;
        $messages['category'] = "Sélectionner une rubrique";
    } else {
        $category = check_num($_POST['category']);
    }

    if (empty($_POST['writer'])){
        $error = true;
        $messages['writer'] = "Sélectionner un auteur";
    } else {
        $writer = check_num($_POST['writer']);
    }
    
    if (!empty($_POST["content"]) and trim($_POST["content"])!="") {
        $content = addslashes(trim(check_varchar($_POST["content"])));
    } else {
        $error = true;
        $messages['content'] = "Pas de contenu";
    }

    $response['success'] = !$error;

    if (!$error){
        if ($edit_mode){
            $id = $article['id'];
            if ($writer == "")$writer = $article['writer_id'];
            update_article($id,$writer,$title,$subtitle,$category,$content, $image, $firebase_ref);
        }
        else {
            if ($writer == "")$writer = $_SESSION['user_id'];
            $resp = create_new_article($writer,$title,$subtitle,$category,$image,$content, $firebase_ref);
            if ($resp->success){
                $response['new_id'] = $resp->data['id'];
                $response['redirect'] = "edit.php?id=".$resp->data['id'];
            }
            else $response['redirect'] =  "review.php";
        }
    }
    else{
        $response['errors'] = $messages;
    }
}
else
{
    $response['success'] = false;
    $response['errors'] = array('post' => "La requête ne contient rien");
}

$json = json_encode($response, JSON_PRETTY_PRINT);

echo $json;

