<?php
// get VID from session
require('access.php');
$VID = $_SESSION['VID'];
// store activity in log
//require('sessioner.php');
//howManyIps();
//functions
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

$mysqli = new mysqli($host, $user, $password, $db);

if ($mysqli->connect_errno) {
    printf("Connect failed: %s\n", $mysqli->connect_error);
    exit();
}

// utf-8 encoding
mysqli_set_charset($mysqli, 'utf8');




//counters
// LCNC
$stringa = "SELECT LCNC,COUNT(LCNC) FROM " . $table . " WHERE STATUS='accepted' OR STATUS='proven' OR STATUS='participant' GROUP BY LCNC ORDER BY LCNC";
$result_counter = $mysqli->query($stringa);

// SEX
$stringa = "SELECT SEX,COUNT(SEX) FROM " . $table . " WHERE STATUS='accepted' OR STATUS='proven' OR STATUS='participant' GROUP BY SEX ORDER BY SEX";
$result_sex = $mysqli->query($stringa);
$NSEX = array('M'=>0,'F'=>0);
while($row_sex = $result_sex->fetch_array()) {
    $NSEX[$row_sex['SEX']] = $row_sex['COUNT(SEX)'];
}
$NTOT = $NSEX['M']+$NSEX['F'];

$SEXR = ($NTOT==0 ? 'n/a' : sprintf('%0.0f', 100.0*$NSEX['F']/$NTOT) ) . ' %';
//$SEXR = ($NTOT==0 ? 'n/a' : $SEXR ) . ' %';

// DEGREE
$stringa = "SELECT DEGREE,COUNT(DEGREE) FROM " . $table . " WHERE STATUS='accepted' OR STATUS='proven' OR STATUS='participant' GROUP BY DEGREE ORDER BY DEGREE";
$result_deg = $mysqli->query($stringa);
$NDEG = array('Bachelor'=>0,'Master'=>0,'PhD'=>0);
while($row_deg = $result_deg->fetch_array()) {
    $NDEG[$row_deg['DEGREE']] = $row_deg['COUNT(DEGREE)'];
}
if ($NTOT==0) {
    $Master_R = 'n/a';
    $PhD_R = 'n/a';
    $Bachelor_R = 'n/a';
} else {
    $Master_R = 100.0*$NDEG['Master']/$NTOT;
    $PhD_R = 100.0*$NDEG['PhD']/$NTOT;
    $Bachelor_R = 100.0-$Master_R-$PhD_R;

    $Master_R = sprintf('%0.0f', $Master_R);
    $PhD_R = sprintf('%0.0f', $PhD_R);
    $Bachelor_R = sprintf('%0.0f', $Bachelor_R);
}




?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Search results</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <!--><link rel="stylesheet" type="text/css" href="style.css"/></!-->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <!-- CSS for overriding td class -->
        <link rel="stylesheet" href="tdStyle.css">

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
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

        <?php

        if (isset($_GET['LCNC'])) {

            // query only with LCNC
            $lcnc = ($_GET['LCNC'] == 'NC UK ' ? 'NC UK & Ireland' : $_GET['LCNC']);
            if (isset($_GET['sorting'])) {
                $stringa = "SELECT * FROM " . $table . " WHERE LCNC='" .  $lcnc . "' ORDER BY " . $_GET['sorting'];
            } else {
                $stringa = "SELECT * FROM " . $table . " WHERE LCNC='" .  $lcnc . "' ORDER BY STATUS";
            }

        } else {
            // normal query

            // retrieve values from GET methods
            $query = $_GET['query']; 
            $sorting = $_GET['sorting'];
            $cfilter = $_GET['cfilter']; // operator
            $cfilter2 = $_GET['cfilter2']; // status
            
            if (!checkp(2,$VID) and $cfilter2!='participant') {
                $cfilter2 = 'participant';
            }

            $sorting = ( $sorting == 'LCNC' ? 'ISNULL(LCNC),LCNC' : $sorting);
            $sorting = ( $sorting == 'CONTRIBUTION' ? 'ISNULL(CONTRIBUTION),CONTRIBUTION' : $sorting);

            // string of gets in this url. need it to send it to the quick check-in, so that it can get me back here
            //$oldget = "query=" . $query . "&sorting=" . $sorting . "&cfilter=" . $cfilter;

            // filtering by operator
            if ($cfilter=='me') {
                $sfilter = 'AND OPERATOR = "' . $VID . '"';
            } elseif ($cfilter=='others') {
                $sfilter = 'AND OPERATOR != "' . $VID . '"';
            } elseif ($cfilter=='visa') {
                $sfilter = 'AND VISA = \'Yes\'';
            } else {
                $sfilter = '';
            }

            // filtering by status
            if ($cfilter2=='accepted') {
                $sfilter2 = "AND STATUS = 'accepted'";
            } elseif ($cfilter2=='rejected') {
                $sfilter2 = "AND STATUS = 'rejected'";
            } elseif ($cfilter2=='withdrawn') {
                $sfilter2 = "AND STATUS = 'withdrawn'";
            } elseif ($cfilter2=='proven') {
                $sfilter2 = "AND STATUS = 'proven'";
            } elseif ($cfilter2=='participant') {
                $sfilter2 = "AND STATUS = 'participant'";
            } else {
                $sfilter2 = "";
            }

            // query db
            $stringa = "SELECT * FROM " . $table . " WHERE (NAME LIKE '%".$query."%' OR SURNAME LIKE '%".$query."%' OR NAME_STRIP LIKE '%".$query."%' OR SURNAME_STRIP LIKE '%".$query."%' ) " . $sfilter . " " . $sfilter2 . " ORDER BY ".$sorting;

        }

        $result = $mysqli->query($stringa);
        $entries = $result->num_rows;

        // output number of rows found
        //echo "Compatible entries: ".$entries.". <br><br>";

        ?>

<?php if (checkp(2,$VID)): ?>
        <nav class="navbar navbar-inverse" data-spy="affix" data-offset-top="10">
            <ul class="nav navbar-nav">
                <!--<li class="active"><a href="#">Basic Topnav</a></li>-->
                <?php
                echo "<li><a " . fcapping_tot($NTOT) . " href=\"stats.php\"><b>ACCEPTED = " . $NTOT . "</b></a></li>";
                ?>
                <li><a href="#" style="background-color:black;color:white"><b>GIRLS AT <?php echo $SEXR; ?></b></a></li>
                <li><a href="#" style="background-color:black;color:white">BSc:<?php echo $Bachelor_R;?> MSc:<?php echo $Master_R;?> PhD:<?php echo $PhD_R;?> [%]</a></li>
                <?php
                while($row_counter = $result_counter->fetch_array()) {
                    echo "<li><a " . fcapping($row_counter['COUNT(LCNC)'],$row_counter['LCNC']) . " href=\"search.php?LCNC=" . $row_counter['LCNC'] . "\">" . $row_counter['LCNC'] . " = " . $row_counter['COUNT(LCNC)'] . "</a></li>";
                }
                ?>
            </ul>
        </nav>
        <?php endif; ?>

        <div class="container-fluid" style="height:1000px">

            <div class="row">
                <div class="col-md-2"></div>
                <div class="col-md-7"><h4>VID: <font color="green"><?php echo $VID;?></font></h4></div>
                <div class="col-md-3"></div>
            </div>

            <div class="row">
                <div class="col-md-2"></div>
                <div class="col-md-7"><?php echo "number of matching entries: ".$entries."<br><br>"; ?></div>
                <div class="col-md-1"><a class="btn btn-default" href="index.php" >New search</a></div>
                <div class="col-md-1">
                    <?php if (checkp(2,$VID)): ?>
                    <a class="btn btn-default" href="search.php?query=&sorting=SURNAME_STRIP&cfilter=all&cfilter2=all" >Full list</a>
                    <div class="col-md-1"><a class="btn btn-default" href="map.php" >Map</a></div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if( checkp(2,$VID) ): ?>
            <div class="row">
                <div class="col-md-1"></div>
                <div class="col-md-10"><b>LEGEND</b></div>
                <div class="col-md-1"></div>
            </div>

            <div class="row">
                <div class="col-md-1"></div>
                <div class="col-md-10">
                    <table class="table">
                        <tr>
                            <td class="<?php echo status_to_color('waiting');?>" style="text-align:center"><b>waiting</b></td>
                            <td class="<?php echo status_to_color('accepted');?>" style="text-align:center"><b>accepted</b></td>
                            <td class="<?php echo status_to_color('proven');?>" style="text-align:center"><b>proven</b></td>
                            <td class="<?php echo status_to_color('participant');?>" style="text-align:center"><b>participant</b></td>
                            <td class="<?php echo status_to_color('rejected');?>" style="text-align:center"><b>rejected</b></td>
                            <td class="<?php echo status_to_color('withdrawn');?>" style="text-align:center"><b>withdrawn</b></td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-1"></div>
            </div>
            <?php endif; ?>

            <br>

            <div class="row">
                <div class="col-md-1"></div>
                <div class="col-md-10">

                    <table class='table'>
                        <tr>


                            <?php

                            if (isset($cfilter)) {
                                $paseo = array('cfilter'=>$cfilter, 'cfilter2'=>$cfilter2, 'query'=>$query);
                            } else {
                                $paseo = array('lcnc'=>$lcnc);
                            }


                            function get_myurl($s,$a) {

                                if ( array_key_exists('cfilter',$a) ) {
                                    // normale
                                    $ek = "\"search.php?cfilter=" . $a['cfilter'] . "&cfilter2=" . $a['cfilter2'] . "&sorting=" . $s . "&query=" . $a['query'] . "\"";
                                } else {
                                    //lcnc
                                    $ek = "\"search.php?LCNC=" . $a['lcnc'] . "&sorting=" . $s . "\"";
                                }

                                return $ek;
                            }
                            ?>

                            <th><a href=<?php echo get_myurl("SURNAME_STRIP",$paseo);?> >SURNAME</a></th>
                            <th><a href=<?php echo get_myurl("NAME_STRIP",$paseo);?> >NAME</a></th>
                            <th><a href=<?php echo get_myurl("NATIONALITY",$paseo);?> >NATIONALITY</a></th>
                            <th><a href=<?php echo get_myurl("LCNC",$paseo);?> >LC/NC</a></th>
                            <?php if(checkp(2,$VID)): ?>
                            <th><a href=<?php echo get_myurl("DOB",$paseo);?> >D.O.B.</a></th>
                            <th><a href=<?php echo get_myurl("DELEGATE",$paseo);?> >DELEGATE</a></th>
                            <th><a href=<?php echo get_myurl("CONTRIBUTION",$paseo);?> >CONTRIBUTION</a></th>
                            <th><a href=<?php echo get_myurl("SUB_DATE",$paseo);?> >SUB TIME</a></th>
                            <th><a href=<?php echo get_myurl("STATUS",$paseo);?> >STATUS</a></th>
                            <?php endif; ?>
                        </tr>

                        <?php

                        // build table printing the rows found

                        while($row = $result->fetch_array())
                        {
                            $linkto = "singleEntry.php?ID=" . $row['ID'];

                            // various columns
                            $col_surname = "<td><a href=\"".$linkto."\">".$row['SURNAME']."</a></td>";

                            $col_name = "<td>".$row['NAME']."</td>";

                            $col_dob = "<td><small>".$row['DOB']."</small></td>";

                            $col_nat = "<td>".$row['NATIONALITY']."</td>";

                            $col_lcnc = "<td>" . ( $row['LCNC']=='IM' ? "" : $row['LCNC'] ) . "</td>";

                            // contribution
                            if ($row['CONTRIBUTION']=='no') {
                                $col_contr = "<td><span id=\"nourl\">NO</span></td>";
                            } else {
                                $col_contr = '';
                                if ($row['CONTRIBUTION']!='post') {
                                    $col_contr .= ($row['URL_TALK']=='' ? "<span id=\"nourl\">talk</span>" : "<span id=\"withurl\">talk</span>");
                                }
                                if ($row['CONTRIBUTION']!='talk') {
                                    $col_contr .= ($col_contr==''?'':' & ') . ($row['URL_POSTER']=='' ? "<span id=\"nourl\">poster</span>" : "<span id=\"withurl\">poster</span>");
                                }
                                $col_contr = '<td>' . $col_contr . '</td>';   
                            }

                            $col_deleg = "<td>" . ( $row['DELEGATE']=="No" ? "" : "yes" ) . "</td>";

                            // sub date
                            $col_subdate = "<td><small>" . $row['SUB_DATE'] . "</small></td>";

                            //status & operator
                            $printvid = (is_null($row['STATUS']) ? "" : $row['OPERATOR']);
                            $backcolor = status_to_color($row['STATUS']);
                            $col_status = "<td class=\"" . $backcolor . "\">" . $printvid . "</td>";

                            echo "<tr>" . $col_surname . $col_name . $col_nat . $col_lcnc;
                            if (checkp(2,$VID)) {
                                echo $col_dob . $col_deleg . $col_contr . $col_subdate . $col_status . "</tr>";
                            }
                            

                        }

                        $result->free();
                        $result_counter->free();
                        $result_sex->free();
                        $result_deg->free();
                        $mysqli->close();

                        ?>
                    </table>

                </div>
                <div class="col-md-1"></div>
            </div>


            <br>
            <div class="row">
                <div class="col-md-1"></div>
                <div class="col-md-10"><a class="btn btn-default" href="index.php" >New search</a></div>
                <div class="col-md-1"></div>
            </div>
            <br><br>

        </div>


    </body>
</html>