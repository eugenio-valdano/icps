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

// CHECK FOR HACKING
$stringa = "SELECT `ID`,`ID_CHECK`,`NAME`,`SURNAME` FROM " . $table . " WHERE `ID`=" . $ID . " AND `ID_CHECK`='" . $ID_CHECK . "'";
$result_check = $mysqli->query($stringa);
$entries_check = $result_check->num_rows;
if ($entries_check == 0) {
    header('Location: ' . '../rooms/acher.php');
    exit;
}
$row_check = $result_check->fetch_array();
$name = $row_check['NAME'] . ' ' . $row_check['SURNAME'];
$result_check->free();

// POSTER
$stringa = "SELECT * FROM " . $table_voting_p . " WHERE `ID`=" . $ID;
$rez_p = $mysqli->query($stringa);
$voted_p = ( $rez_p->num_rows>0 ? true : false );

if (!$voted_p) {

    $list_poster = array();
    $stringa = "SELECT `ID`,`SURNAME`,`NAME`  FROM " . $table . " WHERE `CONTRIBUTION`='post' OR `CONTRIBUTION`='both' ORDER BY `SURNAME`,`NAME`";
    $rez = $mysqli->query($stringa);
    while ( $row = $rez->fetch_array() ) {
        $list_poster[] = array( $row['ID'], $row['SURNAME'] . ' ' . $row['NAME'] ); 
    }
    $rez->free();

}

// TALK
$stringa = "SELECT * FROM " . $table_voting_t . " WHERE `ID`=" . $ID;
$rez_t = $mysqli->query($stringa);
$voted_t = ( $rez_t->num_rows>0 ? true : false );

if (!$voted_t) {

    $list_talk = array();
    $stringa = "SELECT `ID`,`SURNAME`,`NAME`  FROM " . $table . " WHERE `CONTRIBUTION`='talk' OR `CONTRIBUTION`='both' ORDER BY `SURNAME`,`NAME`";
    $rez = $mysqli->query($stringa);
    while ( $row = $rez->fetch_array() ) {
        $list_talk[] = array( $row['ID'], $row['SURNAME'] . ' ' . $row['NAME'] ); 
    }
    $rez->free();
}


$mysqli->close();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Best talk and poster</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <!--><link rel="stylesheet" type="text/css" href="style.css"/></!-->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="../participants/morestyle.css">

        <!-- jquery -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
        <!-- <script src="https://code.jquery.com/jquery-1.12.4.js"></script> -->
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

        <style>
            .small_skip {
                margin-top: 20px;
                margin-bottom: 20px;
            }
            .med_skip {
                margin-top: 20px;
                margin-bottom: 20px;
            }
        </style>
        
    </head>
    <body>

        <div class="med_skip"></div>

        <div class="row">
            <div class="col-md-2 col-xs-2"></div>
            <div class="col-md-8 col-xs-8">
                <h1 class="titolo">ICPS2017 Vote the best poster and talk</h1>
            </div>
            <div class="col-md-2 col-xs-2"></div>
        </div>

        <div class="med_skip"></div>
        
        <div class="row">
            <div class="col-md-2 col-xs-2"></div>
            <div class="col-md-8 col-xs-8" style="font-size: 14pt; text-align: center">
                Hello <?php echo $name; ?>! Here you can vote for the best poster and talk.<br>You can caste your votes only once.<br>You cannot change your vote once you have cast it.
            </div>
            <div class="col-md-2 col-xs-2"></div>
        </div>

        <div class="row">
            <div class="col-md-2 col-xs-2"></div>
            <div class="col-md-8 col-xs-8">
                <h2 class="titolo2">Best poster</h2>
            </div>
            <div class="col-md-2 col-xs-2"></div>
        </div>

        <?php if (!$voted_p): ?>
        <form action="cast.php" method="GET">
            <input type="hidden" name="ID" value="<?php echo $ID; ?>"/>
            <input type="hidden" name="IDC" value="<?php echo $ID_CHECK; ?>"/>
            <div class="row">
                <div class="col-md-2 col-xs-2"></div>
                <div class="col-md-2 col-xs-2"></div>
                <div class="col-md-3 col-xs-4" style="text-align:right">
                    <select name="poster" style="float:left">
                        <option value="-1" style="font-style:oblique">select a name</option>
                        <?php
                        foreach( $list_poster as $x) {
                            echo '<option value="' . $x[0] . '">' . $x[1] . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-5 col-xs-4"><input type="submit" value="cast vote" class="btn btn-primary" style="float:left" /></div>
            </div>
        </form>
        <?php else: ?>
        <div class="row">
            <div class="col-md-2 col-xs-2"></div>
            <div class="col-md-8 col-xs-8" style="text-align:center; font-style:oblique; font-size:18pt">You have already cast your vote.</div>
            <div class="col-md-2 col-xs-2"></div>
        </div>
        <?php endif; ?>

        <div class="med_skip"></div>
        <div class="med_skip"></div>

        <div class="row">
            <div class="col-md-2 col-xs-2"></div>
            <div class="col-md-8 col-xs-8">
                <h2 class="titolo2">Best talk</h2>
            </div>
            <div class="col-md-2 col-xs-2"></div>
        </div>

        <?php if (!$voted_t): ?>
        <form action="cast.php" method="GET">
            <input type="hidden" name="ID" value="<?php echo $ID; ?>"/>
            <input type="hidden" name="IDC" value="<?php echo $ID_CHECK; ?>"/>
            <div class="row">
                <div class="col-md-2 col-xs-2"></div>
                <div class="col-md-2 col-xs-2"></div>
                <div class="col-md-3 col-xs-4" style="text-align:right">
                    <select name="talk" style="float:left">
                        <option value="-1" style="font-style:oblique">select a name</option>
                        <?php
                        foreach( $list_talk as $x) {
                            echo '<option value="' . $x[0] . '">' . $x[1] . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-5  col-xs-4"><input type="submit" value="cast vote" class="btn btn-primary" style="float:left" /></div>
            </div>
        </form>
        <?php else: ?>
        <div class="row">
            <div class="col-md-2 col-xs-2"></div>
            <div class="col-md-8 col-xs-8" style="text-align:center; font-style:oblique; font-size:18pt">You have already cast your vote.</div>
            <div class="col-md-2 col-xs-2"></div>
        </div>
        <?php endif; ?>



    </body>


</html>