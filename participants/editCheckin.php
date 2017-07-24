<?php
// get VID from session
require('access.php');
$VID = $_SESSION['VID'];
// store activity in log
//require('sessioner.php');
//howManyIps();

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
#$table_excursions = $dbinfo[15];
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


// ID is sent via GET method
$ID = $_GET['ID'];
$ID_CHECK = $_GET['IDC'];

if (isset($_GET['DEPOSIT'])) {
    $var = 'DEPOSIT';
    $newv = $_GET['DEPOSIT'];
} elseif (isset($_GET['REGISTRATION'])) {
    $var = 'REGISTRATION';
    $newv = $_GET['REGISTRATION'];
} elseif (isset($_GET['SPORT'])) {
    $var = 'SPORT';
    $newv = $_GET['SPORT'];
} else {
    die('no get variable');
}

// CHECK FOR HACKING
$stringa = "SELECT `ID`,`ID_CHECK` FROM " . $table . " WHERE `ID`=" . $ID . " AND `ID_CHECK`='" . $ID_CHECK . "'";
$result_check = $mysqli->query($stringa);
$entries_check = $result_check->num_rows;
if ($entries_check == 0) {
    header('Location: ' . '../rooms/acher.php');
    exit;
}
$result_check->free();

if ($newv=='NULL') {
    $stringa = 'UPDATE ' . $table . ' SET `' . $var . '` = ' . $newv . ' WHERE ID = ' . $ID; // no quotes around NULL
} else {
    $stringa = 'UPDATE ' . $table . ' SET `' . $var . '` = "' . $newv . '" WHERE ID = ' . $ID;
}
$result = $mysqli->query($stringa);

$stringa = 'UPDATE ' . $table . ' SET `OPERATOR` = "' . $VID . '" WHERE ID = ' . $ID;
$result = $mysqli->query($stringa);

$mysqli->close();

header('Location: ' . "registration.php?ID=" . $ID . "&IDC=" . $ID_CHECK);
exit();

?>