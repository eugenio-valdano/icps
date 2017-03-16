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
$table_excursions = $dbinfo[15];

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
        $mysqli->close();

        ?>

        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-7"><h4>ID: <font color="green"><?php echo $VID;?></font></h4></div>
            <div class="col-md-3"></div>
        </div>

        <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-6">
                <table class='table'>
                    <!--
<tr>
<th>field</th>
<th>value</th>
</tr>
-->
                    <tr>
                        <td style="color:gray">UID</td>
                        <?php $uid = strtolower(substr($row['SURNAME_STRIP'],0,3)) . str_pad($row['ID'], 3, '0', STR_PAD_LEFT) ;?>
                        <td style="color:gray"><?php echo $uid;?></td>
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
                        <td>REQUIRES VISA</td>
                        <?php echo '<td class="' . ($row['VISA']=='Yes'?'danger':'success') . '">' . $row['VISA'] . '</td>'; ?>
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
                        <td>DELEGATE</td>
                        <td><?php echo $row['DELEGATE'];?></td>
                    </tr>
                    <tr>
                        <td>CONTRIBUTION</td>
                        <td><?php echo format_contribution($row);?></td>
                    </tr>
                    <tr>
                        <td>INFO</td>
                        <td><?php echo $row['INFO'];?></td>
                    </tr>
                    <tr>
                        <td>EMAIL</td>
                        <td><?php echo $row['EMAIL'];?></td>
                    </tr>
                    <tr>
                        <td>STATUS</td>
                        <?php
                        $printvid = ( is_null($row['STATUS']) ? "" : $row['OPERATOR']);
                        $backcolor = status_to_color($row['STATUS']);
                        $col_status = "<td class=\"" . $backcolor . "\">" . $printvid . "</td>";
                        echo $col_status;
                        // echo '"' . $row['ID'] . '"'
                        ?>
                    </tr>

                    <tr>

                        <?php if ( isset($privileges) and $privileges[$VID]<=1): ?>
                        <form action="editStatus.php" method="GET">
                            <td>
                                <select name="status" style="float:left">
                                    <option value="waiting"   <?php echo ($row['STATUS']=='waiting' ? 'disabled' : ''); ?> >Waiting list</option>
                                    <option value="accepted"  <?php echo ($row['STATUS']=='accepted' ? 'disabled' : ''); ?> >Accepted</option>
                                    <option value="proven"  <?php echo ($row['STATUS']=='proven' ? 'disabled' : ''); ?> >Proven</option>
                                    <option value="participant"  <?php echo ($row['STATUS']=='participant' ? 'disabled' : ''); ?> >Participant</option>
                                    <option value="rejected"  <?php echo ($row['STATUS']=='rejected' ? 'disabled' : ''); ?> >Rejected</option>
                                    <option value="withdrawn" <?php echo ($row['STATUS']=='withdrawn' ? 'disabled' : ''); ?> >Withdrawn</option>
                                </select>
                            </td>
                            <input type="hidden" name="ID" value=<?php echo '"' . $row['ID'] . '"' ?> />
                            <td>

                                <input type="submit" value="Change status"  style="float:left" />
                            </td>
                        </form>
                        <?php endif; ?>

                    </tr>

                </table>
            </div>
            <div class="col-md-3"></div>
        </div>
        <!-- ESCURSIONE -->
        <?php

        // binary status
        $status_bin = status_binary($row['STATUS']);
        if ($status_bin and $excursion=='none') {
            die('Should be inside excursions, as it is active');
        }

        // IF
        if ($status_bin):
        ?>
        <div class="row">
            <div class="col-md-3"><div class="tiny_skip"></div></div>
            <div class="col-md-6"><div class="tiny_skip"></div></div>
            <div class="col-md-3"><div class="tiny_skip"></div></div>
        </div>
        <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-6"><h4>Excursion</h4></div>
            <div class="col-md-3"></div>
        </div>
        <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-6"><b><?php echo $excursion; ?>:</b> <?php echo $dexcursions[$excursion]; ?></div>
            <div class="col-md-3"></div>
        </div>
        <div class="row">
            <div class="col-md-3"><div class="tiny_skip"></div></div>
            <div class="col-md-6"><div class="tiny_skip"></div></div>
            <div class="col-md-3"><div class="tiny_skip"></div></div>
        </div>
        <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-6">

                <table class='table'>
                    <tr>
                        <td>RANKING</td>
                        <td><?php echo $row['EXCURSIONS'];?></td>
                    </tr>
                    <tr>
                        <td>AUTOMATICALLY ASSIGNED</td>
                        <?php $classex = ($excursion != $excursion_autom ? "class=\"danger\"" : ""); ?>
                        <td <?php echo $classex . '>' . $excursion_autom; ?></td>
                    </tr>
                    <tr>

                        <?php if ( isset($privileges) and $privileges[$VID]==0): ?>
                        <form action="editExcursion.php" method="GET">
                            <td>
                                <select name="excursion" style="float:left">

                                    <?php
                                    foreach ($dexcursions as $key => $value) {
                                        echo "<option value=\"" . $key . "\" " . ($key==$excursion ? 'disabled' : '') . " >" . $key . ": " . $value . "</option>";
                                    }
                                    ?>
                                </select>
                            </td>
                            <input type="hidden" name="ID" value=<?php echo '"' . $ID . '"' ?> />
                            <td>

                                <input type="submit" value="Change excursion"  style="float:left" />
                            </td>
                        </form>
                        <?php endif; // end if privileges ?>

                    </tr>

                    <!-- FINE ESCURSIONE -->
                    <?php endif; // endif $status_bin ?>

                </table>
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
                            <td>PASSPORT No</td>
                            <td><?php echo $row['PASSPORT'];?></td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-3"></div>
            </div>






        </div>



        <br>
        <a class="btn btn-default" href="index.php" >New search</a>
        <a class="btn btn-default" href="search.php?query=&sorting=SURNAME_STRIP&cfilter=all&cfilter2=all" >Full list</a>
        <!-- go back button -->
        <button onclick="goBack()">Go Back</button>
        
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
        if ( isset($privileges) and $privileges[$VID]==0) {
            // EDIT button
            echo '<a class="btn btn-default" href="singleEntryEditable.php?ID=' . $ID . '" >Make editable</a>';
            echo '<br><br><br>';
            
            //URL for ABSTRACTS
            $urlab = 'http://www.ai-sf.it/dbicps/edit_abstract/?name='. $row['NAME_STRIP'] . '&surname=' . $row['SURNAME'] . '&uid='. $uid . '&email='. $row['EMAIL'];
            $urlab = str_replace(' ','%20',$urlab);
            echo 'edit abstract: <a id="urlab" href="' . $urlab . '">' . $urlab . '</a>';
            echo '<button onclick="copyToClipboard(\'#urlab\')">Copy URL</button><br>';
            
            //URL for PAYMENT
            $payim = ($row['LCNC']=='IM' ? 'yes' : 'no');
            $urlpa = 'http://www.ai-sf.it/dbicps/payment_master/?name='. $row['NAME_STRIP'] . '&surname=' . $row['SURNAME'] . '&uid='. $uid . '&im=' . $payim;
            $urlpa = str_replace(' ','%20',$urlpa);
            echo 'pay by cc: <a id="urlpa" href="' . $urlpa . '">' . $urlpa . '</a>';
            echo '<button onclick="copyToClipboard(\'#urlpa\')">Copy URL</button>';
            
        }  
        ?>

        <br><br><br>

    </body>
</html>