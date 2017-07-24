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
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Volley</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <!--><link rel="stylesheet" type="text/css" href="style.css"/></!-->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

        <!-- jquery -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>

        <!-- CSS for overriding td class -->
        <link rel="stylesheet" href="tdStyle.css">
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

        <style>
            /* Note: Try to remove the following lines to see the effect of CSS positioning */
            .affix {
                top: 0;
                width: 100%;
                z-index: 500;
            }

            .affix + .container-fluid {
                padding-top: 70px;
            }

            #withurl {
                color: #00007f !important;
                font-weight: bold;

            }
            #nourl {
                color: #939393 !important;
            }

        </style>

    </head>
    <body>

        <nav class="navbar navbar-inverse" data-spy="affix" data-offset-top="10">
            <ul class="nav navbar-nav">
                <li class="active"><a href="logout.php">VID: <?php echo $VID;?> (logout)</a></li>
                <li class="active"><a href="index.php">New search</a></li>
                <li class="active"><a href="search.php">Full list</a></li>
                <li class="active"><a href="excursions_stat.php">Excursions</a></li>
                <li class="active"><a href="roomView.php">Room view</a></li>
                <li class="active"><a href="registrationStats.php">Registration stats</a></li>
            </ul>
        </nav>

        <div class="container-fluid" style="height:1000px">

            <form>
                <select name="SPORT" style="float:left">
                    <option value="NULL">n/a</option>
                    <?php
                    $stringa = 'SELECT `ID`,`SURNAME`,`NAME`,`SEX` FROM ' . $table . ' ORDER BY `SURNAME_STRIP`';
                    $rex = $mysqli->query($stringa);
                    while($row = $rex->fetch_array()) {
                        echo '<option value="' . $row['ID'] . '">' . $row['SURNAME'] . ' ' . $row['NAME'] . '</option>';
                    }
                    
                    ?>
                </select>
                <input type="submit" value="change"  style="float:left" />
            </form>


        </div>

    </body>

    <?php
    $mysqli->close();
    ?>


</html>