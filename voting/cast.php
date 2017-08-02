<?php

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
$table_questura = $dbinfo[25];
$table_voting_p = $dbinfo[27];
$table_voting_t = $dbinfo[29];

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

if ( isset($_GET['poster']) ) {
    $table_voting = $table_voting_p;
    $v = (int)$_GET['poster'];
} elseif ( isset($_GET['talk']) ) {
    $table_voting = $table_voting_t;
    $v = (int)$_GET['talk'];
} else {
    die('invalid talk/poster');
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


// check: if already voted, stop
$stringa = "SELECT * FROM " . $table_voting . " WHERE `ID`=" . $ID;
$rez = $mysqli->query($stringa);
$voted = ( $rez->num_rows>0 ? true : false );

if ($voted) {
    header("location: index.php?ID=" . $ID . "&IDC=" . $ID_CHECK);
    exit;
}

// check if valid ID
if ($v <= 0) {
    header("location: index.php?ID=" . $ID . "&IDC=" . $ID_CHECK);
    exit;
}

// cast vote
$stringa = 'INSERT INTO ' . $table_voting . ' (`ID`,`VOTE`) VALUES ' . ' (' . $ID . ',' . $v . ');';
$result = $mysqli->query($stringa);

$mysqli->close();


header("location: index.php?ID=" . $ID . "&IDC=" . $ID_CHECK);
exit;

?>