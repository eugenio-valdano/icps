<?php
// get VID from session
require('access.php');
$VID = $_SESSION['VID'];
// store activity in log
require('sessioner.php');
howManyIps();

// connect to db
$dbinfo = explode("\n", file_get_contents('loginDB.txt'))[0];
$dbinfo = explode(" ", $dbinfo);
$user = $dbinfo[1];
$password = $dbinfo[3];
$db = $dbinfo[5];
$host = $dbinfo[7];
$port = $dbinfo[9];
$table = $dbinfo[11];
$table_late = $dbinfo[19];

// table : UNION between early and late
$table = "( (SELECT * FROM `" . $table . "`) UNION ALL (SELECT * FROM `" . $table_late . "`) ) as `everybody`";

$mysqli = new mysqli($host, $user, $password, $db);

if ($mysqli->connect_errno) {
    printf("Connect failed: %s\n", $mysqli->connect_error);
    exit();
}

// utf-8 encoding
mysqli_set_charset($mysqli, 'utf8');


// OLD
// retrieve variables via GET and POST

$ID = $_GET['ID'];
$field = $_GET['field'];
$newValue = $_POST['newValue'];

// build UPDATE query (stringa)

if ($field == "SURNAME" or $field == "NAME") {
 $stringa = "UPDATE " . $table . " SET " . $field . "='" . $newValue . "' WHERE ID=" . $ID;   
} elseif ($field == "ROOM") {
 $stringa = "UPDATE test SET " . $field . "=" . $newValue . " WHERE ID=" . $ID; 
} else {
    // DOB: can update as a string, but there should be a format check
    $stringa = "UPDATE " . $table . " SET " . $field . "='" . $newValue . "' WHERE ID=" . $ID;
}

// send update query

$result = $mysqli->query($stringa);

//$result->free();
$mysqli->close();

// redirect to singleEntry.php, which reloads. so you see the updated value.
$url = "singleEntry.php?ID=" . $ID;
header('Location: ' . $url);

?>
