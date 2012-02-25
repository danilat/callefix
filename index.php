<?php
require 'Slim/Slim.php';

$app = new Slim();

//GET route

$app->get('/', 'home');
$app->post('/', 'create');
$app->get('/detail/:id', 'detail');
$app->post('/api/issues.json', 'create');
$app->get('/api/issues.json', 'issues');
$app->get('/api/categories.json', 'categories');
$app->get('/api/issues/:id.json', 'showIssue');

function home() {
	global $app;
	$categories = getCategories();
	$issues=findIssues();
	$app->render('home.php', array('categories' => $categories, 'issues'=> $issues));
}

function create(){
	global $app;
	$id = saveIssue($_POST, $_FILES);
	$app->redirect('.', 301);
}

function createJSON(){
	global $app;
	$id = saveIssue($_POST, $_FILES);
	echo json_encode($id);
}


function detail($id) { 
	global $app;
	$issue = findOneIssue($id);
	if($app->request()->isAjax()){
		$app->render('detail.php', array('issue'=>$issue));
	}else{
		$app->render('show.php', array('issue'=>$issue));
	}
}

function categories(){
	global $app;
	$categories = getCategories();
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
	$id = null;
	if ($stmt) {
		$stmt->bind_param("issss", $category, $description, $lat, $lng, $imageSrc);
		$stmt->execute();
		$id = $db->insert_id;
	}
	closeDB($db);
	return $id;
}
function findIssues(){
	$db = openDB();
	$issues = array();
	if ($stmt = $db->prepare("SELECT id, category, description, lat, lng FROM issue")) {
		$stmt->execute();
		$stmt->bind_result($id, $category, $description, $lat, $lng);
		while ($stmt->fetch()) {
			$issue = array('id' => $id, 'lat' =>$lat, 'lng' =>$lng, 'category' => $category, 'description'=>$description);
			array_push($issues, $issue);
		}
	}
	closeDB($db);
	return $issues;
}
function findOneIssue($id){
	$db = openDB();
	if ($stmt = $db->prepare("SELECT id, lat, lng, category, description, image_src FROM issue WHERE id = ?")) {
		$stmt->bind_param("i", $id);
		$stmt->execute();
		$stmt->bind_result($is, $lat, $lng, $category, $description, $imageSrc);
		$stmt->fetch();
		$issue = array('id' => $id, 'lat' =>$lat, 'lng' =>$lng, 'category' => $category, 'description' =>$description, 'imageSrc' =>$imageSrc);
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

function saveIssue($post, $files){
	$category = $post["category"];
	$description = $post["description"];
	$lat = $post["lat"];
	$lng = $post["lng"];
	$imageSrc = "";
	if(isset($files)){
		$imageSrc = photoUpload($files["photo"]);
	}
	$id = createIssue($category, $description, $lat, $lng, $imageSrc);
	//sendNotification($id, $category, $description);
	return $id;
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

function sendNotification($id, $category, $description){
	$url = "http://www.zaragoza.es/ciudad/ticketing/saveAnonymousRequest_Ticketing";
	$postParams = array(
		'descriptionSubject' => '[Zarafix] Nueva incidencia de '.$category, 
		'description' => $description. '\n Ver en http://www.zarafix.com/index.php/detail/'.$id
	);
	$session = curl_init($url);
	// definir tipo de petici&oacute;n a realizar: POST
	curl_setopt ($session, CURLOPT_POST, true); 
	// Le pasamos los par&aacute;metros definidos anteriormente
	curl_setopt ($session, CURLOPT_POSTFIELDS, $postParams);
	// s&oacute;lo queremos que nos devuelva la respuesta
	curl_setopt($session, CURLOPT_HEADER, false); 
	curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
	// ejecutamos la petici&oacute;n
	$response = curl_exec($session); 
	// cerramos conexi&oacute;n
	curl_close($session);
	return $response;
}

function getCategories(){
	return array(1 => 'Residuos', 2 => 'Alcantarillado', 3 => 'Tráfico y pavimento', 4 => 'Zonas verdes y de juego', 5 => 'Mobiliario urbano e iluminación', 6 => 'Molestias de construcción', 7 => 'Pintadas', 8 => 'Otros');
}

function getCategory($id){
	$category = getCategories();
	return $category[$id];
}


$app->run();