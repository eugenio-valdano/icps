<?php
// get VID from session
//require('access.php');
//$VID = $_SESSION['VID'];
// store activity in log
//require('sessioner.php');
//howManyIps();
//functions
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
$table_late = $dbinfo[19];
$table_total = $dbinfo[21];

$mysqli = new mysqli($host, $user, $password, $db);

if ($mysqli->connect_errno) {
    printf("Connect failed: %s\n", $mysqli->connect_error);
    exit();
}

// utf-8 encoding
mysqli_set_charset($mysqli, 'utf8');


if (isset($_GET['term'])){
    
    $search_condition = "`SURNAME_STRIP` LIKE '%" . $_GET['term'] . "%' OR `NAME_STRIP` LIKE '%" . $_GET['term'] . "%' OR `SURNAME` LIKE '%" . $_GET['term'] . "%' OR `NAME` LIKE '%" . $_GET['term'] . "%'";
    $stringa = "SELECT * FROM `" . $table_total . "` WHERE (" . $search_condition . ");";
    //$stringa = "SELECT * FROM " . $table_total;
    $result = $mysqli->query($stringa);

    $return_arr = array();
    while($row = $result->fetch_array()) {
        $return_arr[] =  $row['SURNAME_STRIP'] . ' ' . $row['NAME_STRIP'];
    }

    echo json_encode($return_arr);
}


?>