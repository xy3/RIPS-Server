<?php 

// 
// beware.. bad code below
//

define('AUTH', '$2y$10$LK9FNXZMLEXlhZC76jcYnu.S1OhdMWKMEV8xY45YqfTbFtikBO/AO');
// its the j arod irt

if (isset($_REQUEST['action'])) {
	$_REQUEST['action'](Database::newConnection(), $_REQUEST);
}


function status($status, $msg='') {
	echo json_encode(array('status' => $status, 'message' => $msg));
}
function success($msg='') { return status(1, $msg); }
function failure($msg='') { return status(0, $msg); }


function subtitles($dbc, $imdbID) {
	$res = $dbc->query("SELECT * FROM subs WHERE imdbID='$imdbID'");
	if ($res) {
		$row = $res->fetch_assoc();
		$file = $row['file'];
		return $file;
	}
	return false;
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

function info($dbc, $id) {
	$res = $dbc->query("SELECT * FROM vids WHERE id=$id");
	if ($res) {
		$row = $res->fetch_assoc();
		return success($row);
	}
	return failure($row);
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

	$res = $dbc->query("
		INSERT INTO vids (imdbID, vid, name) VALUES ('$imdbID', '$vid', '$name')
		ON DUPLICATE KEY UPDATE imdbID='$imdbID', vid='$vid', name='$name'
		");

	if ($res) {
		return success();
	}
	return failure();
}


function set_as_live($dbc, $data) {
	$imdbID = $data['imdbID'];
	$password = $data['password'];

	if (!($imdbID && $password)) {
		return failure();
	}

	if (!password_verify($password, AUTH)) {return failure();}

	$res = $dbc->query("UPDATE vids SET live=1 WHERE imdbID = '$imdbID'");
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


function remove_from($dbc, $data) {
	$imdbID = $data['imdbID'];
	$password = $data['password'];
	$table = $data['table'];

	if ($imdbID && $password && password_verify($password, AUTH)) {
	} else {
		return failure('auth');
	}
	
	$del = $dbc->query("DELETE FROM `$table` WHERE imdbID='$imdbID'");
	if ($del) {
		return success('Deleted successfully.');
	}
	return failure('del');
}




function add_subs($dbc, $data) {
	$imdbID = $data['imdbID'];
	$hashed_filename = $data['hashed_filename'];
	$file = $data['file'];
	$password = $data['password'];

	if ($imdbID && $hashed_filename && $file && $password) {
	} else {
		return failure();
	}

	if (!password_verify($password, AUTH)) {return failure("PW");}

	$res = $dbc->query("
		REPLACE INTO subs (imdbID, file, name) 
		VALUES ('$imdbID', '$hashed_filename', '$file')"
	);

	if ($res) {
		return success();
	}
	return failure();
}



function subs_exist($dbc, $data) {
	$imdbID = $data['imdbID'];
	if (!$imdbID) {
		return failure();
	}

	$res = $dbc->query("
		SELECT * FROM subs
		WHERE imdbID='$imdbID'
	");

	if ($res && $res->num_rows > 0) {
		return success();
	}
	return failure("Does not exist");
}
