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

        // EXCURSION
        $excursion = $row_ex['ASSIGNED'];
        $excursion_autom = $row_ex['EXCURSIONS'][0];

        /*
        $stringa = "SELECT * FROM " . $table_excursions . " WHERE ID = ".$ID;
        $result_ex = $mysqli->query($stringa);
        $entries_ex = $result_ex->num_rows;
        if ($entries_ex==0) {
            // if not present in excursion database
            $excursion = 'none';
            $excursion_autom = 'none';
        } else {
            $row_ex = $result_ex->fetch_array();
            //$excursion = ($row_ex['ACTIVE']==1 ? $row_ex['EXCURSION_ASSIGNED'] : 'none');
            $excursion = $row_ex['EXCURSION_ASSIGNED'];
            $excursion_autom = $row_ex['EXCURSION_ASSIGNED_AUTOMATIC'];
        }
        $result_ex->free();
        */

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
                        <?php
                        if ($row['AGE']<18) {
                            $ageflag = 'class="danger"';
                            $agemessage = '<br><b>UNDERAGE AT ICPS</b>';
                        } elseif ($row['AGE']>35) {
                            $ageflag = 'class="warning"';
                            $agemessage = '';
                        } else {
                            $ageflag = '';
                            $agemessage = '';
                        }
                        ?>

                        <td>D.O.B.</td>
                        <td <?php echo $ageflag;?>><?php echo $row['DOB'];?> (age <?php echo $row['AGE'];?>) <?php echo $agemessage;?></td>
                    </tr>
                    <tr>
                        <td>GENDER</td>
                        <td><?php echo $row['SEX'];?></td>
                    </tr>
                    <tr>
                        <td>NATIONALITY</td>
                        <td><?php echo $row['NATIONALITY'];?></td>
                    </tr>
                    <tr>
                        <td>COUNTRY OF STUDY</td>
                        <td><?php echo $row['COUNTRY_STUDY'];?></td>
                    </tr>
                    <tr>
                        <td>CURRENTLY ENROLLED IN</td>
                        <td><?php echo $row['DEGREE'];?></td>
                    </tr>
                    <tr>
                        <td>LC/NC</td>
                        <td><?php echo ($row['LCNC']=='IM' ? "Individual member" : $row['LCNC']);?></td>
                    </tr>
                    <tr>
                        <td>EMAIL</td>
                        <td><?php echo $row['EMAIL'];?></td>
                    </tr>
                    <tr>
                        <td>UNIVERSITY</td>
                        <td><?php echo $row['UNIVERSITY'];?></td>
                    </tr>
                    <tr>
                        <td>DELEGATE</td>
                        <td><?php echo $row['DELEGATE'];?></td>
                    </tr>
                    <tr>
                        <td>CONTRIBUTION</td>
                        <td><?php echo format_contribution($row);?></td>
                    </tr>

                    <!-- ROOM -->
                    <?php
                    $preference = (int)$row['PREFERENCE'];
                    if ($preference==0) {
                        $col_roompref = "-";
                    } elseif($preference==-1) {
                        $col_roompref = "<span style=\"font-style:italic;\">single</span>";
                    } else {
                        $stronga = "SELECT `SURNAME`, `ID` FROM `" . $table . "` WHERE `ID`=" . $preference;
                        $rosico = $mysqli->query($stronga);
                        $ronco = $rosico->fetch_array();
                        $linktoronco = "singleEntry.php?ID=" . $row['ID'] . '&IDC=' . $row['ID_CHECK'];
                        $col_roompref = "<a href=\"" . $linktoronco . "\">" . $ronco['SURNAME'] . " (" . $ronco['ID'] . ")</a>";

                    }
                    ?>
                    <tr>
                        <td>ROOM PREFERENCE</td>
                        <td><?php echo $col_roompref;?></td>
                    </tr>

                    <tr>
                        <td>EXCURSION</td>
                        <td><?php echo $row['ASSIGNED'];?></td>
                    </tr>

                    <tr>
                        <td>EXCURSION RANKING</td>
                        <td><?php echo $row['EXCURSIONS'];?></td>
                    </tr>



                </table>
            </div>
            <div class="col-md-3"></div>
        </div>
        <div class="col-md-3"></div>

        <!-- ADDRESS AND STUFF FOR VISA -->

        <div class="row">
            <div class="col-md-3"><div class="tiny_skip"></div></div>
            <div class="col-md-6"><div class="tiny_skip"></div></div>
            <div class="col-md-3"><div class="tiny_skip"></div></div>
        </div>

        <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-6"><h4>Additional details </h4></div>
            <div class="col-md-3"></div>
        </div>

        <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-6">
                <table class='table'>
                    <tr>
                        <td>INFO</td>
                        <td><?php echo $row['INFO'];?></td>
                    </tr>
                    <tr>
                        <td>REQUIRES VISA</td>
                        <td><?php echo '<b>' . $row['VISA'] . '</b>';?></td>
                    </tr>
                    <tr>
                        <td>PASSPORT No</td>
                        <td><?php echo $row['PASSPORT'];?></td>
                    </tr>
                    <tr>
                        <td>DIETARY REQUIREMENTS</td>
                        <td><?php echo $row['DIET'];?></td>
                    </tr>
                    <tr>
                        <td>ALLERGIES</td>
                        <td><?php echo $row['ALLERGIES'];?></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-3"></div>
        </div>

        <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-6"><h4>Address </h4></div>
            <div class="col-md-3"></div>
        </div>

        <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-6">
                <table class='table'>
                    <tr>
                        <td>STREET</td>
                        <td><?php echo $row['ADDRESS_1'] . ($row['ADDRESS_2']==''?'':'<br>'.$row['ADDRESS_2']);?></td>
                    </tr>
                    <tr>
                        <td>CITY</td>
                        <td><?php echo $row['CITY'];?></td>
                    </tr>
                    <tr>
                        <td>PROVINCE</td>
                        <td><?php echo $row['PROV'];?></td>
                    </tr>
                    <tr>
                        <td>ZIP CODE</td>
                        <td><?php echo $row['ZIP'];?></td>
                    </tr>
                    <tr>
                        <td>COUNTRY</td>
                        <td><?php echo $row['COUNTRY'];?></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-3"></div>
        </div>


        </div>


    <br>
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

    // close sql
    $mysqli->close();

    // EDIT button
    if ( checkp(0,$VID) ) {
        echo '<a class="btn btn-danger" href="singleEntryEditable.php?ID=' . $ID . '&IDC=' . $ID_CHECK . '" >Make editable</a>';   
    }
    echo '<br><br>';

    //URL for ABSTRACTS
    $urlab = 'http://www.ai-sf.it/dbicps/edit_abstract/?name='. $row['NAME_STRIP'] . '&surname=' . $row['SURNAME'] . '&uid='. $uid . '&email='. $row['EMAIL'];
    $urlab = str_replace(' ','%20',$urlab);


    //URL for PAYMENT
    $payim = ($row['LCNC']=='IM' ? 'yes' : 'no');
    //$lello = ($row['ID']>500 ? '&round=late' : '');
    //$urlpa = 'http://www.ai-sf.it/dbicps/payment_master/?name='. $row['NAME_STRIP'] . '&surname=' . $row['SURNAME'] . '&uid='. $uid . '&im=' . $payim . $lello;
    $urlpa = 'http://www.ai-sf.it/dbicps/payment_master/?name='. $row['NAME_STRIP'] . '&surname=' . $row['SURNAME'] . '&uid='. $uid . '&im=' . $payim;
    if ((int)$ID>500){
        $urlpa .= '&round=late';
    } 
    $urlpa = str_replace(' ','%20',$urlpa);

    // echoes
    echo 'edit abstract: <a id="urlab" href="' . $urlab . '" style="font-size:8pt;">' . $urlab . '</a><br>';
    echo 'pay by cc: <a id="urlpa" href="' . $urlpa . '" style="font-size:8pt;">' . $urlpa . '</a><br>';
    echo '<button class="btn btn-warning" onclick="copyToClipboard(\'#urlab\')">edit abstract: copy URL</button> ';
    echo '<button class="btn btn-warning" onclick="copyToClipboard(\'#urlpa\')">pay by cc: copy URL</button>';

    ?>

    <br><br><br>

    </body>
</html>