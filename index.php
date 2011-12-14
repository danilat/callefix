<?php
require 'Slim/Slim.php';

$app = new Slim();

//GET route

$app->get('/', 'home');
$app->post('/', 'create');
$app->get('/detail/:id', 'detail');
$app->get('/categories.xml', 'categories');

function home() {
	global $app;
	$categories = array('Residuos', 'Alcantarillado', 'Tráfico y pavimento', 'Zonas verdes y de juego', 'Mobiliario urbano e iluminación', 'Molestias de construcción', 'Pintadas');
	$issues=findIssues();
	$app->render('home.php', array('categories' => $categories, 'issues'=> $issues));
}

function create(){
	global $app;
	$category = $_POST["category"];
	$description = $_POST["description"];
	$lat = $_POST["lat"];
	$lng = $_POST["lng"];
	$imageSrc = "";
	if(isset($_FILES)){
		$imageSrc = photoUpload($_FILES["photo"]);
	}
	createIssue($category, $description, $lat, $lng, $imageSrc);
    $app->redirect('.', 301);
}
function detail($id) { 
	global $app;
	$app->render('detail.php', array('issue'=>$_SESSION['issues'][$id]));
}

function createIssue($category, $description, $lat, $lng, $imageSrc){
	if(!isset($_SESSION['issues'])){
		$_SESSION['issues'] = array();
	}
	$issues = $_SESSION['issues'];
	$issues[count($issues)] = array('category'=>$category, 'description'=>$description, 'lat'=>$lat, 'lng'=>$lng, 'photo'=> $imageSrc);
	$_SESSION['issues'] = $issues;
}
function findIssues(){
	/*$db = openDB();
	$issues = null;
	if ($stmt = $db->prepare("SELECT * FROM issues")) {
		$stmt->execute();
		$stmt->bind_result($issues);
	}
	closeDB($db);*/
	$issues = $_SESSION['issues'];
	return $issues;
}
function openDB(){
	//$mysqli = new mysqli("db393947578.db.1and1.com", "dbo393947578", "=xqoB:yo", "db393947578");
	$db = new mysqli("localhost", "root", "", "zarafix", "3306", "/tmp/mysql.sock");
	if ($db->connect_error) {
		die('Connect Error (' . $db->connect_errno . ') '. $db->connect_error);
	}
	return $db;
}

function closeDB($db){
	$db->close();
}

function photoUpload($file){
	$directory = '/Applications/MAMP/htdocs/zaragozafix/photos/';
	if($file['error'] == 0){
		if(is_uploaded_file($file['tmp_name'])){
			$contentType = $file['type'];
			if($contentType == 'image/jpeg' || $contentType == 'image/png'){
				$filePath = time().$file['name'];
				if (move_uploaded_file($file['tmp_name'], $directory.$filePath)) {
			    	return $filePath;
				}
			}else{
				die("Formato de archivo NO soportado");
			}
		}
	}
}

$app->run();