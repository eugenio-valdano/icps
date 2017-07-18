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
#$table_excursions = $dbinfo[15];
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

$stringa = "SELECT `ROOM`, GROUP_CONCAT(`SURNAME`) AS `a`, GROUP_CONCAT(`ID`) as `b`, GROUP_CONCAT(`REGISTRATION`) as `c`, GROUP_CONCAT(`ID_CHECK`) as `d` FROM " . $table . " WHERE `RESIDENCE`='OLIMPIA' GROUP BY `ROOM` ORDER BY `ROOM`";
$result_olimpia = $mysqli->query($stringa);

$stringa = "SELECT `ROOM`, GROUP_CONCAT(`SURNAME`) AS `a`, GROUP_CONCAT(`ID`) as `b`, GROUP_CONCAT(`REGISTRATION`) as `c`, GROUP_CONCAT(`ID_CHECK`) as `d`  FROM " . $table . " WHERE `RESIDENCE`='VERDI' GROUP BY `ROOM` ORDER BY `ROOM`";
$result_verdi = $mysqli->query($stringa);

$reg_color = array('no'=>'warning', 'yes'=>'success', 'out'=>'info');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Room View</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <!--><link rel="stylesheet" type="text/css" href="style.css"/></!-->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

        <!-- jquery -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

        <!-- CSS for overriding td class -->
        <link rel="stylesheet" href="tdStyle.css">

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
                <li class="active"><a href="#OLIMPIA">Jump to Olimpia</a></li>
                <li class="active"><a href="#VERDI">Jump to Verdi</a></li>
                <li class="active"><a href="registrationStats.php">Registration stats</a></li>
            </ul>
        </nav>

        <div class="container-fluid" style="height:1000px">

            <div class="row">
                <div class="col-md-2"></div>
                <div class="col-md-7" id="OLIMPIA"><h3>OLIMPIA</h3></div>
                <div class="col-md-3"></div>
            </div>

            <div class="row">
                <div class="col-md-2"></div>
                <div class="col-md-7">
                    <table class="table">
                        <?php
                        while($row = $result_olimpia->fetch_array()) {
                            $spu  = '<td><b>O</b></td>';
                            $spu .= '<td>' . $row['ROOM'] . '</td>';

                            $sur = explode(',',$row['a']);
                            $ids = explode(',',$row['b']);
                            $sta = explode(',',$row['c']);
                            $idc = explode(',',$row['d']);
                            $data = array_map(null, $sur, $ids, $sta, $idc);

                            $spu .= '<td>';
                            foreach ($data as $x) {
                                $spak = $x[0] . ' (' . $x[1] . ')';
                                $linko = 'registration.php?ID=' . $x[1] . '&IDC=' . $x[3];

                                $spu .= '<a href="' . $linko . '" class="btn btn-' . $reg_color[$x[2]] . '">' . $spak . '</a> ';
                            }
                            $spu .= '</td>';
                            echo '<tr>' . $spu . '</tr>';
                        }
                        ?>
                    </table>

                </div>
                <div class="col-md-3"></div>
            </div>

            <div class="row">
                <div class="col-md-2"></div>
                <div class="col-md-7" id="VERDI"><h3>VERDI</h3></div>
                <div class="col-md-3"></div>
            </div>

            <div class="row">
                <div class="col-md-2"></div>
                <div class="col-md-7">
                    <table class="table">
                        <?php
                        while($row = $result_verdi->fetch_array()) {
                            $spu  = '<td><b>V</b></td>';
                            $spu .= '<td>' . $row['ROOM'] . '</td>';

                            $sur = explode(',',$row['a']);
                            $ids = explode(',',$row['b']);
                            $sta = explode(',',$row['c']);
                            $idc = explode(',',$row['d']);
                            $data = array_map(null, $sur, $ids, $sta, $idc);

                            $spu .= '<td>';
                            foreach ($data as $x) {
                                $spak = $x[0] . ' (' . $x[1] . ')';
                                $linko = 'registration.php?ID=' . $x[1] . '&IDC=' . $x[3];

                                $spu .= '<a href="' . $linko . '" class="btn btn-' . $reg_color[$x[2]] . '">' . $spak . '</a> ';
                            }
                            $spu .= '</td>';
                            echo '<tr>' . $spu . '</tr>';
                        }
                        ?>
                    </table>

                </div>
                <div class="col-md-3"></div>
            </div>
        </div>

        <?php
        $result_olimpia->free();
        $result_verdi->free();
        $mysqli->close();
        ?>

    </body>

</html>
