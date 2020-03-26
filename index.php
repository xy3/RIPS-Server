<?php 

// works

error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

require 'vendor/autoload.php';
require 'src/php/dbc.inc.php';
require 'src/php/functions.inc.php';

$router = new \Klein\Klein();

function dbc() {
	global $dbc;
	return $dbc;
}


$router->respond('GET', '/', function ($req) {
	return "Home";
});

$router->respond('GET', '/[:id]', function ($req) {
	return show(dbc(), $req->id);
});


$router->respond('POST', '/[api]', function() {});

$router->respond('GET', '/list', function() {
	listmovies(dbc());
});


// $router->onHttpError(function ($code, $router) {
// 	$router->response()->redirect('/')->send();
// });


$router->dispatch();
