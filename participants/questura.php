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
$table_questura = $dbinfo[25];

//choose table
$table = $table_total;



$mysqli = new mysqli($host, $user, $password, $db);

if ($mysqli->connect_errno) {
    printf("Connect failed: %s\n", $mysqli->connect_error);
    exit();
}

// utf-8 encoding
mysqli_set_charset($mysqli, 'utf8');


// ID is sent via POST method
$ID = $_POST['ID'];
$ID_CHECK = $_POST['ID_CHECK'];
$type = $_POST['TYPE'];
$number = $_POST['NUMBER'];
$issue = $_POST['ISSUE'];
$expiry = $_POST['EXPIRY'];
$place = $_POST['PLACE'];

if ($type == '') {
    $type = 'NULL'; 
}
if ($number == '') {
    $number = 'NULL'; 
}
if ($issue == '') {
    $issue = 'NULL';
}
if ($expiry == '') {
    $expiry = 'NULL';
}
if ($place == '') {
    $place = 'NULL';
}

function f($s) {
    return ($s=='NULL' ? $s : '"' . $s . '"');
}

$stringa_q = "SELECT * FROM " . $table_questura . " WHERE ID = ".$ID;
$result_q = $mysqli->query($stringa_q);
$entries_q = $result_q->num_rows;
if ($entries_q==1) {
    $stringa = 'UPDATE ' . $table_questura . ' SET `TYPE`=' . f($type) . ', `NUMBER`=' . f($number) . ', `ISSUE`=' . f($issue) . ', `EXPIRY`=' . f($expiry) . ', `PLACE`=' . f($place) . ' WHERE `ID`=' . $ID;
} elseif ($entries_q==0) {
    $stringa = 'INSERT INTO '. $table_questura . ' (`ID`,`TYPE`,`NUMBER`,`ISSUE`,`EXPIRY`,`PLACE`) VALUES (' . $ID . ',' . f($type). ',' .  f($number). ',' . f($issue) . ',' . f($expiry) . ',' . f($place) . ') ';
} else {
    die('multiple entries in questura, pure php!');
}
//var_dump($stringa);

// execute the query
$result = $mysqli->query($stringa);


//$result->free();
$mysqli->close();  

header("location: registration.php?ID=" . $ID . "&IDC=" . $ID_CHECK);
exit;

?>