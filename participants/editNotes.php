<?php
// get VID from session
require('access.php');
$VID = $_SESSION['VID'];

// load functions
require('../util.php');


// PREVENT HACKING
if (!checkp(0,$VID)) {
    die('Not allowed to edit entries.');
}

// connect to db
$dbinfo = explode("\n", file_get_contents('../loginDB.txt'))[0];
$dbinfo = explode(" ", $dbinfo);
$user = $dbinfo[1];
$password = $dbinfo[3];
$db = $dbinfo[5];
$host = $dbinfo[7];
$port = $dbinfo[9];
$table = $dbinfo[11];
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

$ID = (int)$_POST['ID'];
$ID_CHECK = $_POST['ID_CHECK'];
$new_NOTES = $_POST['new_NOTES'];

// CHECK FOR HACKING
$stringa = 'SELECT `ID`,`ID_CHECK` FROM ' . $table . ' WHERE `ID`=' . $ID . ' AND `ID_CHECK`="' . $ID_CHECK . '"';
$result_check = $mysqli->query($stringa);
$entries_check = $result_check->num_rows;

if ($entries_check != 1) {
    header('Location: ' . '../rooms/acher.php');
    exit;
}
$result_check->free();

// update query
$stringa = 'UPDATE `' . $table . '` SET `NOTES` = "' . $new_NOTES . '" WHERE `ID` = ' . $ID;
$result = $mysqli->query($stringa);

$mysqli->close();  

header("location: registration.php?ID=" . $ID . "&IDC=" . $ID_CHECK);
exit();

?>
