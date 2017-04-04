<?php
// get VID from session
require('access.php');
$VID = $_SESSION['VID'];
// store activity in log
//require('sessioner.php');
//howManyIps();

// functions
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
$table_late = $dbinfo[19];

// table : UNION between early and late
$table = "( (SELECT * FROM `" . $table . "`) UNION ALL (SELECT * FROM `" . $table_late . "`) ) as `everybody`";

$mysqli = new mysqli($host, $user, $password, $db);

if ($mysqli->connect_errno) {
    printf("Connect failed: %s\n", $mysqli->connect_error);
    exit();
}

// utf-8 encoding
mysqli_set_charset($mysqli, 'utf8');


// COUNT PER STATUS
$stringa = "SELECT `STATUS`,COUNT(`STATUS`) as `C` FROM " . $table . " GROUP BY `STATUS` ORDER BY `STATUS`";
$result_count = $mysqli->query($stringa);


// QUERY GLOBAL STATS

$listo = array(" WHERE STATUS='accepted' OR STATUS='proven' OR STATUS='participant'","");
$listo_output = array(); // $listo_output[0=solo accettati, 1=tutti][<nome variabile>]
for ($i = 0; $i < count($listo); ++$i) {

    // SEX
    $stringa = "SELECT SEX,COUNT(SEX) FROM " . $table . $listo[$i] . " GROUP BY SEX ORDER BY SEX";
    $result_sex = $mysqli->query($stringa);
    $NSEX = array('M'=>0,'F'=>0);
    while($row_sex = $result_sex->fetch_array()) {
        $NSEX[$row_sex['SEX']] = $row_sex['COUNT(SEX)'];
    }
    $NTOT = $NSEX['M']+$NSEX['F'];
    if ($NTOT==0) {
        $MALE_R = 'n/a';
        $FEMALE_R = 'n/a';
    } else {
        $MALE_R = sprintf('%0.0f', 100.0*$NSEX['M']/$NTOT);
        $FEMALE_R = sprintf('%0.0f', 100.0*$NSEX['F']/$NTOT);
    }
    /*
    $MALE_R = sprintf('%0.0f', 100.0*$NSEX['M']/$NTOT);
    $MALE_R = ($NTOT==0 ? 'n/a' : $MALE_R ) . ' %';
    $FEMALE_R = sprintf('%0.0f', 100.0*$NSEX['F']/$NTOT);
    $FEMALE_R = ($NTOT==0 ? 'n/a' : $FEMALE_R ) . ' %';
    */

    // DEGREE
    $stringa = "SELECT DEGREE,COUNT(DEGREE) FROM ". $table . $listo[$i] ." GROUP BY DEGREE ORDER BY DEGREE";
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

        $Master_R = sprintf('%0.0f', $Master_R) . ' %';
        $PhD_R = sprintf('%0.0f', $PhD_R) . ' %';
        $Bachelor_R = sprintf('%0.0f', $Bachelor_R) . ' %';
    }

    // 
    // LCNC
    //$stringa = "SELECT LCNC,COUNT(LCNC) FROM " . $table . " WHERE STATUS='accepted' GROUP BY LCNC ORDER BY LCNC";
    //$result_counter = $mysqli->query($stringa);

    // $NTOT, $MALE_R, $FEMALE_R, $Bachelor_R, $Master_R, $PhD_R
    array_push($listo_output, array("NTOT"=>$NTOT, "MALE_R"=>$MALE_R, "FEMALE_R"=>$FEMALE_R, "Bachelor_R"=>$Bachelor_R, "Master_R"=>$Master_R, "PhD_R"=>$PhD_R ) );

    $result_sex->free();
    $result_deg->free();
}

// QUERY LCNC: ATTENZIONE, si tratta di mySQL DURISSIMO

$stringa = "SELECT a.LCNC, a.`COUNT(LCNC)` as 'ALL',  IFNULL(b.`COUNT(LCNC)`,0) AS 'ACCEPTED' FROM ( SELECT LCNC,COUNT(LCNC) FROM " . $table . " GROUP BY LCNC ) a LEFT JOIN ( SELECT LCNC,COUNT(LCNC) FROM " . $table . " WHERE STATUS='accepted' OR STATUS='proven' OR STATUS='participant' GROUP BY LCNC ) b ON a.LCNC=b.LCNC;";
$result_lcnc = $mysqli->query($stringa); // now table with LCNC,ALL,ACCEPTED

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Search results</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <!--><link rel="stylesheet" type="text/css" href="style.css"/></!-->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <link rel="stylesheet" href="tdStyle.css"> <!-- CSS for overriding td class -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script> <!-- google charts -->


    </head>
    <body>

        <nav class="navbar navbar-inverse" data-spy="affix" data-offset-top="10">
            <ul class="nav navbar-nav">
                <li><a href="index.php"><b>NEW SEARCH</b></a></li>
                <li><a href="search.php?query=&sorting=SURNAME_STRIP&cfilter=all&cfilter2=all"><b>FULL LIST</b></a></li>
            </ul>
        </nav>


        <div class="container-fluid" style="height:1000px">


            <div class="row">
                <div class="col-md-2"></div>
                <div class="col-md-7">
                    <table class="table">
                        <?php
                        while($row = $result_count->fetch_array()) {
                            $colorclass = status_to_color($row['STATUS']);
                            $spu = '<td class="' . $colorclass . '">' . $row['STATUS'] . '</td><td  style="text-align:left">' . $row['C'] . '</td>';
                            echo '<tr>' . $spu . '</tr>';
                        }
                        ?>

                    </table>
                </div>
                <div class="col-md-3"></div>



                <div class="row" style="text-align:center">
                    <div class="col-md-4"></div>
                    <div class="col-md-4"><h1>SOME STATS</h1></div>
                    <div class="col-md-4"></div>
                </div>

                <br>




                <div class="row">
                    <div class="col-md-2"></div>
                    <div class="col-md-7">

                        <table class='table'>
                            <tr>
                                <th>ITEM</th>
                                <th>ALL SUBMISSIONS</th>
                                <th>ACCEPTED</th>
                            </tr>

                            <tr>
                                <td>COUNT</td>
                                <td><?php echo $listo_output[1]['NTOT'];?></td>
                                <?php
                                echo '<td ' . fcapping_tot($listo_output[0]['NTOT']) . '>' . $listo_output[0]['NTOT'] . '</td>';
                                ?>
                            </tr>

                            <tr>
                                <td>GIRLS</td>
                                <td><?php echo $listo_output[1]['FEMALE_R'];?></td>
                                <td><?php echo $listo_output[0]['FEMALE_R'];?></td>
                            </tr>

                            <tr>
                                <td>BOYS</td>
                                <td><?php echo $listo_output[1]['MALE_R'];?></td>
                                <td><?php echo $listo_output[0]['MALE_R'];?></td>
                            </tr>

                            <tr>
                                <td>BSc</td>
                                <td><?php echo $listo_output[1]['Bachelor_R'];?></td>
                                <td><?php echo $listo_output[0]['Bachelor_R'];?></td>
                            </tr>

                            <tr>
                                <td>MSc</td>
                                <td><?php echo $listo_output[1]['Master_R'];?></td>
                                <td><?php echo $listo_output[0]['Master_R'];?></td>
                            </tr>

                            <tr>
                                <td>PhD</td>
                                <td><?php echo $listo_output[1]['PhD_R'];?></td>
                                <td><?php echo $listo_output[0]['PhD_R'];?></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>

                            <?php

                            while($row = $result_lcnc->fetch_array()) {
                                echo '<tr>';
                                echo '<td>' . $row['LCNC'] . '</td>';
                                echo '<td>' . $row['ALL']. '</td>';
                                echo '<td ' . fcapping($row['ACCEPTED'],$row['LCNC']) . '>' . $row['ACCEPTED']. '</td>';
                                echo '</tr>';
                            }

                            ?>
                        </table>
                    </div>
                    <div class="col-md-3"></div>
                </div>

                <br><br>

                <div class="row" style="text-align:center">
                    <div class="col-md-2"></div>
                    <div class="col-md-7"><div id="chart_excursions"></div></div>
                    <div class="col-md-3"></div>
                </div>

                <br>

                <div class="row" style="text-align:center">
                    <div class="col-md-2"></div>
                    <div class="col-md-7">
                        <table class="table">
                            <?php
                            foreach ($dexcursions as $key => $value) {
                                $spu = '<td>' . $key . '</td><td  style="text-align:left">' . $value . '</td>';
                                echo '<tr>' . $spu . '</tr>';
                            }
                            ?>
                        </table>
                    </div>
                    <div class="col-md-3"></div>
                </div>

            </div>



            <!-- EXCURSIONS SCRIPT -->

            <?php

            $stringa = "SELECT `EXCURSION_ASSIGNED`,COUNT(`EXCURSION_ASSIGNED`) as `CONTO` FROM `" . $table_excursions . "` WHERE `ACTIVE`=1 GROUP BY `EXCURSION_ASSIGNED` ORDER BY `EXCURSION_ASSIGNED`";
            $result = $mysqli->query($stringa);
            $counter_ex = array();
            while ($row = $result->fetch_array()) {
                $conto = (int)$row['CONTO'];
                $colore = excursion_colorer($conto);
                array_push($counter_ex, array( $row['EXCURSION_ASSIGNED'], $conto, 'color: '. $colore ) );    
            }
            $result->free();
            ?>


            <script>
                google.charts.load('current', {packages: ['corechart', 'bar']});
                google.charts.setOnLoadCallback(drawBasic);

                function drawBasic() {

                    var data_raw = <?php echo json_encode($counter_ex); ?>;
                    data_raw.unshift(['Excursion','Count',{ role: 'style' }]);

                    var data = google.visualization.arrayToDataTable(data_raw);

                    var options = {
                        title: 'Escursion counter',
                        chartArea: {width: '80%'},
                        colors: ['#ADD8E6'],
                        legend: 'none',
                        hAxis: {
                            title: 'People assigned to the excursion',
                            minValue: 0
                        },
                        vAxis: {
                            title: 'Excursion'
                        }
                    };

                    var chart = new google.visualization.BarChart(document.getElementById('chart_excursions'));

                    chart.draw(data, options);
                }

            </script>

            <?php
            $mysqli->close();
            ?>

            </body>
        </html>