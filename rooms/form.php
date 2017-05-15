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
$dbinfo = explode("\n", file_get_contents('loginDB.txt'))[0];
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
$stringa = "SELECT `ID`,`ID_CHECK` FROM " . $table_total . " WHERE `ID`='" . $ID . "' AND `ID_CHECK`='" . $ID_CHECK;
$result_check = $mysqli->query($stringa);
$entries_check = $result_check->num_rows;
if ($entries_check == 0) {
    die("Your malicious hacking attempt has been detected and neutralized. The ICPS Department for Cyber Security has been informed, and this incident will be investigated. You will be prosecuted according to Italian Law, EU Regulations, and the Geneva Convention of 1949.");
}
$result_check->free();


//COUNT SINGLE ROOMS
$stringa = "SELECT `ID` FROM " . $table_total . " WHERE `PREFERENCE`=-1";
$result_sr = $mysqli->query($stringa);
$single_rooms_allocated = $result_sr->num_rows;
$result_sr->free();

// data about the person
$stringa = "SELECT * FROM " . $table_total . " WHERE `ID`=". $ID;
$result_personal = $mysqli->query($stringa);
$personal_data = $result->fetch_array();
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
        <link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.1/themes/base/minified/jquery-ui.min.css" type="text/css" />
        <script type="text/javascript" src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
        <script type="text/javascript" src="http://code.jquery.com/ui/1.10.1/jquery-ui.min.js"></script>

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
                <h2 style="text-align:center">Room selection system</h2>
            </div>
            <div class="col-md-2"></div>
        </div>

        <br>
        
        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <p>We have only single and double rooms. We're assigning single rooms on a first come first served basis, so if you see none left, it means we've already assigned all of them. You can either choose a single room, or a person to share the double room with. If someone has already chosen you to be their roommate, you will see their name, and have the chance to select them back, thus confirming your match. Otherwise, you can select a person among the participants. Start typing their name or surname, and see if they come up in the suggestions. If they do, select them. If they don't, it means they have already either selected a single room, or chosen someone else (sorry mate), so don't bother selecting them.</p>
                <p>If you select a person and they select you back, you'll very likely end up together. If they don't make any selection, you'll be assigned randomly to a room. If they select someone who's not you, you'll be notified of their lack of love, and asked to make another choice. But don't lose faith in humanity just yet, ICPS will be full of people and full of love!</p>
            </div>
            <div class="col-md-2"></div>
        </div>
        
        <br>

        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <h3 style="text-align:center">Hello, <?php echo $personal_data['NAME'] . ' ' . $personal_data['SURNAME'];?></h3>
            </div>
            <div class="col-md-2"></div>
        </div>

        <br>

        <form action="update.php" method="POST">
            <div class="row">
                <div class="col-md-2"></div>
                <div class="col-md-8">
                    <input type="hidden" name="ID" value="<?php echo $ID;?>"></input>
                <input type="hidden" name="ID_CHECK" value="<?php echo $ID_CHECK;?>"></input>

            <table class="table">

                <?php

                if ($personal_data['PREFERENCE']==0) {

                    // single room
                    echo '<tr>';
                    echo '<td>single room</td>';
                    echo '<td></td>';
                    if ($single_rooms_allocated < $N_SINGLE_ROOMS) {
                        echo '<td><input type="SUBMIT" value="-1" name="ID_CHOICE">choose</input></td>';
                        
                    } else {
                        echo '<td style="color:gray">no longer available</td>';
                    }
                    echo '</tr>';

                    // neighbors (who chose you)
                    while($row = $result->fetch_array()) {
                        echo '<tr>';
                        echo '<td>' . $row['SURNAME'] . '</td>';
                        echo '<td>' . $row['NAME'] . '</td>';
                        echo '<td><input type="SUBMIT" value='. $row['ID'] .' name="ID_CHOICE">choose</input></td>';
                        echo '</tr>';
                    }

                    // free choice
                    echo '<tr>';
                    echo '<td><input type="text" name="NAME_CHOICE" value="" class="auto"><td>';
                    echo '</tr>';

                } else {
                    // choice already made

                    // query previous preference
                    if ($personal_data['PREFERENCE']==-1) {
                        $chocco = 'single room.'
                    } else {

                        // data about the person
                        $stringa = "SELECT `SURNAME`,`NAME` FROM " . $table_total . " WHERE `ID`=". $personal_data['PREFERENCE'];
                        $result_chocco = $mysqli->query($stringa);
                        $data_chocco = $result_chocco->fetch_array();
                        $result_chocco->free();
                        $chocco = $data_chocco['NAME'] . ' ' . $data_chocco['SURNAME'];
                    }

                    echo 'You have already made your choice. You have chosen ' . $chocco;

                }

                ?>
            </table>



            </div>
        <div class="col-md-2"></div>
        </div>
    </form>

<!-- script for autocompletion -->
<script type="text/javascript">
    $(function() {
        //autocomplete
        $(".auto").autocomplete({
            source: "autocompletion.php",
            minLength: 3
        });                
    });
</script>

</body>

</html>

<?php
$result->free();
$mysqli->close();
?>