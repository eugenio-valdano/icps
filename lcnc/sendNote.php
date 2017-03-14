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

$ID = $_POST['ID'];
$testo = $_POST['comment'];

echo $ID . ' --- '. $testo;


?>