<?php
// get VID from session
require('access.php');
$VID = $_SESSION['VID'];
// store activity in log
//require('sessioner.php');
//howManyIps();

// load functions
require('util.php');

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


$ID = $_GET['ID'];

$surname = $_GET['new_SURNAME'];
$name = $_GET['new_NAME'];
$surname_strip = $_GET['new_SURNAME_STRIP'];
$name_strip = $_GET['new_NAME_STRIP'];
$dob = $_GET['new_DOB']; # AGE !
$sex = $_GET['new_SEX'];
$nationality = $_GET['new_NATIONALITY'];
$country_study = $_GET['new_COUNTRY_STUDY']; 
$degree = $_GET['new_DEGREE'];
$lcnc = $_GET['new_LCNC'];
$delegate = $_GET['new_DELEGATE'];
$info = $_GET['new_INFO']; 
$email = $_GET['new_EMAIL'];

// lcnc_bool
$lcnc_bool = ($lcnc == 'IM' ? 'IM' : 'LN');

// age
$datetime1 = new DateTime($dob);
$datetime2 = new DateTime('2017-08-07');
$interval = $datetime1->diff($datetime2);
$age = $interval->format('%y');


$stringa = "UPDATE " . $table . " SET `NAME` = '" . $name . "', `SURNAME` = '" . $surname . "', `NAME_STRIP` = '" . $name_strip . "', `SURNAME_STRIP` = '" . $surname_strip . "', `DOB` = '" . $dob . "', `SEX` = '" . $sex . "', `NATIONALITY` = '" . $nationality . "', `COUNTRY_STUDY` = '" . $country_study . "', `DEGREE` = '" . $degree . "', `LCNC` = '" . $lcnc . "', `LCNC_BOOL` = '" . $lcnc_bool . "', `DELEGATE` = '" . $delegate . "', `INFO` = '" . $info . "', `EMAIL` = '" . $email . "', `AGE` = " . $age . " WHERE `ID` = " . $ID;

// execute the query only if you have privileges
if (checkp(0,$VID)) {
    $result = $mysqli->query($stringa);
}


//$result->free();
$mysqli->close();  

header("location: singleEntry.php?ID=" . $ID);

?>