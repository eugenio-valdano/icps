<?php
// get VID from session
require('access.php');
$VID = $_SESSION['VID'];

// connect to db
$dbinfo = explode("\n", file_get_contents('loginDB.txt'))[0];
$dbinfo = explode(" ", $dbinfo);
$user = $dbinfo[1];
$password = $dbinfo[3];
$db = $dbinfo[5];
$host = $dbinfo[7];
$port = $dbinfo[9];
$table = $dbinfo[11];

$mysqli = new mysqli($host, $user, $password, $db);

if ($mysqli->connect_errno) {
    printf("Connect failed: %s\n", $mysqli->connect_error);
    exit();
}

// utf-8 encoding
mysqli_set_charset($mysqli, 'utf8');


// query
$stringa = "SELECT LCNC,COUNT(LCNC) FROM " . $table . " GROUP BY LCNC ORDER BY LCNC";
$result = $mysqli->query($stringa);
$entries = $result->num_rows;


while($row = $result->fetch_array()) {
    echo "<li><a href=\"#\">" . $row['LCNC'] . " = " . $row['COUNT'] . "</a></li>";
}

$result->free();
$mysqli->close();
?>