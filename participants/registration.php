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
$table_questura = $dbinfo[25];

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
EDISU_DOCUMENTS -> null, 'yes'
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
        <!-- <script src="https://code.jquery.com/jquery-1.12.4.js"></script> -->
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

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
                        <td><?php echo $row['NATIONALITY'];?></td>
                    </tr>
                    <!--
<tr>
<td>PASSPORT</td>
<td><?php echo $row['PASSPORT'];?></td>
</tr>
-->
                    <tr>
                        <td>DATE OF BIRTH</td>
                        <td><?php echo $row['DOB'];?></td>
                    </tr>
                    <tr>
                        <td>DELEGATE</td>
                        <?php $spu = (is_null($row['DELEGATE']) ? 'no' : $row['DELEGATE_DETAIL']);?>
                        <td><?php echo $spu;?></td>
                    </tr>
                    <tr>
                        <td>EXPECTED CHECK-IN DATE</td>
                        <td><?php echo $row['CHECKIN_DATE'];?></td>
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
                    <tr>
                        <td>T-SHIRT SIZE</td>
                        <td><?php echo $row['TSHIRT_SIZE'];?></td>
                    </tr>
                    <tr>
                        <td>UNITO WIFI</td>
                        <td><?php echo 'username: <b>' . $row['UNITO_WIFI_USERNAME'] . '</b><br>password: <b>' . $row['UNITO_WIFI_PASSWORD'] . '</b>';?></td>
                    </tr>

                </table>
            </div>
            <div class="col-md-3"></div>
        </div>

        <br>

        <div class="row" style="font-weight:bold;text-align:center;font-size:18pt">
            <div class="col-md-3"></div>
            <div class="col-md-6">CHECK-IN: <span id="checkin_stato"></span></div>
            <div class="col-md-3"></div>
        </div>

        <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-6">
                <table class='table'>

                    <!-- QUESTURA -->

                    <?php
                    // type: 'passport', 'id', 'other'

                    $stringa_q = "SELECT * FROM " . $table_questura . " WHERE ID = ".$ID;
                    $result_q = $mysqli->query($stringa_q);
                    $entries_q = $result_q->num_rows;
                    if ($entries_q==1) {
                        $row_q = $result_q->fetch_array();
                        $questura_type = $row_q['TYPE'];
                        $questura_number = $row_q['NUMBER'];
                        $questura_issue = $row_q['ISSUE'];
                        $questura_expiry = $row_q['EXPIRY'];
                        $questura_place = $row_q['PLACE'];
                        if (is_null($questura_type) or is_null($questura_number) or is_null($questura_issue) or is_null($questura_expiry) or is_null($questura_place)) {
                            $sfondo = 'warning';
                        } else {
                            $sfondo = 'success';   
                        }
                        $precompilato = '#000000';
                    } elseif ($entries_q==0) {
                        $questura_type = '';
                        $questura_number = $row['PASSPORT'];
                        $questura_issue = '';
                        $questura_expiry = '';
                        $questura_place = '';
                        $sfondo = 'danger';
                        $precompilato = '#696969';
                    } else {
                        die('multiple entries in questura!');
                    }
                    ?>

                    <tr>
                        <td class="<?php echo $sfondo; ?>" style="text-align: center; vertical-align: center">DOCUMENT DETAILS</td>
                        <td>
                            <form method="POST" action="questura.php">
                                <input type="hidden" name="ID" value="<?php echo $ID; ?>"/>
                                <input type="hidden" name="ID_CHECK" value="<?php echo $ID_CHECK; ?>"/>
                                <div class="row">
                                    <div class="col-md-6">
                                        <?php echo ( ( $questura_type=='' or is_null($questura_type) ) ? 'set type to ' : '<i>' . 'type: ' . $questura_type . '</i>, change to '); ?>
                                    </div>
                                    <div class="col-md-6">
                                        <select name="TYPE" style="float:left">
                                            <option value="">n/a</option>
                                            <option value="passport">passport</option>
                                            <option value="id">id card</option>
                                            <option value="other">other (CAUTION)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        number
                                    </div>
                                    <div class="col-md-6">
                                        <style>
                                            #NUMBER {
                                                color: <?php echo $precompilato;?>;
                                            }
                                        </style>
                                        <textarea name="NUMBER" rows="1" cols="20" id="NUMBER"><?php echo $questura_number;?></textarea>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        place of issue
                                    </div>
                                    <div class="col-md-6">
                                        <textarea name="PLACE" rows="1" cols="20"><?php echo $questura_place;?></textarea>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        issue
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" id="date_issue" name="ISSUE" value="<?php echo $questura_issue;?>"/>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        expiry
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" id="date_expiry" name="EXPIRY" value="<?php echo $questura_expiry;?>"/>
                                    </div>
                                </div>
                                <script> // if input date is not supported, use datepicker from jQuery
                                    $.datepicker.setDefaults({ dateFormat: 'yy-mm-dd' });
                                    $('#date_issue').datepicker({
                                        changeMonth: true,
                                        changeYear: true
                                    });
                                    $('#date_expiry').datepicker({
                                        changeMonth: true,
                                        changeYear: true
                                    });
                                </script>

                                <div class="row">
                                    <div class="col-md-6">
                                        <input type="submit" value="save data"  style="float:left" />
                                    </div>
                                    <div class="col-md-6">
                                    </div>
                                </div>
                            </form>
                        </td>
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

                    <!-- EDISU -->

                    <tr>
                        <?php $edicheck = ( is_null($row['EDISU_DOCUMENTS']) ? false : true); ?>
                        <td class="<?php echo ($edicheck ? 'success' : 'danger') ?>" style="text-align: center; vertical-align: center">EDISU DOCUMENTS: SIGNED?</td>
                        <td>
                            <form action="editCheckin.php" method="GET">
                                <input type="hidden" name="ID" value=<?php echo '"' . $row['ID'] . '"' ?> />
                                <input type="hidden" name="IDC" value=<?php echo '"' . $row['ID_CHECK'] . '"' ?> />
                                <div class="row">
                                    <div class="col-md-12"><input type="radio" name="edisu" value="yes" <?php echo ($edicheck ? 'checked' : '');?>/> yes</div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3"><input type="radio" name="edisu" value="NULL" <?php echo ($edicheck ? '' : 'checked');?>/> no</div>
                                    <div class="col-md-9"><input type="submit" value="change"  style="float:left" /></div>
                                </div>
                            </form>

                        </td>
                    </tr>
                </table>
            </div>
            <div class="col-md-3"></div>
        </div>
        
        <br>
        
        <div class="row" style="font-weight:bold;text-align:center;font-size:18pt">
            <div class="col-md-3"></div>
            <div class="col-md-6">Sport selection (leave blank for football and volleyball)</div>
            <div class="col-md-3"></div>
        </div>

        <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-6">
                <table class='table'>

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
                                pool counter: <?php echo $sport_counter['pool'] . '/' . $dsport_cap['pool'] . ' ' . ($sport_counter['pool']>$dsport_cap['pool'] ? '<span style="color:red; font-weight:bold">overbooked</span>' : ''); ?>
                                <br>
                                chess counter: <?php echo $sport_counter['chess'] . '/' . $dsport_cap['chess'] . ' ' . ($sport_counter['chess']>$dsport_cap['chess'] ? '<span style="color:red; font-weight:bold">overbooked</span>' : ''); ?>

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

        <?php
        $flago = ( $sfondo=='success' and $row['REGISTRATION']!='no' and $row['DEPOSIT']!='not collected' and !is_null($row['EDISU_DOCUMENTS']) );
        $final_status = ( $flago ? 'COMPLETED' : 'INCOMPLETE');
        $final_color = ( $flago ? 'green' : 'red');
        ?>

        <script>
            //var str = $( "#checkin_stato" ).text();
            $( "#checkin_stato" ).text( "<?php echo $final_status;?>" );
            $( "#checkin_stato" ).css('color', '<?php echo $final_color;?>');
        </script>


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
        <?php $lonko = "registration_pdf.php?ID=" . $ID . '&IDC=' . $ID_CHECK; ?>
        <a class="btn btn-danger" href="<?php echo $lonko; ?>" target="_blank">Download as PDF</a>
        <br><br>

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