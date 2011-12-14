<?php
require 'Slim/Slim.php';

$app = new Slim();

/**
 *
 * Here we define several Slim application routes that respond
 * to appropriate HTTP request methods. In this example, the second
 * argument for `Slim::get`, `Slim::post`, `Slim::put`, and `Slim::delete`
 * is an anonymous function. If you are using PHP < 5.3, the
 * second argument should be any variable that returns `true` for
 * `is_callable()`. An example GET route for PHP < 5.3 is:
 *
 * $app = new Slim();
 * $app->get('/hello/:name', 'myFunction');
 * function myFunction($name) { echo "Hello, $name"; }
 *
 * The routes below work with PHP >= 5.3.
 */

//GET route

$app->get('/', 'home');
$app->post('/', 'create');
$app->get('/detail/:id', 'detail');

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
	$query = "INSERT INTO";
	$statement = $mysqli->prepare($query);
	$issue = new Issue($adapter);
    $app->redirect('.', 301);
}
function detail($id) { 
	global $app;
	$app->render('detail.php', array());
}


function findIssues(){
	//$mysqli = new mysqli("db393947578.db.1and1.com", "dbo393947578", "=xqoB:yo", "db393947578");
	$mysqli = new mysqli("localhost", "root", "", "zarafix", "3306", "/tmp/mysql.sock");
	if ($mysqli->connect_error) {
		die('Connect Error (' . $mysqli->connect_errno . ') '. $mysqli->connect_error);
	}
	$issues = null;
	if ($stmt = $mysqli->prepare("SELECT * FROM issues")) {
		$stmt->execute();
		$stmt->bind_result($issues);
	}
	$mysqli->close();
	return $issues;
}

$app->run();