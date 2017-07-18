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

//++++ REGISTRATION

// values: ('no', 'yes', 'out')
$reg_color = array('no'=>'warning', 'yes'=>'success', 'out'=>'info');
$reg_translate = array('no'=>'not arrived', 'yes'=>'checked in', 'out'=>'checked out');

$stringa = "SELECT `REGISTRATION`, COUNT(`REGISTRATION`) AS `a` FROM `participants` WHERE `RESIDENCE`='OLIMPIA' GROUP BY `REGISTRATION`";
$result_olimpia_r = $mysqli->query($stringa);
$stringa = "SELECT `REGISTRATION`, COUNT(`REGISTRATION`) AS `a` FROM `participants` WHERE `RESIDENCE`='VERDI' GROUP BY `REGISTRATION`";
$result_verdi_r = $mysqli->query($stringa);

$v_olimpia_r = array('no'=>0, 'yes'=>0, 'out'=>0);
while($row = $result_olimpia_r->fetch_array()) {
    $v_olimpia_r[$row['REGISTRATION']] = (int) $row['a'];
}
$v_verdi_r = array('no'=>0, 'yes'=>0, 'out'=>0);
while($row = $result_verdi_r->fetch_array()) {
    $v_verdi_r[$row['REGISTRATION']] = (int) $row['a'];
}


//++++ DEPOSIT

$stringa = "SELECT `DEPOSIT`, COUNT(`DEPOSIT`) AS `a` FROM `participants` WHERE `RESIDENCE`='OLIMPIA' GROUP BY `DEPOSIT`";
$result_olimpia = $mysqli->query($stringa);
$stringa = "SELECT `DEPOSIT`, COUNT(`DEPOSIT`) AS `a` FROM `participants` WHERE `RESIDENCE`='VERDI' GROUP BY `DEPOSIT`";
$result_verdi = $mysqli->query($stringa);

//DEPOSIT = ('not collected', 'collected', 'returned', 'withheld')
$dep_color = array('not collected'=>'danger', 'collected'=>'success', 'returned'=>'info', 'withheld'=>'warning');

$v_olimpia = array('not collected'=>0, 'collected'=>0, 'returned'=>0, 'withheld'=>0);
while($row = $result_olimpia->fetch_array()) {
    $v_olimpia[$row['DEPOSIT']] = (int) $row['a'];
}
$v_verdi = array('not collected'=>0, 'collected'=>0, 'returned'=>0, 'withheld'=>0);
while($row = $result_verdi->fetch_array()) {
    $v_verdi[$row['DEPOSIT']] = (int) $row['a'];
}


$result_olimpia->free();
$result_verdi->free();
$mysqli->close();
// . '( ' . $v_olimpia[$key]*$dil. ' euros)'
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Single entry result</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <!--><link rel="stylesheet" type="text/css" href="style.css"/></!-->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

        <!-- jquery -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

        <!-- CSS for overriding td class -->
        <link rel="stylesheet" href="tdStyle.css">

        <style>
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
            </ul>
        </nav>

        <div class="container-fluid" style="height:1000px">

            <!-- REGISTRATION -->

            <div class="row">
                <div class="col-md-2"></div>
                <div class="col-md-7"><h3>Registrants</h3></div>
                <div class="col-md-3"></div>
            </div>

            <div class="row">
                <div class="col-md-2"></div>
                <div class="col-md-7">
                    <table class="table">
                        <tr>
                            <th style="text-align: center; font-style: oblique">count</th>
                            <th>OLIMPIA</th>
                            <th>VERDI</th>
                            <th>TOTAL</th>
                        </tr>
                        <tr>
                            <?php $key = 'no';?>
                            <th class="<?php echo $reg_color[$key]?>"><?php echo $reg_translate[$key];?></th>
                            <td><?php echo $v_olimpia_r[$key];?></td>
                            <td><?php echo $v_verdi_r[$key];?></td>
                            <td><?php echo $v_olimpia_r[$key]+$v_verdi_r[$key];?></td>
                        </tr>
                        <tr>
                            <?php $key = 'yes';?>
                            <th class="<?php echo $reg_color[$key]?>"><?php echo $reg_translate[$key];?></th>
                            <td><?php echo $v_olimpia_r[$key];?></td>
                            <td><?php echo $v_verdi_r[$key];?></td>
                            <td><?php echo $v_olimpia_r[$key]+$v_verdi_r[$key];?></td>
                        </tr>
                        <tr>
                            <?php $key = 'out';?>
                            <th class="<?php echo $reg_color[$key]?>"><?php echo $reg_translate[$key];?></th>
                            <td><?php echo $v_olimpia_r[$key];?></td>
                            <td><?php echo $v_verdi_r[$key];?></td>
                            <td><?php echo $v_olimpia_r[$key]+$v_verdi_r[$key];?></td>
                        </tr>

                    </table>
                </div>
                <div class="col-md-3"></div>
            </div>


            <!-- DEPOSITS -->

            <div class="row">
                <div class="col-md-2"></div>
                <div class="col-md-7"><h3>Deposits</h3></div>
                <div class="col-md-3"></div>
            </div>

            <div class="row">
                <div class="col-md-2"></div>
                <div class="col-md-7">
                    <table class="table" id="myTable">
                        <tr>
                            <th style="text-align: center; font-style: oblique">count</th>
                            <th>OLIMPIA</th>
                            <th>VERDI</th>
                            <th>TOTAL</th>
                        </tr>
                        <tr>
                            <?php $key = 'not collected';?>
                            <th class="<?php echo $dep_color[$key]?>"><?php echo $key;?></th>
                            <td><?php echo $v_olimpia[$key];?></td>
                            <td><?php echo $v_verdi[$key];?></td>
                            <td><?php echo $v_olimpia[$key]+$v_verdi[$key];?></td>
                        </tr>
                        <tr>
                            <?php $key = 'collected';?>
                            <th class="<?php echo $dep_color[$key]?>"><?php echo $key;?></th>
                            <td><?php echo $v_olimpia[$key];?></td>
                            <td><?php echo $v_verdi[$key];?></td>
                            <td><?php echo $v_olimpia[$key]+$v_verdi[$key];?></td>
                        </tr>
                        <tr>
                            <?php $key = 'withheld';?>
                            <th class="<?php echo $dep_color[$key]?>"><?php echo $key;?></th>
                            <td><?php echo $v_olimpia[$key];?></td>
                            <td><?php echo $v_verdi[$key];?></td>
                            <td><?php echo $v_olimpia[$key]+$v_verdi[$key];?></td>
                        </tr>
                        <tr>
                            <?php $key = 'returned';?>
                            <th class="<?php echo $dep_color[$key]?>"><?php echo $key;?></th>
                            <td><?php echo $v_olimpia[$key];?></td>
                            <td><?php echo $v_verdi[$key];?></td>
                            <td><?php echo $v_olimpia[$key]+$v_verdi[$key];?></td>
                        </tr>

                    </table>
                </div>
                <div class="col-md-3"></div>
            </div>

            <div class="row">
                <div class="col-md-2"></div>
                <div class="col-md-7" style="text-align: center"><button onclick="currencer()" class="btn btn-primary">switch between count and &euro;</button></div>
                <div class="col-md-3"></div>
            </div>

            <script type='text/javascript'>

                var euroflag = false;
                var depositAmount = 50;

                function currencer() {

                    var myTable = document.getElementById('myTable');

                    if (euroflag) {

                        myTable.rows[0].cells[0].innerHTML = 'count';

                        for (r = 1; r <= 4; r++) {
                            for (c = 1;  c <= 3; c++) {
                                myTable.rows[r].cells[c].innerHTML = myTable.rows[r].cells[c].innerHTML / depositAmount;
                            }
                        }

                    } else {

                        myTable.rows[0].cells[0].innerHTML = 'euros (&euro;)';

                        for (r = 1; r <= 4; r++) {
                            for (c = 1;  c <= 3; c++) {
                                myTable.rows[r].cells[c].innerHTML = myTable.rows[r].cells[c].innerHTML * depositAmount;
                            }
                        }

                    }

                    euroflag = !euroflag;
                }
            </script>

        </div>

    </body>

</html>
