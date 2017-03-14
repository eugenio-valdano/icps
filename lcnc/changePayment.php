<?php
// get VID from session
require('access2.php');
$VID = $_SESSION['VID2'];
$LCNC = $_SESSION['LCNC'];
$NATION = $_SESSION['NATION'];

// store activity in log
require('../sessioner.php');
//howManyIps();

// connect to db
$dbinfo = explode("\n", file_get_contents('../loginDB.txt'))[0];
$dbinfo = explode(" ", $dbinfo);
$user = $dbinfo[1];
$password = $dbinfo[3];
$db = $dbinfo[5];
$host = $dbinfo[7];
$port = $dbinfo[9];
$table = $dbinfo[11];
$gmail_pwd = $dbinfo[13];


$mysqli = new mysqli($host, $user, $password, $db);

if ($mysqli->connect_errno) {
    printf("Connect failed: %s\n", $mysqli->connect_error);
    exit();
}

// utf-8 encoding
mysqli_set_charset($mysqli, 'utf8');

// ID is sent via GET method
$ID = $_GET['ID'];


// select single row, using ID
$stringa = 'SELECT * FROM ' . $table . ' WHERE ID = '.$ID;
$result = $mysqli->query($stringa);
$entries = $result->num_rows;

// should never be more than one result! (because ID must be unique)
if ($entries != 1) {
    die("CONFLICTING IDs !! " . $entries);
}

$row = $result->fetch_array();
$new_status = ($row['LCNC_BOOL']=='P' ? 'IM' : 'P');

// UPDATE QUERY
$stringa = "UPDATE " . $table . " SET LCNC_BOOL='" . $new_status . "' WHERE ID=" . $ID;

// send update query
$result = $mysqli->query($stringa);

$mysqli->close();

header('Location: ' . 'lcnc.php');
?>