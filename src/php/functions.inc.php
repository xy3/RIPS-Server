<?php 

// 
// beware.. bad code below
//


global $dbc;


define('AUTH', '$2y$10$LK9FNXZMLEXlhZC76jcYnu.S1OhdMWKMEV8xY45YqfTbFtikBO/AO');


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


function delete($dbc, $data) {
	$id = $data['id'];
	$password = $data['password'];

	if ($id && $password && password_verify($password, AUTH)) {
	} else {
		return failure('auth');
	}
	

	$sel = "SELECT * FROM vids WHERE id='$id'";
	$del = "DELETE FROM vids WHERE id='$id'";
	
	$r1 = $dbc->query($sel);
	if ($r1 && $r1->num_rows)
	{
		$row = $r1->fetch_assoc();
		$imdbID = $row['imdbID'];
		$vid = $row['vid'];
		$name = $row['name'];
		
		$ins = "INSERT INTO to_delete VALUES ('$id', '$imdbID', '$vid', '$name')";
		// Remove from active table and add to the
		// 'to_delete' table for later file deletion
		$r1 = $dbc->query($del);
		$r2 = $dbc->query($ins);
		if (!$r1) {
			return failure('r1');
		}
		if (!$r2) {
			return failure('r2');
		}
		return success();
	}
	
	return failure('db');
}