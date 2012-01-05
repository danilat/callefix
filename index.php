<?php
require 'Slim/Slim.php';

$app = new Slim();

//GET route

$app->get('/', 'home');
$app->post('/', 'create');
$app->get('/detail/:id', 'detail');
$app->get('/categories.xml', 'categories');
$app->get('/api/issues.json', 'issues');
$app->get('/api/categories.json', 'categories');
$app->get('/api/issues/:id.json', 'showIssue');

function home() {
	global $app;
	$categories = array('Residuos', 'Alcantarillado', 'Tráfico y pavimento', 'Zonas verdes y de juego', 'Mobiliario urbano e iluminación', 'Molestias de construcción', 'Pintadas', 'Otros');
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
	$issue = findOneIssue($id);
	$app->render('detail.php', array('issue'=>$issue));
}

function categories(){
	global $app;
	$categories = array('Residuos', 'Alcantarillado', 'Tráfico y pavimento', 'Zonas verdes y de juego', 'Mobiliario urbano e iluminación', 'Molestias de construcción', 'Pintadas', 'Otros');
	echo json_encode($categories);
}

function issues(){
	global $app;
	$issues=findIssues();
	echo json_encode($issues);
}

function showIssue($id) { 
	global $app;
	$issue = findOneIssue($id);
	echo json_encode($issue);
}

function createIssue($category, $description, $lat, $lng, $imageSrc){
	$db = openDB();
	$stmt = $db->prepare("INSERT INTO issue(category, description, lat, lng, image_src) VALUES (?, ?, ?, ?, ?)");
	if ($stmt) {
		$stmt->bind_param("sssss", $category, $description, $lat, $lng, $imageSrc);
		$stmt->execute();
		$id = $db->insert_id;
	}
	closeDB($db);
	return $id;
}
function findIssues(){
	$db = openDB();
	$issues = array();
	if ($stmt = $db->prepare("SELECT id, lat, lng FROM issue")) {
		$stmt->execute();
		$stmt->bind_result($id, $lat, $lng);
		while ($stmt->fetch()) {
			$issue = array('id' => $id, 'lat' =>$lat, 'lng' =>$lng);
			array_push($issues, $issue);
		}
	}
	closeDB($db);
	return $issues;
}
function findOneIssue($id){
	$db = openDB();
	if ($stmt = $db->prepare("SELECT category, description, image_src FROM issue WHERE id = ?")) {
		$stmt->bind_param("i", $id);
		$stmt->execute();
		$stmt->bind_result($category, $description, $imageSrc);
		$stmt->fetch();
		$issue = array('id' => $id, 'category' => $category, 'description' =>$description, 'imageSrc' =>$imageSrc);
	}
	closeDB($db);
	return $issue;
}
function openDB(){
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
	$directory = 'photos/';
	if($file['error'] == 0){
		if(is_uploaded_file($file['tmp_name'])){
			$contentType = $file['type'];
			if($contentType == 'image/jpeg' || $contentType == 'image/png'){
				$filePath = time(). str_replace (" ", "", $file['name']);
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