<?php 

init();

/**
 * Initialises database and PHP session. 
 * 
 * **Is needed for any page to work !**
 */
function init(){
    
    //DB CONFIG
	$db_config = load_config();
	if (!count($db_config)){
		die('Pas de fichier de configuration');
	}
    
    global $db;
    //Database init
    try {
    	$db = new PDO('mysql:host='.$db_config['host'].';dbname='.$db_config['name'].';charset=utf8', $db_config['login'], $db_config['password']);
    } catch(Exception $e) {
            die('Erreur : '.$e->getMessage());
    }

    //Session init
    session_start();

    //Time zone
    date_default_timezone_set ("Europe/Paris");
}

/**
 * WARNING: a config.php file is necessary
 */
function load_config(){

	if (file_exists('config.php')){
		include 'config.php';
	}
	elseif (file_exists('../config.php')){
		include '../config.php';
	}
	else{
		return array();
	}
	return ['host'=>$db_host,'name'=>$db_name,'login'=>$db_login,'password'=>$db_password];
}

/**
 * Response wrapper
 */
class Response{
    /** Response state flag */
    public $success = true;
    /** Response data */
    public $data;
    /** Response message */
    public $msg = "";

    public function __construct() {
    }

    /**
     * Factory : Success 
     */
    public static function New($dat) {
        $instance = new self();
        $instance->data = $dat;
        return $instance;
    }

    /**
     * Factory : message
     */
    public static function Message($dat) {
        $instance = new self();
        $instance->msg = $dat;
        return $instance;
    }

    /**
     * Factory : error
     */
    public static function Error($msg = "") {
        $instance = new self();
        $instance->success = false;
        $instance->msg = $msg;
        return $instance;
    }

    /**
     * Magic function toString() -> cast to string.
     */
    public function __toString(){
        return $this->msg;
    }

    /**
     * Returns opposite of success
     */
    public function fail(){
        return !$this->success;
    }

    /** Set $msg variable, allows decorator pattern
     * @return Response $this object (decorator pattern)
     */
    public function setMessage($msg){
        $this->msg = $msg;
        return $this;
    }

}

/**
 * Returns an instance of the database PDO object
 * 
 * @return PDO Instance of the database
 */
function get_db(){
	return $GLOBALS['db'];
}

/**
 * Performs a PDO query() or prepare() + execute() depending on second argument.
 */
function query_db($sql, $args = null){
    if ($args){
        $reqex = get_db()->prepare($sql);
        $reqex->execute($args);
        return $reqex;
    }
    else {
        $req_query = get_db()->query($sql);
        return $req_query;
    }
}

/**
 * Checks if the argument is numeric and applies conversion
 */
function check_num($data = 0) {
	if(is_numeric($data) or $data == '') {
		return $data;
	}
	die("format de donnée invalide: '".$data."'");
}

/**
 *  Checks if the argument is a varchar
 */
function check_varchar($data = "") {
	if(!preg_match("/[\w\d\s]*/", $data)) {
		die("format de donnée invalide");
	}
	return $data;
}

/**
 * Checks user authentication.
 * 
 * TODO : This function will need improvments if more authentication levels are used
 */
function check_auth($level = 0){
    return (isset($_SESSION['level']) and $_SESSION['level'] >= $level);
}

function starts_with ($string, $startString) 
{ 
    $len = strlen($startString); 
    return (substr($string, 0, $len) === $startString); 
} 

/**
 * Authenticates the user logging in. Includes additional security against brute force attacks
 * 
 * @param string $login User login
 * @param string $password User password
 * @return Response user data or error message
 */
function sign_in($login, $password){
    $lockout_time = 10;
    $allowed_attempts = 3;

    $req_auth = query_db('SELECT * from users WHERE login=? LIMIT 1', array($login));
    if ($result =$req_auth->fetch()){
        $failure_count = $result['failure_count'];
        $block_time = strtotime($result['block_time']);
        $time = time();

        if (($failure_count >= $allowed_attempts) and ($time-$block_time) < $lockout_time*$failure_count*$failure_count){
            return Response::Error("Compte bloqué pendant ".(($block_time + $lockout_time*$failure_count*$failure_count)-$time)."s");
        }
        else{
            if ($result['password'] == sha1($password)){
                query_db('UPDATE users SET failure_count = 0 WHERE login = ? LIMIT 1', array($login));
                $_SESSION['user_id'] = $result['id'];
                $_SESSION['level']= $result['level'];
                return Response::New($result);
            }
            else{
                $failure_count++;
                query_db('UPDATE users SET failure_count = ?, block_time = CURRENT_TIMESTAMP WHERE login = ? LIMIT 1', array($failure_count, $login));
                if ($failure_count < $allowed_attempts)
                    return Response::Error("Mot de passe incorrect.<br/>".($allowed_attempts-$failure_count)." essais restants");
                else
                    return Response::Error("Compte bloqué pendant ".($lockout_time*$failure_count*$failure_count)."s");
            }
        }
    }
    else {
        return Response::Error("Identifiant ou mot de passe incorrect");
    }
}

#CATEGORIES

/**
 * Gets all the categories
 */
function get_all_categories(){
    $result = array();
    $req_categories = query_db('SELECT * from categories');
    while ($data = $req_categories->fetch()){
        $result[$data['id']]=$data;
    }
    return Response::New($result);
}

#USERS

/**
 * Returns a single user's data
 * 
 * @param number user id
 * @return Response user data or error message
 */
function get_user($id){
    $req_user = query_db('SELECT * from users WHERE id=?',array($id));
    if ($result =$req_user->fetch()){
        return Response::New($result);
    }
    else{
        return Response::Error("Pas d'utilisateur trouvé");
    }
}

/**
 * Returns all users from database
 */
function get_all_users(){
    $req_users = query_db('SELECT * from users ORDER BY level DESC');
    if ($result =$req_users->fetchAll()){
        return Response::New($result);
    }
    else{
        return Response::Error("Pas d'utilisateur trouvé");
    }
}

/**
 * Creates a new user
 * 
 * @todo Safety check mostly
 */
function create_new_user($level,$name,$description,$login){
    
	$login  = check_varchar($login);
	$req = query_db('SELECT login FROM users WHERE login= ? LIMIT 1',array($login));

	//Si un autre utilisateur est trouvé dans la DB avec le même login
	if ($req->fetch()){
		return Response::Error("Ce login est déjà utilisé");
	} else {
        query_db('INSERT INTO users(level,name, description,picture,login,password) VALUES(?,?,?,?,?,SHA1(?))',array($level,$name,$description,"",$login,$login));
        header('Location: about.php');
    }
}

# ARTICLES

/**
 * Returns all articles
 * 
 * @return Response All visible articles by date descending
 */
function get_articles($amount=0){
    if ($amount){
        $req_articles = query_db('SELECT * FROM articles WHERE hidden=FALSE ORDER BY date DESC LIMIT '.$amount);
    }
    else{
        $req_articles = query_db('SELECT * FROM articles WHERE hidden=FALSE ORDER BY date DESC');
    }
    $articles = $req_articles->fetchAll();
    $ret = Response::New($articles);
    return $ret;
}

function get_articles_filtered($filter){
    $category = "";
    if (is_array($filter)){
        if (isset($filter['category'])){
            $category = $filter['category'];
        }
    }

    if ($category!=""){
        return Response::New(query_db('SELECT * FROM articles WHERE hidden=FALSE AND category_id = ? ORDER BY date DESC',array($category))->fetchAll());
    }
    else{
        return Response::New(array());
    }
}


function get_grouped_articles(){
    $articles = array();
    $req_articles = query_db('SELECT * FROM articles WHERE hidden=FALSE ORDER BY date DESC');
    while ($data = $req_articles->fetch()){
        if (!isset($articles[$data['category_id']])){
            $articles[$data['category_id']]=array();
        }
        array_push($articles[$data['category_id']],$data);
    }
    return Response::New($articles);
}

/**
 * Returns all articles
 * 
 * @return Response All visible articles by date descending
 */
function get_first_articles(){
    $req_articles = query_db('SELECT * FROM articles WHERE hidden=FALSE ORDER BY date DESC LIMIT 3');
    $articles = $req_articles->fetchAll();
    $ret = Response::New($articles);
    return $ret;
}

/**
 * Returns hidden articles
 * 
 * @return Response All hidden articles by date descending
 */
function get_hidden_articles(){
    $req_articles = query_db('SELECT * FROM articles WHERE hidden=TRUE ORDER BY date DESC');
    $articles = $req_articles->fetchAll();
    $ret = Response::New($articles);
    return $ret;
}

/**
 * Returns the article matching given id
 *
 * @param number $id
 * @return Response article or error
 */
function get_article($id){
    $id = check_num($id);
    $req_article = query_db('SELECT articles.*, users.name, users.picture FROM articles LEFT JOIN users ON articles.writer_id=users.id WHERE articles.id = ?', array($id));
    if ($data = $req_article->fetch()){
        return Response::New($data);
    }
    else return Response::Error("Pas d'article trouvé");
}

/**
 * Creates a new article
 * 
 * @param string $title
 * @param string $subtitle
 * @param string $image Path to cover image
 * @param string $content Article content - Markdown
 * 
 * @return Response
 */
function create_new_article($writer, $title,$subtitle,$category,$image,$content,$firebase){
    query_db('INSERT INTO articles(writer_id,hidden,title,subtitle,category_id,image,content, firebase_ref) VALUES(?,?,?,?,?,?,?,?)', array($writer,1,$title,$subtitle,$category,$image,$content,$firebase));
    $req_check = query_db('SELECT id FROM articles ORDER BY id DESC LIMIT 1');
    if ($data = $req_check->fetch()){
        return Response::New($data);
    }
    else return Response::Error("Article non sauvegardé");
}

/**
 * Updates an article. 
 * 
 * TODO : This would require additional safety checks if article writing is opened to freelances
 */
function update_article($id,$writer,$title,$subtitle,$category,$content,$image,$firebase){
    query_db('UPDATE articles SET writer_id = ?,title =?, subtitle = ?,category_id = ?, content = ?, image=?, firebase_ref=? WHERE id = ? LIMIT 1', array($writer,$title,$subtitle,$category,$content,$image,$firebase,$id));
}

/**
 * Publish or hide an article
 */
function show_article($show, $id){
    query_db('UPDATE articles SET hidden=?, date = NOW() WHERE id = ? LIMIT 1', array($show,$id));
}

function delete_article($id){
    query_db('DELETE FROM articles WHERE id=? LIMIT 1', array($id));
}

/**
 * Updates profile infos
 * 
 * TODO : improvements - additional checks ?
 */
function update_infos($id,$name,$description){
    query_db('UPDATE users SET name =?, description = ? WHERE id = ? LIMIT 1', array($name,$description,$id));
}

#REVIEWS (not used at the moment)

/**
 * Reviews
 */
function get_reviews($article_id){
    return Response::New(query_db('SELECT reviews.*,users.name,users.picture from reviews LEFT JOIN users ON reviews.writer_id=users.id WHERE article_id=?',array($article_id))->fetchAll());
}

function add_review($writer, $article){
    $result = query_db('SELECT * FROM reviews WHERE writer_id=? AND article_id=?',array($writer,$article))->fetch();
    if (!$result){
        query_db('INSERT INTO reviews(writer_id,article_id) VALUES (?,?)',array($writer,$article));
    }
    else{
        query_db('DELETE FROM reviews WHERE id=?',array($result['id']));
    }
}

function delete_review($id){
    query_db('DELETE from reviews WHERE id=?',array($id));
}

/**
 * Updates password
 *
 * @param string $old Old password
 * @param string $new New password
 * @param string $new2 New password confirmation
 * @return Response success or error messages
 */
function change_password($id,$old, $new, $new2) {
	if (sha1($new) != sha1($new2)) {
		return Response::Error("Les mots de passes sont différents");
    }
    if (strlen($new)<5){
        return Response::Error("Le nouveau mot de passe est trop court");
    }
	$pass  = check_varchar($old);
	$npass = check_varchar($new);
	$req = query_db('SELECT * FROM users WHERE id= ? AND password=SHA1(?) LIMIT 1',array( $id, $pass));

	//Si l'utilisateur est trouvé dans la DB
	if ($req->fetch()){
		$req = query_db('UPDATE users SET password=SHA1(?) WHERE id=? LIMIT 1',array($npass, $id));
		return Response::Message("Changement effectué");
	} else {
		return Response::Error("L'ancien mot de passe est incorrect");
	}
}

/**
 * ADMIN METHOD
 * Reset password
 */
function reset_password($id){
    $user = get_user($id);
    if ($user->success){
        query_db('UPDATE users SET password = SHA1(?) WHERE id = ?', array($user->data['login'],$id));
    }
}

/**
 * Updates login
 * 
 * @param mixed $old Old login
 * @param mixed $new New login
 * @return Response Message or Error
 */
function change_login($id, $old, $new) {
    $new = trim($new);
	if (sha1($old) == sha1($new)) {
		return Response::Error("Le nouveau login est identique à l'ancien");
    }
    if (strlen($new)<4){
        return Response::Error("Le nouveau login est trop court");
    }
	$login  = check_varchar($new);
	$req = query_db('SELECT login FROM users WHERE login= ? LIMIT 1',array($login));

	//Si un autre utilisateur est trouvé dans la DB avec le même login
	if ($req->fetch()){
		return Response::Error("Ce login est déjà utilisé");
	} else {
		$req = query_db('UPDATE users SET login=? WHERE id=? LIMIT 1',array($login, $id));
		return Response::Message("Changement effectué");
	}
}

/**
  * Uploads profile picture
  */
function upload_profile_picture($id,$old){
    $target_dir = "images/team/";
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($_FILES["profilePicture"]["name"],PATHINFO_EXTENSION));

    $final_file = $target_dir . "team".$id."_".time().".".$imageFileType;
    // Check if image file is a actual image or fake image
    if(isset($_POST["submit"])) {
        $check = getimagesize($_FILES["profilePicture"]["tmp_name"]);
        if(!$check) {
            $uploadOk=0;
        }
    }

    // Check if file already exists,
    if ($old != "" and file_exists($old)) {
        if (!unlink($old)){
            $uploadOk=0;
        }
    }
    // Check file size
    if ($_FILES["profilePicture"]["size"] > 5000000) {
        return Response::Error("Le fichier est trop gros");
    }
    // Allow certain file formats
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
    && $imageFileType != "gif" ) {
        $uploadOk=0;
    }
    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        return Response::Error("Sorry, your file was not uploaded.");
    // if everything is ok, try to upload file
    } else {
        if (move_uploaded_file($_FILES["profilePicture"]["tmp_name"], $final_file)) {
            query_db('UPDATE users set picture=? WHERE id=? LIMIT 1', array($final_file,$id));
            return Response::New($final_file)->setMessage("Photo de profil changée");
        } else {
            return Response::Error("Sorry, there was an error uploading your file.");
        }
    }
}

/**
 * Scans directory and sorts files by last modification date
 */
function scan_dir($dir) {
    $ignored = array('.', '..', '.svn', '.htaccess');

    $files = array();    
    foreach (scandir($dir) as $file) {
        if (in_array($file, $ignored)) continue;
        $files[$file] = filemtime($dir . '/' . $file);
    }

    arsort($files);
    $files = array_keys($files);

    return ($files) ? $files : false;
}
?>