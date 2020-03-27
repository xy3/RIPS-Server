<?php 


global $dbc;


define('AUTH', '$2y$10$r2xFTx2FtPCjlM.zdOslMunSAgQ5KvEipKuWgKvlac8DA/VwijfKy');


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
	$password = $data['password'];

	if ($imdbID && $vid && $name && $password) {
	} else {
		return failure();
	}

	if (!password_verify($password, AUTH)) {return failure();}

	$res = $dbc->query("INSERT INTO vids (imdbID, vid, name) VALUES ('$imdbID', '$vid', '$name')");

	if ($res) {
		return success();
	}
	return failure();
}


function remove($dbc, $data) {
	$id = $data['id'];
	$password = $data['password'];

	if ($id && $password && password_verify($password, AUTH)) {
	} else {
		return failure();
	}

	$res1 = $dbc->query("SELECT * FROM vids WHERE id='$id'");

	if ($res1 && $res1->num_rows) 
	{
		$vid = $res1->fetch_assoc()['vid'];
		$deleted = unlink('src/vid/'.$vid);
		
		if ($deleted) {
			$res2 = $dbc->query("DELETE FROM vids WHERE id='$id'");
			return success();
		}
	}
	return failure();
}