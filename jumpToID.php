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

if ($ID == '' or !is_numeric($ID)) {
    $ID = 999999;
}


// select single row, using ID
$stringa = "SELECT * FROM " . $table . " WHERE ID = ".$ID;
$result = $mysqli->query($stringa);
$entries = $result->num_rows;
$result->free();
$mysqli->close();

// should never be more than one result! (because ID must be unique)
if ($entries > 1) {
    die("CONFLICTING IDs !");
}

if ($entries < 1):
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Bad ID</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <!--><link rel="stylesheet" type="text/css" href="style.css"/></!-->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    </head>
    <body>

        <br>

        <div class="row">
            <div class="col-md-4"></div>
            <div class="col-md-4" style="text-align:center"><img style="width:162px;height:243px;" src="LOGO.jpg" alt="ICPS_logo"></div>
            <div class="col-md-4"></div>
        </div>

        <br><br>

        <div class="row" style="text-align:center">
            <div class="col-md-2"></div>
            <div class="col-md-8"><b>The ID does not exist</b></div>
            <div class="col-md-2"></div>
        </div>
        <br><br>
        <div class="row" style="text-align:center">
            <div class="col-md-2"></div>
            <div class="col-md-8"><a class="btn btn-default" href="index.php" >RETRY</a></div>
            <div class="col-md-2"></div>
        </div>

    </body>
</html>

<?php
endif;

if ($entries == 1){
    header('Location: ' . 'singleEntry.php?ID=' . $ID);
}


?>