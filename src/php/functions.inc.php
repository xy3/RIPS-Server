<?php 


global $dbc;

if (isset($_REQUEST['action'])) {
	$_REQUEST['action']($dbc, $_REQUEST);
}


function status($status, $msg='') {
	echo json_encode(array('status' => $status, 'message' => $msg));
}
function success($msg='') { return status(1, $msg); }
function failure($msg='') { return status(0, $msg); }


function check($str) {
	return isset($str) && strlen($str);
}


function show($dbc, $id) {
	$res = $dbc->query("SELECT * FROM vids WHERE id=$id");
	if ($res) {
		$row = $res->fetch_assoc();
		require_once 'src/php/viewnormal.inc.php';
		$fp = $row['vid'];
		$s = new VStream('src/vid/'.$fp);
		$s->start();
	}
}

function listmovies($dbc) {
	$res = $dbc->query("SELECT * FROM vids");
	$rows = [];
	
	if ($res) {
		while ($row = $res->fetch_assoc()) {
			$rows[] = $row;
		}
		echo json_encode($rows);
	}
}


function add($dbc, $data) {
	$imdbID = $data['imdbID'];
	$vid = $data['vid'];
	$name = $data['name'];

	if ($imdbID && $vid && $name) {
	} else {
		return failure();
	}

	$res = false;
	try {
		$res = $dbc->query("INSERT INTO vids (imdbID, vid, name) VALUES ('$imdbID', '$vid', '$name')");
		if ($res) {
			return success();
		}
	} catch (Exception $e) {
		return failure();
	}
	return failure();
}