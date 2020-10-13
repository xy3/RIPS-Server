<?php 

require 'vendor/autoload.php';
require 'src/php/dbc.inc.php';
require 'src/php/functions.inc.php';

$router = new \Klein\Klein();


$router->respond('GET', '/', function ($req) {
	return "Home";
});

$router->respond('GET', '/subs/[:imdbID]', function ($req, $resp) {
	header('Content-Type: text/vtt');
	$subs = subtitles(Database::newConnection(), $req->imdbID);
	if ($subs) {
		$file = 'src/subs/' . $subs;
		return $resp->file($file);
	}
	return "None";
});

$router->respond('POST', '/api', function() {});

$router->respond('GET', '/list', function() {
	listmovies(Database::newConnection());
});

$router->respond(['POST', 'GET'], '/info/[:id]', function($req) {
	info(Database::newConnection(), $req->id);
});

$router->dispatch();
