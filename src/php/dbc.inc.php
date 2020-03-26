<?php 


DEFINE ('DB_HOST', 'localhost');
DEFINE ('DB_USER', 'depp');
DEFINE ('DB_PASS', 'wolftone14');
DEFINE ('DB_NAME', 'rips');

$dbc = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

/* check connection */
if ($dbc->connect_error) {
    die('Connect Error (' . $dbc->connect_errno . ') '. $dbc->connect_error);
}