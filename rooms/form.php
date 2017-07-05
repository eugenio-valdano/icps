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

// GETs
$ID = $_GET['ID'];
$ID_CHECK = $_GET['IDC'];


//CHECK
$stringa = "SELECT `ID`,`ID_CHECK` FROM " . $table_total . " WHERE `ID`=" . $ID . " AND `ID_CHECK`='" . $ID_CHECK . "'";
$result_check = $mysqli->query($stringa);
$entries_check = $result_check->num_rows;
if ($entries_check == 0) {
    header('Location: ' . 'acher.php');
    exit;
}
$result_check->free();


//COUNT SINGLE ROOMS
$stringa = "SELECT `ID` FROM " . $table_total . " WHERE `PREFERENCE`=-1";
$result_sr = $mysqli->query($stringa);
$single_rooms_allocated = $result_sr->num_rows;
$result_sr->free();

//COUNT DOUBLE ROOMS
$stringa = "SELECT `ID` FROM " . $table_total . " WHERE `ROOM_DEF`>0";
$result_sr = $mysqli->query($stringa);
$double_rooms_allocated = $result_sr->num_rows / 2;
$result_sr->free();

// data about the person
$stringa = "SELECT * FROM " . $table_total . " WHERE `ID`=". $ID;
$result_personal = $mysqli->query($stringa);
$personal_data = $result_personal->fetch_array();
$result_personal->free();

//IN-NEIGHBORS
$stringa = "SELECT * FROM " . $table_total . " WHERE `PREFERENCE`=" . $ID;
$result = $mysqli->query($stringa);
$entries = $result->num_rows;


?>



<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Rooms</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <!--><link rel="stylesheet" type="text/css" href="style.css"/></!-->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <!-- CSS for overriding td class -->
        <link rel="stylesheet" href="tdStyle.css">

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

        <!-- css and js for the autocomplete -->
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

    </head>
    <body>

        <br>

        <div class="row">
            <div class="col-md-4"></div>
            <div class="col-md-4" style="text-align:center"><img style="width:135px;height:202px;" src="../LOGO.jpg" alt="ICPS_logo"></div>
            <div class="col-md-4"></div>
        </div>

        <br>

        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <h2 style="text-align:center">Room selection is no longer active. All rooms have been assigned.</h2>
            </div>
            <div class="col-md-2"></div>
        </div>

        <br>

</body>

</html>

<?php
$result->free();
$mysqli->close();
?>