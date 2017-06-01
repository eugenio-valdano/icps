<?php
// get VID from session
require('access.php');
$VID = $_SESSION['VID'];

// load functions
require('../util.php');

// connect to db
$dbinfo = explode("\n", file_get_contents('../loginDB.txt'))[0];
$dbinfo = explode(" ", $dbinfo);
$user = $dbinfo[1];
$password = $dbinfo[3];
$db = $dbinfo[5];
$host = $dbinfo[7];
$port = $dbinfo[9];
$table = $dbinfo[11];
$table_excursions = $dbinfo[15];
$table_late = $dbinfo[19];
$table_total = $dbinfo[21];

//choose table
$table = $table_total;

$mysqli = new mysqli($host, $user, $password, $db);

if ($mysqli->connect_errno) {
    printf("Connect failed: %s\n", $mysqli->connect_error);
    exit();
}

// utf-8 encoding
mysqli_set_charset($mysqli, 'utf8');


// read POST
$ID = $_POST['ID'];
$ID_CHECK = $_POST['ID_CHECK'];
$excursion = $_POST['excursion'];

// EXCURSION
$stringa = "SELECT * FROM " . $table_excursions . " WHERE ID = ".$ID;
$result_ex = $mysqli->query($stringa);
$entries_ex = $result_ex->num_rows;
$result_ex->free();
if ($entries_ex==0) {
    // not present 
    $stringa_edit = "INSERT INTO " . $table_excursions . "(`ID`,`EXCURSION_ASSIGNED`) VALUES (" . $ID . ",'" . $excursion . "')";

} elseif ($entries_ex==1) {
    // present
    $stringa_edit = "UPDATE " . $table_excursions . " SET EXCURSION_ASSIGNED = '" . $excursion . "' WHERE ID = ".$ID;

} else {
    die('More than one ID.');
}

// execute edit query
$result = $mysqli->query($stringa_edit);

$mysqli->close();

header("location: singleEntry.php?ID=" . $ID . '&IDC='. $ID_CHECK);

?>