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


// ID
$ID = $_POST['ID'];
$ID_CHECK = $_POST['ID_CHECK'];

// new values
$surname = $_POST['new_SURNAME'];
$name = $_POST['new_NAME'];
$surname_strip = $_POST['new_SURNAME_STRIP'];
$name_strip = $_POST['new_NAME_STRIP'];
$dob = $_POST['new_DOB']; # AGE !
$sex = $_POST['new_SEX'];
$nationality = $_POST['new_NATIONALITY'];
$country_study = $_POST['new_COUNTRY_STUDY']; 
$degree = $_POST['new_DEGREE'];
$lcnc = $_POST['new_LCNC'];
//$delegate = $_POST['new_DELEGATE'];
$info = $_POST['new_INFO']; 
$email = $_POST['new_EMAIL'];
$university = $_POST['new_UNIVERSITY'];

// lcnc_bool
$lcnc_bool = ($lcnc == 'IM' ? 'IM' : 'LN');

// age
$datetime1 = new DateTime($dob);
$datetime2 = new DateTime('2017-08-07');
$interval = $datetime1->diff($datetime2);
$age = $interval->format('%y');


$stringa = "UPDATE `" . $table . "` SET `NAME` = \"" . $name . "\", `SURNAME` = \"" . $surname . "\", `NAME_STRIP` = \"" . $name_strip . "\", `SURNAME_STRIP` = \"" . $surname_strip . "\", `DOB` = \"" . $dob . "\", `SEX` = \"" . $sex . "\", `NATIONALITY` = \"" . $nationality . "\", `COUNTRY_STUDY` = \"" . $country_study . "\", `DEGREE` = \"" . $degree . "\", `LCNC` = \"" . $lcnc . "\", `LCNC_BOOL` = \"" . $lcnc_bool . "\", `INFO` = \"" . $info . "\", `EMAIL` = \"" . $email . "\", `AGE` = " . $age . ", `UNIVERSITY` = \"" . $university . "\" WHERE `ID` = " . $ID;

//var_dump($stringa);
//exit;

// execute the query
$result = $mysqli->query($stringa);


//$result->free();
$mysqli->close();  

header("location: singleEntry.php?ID=" . $ID . "&IDC=" . $ID_CHECK);
?>