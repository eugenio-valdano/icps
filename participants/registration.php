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


/*
REGISTRATION = ('no', 'yes', 'out')
DEPOSIT = ('not collected', 'collected', 'returned', 'withheld')
OPERATOR -> null, or vid
SPORT -> null, 'pool', 'chess'
*/
$dep_color = array('not collected'=>'danger', 'collected'=>'success', 'returned'=>'info', 'withheld'=>'warning');
$reg_color = array('no'=>'warning', 'yes'=>'success', 'out'=>'info');
$reg_translate = array('no'=>'not arrived', 'yes'=>'checked in', 'out'=>'checked out');
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

        <!-- CSS for overriding td class -->
        <link rel="stylesheet" href="tdStyle.css">

    </head>
    <body>



        <?php

        // ID is sent via GET method
        $ID = $_GET['ID'];
        $ID_CHECK = $_GET['IDC'];

        // CHECK FOR HACKING
        $stringa = "SELECT `ID`,`ID_CHECK` FROM " . $table . " WHERE `ID`=" . $ID . " AND `ID_CHECK`='" . $ID_CHECK . "'";
        $result_check = $mysqli->query($stringa);
        $entries_check = $result_check->num_rows;
        if ($entries_check == 0) {
            header('Location: ' . '../rooms/acher.php');
            exit;
        }
        $result_check->free();


        // select single row, using ID
        $stringa = "SELECT * FROM " . $table . " WHERE ID = ".$ID;
        $result = $mysqli->query($stringa);
        $entries = $result->num_rows;

        // should never be more than one result! (because ID must be unique)
        if ($entries < 1) {
            die("ID NOT FOUND !");
        } elseif ($entries >1) {
            die("CONFLICTING IDs !");
        }

        // fetch data
        $row = $result->fetch_array();

        $result->free();

        // SPORT COUNTER
        $sport_counter = array('pool'=>0, 'chess'=>0);
        $stringa = 'SELECT `SPORT`,COUNT(`SPORT`) AS `a` FROM ' . $table . ' GROUP BY `SPORT`';
        $respo = $mysqli->query($stringa);
        while($row2 = $respo->fetch_array()) {
            if ( !is_null($row2['SPORT']) ) {
                $sport_counter[$row2['SPORT']] = (int) $row2['a'];
            }
        }
        $respo->free();

        ?>

        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-7"><h4>Logged in as: <?php echo $VID;?> (<a href="logout.php">log out</a>)</h4></div>
            <div class="col-md-3"></div>
        </div>

        <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-6">
                <table class='table'>

                    <tr>
                        <td style="color:gray">ID</td>
                        <td style="color:gray"><?php echo $ID . ' (' . $ID_CHECK . ')';?></td>
                    </tr>
                    <tr>
                        <td>SURNAME</td>
                        <td><?php echo '<b>' . $row['SURNAME'] . '</b>';?></td>
                    </tr>
                    <tr>
                        <td>NAME</td>
                        <td><?php echo '<b>' . $row['NAME'] . '</b>';?></td>
                    </tr>
                    <tr>
                        <td>NATIONALITY</td>
                        <td><?php echo '<b>' . $row['NATIONALITY'] . '</b>';?></td>
                    </tr>
                    <tr>
                        <td>PASSPORT</td>
                        <td><?php echo '<b>' . $row['PASSPORT'] . '</b>';?></td>
                    </tr>

                    <!-- ROOM ASSIGNMENT -->
                    <?php
                    if ( is_null($row['ROOM']) ) {
                        $col_roompref = '<i>no room</i>';
                    } else {
                        $col_roompref = '<b>' . substr($row['RESIDENCE'],0,1) . '</b> - ' . $row['ROOM'] ;
                    }

                    $pairing = (int)$row['ROOM_PAIRING'];
                    if ($pairing>0) {
                        $stronga = "SELECT `SURNAME`, `ID`, `ID_CHECK` FROM `" . $table . "` WHERE `ID`=" . $pairing;
                        $rosico = $mysqli->query($stronga);
                        $ronco = $rosico->fetch_array();
                        $linktoronco = "registration.php?ID=" . $ronco['ID'] . '&IDC=' . $ronco['ID_CHECK'];
                        $col_roompref .= "  --- sleeping with <a href=\"" . $linktoronco . "\">" . $ronco['SURNAME'] . " (" . $ronco['ID'] . ")</a>";
                    }

                    ?>
                    <tr>
                        <td>ROOM</td>
                        <td><?php echo $col_roompref;?></td>
                    </tr>

                    <!-- REGISTRATION -->
                    <tr>
                        <td class="<?php echo $reg_color[$row['REGISTRATION']];?>" style="text-align: center; vertical-align: center; font-weight:bold">
                            <?php echo 'status: ' . $reg_translate[$row['REGISTRATION']];?>
                        </td>

                        <form action="editCheckin.php" method="GET">
                            <td>
                                <select name="REGISTRATION" style="float:left">
                                    <option value="no" <?php echo ($row['REGISTRATION']=='no' ? 'disabled' : ''); ?> >set as not arrived</option>
                                    <option value="yes" <?php echo ($row['REGISTRATION']=='yes' ? 'disabled' : ''); ?> >check in</option>
                                    <option value="out" <?php echo ($row['REGISTRATION']=='out' ? 'disabled' : ''); ?> >check out</option>
                                </select>
                                <input type="hidden" name="ID" value=<?php echo '"' . $row['ID'] . '"' ?> />
                                <input type="hidden" name="IDC" value=<?php echo '"' . $row['ID_CHECK'] . '"' ?> />
                                <input type="submit" value="change"  style="float:left" />
                            </td>
                        </form>
                    </tr>

                    <!-- DEPOSIT -->
                    <tr>
                        <td class="<?php echo $dep_color[$row['DEPOSIT']];?>" style="text-align: center; vertical-align: center; font-weight:bold">
                            <?php echo 'deposit ' . $row['DEPOSIT'];?>
                        </td>

                        <form action="editCheckin.php" method="GET">
                            <td>
                                <select name="DEPOSIT" style="float:left">
                                    <option value="not collected" <?php echo ($row['DEPOSIT']=='not collected' ? 'disabled' : ''); ?> >Not collected</option>
                                    <option value="collected" <?php echo ($row['DEPOSIT']=='collected' ? 'disabled' : ''); ?> >Collected</option>
                                    <option value="returned" <?php echo ($row['DEPOSIT']=='returned' ? 'disabled' : ''); ?> >Returned</option>
                                    <option value="withheld" <?php echo ($row['DEPOSIT']=='withheld' ? 'disabled' : ''); ?> >Withheld</option>
                                </select>
                                <input type="hidden" name="ID" value=<?php echo '"' . $row['ID'] . '"' ?> />
                                <input type="hidden" name="IDC" value=<?php echo '"' . $row['ID_CHECK'] . '"' ?> />
                                <input type="submit" value="change"  style="float:left" />
                            </td>
                        </form>
                    </tr>

                    <!-- SPORT -->
                    <tr>
                        <td style="text-align: center; vertical-align: center; font-weight:bold">
                            <?php echo 'sport: ' . (is_null($row['SPORT']) ? 'n/a' : $row['SPORT']);?>
                        </td>

                        <form action="editCheckin.php" method="GET">
                            <td>
                                <select name="SPORT" style="float:left">
                                    <option value="NULL" <?php echo (is_null($row['SPORT']) ? 'disabled' : ''); ?> >n/a</option>
                                    <option value="pool" <?php echo ($row['SPORT']=='pool' ? 'disabled' : ''); ?> >pool</option>
                                    <option value="chess" <?php echo ($row['SPORT']=='chess' ? 'disabled' : ''); ?> >chess</option>
                                </select>
                                <input type="hidden" name="ID" value=<?php echo '"' . $row['ID'] . '"' ?> />
                                <input type="hidden" name="IDC" value=<?php echo '"' . $row['ID_CHECK'] . '"' ?> />
                                <input type="submit" value="change"  style="float:left" />
                                
                                <!-- sport counters -->
                                <br><br>
                                pool: <?php echo $sport_counter['pool'] . '/' . $dsport_cap['pool'] . ' ' . ($sport_counter['pool']>$dsport_cap['pool'] ? '<span style="color:red; font-weight:bold">overbooked</span>' : ''); ?>
                                <br>
                                chess: <?php echo $sport_counter['chess'] . '/' . $dsport_cap['chess'] . ' ' . ($sport_counter['chess']>$dsport_cap['chess'] ? '<span style="color:red; font-weight:bold">overbooked</span>' : ''); ?>
                                
                            </td>
                        </form>
                    </tr>

                    <!-- OPERATOR -->
                    <tr>
                        <td>LAST CHANGE MADE BY</td>
                        <td><?php echo (is_null($row['OPERATOR']) ? '<i>no changes made</i>' : $row['OPERATOR'] );?></td>
                    </tr>




                </table>
            </div>
            <div class="col-md-3"></div>
        </div>


        <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-6"><h4>Internal notes<br><small>Use no single or double quotes.</small></h4></div>
            <div class="col-md-3"></div>
        </div>


        <form method="POST" action="editNotes.php">
            <div class="row">
                <div class="col-md-3"></div>
                <div class="col-md-6">
                    <?php
                    echo "<input type=\"hidden\" name=\"ID\" value=\"" . $ID . "\"></input>";
                    echo "<input type=\"hidden\" name=\"ID_CHECK\" value=\"" . $ID_CHECK . "\"></input>";
                    echo "<textarea name=\"new_NOTES\" rows=\"5\" cols=\"50\">" . $row['NOTES'] . "</textarea>";
                    ?>
                </div>
                <div class="col-md-3"></div>
            </div>
            <div class="row">
                <div class="col-md-3"></div>
                <div class="col-md-6"><input type="submit" value="Edit notes" class="btn btn-primary" onclick="return confirm('Do you wish to edit the note?')"/></div>
                <div class="col-md-3"></div>
            </div>
        </form>



        <br><br>
        <?php $lonko = "singleEntry.php?ID=" . $ID . '&IDC=' . $ID_CHECK; ?>
        <a class="btn btn-success" href="<?php echo $lonko; ?>" >Go to personal page</a>
        <a class="btn btn-info" href="index.php" >New search</a>
        <a class="btn btn-info" href="search.php" >Full list</a>
        <!-- go back button -->
        <button onclick="goBack()" class="btn btn-info">Go Back</button>

        <script>
            function goBack() {
                window.history.back();
            }

            function copyToClipboard(element) {
                var $temp = $("<input>");
                $("body").append($temp);
                $temp.val($(element).text()).select();
                document.execCommand("copy");
                $temp.remove();
            }
        </script>

        <?php
        $mysqli->close();
        ?>

    </body>

</html>